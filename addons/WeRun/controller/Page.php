<?php

namespace addons\WeRun\controller;

use addons\WeRun\model\WerunLog;
use myxland\addons\library\AddonController;
use think\Container;
use think\facade\Session;
use app\common\model\ManageRoleOperationRel;
use app\common\model\Operation;
use Request;


class Page extends AddonController
{
    protected function initialize()
    {
        parent::initialize();

        // 
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能
    }

    // public function index()
    // {
    //     if ($this->request->isAjax()) {
    //         $params = input();
    //         $stockModel = new WerunLog();
    //         return $stockModel->getList($params);
    //     }
    //     return $this->fetch();
    // }
    /**
     * 今日步数及今日天气
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-03
     * @return void
     */
    public function today()
    {
        if ($this->request->isPost()) {
            $token = input('token', '');
            // $userId = getUserIdByToken($token);
            // if(!$userId) return error_code(10000);
            $userId = 1;
            $stockModel = new WerunLog();
            return $stockModel->getInfo($userId, input('city'));
        }
        return $this->fetch();
    }
    /**
     * 步数统计
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-03
     * @return void
     */
    public function log()
    {
        if ($this->request->isPost()) {
            $token = input('token', '');
            // $userId = getUserIdByToken($token);
            // if(!$userId) return error_code(10000);
            $userId = 1;
            $werunModel = new WerunLog();
            $type = input('post.type',1);
            $year = input('post.year',date('Y'));
            $month = input('post.month',date('m'));
            $day = input('post.day',date('d'));

            return  $werunModel->statistic($userId, $type, $year, $month, $day);
        }
        return $this->fetch();
    }
    /**
     * 更新步数
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-03
     * @return void
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $token = input('token', '');
            $userId = getUserIdByToken($token);
            $userId = 1;

            if (!$userId) return error_code(10000);
            $encryptedData = input('post.encryptedData', '');
            $iv = input('post.iv', '');
            if (!$encryptedData || !$iv) return \error_code(10003);
            $werunModel = new WerunLog();
            return $werunModel->updateLog($userId, $encryptedData, $iv);
        }
        return '';
    }
}
