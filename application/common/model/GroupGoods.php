<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class GroupGoods extends Common
{

    /**
     * 判断当前商品在未结束的活动中是否存在
     * @param int $goods_id 商品id
     * @param int $promotion_id 促销id
     * @return array|bool|null|\PDOStatement|string|\think\Model
     */
    public function checkInActivity($goods_id = 0, $promotion_id = 0)
    {
        $promotionModel = new Promotion();
        $where   = [];
        $where[] = ['gg.goods_id', '=', $goods_id];
        $where[] = ['pr.etime', '>', time()];
        $where[] = ['pr.id', 'neq', $promotion_id];
        $where[] = ['pr.type', 'in', [$promotionModel::TYPE_GROUP,$promotionModel::TYPE_SKILL]];
        $goods   = $this
            ->alias("gg")
            ->field("pr.*,gg.goods_id,g.name as goods_name,g.image_id as goods_image_id")
            ->join("promotion pr", "gg.rule_id = pr.id")
            ->join("goods g", "g.id = gg.goods_id")
            ->where($where)
            ->find();
        if ($goods) {
            return $goods;
        } else {
            return false;
        }
    }
}