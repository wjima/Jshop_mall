<?php

namespace addons\StockControl\controller;

use addons\StockControl\model\Stock;
use addons\WelfarePro\model\WelfareproCoupon;
use addons\WelfarePro\model\WelfareproCouponLog;
use addons\WelfarePro\model\WelfareproHb;
use addons\WelfarePro\model\WelfareproHblog;
use addons\WelfarePro\model\WelfareproHbuser;
use app\common\model\Coupon;
use app\common\model\Products;
use app\common\model\Promotion;
use myxland\addons\library\AddonController;
use Request;
use think\Container;
use think\facade\Session;
use app\common\model\Operation;
use app\common\model\ManageRoleOperationRel;



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

    public function stock1Index()
    {
        if($this->request->isAjax()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->getStockList(1,$params);
        }
        return $this->fetch();
    }
    public function stock1Add(){
        $this->view->engine->layout(false);
        if($this->request->isPost()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->addStock(1,$params);
        }
        $productModel = new Products();
        $products = $productModel->field(['id','goods_id','sn'])->select()->toArray();
        $this->assign('products',$products);
        return [
            'status'=>true,
            'msg'=>'',
            'data'=>$this->fetch()
        ];
    }
    public function stock2Index()
    {
        if($this->request->isAjax()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->getStockList(2,$params);
        }
        return $this->fetch();
    }
    public function stock2Add(){
        $this->view->engine->layout(false);
        if($this->request->isPost()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->addStock(2,$params);
        }
        $productModel = new Products();
        $products = $productModel->field(['id','goods_id','sn'])->select()->toArray();
        $this->assign('products',$products);
        return [
            'status'=>true,
            'msg'=>'',
            'data'=>$this->fetch()
        ];
    }

}