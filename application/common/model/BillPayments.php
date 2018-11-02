<?php
namespace app\common\model;

use think\Validate;
use think\Db;
use think\model\concern\SoftDelete;

class BillPayments extends Common
{

    protected $pk = 'payment_id';

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    //软删除位
    protected $deleteTime = 'isdel';

    const STATUS_NOPAY = 1;        //支付状态 未支付
    const STATUS_PAYED = 2;       //支付状态，已支付
    const STATUS_OTHER = 3;        //支付状态，其他

    const TYPE_ORDER = 1;       //单据类型 订单

//
//    const SEX_BOY = 1;
//    const SEX_GIRL = 2;
//    const SEX_OTHER = 3;
//
//    //protected $resultSetType = 'collection';
//
//    protected $rule = [
//        'username' => 'length:6,20|alphaDash',
//        'mobile' => ['regex' => '^1[3|4|5|7|8][0-9]\d{4,8}$'],
//        'sex' => 'in:1,2,3',
//        'nickname' => 'length:2,50',
//        'password' => 'confirm:re_password',
//    ];
//    protected $msg = [
//        'username.length' => '用户名长度6~20位',
//        'username.alphaDash' => '用户名只能是字母、数字或下划线组成',
//        'mobile' => '请输入一个合法的手机号码',
//        'sex' => '请选择合法的性别',
//        'nickname' => '昵称长度为2-50个字符',
//        'password' => '两次输入密码不一致',
//    ];

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;

        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['payment_id']) && $post['payment_id'] != ""){
            $where[] = ['payment_id', 'like', '%'.$post['payment_id'].'%'];
        }
        if(isset($post['mobile']) && $post['mobile'] != ""){
            if($user_id = get_user_id($post['mobile'])){
                $where[] = ['user_id', 'eq', $user_id];
            }else{
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }
        if(isset($post['trade_no']) && $post['trade_no'] != ""){
            $where[] = ['trade_no', 'eq', $post['trade_no']];
        }
        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }
        if(isset($post['type']) && $post['type'] != ""){
            $where[] = ['type', 'eq', $post['type']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = 'utime desc';
        return $result;
    }
    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k => $v) {
            if($v['status']) {
                $list[$k]['status'] = config('params.bill_payments')['status'][$v['status']];
            }
            if($v['user_id']) {
                $list[$k]['user_id'] = get_user_info($v['user_id']);
            }
            if($v['payment_code']) {
                $list[$k]['payment_code'] = config('params.payments')[$v['payment_code']];
            }
            if($v['utime']) {
                $list[$k]['utime'] = getTime($v['utime']);
            }
            if($v['type']) {
                $list[$k]['type'] = config('params.bill_payments')['type'][$v['type']];
            }
        }
        return $list;
    }

    /**
     * 支付，先生成支付单，然后去支付
     * @param $source_str
     * @param $payment_code
     * @param string $user_id
     * @param int $type
     * @param $params           支付的时候用到的参数，如果是微信支付的话，这里可以传trade_type=>'JSAPI'(小程序支付),或者'MWEB'(h5支付),当是JSPI的时候，可以不传其他参数了，默认就可以，默认的这个值就是JSAPI，如果是MWEB的话，需要传wap_url(网站url地址)参数和wap_name（网站名称）参数，其他支付方式需要传什么参数这个以后再说
     * @return mixed
     */
    public function pay($source_str, $payment_code, $user_id = '', $type = self::TYPE_ORDER,$params = []){

        //判断支付方式是否开启
        $paymentsModel = new Payments();
        $paymentInfo = $paymentsModel->getPayment($payment_code, $paymentsModel::PAYMENT_STATUS_YES);
        if(!$paymentInfo){
            return error_code(10050);
        }
        $result = $this->toAdd($source_str, $payment_code, $user_id, $type,$params);
        if(!$result['status']){
            return $result;
        }


        $conf = json_decode($paymentInfo['params'],true);

        //去支付
        $payment = \org\Payment::create($payment_code,$conf);       //'wechatpay'
        $result1 = $payment->pay($result['data']);

        //更新
        if($result1['status'])
        {
            $wh[] = ['payment_id', 'eq', $result['data']['payment_id']];
            $da['generate_params'] = json_encode($result1['data']);
            $this->save($da, $wh);
        }

        return $result1;

    }

    /**
     * 生成支付单,只是单纯的生成了支付单
     * @param $source_str           资源id字段
     * @param $payment_code         支付方式
     * @param string $user_id       支付用户id
     * @param int $type             支付类型
     * @return array
     */
    public function toAdd($source_str, $payment_code, $user_id = '', $type = self::TYPE_ORDER,$params = [])
    {
        $result = [
            'status' => false,
            'data' => array(),
            'msg' => ''
        ];
        //判断支付方式
        $paymentsModel = new Payments();
        $paymentsInfo = $paymentsModel->getPayment($payment_code, $paymentsModel::PAYMENT_STATUS_YES);
        if(!$paymentsInfo){
            return error_code(10058);
        }

        $paymentRel = $this->formatPaymentRel($source_str, $type);
        if(!$paymentRel['status']){
            return $paymentRel;
        }

        Db::startTrans();
        try {
            $data['payment_id'] = get_sn(2);
            $data['money'] = $paymentRel['data']['money'];
            if($user_id == ''){
                $data['user_id'] = $paymentRel['data']['user_id'];
            }else{
                $data['user_id'] = $user_id;
            }
            $data['payment_code'] = $payment_code;
            $data['ip'] = get_client_ip();
            $data['params'] = json_encode($params);         //支付的时候，用到的参数
            $this->save($data);
            //上面保存好收款单表，下面保存收款单明细表
            foreach($paymentRel['data']['rel'] as $k => $v){
                $rel_data['payment_id'] = $data['payment_id'];
                $rel_data['source_id'] = $v['source_id'];
                $rel_data['money'] = $v['money'];
                $rel_arr[] = $rel_data;
            }
            $billPaymentsRelModel = new BillPaymentsRel();
            $billPaymentsRelModel->saveAll($rel_arr);

            Db::commit();


        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
        //判断支付单金额是否为0，如果为0，直接支付成功,
        if($data['money'] == 0 ||$data['money'] == '0' || $data['money'] == '0.00'){
            $this->toUpdate($data['payment_id'],$this::STATUS_PAYED,$data['payment_code'],'金额为0，自动支付成功','');
            return error_code(10059);
        }
        $result['status'] = true;
        $result['data'] = $data;
        return $result;


    }

    /**
     * 支付成功后，更新支付单状态
     * @param $payment_id
     * @param $status     支付单状态
     * @param string $payment_code  支付方式编码
     * @param string $payed_msg     支付回调后的状态描述
     * @param string $trade_no      第三方支付单号
     * @return array
     */
    public function toUpdate($payment_id, $status, $payment_code='', $payed_msg='', $trade_no='')
    {
        $result = [
            'status' => false,
            'data' => array(),
            'msg' => ''
        ];
        $data['status'] = $status;
        $data['payment_code'] = $payment_code;
        $data['payed_msg'] = $payed_msg;
        $data['trade_no'] = $trade_no;

        $where['payment_id'] = $payment_id;
        $where['status'] = self::STATUS_NOPAY;
        $billPaymentInfo = $this->where($where)->find();
        if(!$billPaymentInfo){
            $result['msg'] = '没有找到此未支付的支付单号';
            return $result;
        }

        Db::startTrans();
        try {
            $this->where($where)->data($data)->update();
            if($status == self::STATUS_PAYED){
                $billPaymentsRelModel = new BillPaymentsRel();
                $billPaymentRelList = $billPaymentsRelModel->where(array('payment_id'=>$payment_id))->select();
                if($billPaymentInfo['type'] == self::TYPE_ORDER){
                    //如果是订单类型，做支付后处理
                    $orderModel = new Order();
                    foreach($billPaymentRelList as $k => $v){
                        $orderModel->pay($v['source_id'], $payment_code);
                    }
                }elseif(false){
                    //::todo 其他业务逻辑
                }

                //存储微信消息模板 todo::不清楚 $billPaymentInfo['generate_params'] 里面存的什么数据格式
                if(isset($billPaymentInfo['generate_params']))
                {
                    $generate_params = json_decode($billPaymentInfo['generate_params'], true);
                    if($generate_params['package'])
                    {
                        $prepay_id = str_replace("prepay_id=","", $generate_params['package']);
                        if($prepay_id)
                        {
                            //获取orderid
                            $billPaymentID = $billPaymentsRelModel->where(array('payment_id'=>$payment_id))->find();

                            $templateMessageModel = new TemplateMessage();
                            $message = [
                                'type' => $templateMessageModel::TYPE_PAYMENT,
                                'code' => $billPaymentID['source_id'],
                                'form_id' => $prepay_id,
                                'status' => $templateMessageModel::SEND_STATUS_NO
                            ];
                            $templateMessageModel->addSend($message);
                        }
                    }
                }
            }
            Db::commit();
            $result['status'] = true;
            $result['data'] = $payment_id;
            $result['msg'] = '支付成功';
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            trace("支付单".$billPaymentInfo['payment_id']."更新状态报错，报:".$result['msg'],'money');
            return $result;

        }

        return $result;

    }

    /**
     * 生成支付单的时候，格式化支付单明细
     * @param $source_str
     * @param $type
     * @return array
     */
    public function formatPaymentRel($source_str,$type)
    {
        $result = [
            'status' => false,
            'data' => [],
//                'user_id' => '',                //用户id
//                'money' => 0,                   //总金额
//                'rel' => array()
            'msg' => ''
        ];
        $source_arr = explode(',',$source_str);
        if($type == self::TYPE_ORDER){
            //如果是订单生成支付单的话，取第一条订单的店铺id，后面的所有订单都要保证是此店铺的id
            $orderModel = new Order();

            $data['money'] = 0;
            foreach($source_arr as $k => $v){
                $where['order_id'] = $v;
                $where['pay_status'] = $orderModel::PAY_STATUS_NO;
                $where['status'] = $orderModel::ORDER_STATUS_NORMAL;
                $order_info = $orderModel->where($where)->find();
                if($order_info){
                    $data['rel'][] = array(
                        'source_id' => $v,
                        'money' => $order_info->order_amount,
                    );
                    $data['money'] += $order_info->order_amount;
                }else {
                    $result['msg'] = '订单号：'.$v.'没有找到,或不是未支付状态';
                    return $result;
                }
            }
            $result['status'] = true;
            $result['data'] = $data;
        }elseif(false){
            //::todo 其他业务逻辑
        }else{
            return error_code(10054);
        }

        return $result;

    }

    /**
     * 取支付单明细
     * @param $payment_id
     * @return array|null|\PDOStatement|string|\think\Model
     */
    public function getInfo($payment_id)
    {
        $result = [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];
        $where['payment_id'] = $payment_id;
        $billPaymentInfo = $this->where($where)->find();
        if(!$billPaymentInfo){
            $result['msg'] = '没有找到此支付记录';
            return error_code(10002);
        }
        $billPaymentInfo['rel'] = $billPaymentInfo->rel;
        $result['data'] = $billPaymentInfo;
        return $result;
    }

    //根据资源id和类型取支付成功的支付单,可能查不到内容，所以，要用的话，在外面一定要判断一下。
    public function getSuccessPaymentInfo($source_id,$type){
        return $this
            ->field('p.*,pr.source_id,pr.money as pr_money')
            ->alias('p')
            ->join(config('database.prefix').'bill_payments_rel pr','pr.payment_id = p.payment_id')
            ->where([
                ['pr.source_id','eq',$source_id],
                ['p.type', 'eq', $type],
                ['p.status','eq',self::STATUS_PAYED]
            ])
            ->find();

    }


    //一对多关联支付单明细
    public function rel()
    {
        return $this->hasMany('BillPaymentsRel','payment_id','payment_id');
    }

    /**
     * 支付单统计
     * @return array
     */
    public function statistics()
    {
        $num = 7;
        $day = date('Y-m-d', strtotime('-'.$num.' day'));
        $sql = 'SELECT DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d") as day, count(*) as nums FROM '.config('database.prefix')
            .'bill_payments WHERE from_unixtime(ctime) >= "'.$day.'" AND `status` = 2 AND `type` = 1 GROUP BY DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d")';
        $res = Db::query($sql);
        $data = get_lately_days($num, $res);
        return ['day' => $data['day'], 'data' => $data['data']];
    }
}