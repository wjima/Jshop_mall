<?php
namespace app\common\model;

/**
 * 订单记录表
 * Class OrderLog
 * @package app\common\model
 */
class OrderLog extends Common
{
    const LOG_TYPE_CREATE = 1; //订单创建
    const LOG_TYPE_PAY = 2; //订单支付
    const LOG_TYPE_SHIP = 3; //订单发货
    const LOG_TYPE_SIGN = 4; //订单签收
    const LOG_TYPE_EVALUATION = 5; //订单评价
    const LOG_TYPE_COMPLETE = 6; //订单完成
    const LOG_TYPE_CANCEL = 7; //订单取消
    const LOG_TYPE_EDIT = 8; //订单编辑
    const LOG_TYPE_AUTO_SIGN = 9; //订单自动签收
    const LOG_TYPE_AUTO_EVALUATION = 10; //订单自动评价
    const LOG_TYPE_AUTO_COMPLETE = 11; //订单自动完成
    const LOG_TYPE_AUTO_CANCEL = 12; //订单自动取消

    protected $log_type = [
        self::LOG_TYPE_CREATE => '订单创建',
        self::LOG_TYPE_PAY => '订单支付',
        self::LOG_TYPE_SHIP => '订单发货',
        self::LOG_TYPE_SIGN => '订单签收',
        self::LOG_TYPE_EVALUATION => '订单评价',
        self::LOG_TYPE_COMPLETE => '订单完成',
        self::LOG_TYPE_CANCEL => '订单取消',
        self::LOG_TYPE_EDIT => '订单编辑',
        self::LOG_TYPE_AUTO_SIGN => '订单自动签收',
        self::LOG_TYPE_AUTO_EVALUATION => '订单自动评价',
        self::LOG_TYPE_AUTO_COMPLETE => '订单自动完成',
        self::LOG_TYPE_AUTO_CANCEL => '订单自动取消'
    ];


    /**
     * 添加记录
     * @param $order_id
     * @param $user_id
     * @param $type
     * @param $msg
     * @param $data
     * @return int|string
     */
    public function addLog($order_id, $user_id, $type, $msg, $data)
    {
        $info['order_id'] = $order_id;
        $info['user_id'] = $user_id;
        $info['type'] = $type;
        $info['msg'] = $msg;
        $info['data'] = json_encode($data);
        $info['ctime'] = time();

        $res = $this->insert($info);
        return $res;
    }


    /**
     * 批量插入
     * @param $order_infos
     * @param $type
     * @param $msg
     * @param $data
     * @return int|string
     */
    public function addLogs($order_infos, $type, $msg, $data)
    {
        $all = [];
        foreach ($order_infos as $v)
        {
            $all[] = [
                'order_id' => $v['order_id'],
                'user_id' => $v['user_id'],
                'type' => $type,
                'msg' => $msg,
                'data' => json_encode($data),
                'ctime' => time()
            ];
        }
        $res = $this->insertAll($all);
        return $res;
    }


    /**
     * 获取订单记录
     * @param $order_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderLog($order_id)
    {
        $return = [
            'status' => true,
            'msg' => '',
            'data' => []
        ];
        if(!$order_id)
        {
//            $return['status'] = false;
//            $return['msg'] = '没有订单号';
            return error_code(13100);
        }
        $where[] = ['order_id', 'eq', $order_id];
        $res = $this->field('id, order_id, user_id, type, msg, ctime')
            ->where($where)
            ->order('ctime desc')
            ->select();
        $return['data'] = $res;
        if(count($res) < 1)
        {
            $return['msg'] = '没有数据';
        }
        else
        {
            foreach($return['data'] as &$v)
            {
                $v['type'] = $this->log_type[$v['type']];
                $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
            }
            $return['msg'] = '获取成功';
        }
        return $return;
    }
}
