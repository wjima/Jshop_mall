<?php

namespace addons\FreePackage\controller;

use addons\FreePackage\model\FreePackage;
use app\common\model\Order;
use app\common\model\OrderLog;
use myxland\addons\library\AddonController;
use think\facade\Cache;
use think\facade\Request;
use think\Container;
use think\facade\Session;
use app\common\model\Operation;
use app\common\model\ManageRoleOperationRel;
use app\common\model\Goods;

class Index extends AddonController
{

    protected function initialize()
    {
        parent::initialize();

        //想实现判断后台的登陆判断很简单，写如下代码即可
        Session::init([
            'prefix'         => 'manage',
            'type'           => '',
            'auto_start'     => true,
        ]);

        if (!session('?manage')) {
            cookie('redirect_url', Container::get('request')->url(), 3600);
            $this->redirect('manage/common/login');
        }

        $operationModel = new Operation();

        //判断当前是否有权限操作
        $mrorModel = new ManageRoleOperationRel();
        $permRe    = $mrorModel->checkPerm(session('manage.id'), $operationModel::MENU_MANAGE, $this->controller, $this->action,$this->addon);
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
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能
    }

    // 基础设置
    public function setting(){
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => '保存成功'
        ];
        $addonModel = new \app\common\model\Addons();
        $config     = $addonModel->getSetting('FreePackage');

        $setting = isset($config['setting']) ? $config['setting'] : [];
        if (Request::isPost()) {
            $params = input('param.');
            if (isset($params['__Jshop_Token__'])) {
                unset($params['__Jshop_Token__']);
            }
            unset($params['validate_form']);
            unset($params['route']);

            if(isset($params['combo_num']) && $params['combo_num'] < 3){
                $result['status'] = false;
                $result['msg'] = '套餐最低数量为3件！';
                return $result;
            }

            // 限制活动说明字数
            if(isset($params['combo_desc']) && $params['combo_desc']){
                if(strlen($params['combo_desc']) > 240){
                    $result['status'] = false;
                    $result['msg'] = '活动说明长度最多只能为80个字';
                    return $result;
                }
            }

            $uData = $config;
            if ($config) {
                $uData['setting'] = array_merge($setting, $params);//todo 所有插件列表上面的配置，都要以setting为name
            }

            $addonModel->doSetting($uData, 'FreePackage');
            Cache::clear();
            return $result;
        } else {
            $this->assign('data', $setting);
        }
        return $this->fetch();
    }

    public function view()
    {
        $id = input('id');
        $return = error_code(10037);
        $this->view->engine->layout(false);
        $orderModel = new Order();
        $order_info = $orderModel->getOrderInfoByOrderID($id);
        $this->assign('order', $order_info);

        $return['status'] = true;
        $return['msg'] = '成功';
        $return['data'] = $this->fetch('view');
        return $return;
    }

    // 套餐列表
    public function index(){
        if($this->request->isAjax()){
            $freePackageModel = new FreePackage();
            return $freePackageModel->tableData(input());
        }
        return $this->fetch('index');
    }

    // 添加套餐
    public function add()
    {
        $this->view->engine->layout(false);
        if (request()->isPost()) {
            $freePackageModel = new FreePackage();
            return $freePackageModel->saveData(input());
        }

        return [
            'status' => true,
            'msg' => '获取成功',
            'data' => $this->fetch()
        ];
    }

    public function edit()
    {
        $this->view->engine->layout(false);
        $freePackageModel = new FreePackage();
        $id = input('id/d', 0);
        if (!$id)  return error_code(10003);
        if (request()->isPost()) {
            return $freePackageModel->editData(input(), $id);
        }
        $data = $freePackageModel->getInfo($id);
        if (!$data['status']) {
            return $data;
        }
        $this->assign('info', $data['data']);
        return [
            'status' => true,
            'msg' => '获取成功',
            'data' => $this->fetch('edit')
        ];
    }


    public function del()
    {
        if (!input('?param.id')) {
            return error_code(10003);
        }
        $id = input('param.id');
        $freePackageModel = new FreePackage();
        return $freePackageModel->del($id);
    }

}