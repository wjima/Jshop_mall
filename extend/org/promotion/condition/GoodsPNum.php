<?php

namespace org\promotion\condition;


class GoodsPNum implements Condition
{
    function jshop($params, &$cart, $promotionInfo){
        $re = false;
        foreach($cart['list'] as $k => &$v){
            if(isset($v['products']['promotion_list'][$promotionInfo['id']]) && $v['products']['promotion_list'][$promotionInfo['id']]){
                if($v['nums'] >= $params['num']){
                    if(!$re){
                        $re = true;
                    }
                }else{
                    $v['products']['promotion_list'][$promotionInfo['id']] = false;
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
        return "购买所有商品";
    }

}
