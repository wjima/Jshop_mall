<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\b2c\controller;

use app\common\controller\Base;
use app\common\model\Bargain;
use app\common\model\BargainRecord;
use app\common\model\OperationLog;
use app\common\model\Order;
use app\common\model\PintuanRecord;

/**
 * 自动任务
 * Class Crontab
 * @package app\b2c\controller
 */
class Crontab extends Base
{
    public $sTime = 0;


    public function initialize()
    {
        parent::initialize();
        set_time_limit(0);
        $this->sTime = $this->getMS();
    }


    /**
     * 执行结束打印
     */
    public function __destruct()
    {
        $eTime = $this->getMS();
        $time  = $eTime - $this->sTime;
        print('----- Jshop定时任务执行完成，耗时：' . $time . " ms -----\n");
    }


    /**
     * 获取毫秒时间戳
     * @return float|int
     */
    protected function getMS()
    {
        $ms = microtime(true);
        return $ms * 1000;
    }


    /**
     * 取消订单
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cancle()
    {
        $setting = getSetting('order_cancel_time');
        $orderModel = new Order();
        $orderModel->autoCancel($setting);
    }


    /**
     * 订单自动完成
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function complete()
    {
        $setting = getSetting('order_complete_time');
        $orderModel = new Order();
        $orderModel->autoComplete($setting);
    }


    /**
     * 自动评价订单
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function evaluate()
    {
        $setting = getSetting('order_autoEval_time');
        $orderModel = new Order();
        $orderModel->autoEvaluate($setting);
    }


    /**
     * 订单自动签收
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sign()
    {
        $setting = getSetting('order_autoSign_time');
        $orderModel = new Order();
        $orderModel->autoSign($setting);
    }


    /**
     * 催付款
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function remind()
    {
        $setting = getSetting('order_cancel_time');
        $orderModel = new Order();
        $orderModel->remind_order_pay($setting);
    }


    /**
     * 拼团自动取消到期团，每分钟执行一次
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pintuanCancle()
    {
        $PintuanRecordModel = new PintuanRecord();
        $PintuanRecordModel->autoCancle();
    }


    /**
     * 定期清理7天前操作日志
     * @return false|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function removeOpLog()
    {
        $operateLog = new OperationLog();
        $where[] = ['ctime', '<=', time() - 86400 * 7];
        $operateLog->where($where)->delete();
        return json_encode(['status' => true, 'msg' => '清理完成']);
    }

    /**
     * 砍价活动过期的状态变更
     */
    public function bargainCancle()
    {
        $bargainRecordModel = new BargainRecord();
        $bargainRecordModel->bargainCancle();
        return json_encode(['status' => true, 'msg' => '取消成功']);
    }
}