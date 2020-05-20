<?php

namespace org\promotion\condition;

use app\common\model\Goods;
use app\common\model\GoodsCat;

class GoodsCats implements Condition
{
    function jshop($params, &$cart, $promotionInfo){
        $re = false;
        $goodsModel = new Goods();
        $goodsCatModel = new GoodsCat();
        foreach($cart['list'] as $k => &$v){
            $goodsInfo = $goodsModel->find($v['products']['goods_id']);
            if(!$goodsInfo || !$goodsInfo['goods_cat_id']){
                continue;
            }
            if($goodsCatModel->isChild($params['cat_id'],$goodsInfo['goods_cat_id'])){
                if(!$re){
                    $re = true;
                }
                if(!isset($v['products']['promotion_list'][$promotionInfo['id']])){
                    $v['products']['promotion_list'][$promotionInfo['id']] = $promotionInfo['name'];
                }
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
        if($params['cat_id'] == ''){
            $result['msg'] = "请选择商品分类";
            return $result;
        }
        $result['status'] = true;
        return $result;
    }

    function getMsg($params)
    {
        return "购买指定分类商品";
    }

}
