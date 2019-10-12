<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://www.jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

/**
 * 发货详单
 * Class BillDeliveryItems
 * @package app\common\model
 * @author keinx
 */
class BillDeliveryItems extends Common
{
    /**
     * 获取发货详情
     * @param $delivery_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($delivery_id)
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];

        $where[] = ['d.delivery_id', 'eq', $delivery_id];
        $return['data'] = $this->alias('d')
            ->field('d.nums as nums2, sum(d.nums) as nums, o.name, o.sn, o.bn, o.nums as total2, sum(o.nums) as total, o.addon, o.product_id')
            ->join('order_items o', 'd.order_items_id = o.id', 'left')
            ->where($where)
            ->group('product_id')
            ->select();

        if ($return['data']) {
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }

        return $return;
    }
}