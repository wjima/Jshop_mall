<?php

namespace app\common\model;

/**
 * 发货单关联表
 * Class BillDeliveryOrderRel
 * @package app\common\model
 */
class BillDeliveryOrderRel extends Common
{
    /**
     * 获取发货单号列表
     * @param $order_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDeliveryListByOrderId($order_id)
    {
        $delivery_id_list = $this->field('delivery_id')
            ->where('order_id', 'eq', $order_id)
            ->select()
            ->toArray();

        $delivery_id_list = array_column($delivery_id_list, 'delivery_id');
        return $delivery_id_list;
    }


    /**
     * 获取订单号列表
     * @param $delivery_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderListByDeliveryId($delivery_id)
    {
        $order_id_list = $this->field('order_id')
            ->where('delivery_id', 'eq', $delivery_id)
            ->select()
            ->toArray();

        $order_id_list = array_column($order_id_list, 'order_id');
        return $order_id_list;
    }
}