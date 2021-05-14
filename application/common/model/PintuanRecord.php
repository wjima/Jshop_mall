<?php
namespace app\common\model;



use app\common\model\Goods;
use think\Model;
use think\Db;

class PintuanRecord extends Model{

    const STATUS_COMM = 1; //拼团中
    const STATUS_FULL = 2;  //团满了
    const STATUS_FINISH = 3; //开团成功
    const STATUS_FAIL = 4; //开团失败

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';


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

    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {

        $where = [];
        if (isset($post['order_id']) && $post['order_id'] != "") {
            $where[] = ['order_id', 'eq', $post['order_id']];
        }

        if (isset($post['team_id']) && $post['team_id'] != "") {
            $where[] = ['team_id', 'eq', $post['team_id']];
        }

        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['ctime' => 'desc'];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k=>$v){

            $list[$k]['status_name'] = config('params.pintuan_record')['status'][$v['status']];

            $list[$k]['user_nickname'] = get_user_info($v['user_id'],'nickname');
            $list[$k]['close_time'] = getTime($v['close_time']);
            $list[$k]['ctime'] = getTime($v['ctime']);
            $list[$k]['utime'] = getTime($v['utime']);


        }
        return $list;
    }


    //生成订单的时候，增加信息
    public function orderAdd($order, $items, $params){

        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $pintuanGoodsModel = new PintuanGoods();
        $pintuanRuleModel = new PintuanRule();

        //获得订单明细的第一条记录，因为拼团只有一个商品，所以，就获取第一条记录即可
        $order_item = current($items);

        if(isset($params['team_id'])){
            //参加别人拼团
            $pr_where[] = ['id', 'eq',$params['team_id']];
            $pr_where[] = ['close_time','>',time()];
            $pr_where[] = ['status','eq',self::STATUS_COMM];
            $info = $this->where($pr_where)->find();
            if(!$info){
                return error_code(15607);
            }
            //判断订单商品是否是要参加的拼团的商品
            if($info['goods_id'] != $order_item['goods_id']){
                return error_code(15608);
            }
            if($info['status'] == self::STATUS_COMM){
                //参加团
                $model = new PintuanRecord();
                $model->team_id = $info['team_id'];
                $model->user_id = $order['user_id'];
                $model->rule_id = $info['rule_id'];
                $model->status = self::STATUS_COMM;
                $model->order_id = $order['order_id'];
                $model->goods_id = $order_item['goods_id'];

                $model->close_time = $info['close_time'];

                $model->save();

                //判断团是否满了，如果满了，就更新状态
                $params = json_decode($info['params'],true);
                if(isset($params['people_number'])){
                    $team_where[] = ['team_id','eq',$info['team_id']];
                    $team_count = $this->where($team_where)->count();
                    if($team_count >= $params['people_number']){
                        $team_data['status'] = self::STATUS_FULL;       //如果如果团满了，就做状态
                        $this->save($team_data,$team_where);
                    }
                }
            }else{
                //开新团
                $this->openTeam($order, $order_item['goods_id']);
            }
        }else{
            //去开团
            $this->openTeam($order, $order_item['goods_id']);
        }

        $result['status'] = true;
        return $result;
    }

    //订单支付之后，更新拼团状态，如果拼团满了，
    public function pay($order_id){
        $Promotion = new PromotionRecord();
        $where[] = ['order_id','=', $order_id];
        $where[] = ['status', 'in',[self::STATUS_COMM,self::STATUS_FULL]];
        $info = $this->where($where)->find();
        if(!$info){
            return false;
        }

        //去取开团人的开团记录，取里面的拼团人数
        $finfo = $this->where('id',$info['team_id'])->find();
        if(!$finfo){
            return false;
        }
        $params = json_decode($finfo['params'],true);
        if(!isset($params['people_number'])){
            return false;
        }

        //去取参加拼团的人数
        $team_where[] = ['pr.team_id','eq',$info['team_id']];
        $team_where[] = ['pr.status', 'in', [self::STATUS_COMM,self::STATUS_FULL]];
        $team_where[] = ['o.pay_status', '>', 1];
        $team_count = $this
            ->alias('pr')
            ->join('order o', 'o.order_id = pr.order_id')
            ->where($team_where)
            ->count();
        if($team_count >= $params['people_number']){
            $team_data['status'] = self::STATUS_FINISH;       //如果拼团成功
            $this->save($team_data,['team_id'=>$info['team_id']]);
//            $Promotion->sendmessage($team_where);//拼团成功发送消息
        }

        return true;
    }

    //开团
    private function openTeam($order,$goods_id){
        $pintuanGoodsModel = new PintuanGoods();
        $pintuanRuleModel = new PintuanRule();
        // 自己创建拼团
        $where = [];
        //取得规则id
        $where[] = ['status','eq',$pintuanRuleModel::STATUS_ON];
        $where[] = ['goods_id', 'eq', $goods_id];

        $pinfo = $pintuanGoodsModel
            ->alias('pg')
            ->join('pintuan_rule pr','pr.id = pg.rule_id')
            ->where($where)
            ->find();

        if(!$pinfo){
            return error_code(10000);
        }
        $model = new PintuanRecord();
        $model->user_id = $order['user_id'];
        $model->rule_id = $pinfo['id'];
        $model->status = self::STATUS_COMM;
        $model->order_id = $order['order_id'];
        $model->goods_id = $goods_id;

        //冗余拼团人数，拼团结束时间字段
        $model->close_time = time() + $pinfo['significant_interval'] * 3600;
        $params = [
            'people_number' => $pinfo['people_number']
        ];
        $model->params = json_encode($params);
        $model->save();
        $model->team_id = $model->id;
        $model->save();
    }

    /**
     * 取得商品的所有拼团记录
     * @param $rule_id
     * @param $goods_id
     * @param int $status
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getRecord($rule_id, $goods_id, $status = 0, $page = 1, $limit = 10)
    {
        $result           = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $pintuanRuleModel = new PintuanRule();
        $pinfo            = $pintuanRuleModel->where('id', $rule_id)->find();
        if (!$pinfo) {
            return error_code(10000);
        }

        $orderModel = new Order();

        if ($status != 0) {
            $where[] = ['status', 'eq', $status];
            if ($status == 1) {       //如果取的是当前正在进行的团的话，这里取还没有结束的团记录，
                $where[] = ['close_time', '>', time()];
            }
        }
        $where[] = ['rule_id', 'eq', $rule_id];
        $where[] = ['goods_id', 'eq', $goods_id];


        $data = $this->where($where)->where("id = team_id")->page($page, $limit)->select();

        $count = $this->where($where)->where("id = team_id")->count();
        $ruleModel = new PintuanRule();
        if (!$data->isEmpty()) {
            foreach ($data as $k => $v) {
                $ruleInfo = $ruleModel->where("id","eq",$v["rule_id"])->find();
                if($data[$k]['close_time'] >= $ruleInfo['etime']){
                    $data[$k]['close_time'] = $ruleInfo['etime'];
                }
                $data[$k]['user_avatar'] = _sImage(get_user_info($v['user_id'], 'avatar'));
                $data[$k]['nickname']    = get_user_info($v['user_id'], 'nickname');
                //获取拼团团队记录
                $where1            = [];
                $where1[]          = ['team_id', 'eq', $v['team_id']];
                $data[$k]['teams'] = $this->where($where1)->select();
                foreach ($data[$k]['teams'] as $i => $j) {
                    $data[$k]['teams'][$i]['user_avatar'] = _sImage(get_user_info($j['user_id'], 'avatar'));
                    $data[$k]['teams'][$i]['nickname']    = get_user_info($j['user_id'], 'nickname');
                }
                //计算还剩几个人拼成功
                $data[$k]['team_nums'] = count($data[$k]['teams']);
                if ($data[$k]['team_nums'] < $pinfo['people_number']) {
                    $data[$k]['team_nums'] = $pinfo['people_number'] - $data[$k]['team_nums'];
                } else {
                    unset($data[$k]);
                }


            }
            $result['data'] = $data;
        }
        return $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $data,
            'total'  => ceil($count / $limit)
        ];


    }

    //获取拼团团队人数
    public function getTeamList($team_id=0,$order_id=0){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '',
        ];
        if($team_id == 0 && $order_id == 0){
            return error_code(15606);
        }

        if($team_id == 0){
            //根据订单id取teamid
            $where_order[] = ['order_id','eq', $order_id];
            $info = $this->where($where_order)->find();
            if (!$info) {
                // $result['msg'] = "没有找到拼团记录";
                return error_code(15605);
            }
            $team_id = $info['team_id'];
        }

        //根据team_id取发起团的信息
        $first_team = $this->where('id',$team_id)->find();
        if(!$first_team){
            return error_code(15609);
        }
        
        $first_team['user_avatar'] = _sImage(get_user_info($first_team['user_id'],'avatar'));
        $first_team['nickname'] = get_user_info($first_team['user_id'],'nickname');
        //获取拼团团队记录
        $where1[] = ['team_id', 'eq', $team_id];
        $first_team['teams'] = $this->where($where1)->select();
        foreach($first_team['teams'] as $i => $j){
            $first_team['teams'][$i]['user_avatar'] = _sImage(get_user_info($j['user_id'],'avatar'));
            $first_team['teams'][$i]['nickname'] = get_user_info($j['user_id'],'nickname');
        }
        //计算还剩几个人拼成功
        $first_team['team_nums'] = count($first_team['teams']);
        $params = json_decode($first_team['params'],true);

        if($first_team['team_nums'] < $params['people_number']){
            $first_team['team_nums'] = $params['people_number'] - $first_team['team_nums'];
        }else{
            $first_team['team_nums'] = 0;
        }
        $first_team['people_number'] = $params['people_number'];
        return $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $first_team
        ];
    }

    /**
     * 自动取消到时间的团
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function autoCancle()
    {
        $time = time();
        $where[] = ['pre.close_time', '<', $time];
        $where[] = ['pre.status', 'in', [self::STATUS_COMM,self::STATUS_FULL]];

        $list = $this
            ->alias('pre')
            ->where($where)
            ->where("pre.id = pre.team_id")
            ->field('pre.*')
            ->select();

        if (!$list->isEmpty()) {
            foreach ($list as $v) {
                $team_list = $this->where('team_id', $v['id'])->select();
                //更新拼团状态为失败
                $data['status']     = self::STATUS_FAIL;
                $pintuanRecordModel = new PintuanRecord();
                $pintuanRecordModel->save($data, ['team_id' => $v['id']]);

                if (!$team_list->isEmpty()) {
                    foreach ($team_list as $j) {

                        //给这个订单作废，如果有支付，并退款
                        $this->cancleOrder($j['order_id']);
                    }
                }
            }
        }

        //结束掉的拼团活动里的拼团记录，也需要关闭掉
        $where = [];
        $where[] = ['pre.status', 'in', [self::STATUS_COMM,self::STATUS_FULL]];
        $where[] = ['pru.etime', '<', $time];//结束掉的拼团活动里面的拼团记录，也需要关闭掉

        $list = $this
            ->alias('pre')
            ->join('pintuan_rule pru', 'pru.id = pre.rule_id')
            ->where($where)
            ->where("pre.id = pre.team_id")
            ->field('pre.*')
            ->select();

        if (!$list->isEmpty()) {
            foreach ($list as $v) {
                $team_list = $this->where('team_id', $v['id'])->select();
                //更新拼团状态为失败
                $data['status']     = self::STATUS_FAIL;
                $pintuanRecordModel = new PintuanRecord();
                $pintuanRecordModel->save($data, ['team_id' => $v['id']]);

                if (!$team_list->isEmpty()) {
                    foreach ($team_list as $j) {

                        //给这个订单作废，如果有支付，并退款
                        $this->cancleOrder($j['order_id']);
                    }
                }
            }
        }
        return true;
    }

    /**
     * 拼团订单取消，如果已支付自动退款，如果未支付，作废
     * @param $order_id
     */
    private function cancleOrder($order_id){
        $orderModel = new Order();
        $promotionRecord = new PromotionRecord();
        $order_info = $orderModel->getOrderInfoByOrderID($order_id);
        //如果订单已经完成或者已经取消就不做任何操作了
        if($order_info['status'] != $orderModel::ORDER_STATUS_NORMAL){
            return true;
        }

        if($order_info['ship_status'] != $orderModel::SHIP_STATUS_NO){
            //如果已经发货了，就不管了，手动退款吧
            return error_code(10000);
        }
        if($order_info['pay_status'] == $orderModel::PAY_STATUS_NO){
            //未支付
            $orderModel->cancel($order_id);
        }else{
            //已支付，生成退款单，并直接退款，之后，更改订单状态
            $billRefundModel = new BillRefund();
            $re = $billRefundModel->toAdd($order_info['user_id'],$order_info['order_id'],1,$order_info['payed'],"");
            if(!$re['status']){
                return $re;
            }
            $where[] = ['source_id', 'eq', $order_info['order_id']];
            $where[] = ['status', 'eq',1];
            $where[] = ['type', 'eq',1];
            $refundInfo = $billRefundModel->where($where)->find();
            if(!$refundInfo){
                //没有找到退款单
                return error_code(10000);
            }
            //去退款
            $billRefundModel->toRefund($refundInfo['refund_id'],2);

            //更新订单状态为已退款已完成
            $order_data['pay_status'] = $orderModel::PAY_STATUS_REFUNDED;
            $order_data['status']     = $orderModel::ORDER_STATUS_COMPLETE;
            $orderModel->save($order_data,['order_id'=>$order_info['order_id']]);
//            $promotionRecord->hookdata($order_id);//拼团失败发送消息
        }
        return true;
    }

}
