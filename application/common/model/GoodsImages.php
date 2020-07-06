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
 * 商品图片关联表
 * Class GoodsType
 * @package app\common\model
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:09
 */
class GoodsImages extends Common
{

    /**
     * 批量保存商品图片
     * @param $data
     * @return int|string
     */
    public function batchAdd($data, $goods_id=0)
    {
        if($goods_id){
            if (self::get(['goods_id' => $goods_id])) {
                $this->where(['goods_id' => $goods_id])->delete();
            }
        }
        return $this->saveAll($data);
    }

    /**
     * 获取商品所有图片
     * @param $goods_id
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-06 11:32
     */
    public function getAllImages($goods_id)
    {
        $result = error_code(10027);
        if(!$goods_id){
            return error_code(12009);
        }
        $images = $this->where([ 'goods_id' => $goods_id ])->order('sort desc')->select();
        if(!$images->isEmpty()) {
            $result['status'] = true;
            $result['msg'] = '查询成功';
            $result['data'] = $images->toArray();
        }
        return $result;
    }

    /**
     * 删除图片
     * @param $goods_id
     * @return array
     */
    public function delImages($goods_id)
    {
        $result = error_code(10023);
        if(!$goods_id){
            return error_code(12009);
        }
        $resImages = $this->getAllImages($goods_id);
        if ($resImages['status']) {
            foreach ($resImages['data'] as $relImage) {
                delImage($relImage['image_id']);
            }
        }
        $result['status'] = true;
        $result['msg'] = '删除成功';
        return $result;
    }

}