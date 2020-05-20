<?php

namespace org\promotion\condition;

/**
 * 所有商品满足条件
 * Class GoodsAll
 * @package org\promotion\condition
 */
class GoodsAll implements Condition
{
    function jshop($params, &$cart, $promotionInfo){
            foreach($cart['list'] as $k => &$v){
                if(!isset($v['products']['promotion_list'][$promotionInfo['id']]) || $v['products']['promotion_list'][$promotionInfo['id']]){
                    $v['products']['promotion_list'][$promotionInfo['id']] = $promotionInfo['name'];
                }
            }
        return true;
    }
    function manageCheck($params){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];

        $result['status'] = true;
        return $result;
    }
    function getMsg($params)
    {
        return "购买所有商品";
    }

}
