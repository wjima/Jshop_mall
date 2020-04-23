<?php

namespace addons\WelfarePro\controller;

//演示插件的接口使用方法，此控制器命名可以随便命名，但是继承的类一定要集成这个\app\common\controller\Api
use addons\WelfarePro\model\WelfareproCoupon;
use addons\WelfarePro\model\WelfareproHb;
use app\common\model\User;

class Api extends \app\common\controller\Api
{
    //领取红包
    public function hb()
    {
        $result = [
            'status' => false,
            'data' => "",
            'msg' => ''
        ];
        if(!input('?param.userShareCode')){
            $data['msg'] = "userShareCode必须填";
            return $result;
        }
        $m = new WelfareproHb();
        return $m->sendHb($this->userId,input('param.userShareCode'));
    }

    //领优惠券
    public function coupon()
    {
        $result = [
            'status' => false,
            'data' => "",
            'msg' => ''
        ];
        $result['status'] = true;
        $result['msg'] = "领取成功";
        if(!input('?param.userShareCode')){
            $data['msg'] = "userShareCode必须填";
            return $result;
        }

        $m = new WelfareproCoupon();
        return $m->sendCoupon($this->userId,input('param.userShareCode'));
    }

}