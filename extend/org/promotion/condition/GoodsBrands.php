<?php

namespace org\promotion\condition;

use app\common\model\Goods;
use app\common\model\GoodsCat;

class GoodsBrands implements Condition
{
    function jshop($params, &$cart, $promotionInfo){
        $re = false;
        $goodsModel = new Goods();
        foreach($cart['list'] as $k => &$v){
            $type = false;
            $goodsInfo = $goodsModel->find($v['products']['goods_id']);
            if($goodsInfo && $goodsInfo['goods_cat_id']){
                if($goodsInfo->brand && $goodsInfo->brand['id'] == $params['brand_id']){
                    if(!$re){
                        $re = true;
                    }
                    $type = $promotionInfo['name'];
                }
            }
            if(!isset($v['products']['promotion_list'][$promotionInfo['id']]) || $v['products']['promotion_list'][$promotionInfo['id']]){
                $v['products']['promotion_list'][$promotionInfo['id']] = $type;
            }
        }
        return $re;
    }

    function manageCheck($params){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if($params['brand_id'] == ''){
            $result['msg'] = "请选择品牌";
            return $result;
        }
        $result['status'] = true;
        return $result;
    }

    function getMsg($params)
    {
        return "购买指定品牌商品";
    }

}
