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
        foreach ((array)$catIds as $key => $value) {
            $iData[] = [
                'goods_id'     => $goods_id,
                'goods_cat_id' => $value,
            ];
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
     * @return array
     */
    public function getGoodsIdByCat($catid)
    {
        if(is_array($catid)){
            $data = $this->where([['goods_cat_id', 'in', $catid]])->select();
        }else{
            $data = $this->where([['goods_cat_id', '=', $catid]])->select();
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