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
    public function stockIndex(){
        if($this->request->isAjax()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->stockIndex($params);
        }
        return $this->fetch('stockindex');
    }
    public function editStock(){
        $res = [
            'status'=>false,
            'msg'=>'参数错误',
            'data'=>0
        ];
        $product_id = input('id/d',0);
        if(empty($product_id)) return $res;
        $stock = input('stock/d',0);
        $stockModel = new Stock();
        return $stockModel->editStock($product_id,$stock);
    }

    /**
     * 入库单列表
     * @return array|mixed
     */
    public function stock1Index()
    {
        if($this->request->isAjax()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->getStockList(1,$params);
        }
        return $this->fetch('stock1index');
    }

    /**
     * 新增入库单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function stock1Add(){
        $this->view->engine->layout(false);
        if($this->request->isPost()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->addStock(1,$params);
        }
        $productModel = new Products();
        $products = $productModel->with(['goodsInfo'])->field(['id','goods_id','sn','stock','spes_desc'])->select()->toArray();
        $this->assign('products',$products);
        return [
            'status'=>true,
            'msg'=>'',
            'data'=>$this->fetch('stockadd')
        ];
    }

    /**
     * 出库单列表
     * @return array|mixed
     */
    public function stock2Index()
    {
        if($this->request->isAjax()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->getStockList(2,$params);
        }
        return $this->fetch('stock2index');
    }

    /**
     * 新增出库单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function stock2Add(){
        $this->view->engine->layout(false);
        if($this->request->isPost()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->addStock(2,$params);
        }
        $productModel = new Products();
        $products = $productModel->with(['goodsInfo'])->field(['id','goods_id','sn','stock','spes_desc'])->select()->toArray();
        $this->assign('products',$products);
        return [
            'status'=>true,
            'msg'=>'',
            'data'=>$this->fetch('stockadd')
        ];
    }

    /**
     * 入库出库详情
     * @return array
     */
    public function stockView(){
        $this->view->engine->layout(false);
        $res = [
            'status'=>false,
            'msg'=>'参数错误',
            'data'=>''
        ];
        $type = input('type/d',1);
        if(!in_array($type,[1,2]))  return $res;
        $id = input('id','');
        if(empty($id)) return $res;
        $stockModel = new Stock();
        $info = $stockModel->stockInfo($id,$type);
        if(empty($info)) return $res;
        $this->assign('info',$info);
        $this->assign('type',$type);
//        dump($info);die;
        $res['status'] = true;
        $res['data'] = $this->fetch('stockview');
        return $res;
    }

    /**
     * 库存盘点
     * @return array|mixed
     */
    public function index(){
        if($this->request->isAjax()){
            $params = input();
            $stockModel = new Stock();
            return $stockModel->getStockCheck($params);
        }
        $sn = input('sn/s','');
        $this->assign('sn',$sn);
        return $this->fetch('index');
    }

}