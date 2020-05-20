<?php

namespace org\promotion\result;

/**
 * 指定商品第X件打X折
 * Class GoodsHalfPrice
 * @package org\promotion\result
 */
class GoodsHalfPrice implements Result
{
    function jshop($params, &$cart, $promotionInfo){
        $goods = [];
        foreach($cart['list'] as $k => &$v){
            if(!isset($v['products']['promotion_list'][$promotionInfo['id']])){
                continue;
            }
            //累加商品数量,初始为0
            if(!isset($goods[$v['products']['goods_id']])){
                $goods[$v['products']['goods_id']] = 0;
            }
            $the_nums = $goods[$v['products']['goods_id']];
            do {
                $goods[$v['products']['goods_id']]++;
                //判断是否能整除
                if(($goods[$v['products']['goods_id']] % $params['num']) == 0){
                    $this->t($v,$params['money'], $promotionInfo, $cart);
                }

            } while($goods[$v['products']['goods_id']] < $the_nums + $v['nums'] );
        }
        return true;
    }
    private function t(&$v,$money, $promotionInfo,&$cart){
        //判断极端情况，减的太多，超过商品单价了，那么就最多减到0
        if($v['products']['price'] < $money){
            $money = $v['products']['price'];
        }

        //此次商品促销一共优惠了多少钱
        $promotionMoney = $money;               //从其他地方拷贝过来的，其实money和promotionMoney的值是一样的
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
        if($params['num'] == '' || $params['num'] == '0'){
            $result['msg'] = "请输入数量";
            return $result;
        }
        if($params['num'] < 2){
            $result['msg'] = "请输入大于2的整数";
            return $result;
        }

        $result['status'] = true;
        return $result;
    }
    function getMsg($params)
    {
        return '第'.$params['num'].'件优惠'.$params['money'].'元';
    }
}
