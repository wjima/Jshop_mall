<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;


/**
 * 商品扩展分类
 * Class GoodsExtendCat
 * @package app\common\model
 */
class GoodsExtendCat extends Common
{


    /**
     * 保存扩展分类
     * @param array $catIds
     * @param int $goods_id
     * @return bool
     */
    public function saveCat($catIds = [], $goods_id = 0)
    {
        $this->where([['goods_id', '=', $goods_id]])->delete();
        $iData = [];
        $catIds = array_diff((array)$catIds,[0]);
        foreach ((array)$catIds as $key => $value) {
            if(!empty($value)){
                $iData[] = [
                    'goods_id'     => $goods_id,
                    'goods_cat_id' => $value,
                ];
            }

        }
        if ($iData) {
            return $this->saveAll($iData);
        }
        return true;
    }

    /**
     * 获取全部扩展分类
     * @param int $goods_id
     * @return array
     */
    public function getAll($goods_id = 0)
    {
        $data = $this->where([['goods_id', '=', $goods_id]])->select();
        if(!$data->isEmpty()){
            return $data->toArray();
        }else{
            return [];
        }
    }

    /**
     * 获取商品id
     * @param $catid
     * @param bool $status //上架状态 true = 上架的商品  false = 全部商品
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsIdByCat($catid, $status = false)
    {
        $where = [];
        if($status){
            $goodsModel = new Goods();
            $where[] = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];
        }

        if(is_array($catid)){
            $where[] = ['e.goods_cat_id', 'in', $catid];
            $data = $this->alias('e')->field('e.*')
                ->join('goods g', 'g.id = e.goods_id')
                ->where($where)
                ->select();
        }else{
            $where[] = ['goods_cat_id', '=', $catid];
            $data = $this->alias('e')->field('e.*')
                ->join('goods g', 'g.id = e.goods_id')
                ->where($where)
                ->select();
        }
        if(!$data->isEmpty()){
            $goods = $data->toArray();
            $goodsIds = array_column($goods,'goods_id');
            return $goodsIds;
        }else{
            return [];
        }
    }
}