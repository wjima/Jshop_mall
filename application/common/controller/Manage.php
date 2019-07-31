<?php

/**
 * 总后台的控制器基类，用于权限判断和一些统一的后台操作
 *
 * @author sin
 *
 */

namespace app\common\controller;

use app\common\model\ManageRoleOperationRel;
use think\Container;
use app\common\model\Operation;
use Request;


class Manage extends Base
{
    protected function initialize()
    {
        parent::initialize();
        //没有登陆，请先登录
        if (!session('?manage')) {
            cookie('redirect_url', Container::get('request')->url(), 3600);
            $this->redirect('manage/common/login');
        }

        $cont_name = request()->controller();
        $act_name  = request()->action();

        $operationModel = new Operation();

        //判断当前是否有权限操作
        $mrorModel = new ManageRoleOperationRel();
        $permRe    = $mrorModel->checkPerm(session('manage.id'), $operationModel::MENU_MANAGE, $cont_name, $act_name);
        if (!$permRe['status']) {
            if (Request::isAjax()) {
                $err = [
                    'status' => false,
                    'data'   => '',
                    'msg'    => $permRe['msg']
                ];
                echo json_encode($err);
                die();
                //return $permRe;
            } else {
                $this->error($permRe['msg']);
            }
        }

        $jshopHost = Container::get('request')->domain();
        $this->assign('jshopHost', $jshopHost);
        //店铺名称
        $shop_name = getSetting('shop_name');
        $this->assign('shop_name', $shop_name);
        $this->view->engine->layout('layout');

    }
}
