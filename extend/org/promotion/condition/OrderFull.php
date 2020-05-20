<?php

namespace org\promotion\condition;

class OrderFull implements Condition
{
    function jshop($params, &$cart, $promotionInfo){
        if($cart['amount']>=$params['money']){
            if(!isset($cart['promotion_list'][$promotionInfo['id']])){
                $cart['promotion_list'][$promotionInfo['id']] = $promotionInfo['name'];
            }
            return true;
        }else{
            return false;
        }
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
        if($params['money'] == ''){
            $result['msg'] = "请输入金额";
            return $result;
        }

        $result['status'] = true;
        return $result;
    }

    function getMsg($params)
    {
        return '购买订单满'.$params['money'].'元 ';
    }


}
