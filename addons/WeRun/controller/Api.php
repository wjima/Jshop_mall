<?php

namespace addons\WeRun\controller;

use addons\WeRun\model\Werun;
use addons\WeRun\model\WerunLog;

//演示插件的接口使用方法，此控制器命名可以随便命名，但是继承的类一定要集成这个\app\common\controller\Api
class Api extends \app\common\controller\Api
{
    public function jshop()
    {
        $data = [
            'status' => true,
            'data' => $this->userId,
            'msg' => '123'
        ];
        return $data;
    }

    /**
     * 今日步数、天气、排行榜
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-05
     * @return array
     */
    public function today()
    {
        $werunModel = new Werun();
        return $werunModel->getInfo($this->userId, input('city'));
    }

    /**
     * 统计页面
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-05
     * @return array
     */
    public function statistic()
    {
        $werunModel = new Werun();
        $type = input('post.type',1);
        $year = input('post.year',date('Y'));
        $month = input('post.month',date('m'));
        $day = input('post.day',date('d'));
        return  $werunModel->statistic($this->userId, $type, $year, $month, $day);
    }

    /**
     * 兑换记录
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-05
     * @return array
     */
    public function log()
    {
        $params = input('post.');
        $werunRunModel = new WerunLog();
        return $werunRunModel->log($this->userId,$params);
    }
    /**
     * 更新步数
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-05
     * @return array
     */
    public function update()
    {
        $encryptedData = input('post.encryptedData', '');
        $iv = input('post.iv', '');
        if (!$encryptedData || !$iv) return \error_code(10003);
        $werunModel = new Werun();
        return $werunModel->updateLog($this->userId, $encryptedData, $iv);
    }
    /**
     * 兑换积分
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-05
     * @return array
     */
    public function point()
    {
        $werunModel = new Werun();
        return $werunModel->exchangePoint($this->userId);
    }

    /**
     * 获取待兑换积分
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-08
     * @return array
     */
    public function collect()
    {
        $werunModel = new Werun();
        return $werunModel->getCollect($this->userId);
    }
}