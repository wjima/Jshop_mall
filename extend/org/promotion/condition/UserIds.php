<?php

namespace org\promotion\condition;

use app\common\model\User;

class UserIds implements Condition
{
    function jshop($params, &$cart, $promotionInfo){

        $user_ids = explode(',',$params['user_id']);
        if(in_array($cart['user_id'], $user_ids)){
            if(!isset($cart['promotion_list'][$promotionInfo['id']])){
                $cart['promotion_list'][$promotionInfo['id']] = $promotionInfo['name'];
            }
            return true;
        }

        return false;
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
        return "专享";
    }


}
