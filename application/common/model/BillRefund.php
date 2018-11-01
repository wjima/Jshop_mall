<?php
namespace app\common\model;

use think\Db;

class BillRefund extends Common
{

    protected $pk = 'refund_id';

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    //软删除位
    protected $deleteTime = 'isdel';


    const STATUS_NOREFUND = 1;        //退款状态 未退款
    const STATUS_REFUND = 2;       //退款状态，已退款
    const STATUS_FAIL = 3;        //退款状态,退款失败，可以再次退款
    const STATUS_REFUSE = 4;        //退款拒绝


    const TYPE_ORDER = 1;       //单据类型 订单


    public function toAdd($user_id,$source_id,$type,$money,$aftersales_id)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if($money == 0){
            return error_code(13208);
        }

        $data['refund_id'] = get_sn(6);
        $data['money'] = $money;
        $data['user_id'] = $user_id;
        $data['aftersales_id'] = $aftersales_id;
        $data['source_id'] = $source_id;
        $data['type'] = $type;

        //取支付成功的支付单号
        $billPaymentModel = new BillPayments();
        $paymentsInfo = $billPaymentModel->getSuccessPaymentInfo($source_id,$type);
        if($paymentsInfo){
            $data['payment_code'] = $paymentsInfo['payment_code'];
            $data['trade_no'] = $paymentsInfo['trade_no'];      //支付成功时的第三方交易流水号
        }
        $this->save($data);

        $result['status'] = true;
        return $result;

    }

    /**
     * 退款单去退款或者拒绝
     * @param $seller_id    店铺id
     * @param $refund_id    退款单id
     * @param $status       2或者3，通过或者拒绝
     * @param string $payment_code      退款方式，如果和退款单上的一样，说明没有修改，原路返回，否则只记录状态，不做实际退款
     * @return array|\think\Config
     */
    public function toRefund($refund_id,$status,$payment_code="")
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $where = [
            'refund_id' => $refund_id,
            'status' => self::STATUS_NOREFUND
        ];
        $info = $this->where($where)->find();
        if(!$info){
            return error_code(13210);
        }

        if($status == self::STATUS_REFUND){
            //退款同意
            //如果前端传过来的退款方式和退款单上的退款方式一样的话，就说明是原路返回，试着调用支付方式的退款方法,如果不一样的话，就直接做退款单的退款状态为已退款就可以了
            if($payment_code == $info['payment_code'] && $payment_code!='offline'){//修复线下退款bug
                $result = $this->paymentRefund($refund_id);
            }else{
                //只修改状态，不做实际退款，实际退款线下去退。
                $data['status'] = self::STATUS_REFUND;
                $this->where($where)->data($data)->update();
                $result['status'] = true;
                $result['msg'] = '退款单退款成功';
            }
            //发送退款消息
            $eventData              = $info->toArray();
            sendMessage($info['user_id'], 'refund_success', $eventData);
            return $result;
        }elseif($status == self::STATUS_REFUSE){
            //退款拒绝

            $data['status'] = $status;
            $this->where($where)->data($data)->update();
            $result['status'] = true;
            $result['msg'] = '退款单拒绝成功';
            return $result;
        }else{
            return error_code(10000);
        }
    }

    /**
     * 如果是在线支付的原路退还，去做退款操作
     */
    public function paymentRefund($refund_id)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $where[] = ['refund_id' ,'eq', $refund_id];
        $where[] = ['status' ,'neq', self::STATUS_REFUND];

        $info = $this->where($where)->find();
        if(!$info){
            return error_code(13210);
        }

        //取支付成功的支付单号
        $billPaymentModel = new BillPayments();
        $paymentsInfo = $billPaymentModel->getSuccessPaymentInfo($info['source_id'],$info['type']);
        if(!$paymentsInfo){
            $result['msg'] = '没有找到支付成功的支付单号';
            return $result;
        }
        if($info['payment_code'] != $paymentsInfo['payment_code']){
            $result['msg'] = '退款单退款方式和支付方式不一样，原路退还失败';
            return $result;
        }

        //取此支付方式的信息，然后去支付
        $paymentsModel = new Payments();
        $paymentInfo = $paymentsModel->getPayment($info['payment_code'], $paymentsModel::PAYMENT_STATUS_YES);
        if(!$paymentInfo){
            return error_code(10050);
        }

        $conf = json_decode($paymentInfo['params'],true);

        //去退款
        $payment = \org\Payment::create($info['payment_code'],$conf);
        $re = $payment->refund($info,$paymentsInfo);

        Db::startTrans();
        try {
            if($re['status']){
                $data['status'] = self::STATUS_REFUND;
            }else{
                $data['status'] = self::STATUS_FAIL;
                $data['memo'] = $re['msg'];
            }
            $this->where($where)->data($data)->update();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }




        return $re;


    }


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
        $re['sql'] = $this->getLastSql();

        return $re;
    }
    protected function tableWhere($post)
    {
        $where = [];
//        if(isset($post['source_id']) && $post['source_id'] != ""){      //这个得关联查询：：todo
//            $where[] = ['order_id', 'like', '%'.$post['order_id'].'%'];
//        }
        if(isset($post['refund_id']) && $post['refund_id'] != ""){
            $where[] = ['refund_id', 'like', '%'.$post['refund_id'].'%'];
        }
        if(isset($post['mobile']) && $post['mobile'] != ""){
            if($user_id = get_user_id($post['mobile'])){
                $where[] = ['user_id', 'eq', $user_id];
            }else{
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }

        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }
        if(isset($post['type']) && $post['type'] != ""){
            $where[] = ['type', 'eq', $post['type']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "status asc,utime desc";
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
                $list[$k]['status_name'] = config('params.bill_refund')['status'][$v['status']];
            }
            if($v['user_id']) {
                $list[$k]['user_id'] = get_user_info($v['user_id']);
            }

            if($v['ctime']) {
                $list[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
            }
            if($v['type']) {
                $list[$k]['type'] = config('params.bill_refund')['type'][$v['type']];
            }
            if($v['payment_code']) {
                $list[$k]['payment_code'] = get_payment_info($v['payment_code']);
            }

        }
        return $list;
    }
}
