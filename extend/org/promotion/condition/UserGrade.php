<?php

namespace org\promotion\condition;

use app\common\model\User;

class UserGrade implements Condition
{
    function jshop($params, &$cart, $promotionInfo){
        $userModel = new User();
        $where[] = ['id', 'eq', $cart['user_id']];
        $info = $userModel->where($where)->find();
        if(!$info){
            return false;
        }
        foreach($params as $k => $v){
            if($info['grade'] == $k){
                if(!isset($cart['promotion_list'][$promotionInfo['id']])){
                    $cart['promotion_list'][$promotionInfo['id']] = $promotionInfo['name'];
                }
                return true;
            }
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
        return "指定用户等级";
    }
}
