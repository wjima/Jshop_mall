<?php

namespace addons\WeRun\controller;

use addons\WeRun\model\Werun;
use myxland\addons\library\AddonController;


class Crontab extends AddonController
{
    protected function initialize()
    {
        parent::initialize();

        // 
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能
    }

    /**
     * 年度清除
     * 建议定时任务每年1月1日执行一次即可。
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-10
     * @return 
     */
    public function resetYear()
    {
        $werunModel = new Werun();
        return $werunModel->clearStepsByYears();
    }

    /**
     * 将30天前的所有不满足条件的步数作废
     * 每天执行一次或每月执行一次均可
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-10
     * @return 
     */
    public function resetMonth()
    {
        $werunModel = new Werun();
        return $werunModel->clearStepsByMonth();
    }
}
