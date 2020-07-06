<?php

namespace org\promotion\result;

/**
 * 指定商品减固定金额
 * Class GoodsReduce
 * @package org\promotion\result
 */
class GoodsReduce implements Result
{
    function jshop($params, &$cart, $promotionInfo){
        foreach($cart['list'] as $k => &$v){
            if(!isset($v['products']['promotion_list'][$promotionInfo['id']])){
                continue;
            }
            $money = $params['money'];              //商品优惠多少钱
            $promotionMoney = 0;                    //商品合计优惠了多少钱

            //判断极端情况，减的太多，超过商品单价了，那么就最多减到0
            if($v['products']['price'] < $params['money']){
                $money = $v['products']['price'];
            }
            $v['products']['price'] = bcsub($v['products']['price'], $money, 2);

            //此次商品促销一共优惠了多少钱
            $promotionMoney = bcmul($v['nums'], $money, 2);
            //设置商品优惠总金额
            if(!isset($v['products']['promotion_amount'])){
                $v['products']['promotion_amount'] = 0;
            }
            $v['products']['promotion_amount'] = bcadd($v['products']['promotion_amount'], $promotionMoney, 2);
            $v['products']['amount'] = bcsub($v['products']['amount'], $promotionMoney, 2);


            //如果选中了，就减总价
            if($v['is_select']){
                //设置总的商品促销金额
                $cart['goods_pmt'] = bcadd($cart['goods_pmt'], $promotionMoney, 2);
                $cart['goods_amount'] = bcsub($cart['goods_amount'], $promotionMoney, 2);
                //设置总的价格
                $cart['amount'] = bcsub($cart['amount'], $promotionMoney, 2);

                //优惠券商品促销金额也加到订单上面，让这个字段标示实际的优惠金额
                if($promotionInfo['type'] == $promotionInfo::TYPE_COUPON){
                    //优惠券促销金额
                    $cart['coupon_pmt'] = bcadd($cart['coupon_pmt'], $promotionMoney, 2);
                }
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
        return '减'.$params['money'].'元 ';
    }

}
