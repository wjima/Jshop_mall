<?php

namespace app\common\model;

use think\Validate;
use think\Model;

//活动订单记录，目前主要记录团购秒杀
class PromotionRecord extends Common
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const TYPE_GROUP = 1;
    const TYPE_SKILL = 2;

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;

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

        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = [];
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
        foreach ($list as $k => $v) {
            $list[$k]['nickname'] = get_user_info($v['user_id'], 'nickname');
            $list[$k]['ctime']    = getTime($v['ctime']);
            $list[$k]['utime']    = getTime($v['utime']);
            $list[$k]['avatar']   = _sImage(get_user_info($v['user_id'], 'avatar'));
        }
        return $list;
    }


    //生成订单的时候，增加信息
    public function orderAdd($order, $items, $params, $type = 1)
    {

        $order_item = current($items);

        if (isInGroup($order_item['goods_id'], $params['group_id'], $promotion, $order['order_type'])) {
            $promotion_params = json_decode($promotion['params'], true);
            $orderModel       = new Order();
            $check_order      = $orderModel->findLimitOrder($order_item['product_id'], $order['user_id'], $promotion, $order['order_type']);
            if (isset($promotion_params['max_goods_nums']) && $promotion_params['max_goods_nums'] != 0) {
                if (($check_order['data']['total_orders'] + 1) > $promotion_params['max_goods_nums']) {
//                    $result['msg'] = '该商品已超过当前活动最大购买量';
                    return error_code(15610);
                }
            }

            if (isset($promotion_params['max_nums']) && $promotion_params['max_nums'] != 0) {
                if ((1 + $check_order['data']['total_user_orders']) > $promotion_params['max_nums']) {
//                    $result['msg'] = '您已超过该活动最大购买量';
                    return error_code(17611);
                }
            }
        }

        $recData = [
            'promotion_id' => $params['group_id'],
            'user_id'      => $order['user_id'],
            'goods_id'     => $order_item['goods_id'],
            'product_id'   => $order_item['product_id'],
            'order_id'     => $order['order_id'],
            'type'         => $type,
        ];

        if (!$this->save($recData)) {
            return error_code(10004);

        }
        $result     = [
            'status' => true,
            'data'   => [],
            'msg'    => ''
        ];
        return $result;
    }

    /**
     * 获取参与记录
     * @param string $fields
     * @param array $where
     * @param array $order
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getList($fields = '*', $where = [], $order = ['ctime' => 'desc'], $page = 1, $limit = 10)
    {
        $result  = [
            'status' => true,
            'msg'    => '',
            'data'   => [],
        ];
        $list                    = $this
            ->field($fields)
            ->where($where)
            ->order($order)
            ->page($page, $limit)
            ->select();
        $total                   = $this
            ->field($fields)
            ->where($where)
            ->count();
        $list                    = $this->tableFormat($list);
        $result['data']['list']  = $list;
        $result['data']['count'] = $total;
        return $result;
    }
    /**
     * 拼团失败，发送订阅消息，处理数据
     * @param $order_id
     */
    public function hookdata($order_id){
        $orderModel=new Order();
        $recordModel=new PintuanRecord();
        $ruleModel=new PintuanRule();
        $orderInfo=$orderModel->where("order_id",'eq',$order_id)->find();
        $recordInfo=$recordModel->where("order_id",'eq',$order_id)->find();
        $ruleInfo=$ruleModel->where("id",'eq',$recordInfo["rule_id"])->find();
        $hookdata['params']['order_id']=$order_id;
        $hookdata['params']['name']=$ruleInfo["name"];
        $hookdata['params']['money']=$orderInfo["order_amount"];
        $hookdata['params']['refundInfo']="拼团失败，退还金额";
        $hookdata['params']['time']=date("Y-m-d H:i:s");
        $hookdata['user_id']=$orderInfo['user_id'];
        $hookdata['code']="pintuan_refund";
        hook('sendwxmessage', ['params' => [
            'user_id'   => $hookdata["user_id"],
            'code'      => $hookdata["code"],
            'params'    => $hookdata["params"],
        ]]);
        return $hookdata;
    }
    /**
     * 拼团成功，发送订阅消息，处理数据
     * @param $order_id
     */
    public function sendmessage($where){
        $recordModel=new PintuanRecord();
        $ruleModel=new PintuanRule();
        $orderModel=new Order();
        $record=$recordModel->where($where)->order('id', 'asc')->select();
        $people_number = '5';
        foreach($record as $k=>$v){
            $hookdata = [];
            if ($v['params']) {
                $params = json_decode($v['params'], true);
                $people_number = isset($params['people_number']) ? $params['people_number'] : 5;
            }
            //$people_number = isset($v['people_number'])?$v['people_number']:5;
            $hookdata['params']['order_id']=$v["order_id"];
            $hookdata['params']['people_number']=$people_number;
            $ruleInfo=$ruleModel->where("id",'eq',$v['rule_id'])->find();
            $hookdata['params']['name']=$ruleInfo["name"];
            $orderInfo=$orderModel->where("order_id",'eq',$v["order_id"])->find();
            $hookdata['params']['order_amount']=$orderInfo["order_amount"];
            $hookdata['params']['time']=date("Y-m-d H:i:s");
            $hookdata['user_id']=$v['user_id'];
            $hookdata['code']="pintuan_success";
            error_log(var_export([$hookdata,$where,$record],true),3,__FILE__.'.log');
            hook('sendwxmessage', ['params' => [
                'user_id'   => $hookdata["user_id"],
                'code'      => $hookdata["code"],
                'params'    => $hookdata["params"],
            ]]);
        }
        return true;
    }
}

