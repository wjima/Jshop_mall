<?php

namespace addons\MiniManage\controller;

use addons\MiniManage\model\AdminToken;

class Login extends \app\common\controller\Api
{
    public function login(){
        $sign = input("sign");
        $issign= \think\facade\Cache::get("sign");
        if(!$issign || !$sign || $sign!=$issign){
            return [
                "status" => false,
                "msg"    => "签名不正确或签名过期",
                "data"   => []
            ];
        }
        $Model = new AdminToken();
        return json($Model->tologin(input()));

    }
}