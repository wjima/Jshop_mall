<?php
namespace app\common\model;
use think\Validate;
use org\Exp;
use think\Db;

/**
 * 发货单
 * Class BillDelivery
 * @package app\common\model
 * @author keinx
 */
class BillDelivery extends Common
{
    const STATUS_READY = 1;                 //准备发货
    const STATUS_ALREADY = 2;               //已发货
    const STATUS_CONFIRM = 3;               //已确认
    const STATUS_OTHER = 4;                 //其他
    const ORDER_STATUS_NORMAL = 1;          //订单状态正常
    const ORDER_STATUS_COMPLETE = 2;        //订单状态完成
    const ORDER_STATUS_CANCEL = 3;          //订单状态取消
    const PAY_STATUS_NO = 1;                //未付款
    const PAY_STATUS_YES = 2;               //已付款
    const PAY_STATUS_PARTIAL_YES = 3;       //部分付款
    const PAY_STATUS_PARTIAL_NO = 4;        //部分退款
    const PAY_STATUS_REFUNDED = 5;          //已退款
    const SHIP_STATUS_NO = 1;               //未发货
    const SHIP_STATUS_PARTIAL_YES = 2;      //部分发货
    const SHIP_STATUS_YES = 3;              //已发货

    /**
     * 发货详情关联
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('BillDeliveryItems', 'delivery_id', 'delivery_id');
    }

    /**
     * 发货
     * @param $order_id
     * @param $logi_code
     * @param $logi_no
     * @param $memo
     * @param $ship_data
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function ship($order_id, $logi_code, $logi_no, $memo, $ship_data)
    {
        //获取订单详情
        $order = model('common/Order')->get($order_id);
        $order->items;
        //$logistics = model('common/Ship')->get($order['logistics_id']);
        //订单验证
        if($order['status'] != self::ORDER_STATUS_NORMAL)
        {
            return error_code(13300);
        }
        if($order['pay_status'] == self::PAY_STATUS_NO)
        {
            return error_code(13301);
        }
        if($order['ship_status'] != self::SHIP_STATUS_NO && $order['ship_status'] != self::SHIP_STATUS_PARTIAL_YES)
        {
            return error_code(13302);
        }
        //数量验证
        $order_item = [];
        foreach($order['items'] as $k => $v)
        {
            $order_item[$v['id']] = $v['nums']-$v['sendnums'];
        }
        $ship_item = [];
        foreach($ship_data as $k => $v)
        {
            $ship_item[$v[0]] = $v[1];
        }
        foreach($ship_item as $k => $v)
        {
            if(!$order_item[$k])
            {
                return error_code(13303);
            }
            if($order_item[$k] < $v)
            {
                return error_code(13304);
            }
        }
        //组装发货总单需要的信息
        $delivery_id = get_sn(8);
        $bull_delivery = array(
            'delivery_id' => $delivery_id,
            'order_id' => $order['order_id'],
            'user_id' => $order['user_id'],
            'logi_code' => $logi_code,
            'logi_no' => $logi_no,
            'ship_area_id' => $order['ship_area_id'],
            'ship_address' => $order['ship_address'],
            'ship_name' => $order['ship_name'],
            'ship_mobile' => $order['ship_mobile'],
            'status' => self::STATUS_READY,
            'memo' => $memo,
            'ctime' => time(),
            'utime' => time()
        );

        $return = false;
        Db::startTrans();
        try{
            //插入发货总单
            $this->insert($bull_delivery);

            //订单记录
            $orderLog = new OrderLog();
            $orderLog->addLog($bull_delivery['order_id'], $bull_delivery['user_id'], $orderLog::LOG_TYPE_SHIP, '订单发货操作', [$order_id, $logi_code, $logi_no, $memo, $ship_data]);

            //插入发货详单，修改库存
            $goodsModel = new Goods();
            $orderItem = new OrderItems();
            $item_data = [];
            foreach($ship_item as $k => $v)
            {
                $item_data[] = [
                    'delivery_id' => $delivery_id,
                    'order_items_id' => $k,
                    'nums' => $v
                ];

                $product = $orderItem->field('product_id')->where('id', 'eq', $k)->find();
                if(!$product)
                {
                    return error_code(13306);
                }
                $re = $goodsModel->changeStock($product['product_id'], 'send', $v);
                if(!$re['status'])
                {
                    return error_code(13307);
                }
            }
            model('common/BillDeliveryItems')->insertAll($item_data);
            //修改订单详细表
            model('common/OrderItems')->ship($order['order_id'], $ship_data);
            //修改订单主表
            model('common/Order')->ship($order['order_id']);
            $return = true;
            Db::commit();

            //发送发货成功信息
            $shipModel          = new Ship();
            $ship               = $shipModel->getInfo(['id' => $order['logistics_id']]);
            $order['ship_id']   = $ship['name'];
            $order['ship_addr'] = get_area($order['ship_area_id']) . $order['ship_address'];

            $order['logistics_name'] = get_logi_info($logi_code);
            $order['ship_no']        = $logi_no;
            $eventData = $order->toArray();
            sendMessage($bull_delivery['user_id'], 'delivery_notice',$eventData );
        }catch(\Exception $e){
            Db::rollback();
        }
        if(!$return)
        {
            return error_code(13305);
        }
        $return_data = [
            'status' => true,
            'msg' => '发货成功',
            'data' => ''
        ];
        return $return_data;
    }

    /**
     * 确认签收
     * @param $order_id
     * @return false|int
     */
    public function confirm($order_id)
    {
        $where[] = ['order_id', 'eq', $order_id];
        $data['confirm_time'] = time();
        $res = $this->save($data, $where);
        return $res;
    }

