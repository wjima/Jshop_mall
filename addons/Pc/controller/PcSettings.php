<?php

namespace addons\Pc\controller;

use myxland\addons\library\AddonController;
use think\Container;
use think\facade\Request;
use think\facade\Session;

class PcSettings extends AddonController
{

    protected function initialize()
    {
        parent::initialize();

        //想实现判断后台的登陆判断很简单，写如下代码即可
        Session::init([
            'prefix'     => 'manage',
            'type'       => '',
            'auto_start' => true,
        ]);

        if (!session('?manage')) {
            cookie('redirect_url', Container::get('request')->url(), 3600);
            $this->redirect('manage/common/login');
        }
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能
    }

    public function index()
    {
        $model = new \addons\Pc\model\PcSettings();
        if (Request::isAjax()) {
            $data = [
                'business'  => input('business', ''),
                'phone'     => input('phone', ''),
                'welcomes'  => input('welcomes', ''),
                'app_image' => input('app_image', ''),
            ];
            foreach ($data as $k => $v) {
                $model->setValue($k, $v);
            }
            $result = array(
                'status' => true,
                'data'   => [],
                'msg'    => '保存成功'
            );
            return $result;

        }
        $info = $model->select();
        $this->assign('info', $info);
        $data = $model->getAll();

        $this->assign('data', $data);
        return $this->fetch();
    }
}
