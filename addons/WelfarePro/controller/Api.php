<?php

namespace addons\WelfarePro\controller;

//演示插件的接口使用方法，此控制器命名可以随便命名，但是继承的类一定要集成这个\app\common\controller\Api
class Api extends \app\common\controller\Api
{
    public function hb()
    {
        $data = [
            'status' => true,
            'data' => $this->userId,
            'msg' => '恭喜你，领取成功'
        ];
        return $data;
    }
}