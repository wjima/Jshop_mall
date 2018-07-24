<?php
namespace app\crontab;
use taskphp\Utils;

/**
 * 订单相关定时任务
 */
class Order
{
    /**
     * 取消订单
     */
    public static function cancle(){
        Utils::log('自动取消订单任务开始时间：'.date('Y-m-d H:i:s').PHP_EOL);

        //error_log(var_export(json_decode($sellerinfo),true),3,__FILE__.'.log');

        $seller_ids = model('common/Seller')->field('id')->select();
        Utils::log(\GuzzleHttp\json_encode($seller_ids));

        $seller_setting = [];
        foreach ($seller_ids as $v)
        {
            $seller_setting[$v['id']] = getShopSetting($v['id'], 'order_cancel_time');
        }
        $order = model('common/Order');
        $order->autoCancel($seller_setting);
        Utils::log('自动取消订单任务结束时间：'.date('Y-m-d H:i:s').PHP_EOL);
    }

    /**
     * 订单自动完成
     */
    public static function complete()
    {
        Utils::log('自动完成订单任务开始时间：'.date('Y-m-d H:i:s').PHP_EOL);
        $seller_ids = model('common/Seller')->field('id')->select();
        $seller_setting = [];
        foreach ($seller_ids as $v)
        {
            $seller_setting[$v['id']] = getShopSetting($v['id'], 'order_complete_time');
        }
        model('common/Order')->autoComplete($seller_setting);
        Utils::log('自动完成订单任务结束时间：'.date('Y-m-d H:i:s').PHP_EOL);
    }

    /**
     * 自动评价订单
     */
    public static function evaluate()
    {
        Utils::log('自动评价订单任务开始时间：'.date('Y-m-d H:i:s').PHP_EOL);
        $seller_ids = model('common/Seller')->field('id')->select();
        $seller_setting = [];
        foreach ($seller_ids as $v)
        {
            $seller_setting[$v['id']] = getShopSetting($v['id'], 'order_autoEval_time');
        }
        model('common/Order')->autoEvaluate($seller_setting);
        Utils::log('自动评价订单任务结束时间：'.date('Y-m-d H:i:s').PHP_EOL);
    }

    /**
     * 订单自动签收
     */
    public static function sign()
    {
        Utils::log('自动签收订单任务开始时间：'.date('Y-m-d H:i:s').PHP_EOL);

        $seller_ids = model('common/Seller')->field('id')->select();
        $seller_setting = [];
        foreach ($seller_ids as $v)
        {
            $seller_setting[$v['id']] = getShopSetting($v['id'], 'order_autoSign_time');
        }
        model('common/Order')->autoSign($seller_setting);

        Utils::log('自动签收订单任务结束时间：'.date('Y-m-d H:i:s').PHP_EOL);
    }
}