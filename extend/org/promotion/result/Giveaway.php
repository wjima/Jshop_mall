<?php

namespace org\promotion\result;

use app\common\model\Cart;
use app\common\model\Goods;
use app\common\model\Products;

/**
 * 满足条件送赠品
 * Class OrderReduce
 * @package org\promotion\result
 */
class Giveaway implements Result
{
    function jshop($params, &$cart, $promotionInfo){
        $goods = explode(',', $params['goods_id']);
        $productModel = new Products();
        foreach($goods as $v){
            //根据商品找默认货品
            $where = [];
            $where[] = ['is_defalut', '=', 1];
            $where[] = ['goods_id','=', $v];
            $info = $productModel->where($where)->find();
            if(!$info){
                continue;
            }
            $cartInfo = $this->getCartInfo($info['id'],$params['nums']);
            if($cartInfo['status']){
                $cart['list'][] = $cartInfo['data'];
            }
        }
        return true;
    }

    //根据货品id去构建购物车明细数据，具体数据结构要参考Cart模型里的getList方法
    private function getCartInfo($product_id,$nums){
        $result = [
            'status' => true,
            'data' => [
                'id' => 0,
                'user_id' => 0,
                'product_id' => $product_id,
                'nums' => (int)$nums,
                'type' => Cart::TYPE_GIVEAWAY,
                'weight' => 0,
                'products' => [],
                'is_select' => true,
            ],
            'msg' => ''
        ];
        $productsModel = new Products();
        $goodsModel    = new Goods();
        //如果没有此商品，就在购物车里删掉
        $productInfo = $productsModel->getProductInfo($product_id, false);           //第二个参数是不算促销信息,否则促销信息就算重复了
        if (!$productInfo['status']) {
            return $productInfo;
        }

        //判断库存是否够
        if($nums > ($productInfo['data']['stock'])){
            return error_code(12702);
        }

        $goodsWeight        = $goodsModel->getWeight($product_id);
        $result['data']['weight'] = $goodsWeight;


        $productInfo['data']['price'] = 0;
        $productInfo['data']['amount'] = 0;
        //$productInfo['data']['name'] .= "[赠品]";

        $result['data']['products'] = $productInfo['data'];
        return $result;
    }

    function manageCheck($params){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if($params['nums'] == '' || $params['nums'] == '0'){
            $result['msg'] = "请输入数量";
            return $result;
        }
        if(['goods_id'] == ''){
            $result['msg'] = "请选择赠品";
            return $result;
        }
        $result['status'] = true;
        return $result;
    }
    function getMsg($params)
    {
        return '满足条件送赠品 ';
    }

}
