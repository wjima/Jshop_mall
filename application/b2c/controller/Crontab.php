<?php
namespace app\b2c\controller;

use app\common\controller\Base;

class Crontab extends Base
{
    /**
     * 取消订单
     */
    public function cancle()
    {
        $setting = getSetting('order_cancel_time');
        model('common/Order')->autoCancel($setting);
    }

    /**
     * 订单自动完成
     */
    public function complete()
    {
        $setting = getSetting('order_complete_time');
        model('common/Order')->autoComplete($setting);
    }

    /**
     * 自动评价订单
     */
    public function evaluate()
    {
        $setting = getSetting('order_autoEval_time');
        model('common/Order')->autoEvaluate($setting);
    }

    /**
     * 订单自动签收
     */
    public function sign()
    {
        $setting = getSetting('order_autoSign_time');
        model('common/Order')->autoSign($setting);
    }

    /**
     * 催付款
     */
    public function remind()
    {
        $setting = getSetting('order_cancel_time');
        model('common/Order')->remind_order_pay($setting);
    }

    /**
     * 拼团自动取消到期团，每分钟执行一次
     */
    public function pintuanCancle(){
        model('common/PintuanRecord')->autoCancle();
    }
}