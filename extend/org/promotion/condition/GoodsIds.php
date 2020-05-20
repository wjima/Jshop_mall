<?php

namespace org\promotion\condition;

class GoodsIds implements Condition
{
    function jshop($params, &$cart, $promotionInfo){
        $re = false;
        $goods_ids = explode(',',$params['goods_id']);
        foreach($cart['list'] as $k => &$v){
            $type = false;
            if(in_array($v['products']['goods_id'], $goods_ids) ){
                //判断返回值,也就是购物车中，只要有一个，就返回正确
                if(!$re){
                    $re = true;
                }
                $type = $promotionInfo['name'];
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
        if(['goods_id'] == ''){
            $result['msg'] = "请选择商品";
            return $result;
        }
        $result['status'] = true;
        return $result;
    }

    function getMsg($params)
    {
        return "购买指定商品";
    }
}