    /**
     * 获取物流信息接口
     * 根据订单号查询
     * User:tianyu
     * @param string $order_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLogisticsInformation($order_id)
    {
        $result = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];
        $deliveryInfo = $this->where('order_id', $order_id)->find();
        if ($deliveryInfo) {
            // 获取发货单物流公司编码和单号
            if ( !$deliveryInfo[ 'logi_code' ] && !$deliveryInfo[ 'logi_no' ] ) {
                return error_code(10051);
            }

            $logistics = $this->logistics_query($deliveryInfo['logi_code'], $deliveryInfo['logi_no']);
            if ($logistics['status'] === '200')
            {
                $result['status'] = true;
                $result['msg'] = '获取成功';
                $result['data'] = [
                    'list' => $logistics['data'],
                    'state' => config('params.order')['logistics_state'][$logistics['state']]
                ];
            } else {
                $result['msg'] = $logistics['message'];
            }
        }
        return $result;
    }


    /**
     *  物流信息查询
     *  根据快递编码和单号查询
     * @param $code
     * @param $no
     * @return array
     */
    public function getLogistic ($code, $no)
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        $logisticsInfo = $this->logistics_query($code, $no);
        if ($logisticsInfo['status'] === '200')
        {
            $result['data']['info'] = [
                'no' => $logisticsInfo['nu'],
                'data' => $logisticsInfo['data'],
                'state' => $logisticsInfo['state'],
                'state_name' => config('params.order')['logistics_state'][$logisticsInfo['state']]
            ];
        } else {
            $result['status'] = false;
            $result['msg'] = $logisticsInfo['message'];
        }

        return $result;
    }

    /**
     *  api获取快递信息
     * User:tianyu
     * @param $com      快递公司编码
     * @param $code     物流单号
     * @return mixed
     */
    private function logistics_query( $com, $code )
    {
        $exp = new Exp();
        $res = $exp->postCurl($exp->assembleParam($com, $code));
        return $res;
    }


    /**
     * 获取发货单列表
     * @param int $page
     * @param int $limit
     * @param array $input
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($page = 1, $limit = 20, $input = [])
    {
        $where = [];
        if($input['delivery_id'])
        {
            $where[] = ['delivery_id', 'like', '%'.$input['delivery_id'].'%'];
        }
        if($input['order_id'])
        {
            $where[] = ['order_id', 'like', '%'.$input['order_id'].'%'];
        }
        if($input['logi_no'])
        {
            $where[] = ['logi_no', 'like', '%'.$input['logi_no'].'%'];
        }
        if($input['mobile'])
        {
            $where[] = ['ship_mobile', 'like', '%'.$input['mobile'].'%'];
        }

        $res = $this->where($where)
            ->order('ctime desc')
            ->page($page, $limit)
            ->select();
        $count = $this->where($where)
            ->count();
        if($res)
        {
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res,
                'count' => $count
            ];
        }
        else
        {
            $return_data = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $res,
                'count' => $count
            ];
        }
        return $return_data;
    }

    /**
     * 获取发货单详情
     * @param $delivery_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDeliveryInfo($delivery_id)
    {
        $where[] = ['delivery_id', 'eq', $delivery_id];

        $res = $this::with('items')->where($where)
            ->find();
        if($res)
        {
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res
            ];
        }
        else
        {
            $return_data = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $res
            ];
        }
        return $return_data;
    }

    /**
     * 发货单统计
     * @return array
     */
    public function statistics()
    {
        $num = 7;
        $day = date('Y-m-d', strtotime('-'.$num.' day'));
        $sql = 'SELECT DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d") as day, count(*) as nums FROM '.config('database.prefix')
            .'bill_delivery WHERE from_unixtime(ctime) >= "'.$day.'" GROUP BY DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d")';
        $res = Db::query($sql);
        $data = get_lately_days($num, $res);
        return $data['data'];
    }
}