<?php

namespace org\promotion\result;

/**
 * 订单减固定金额
 * Class OrderReduce
 * @package org\promotion\result
 */
class OrderReduce implements Result
{
    function jshop($params, &$cart, $promotionInfo){
        $money = $params['money'];              //订单优惠多少钱

        //判断极端情况，减的太多，超过购物车的总金额了，那么就最多减到0
        if($cart['amount'] < $params['money']){
            $money = $cart['amount'];
        }
        //总价格修改
        $cart['amount'] = bcsub($cart['amount'], $money, 2);
        //总促销修改
        $cart['order_pmt'] = bcadd($cart['order_pmt'], $money, 2);
        //设置促销列表
        if(!isset($cart['promotion_list'][$promotionInfo['id']])){
            $cart['promotion_list'][$promotionInfo['id']] = $promotionInfo['name'];
        }

        //coupon_pmt里存的优惠券使用的金额，此字段仅供参考，分账等的时候不做实际操作，以order_pmt里的为准
        if($promotionInfo['type'] == $promotionInfo::TYPE_COUPON){
            //优惠券促销金额
            $cart['coupon_pmt'] = bcadd($cart['coupon_pmt'], $money, 2);
        }

        return true;
    }

    function manageCheck($params){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$params['money'])){
            $result['msg'] = "请正确输入金额，最多2位小数";
            return $result;
        }
        if($params['money'] == '' || $params['money'] == '0'){
            $result['msg'] = "请输入金额";
            return $result;
        }
        $result['status'] = true;
        return $result;
    }
    function getMsg($params)
    {
        return '订单减'.$params['money'].'元 ';
    }

}
