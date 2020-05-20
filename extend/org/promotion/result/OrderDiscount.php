<?php

namespace org\promotion\result;

/**
 * 订单打XX折
 * Class OrderDiscount
 * @package org\promotion\result
 */
class OrderDiscount implements Result
{
    function jshop($params, &$cart, $promotionInfo){
        //判断参数是否设置的正确
        if($params['discount'] >=10 || $params['discount'] <=0){
            return true;
        }
        $order_amount = $cart['amount'];

        //总价格修改
        $cart['amount'] = bcdiv(bcmul(bcmul($cart['amount'],$params['discount'],3),10,2), 100, 2);
        //总促销修改
        $cart['order_pmt'] = bcadd($cart['order_pmt'], bcsub($order_amount, $cart['amount'], 2), 2);
        //设置促销列表
        if(!isset($cart['promotion_list'][$promotionInfo['id']])){
            $cart['promotion_list'][$promotionInfo['id']] = $promotionInfo['name'];
        }

        //coupon_pmt里存的优惠券使用的金额，此字段仅供参考，分账等的时候不做实际操作，以order_pmt里的为准
        if($promotionInfo['type'] == $promotionInfo::TYPE_COUPON){
            //优惠券促销金额
            $cart['coupon_pmt'] = bcadd($cart['coupon_pmt'], bcsub($order_amount, $cart['amount'], 2), 2);
        }

        return true;
    }

    function manageCheck($params){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if(!preg_match("/^[0-9]+(.[0-9]{1})?$/",$params['discount'])){
            $result['msg'] = "请正确输入折扣，最多1位小数";
            return $result;
        }
        if($params['discount'] == '' || $params['discount'] == '0'){
            $result['msg'] = "请输入0-10之间的数字";
            return $result;
        }
        if(!($params['discount']>0 && $params['discount'] < 10)){
            $result['msg'] = "请输入0-10之间的数字";
            return $result;
        }
        $result['status'] = true;
        return $result;
    }
    function getMsg($params)
    {
        return '订单打'.$params['discount'].'折 ';
    }

}
