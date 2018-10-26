<?php
namespace app\common\model;
use think\Db;

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
        $where[] = ['d.delivery_id', 'eq', $delivery_id];
        $res = $this->alias('d')
            ->field('d.nums as nums, o.name as product_name')
            ->join(config('database.prefix').'order_items o', 'd.order_items_id = o.id', 'left')
            ->where($where)
            ->select();
        if($res)
        {
            $return = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res
            ];
        }
        else
        {
            $return = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $res
            ];
        }
        return $return;
    }
}