<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillAftersales as BillAfterSalesModel;
use app\common\model\Payments;
use Request;

class BillAftersales extends Manage
{
    public function index()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $billAfterSalesModel = new BillAfterSalesModel();
            return $billAfterSalesModel->tableData($data);
        }
        $this->assign('sellerList',getSellerList());
        return $this->fetch('index');
    }


    //售后单查看
    public function view()
    {
        $this->view->engine->layout(false);
        if(!input('?param.aftersales_id')){
            return error_code(13217);
        }
        $billAftersalesModel = new BillAfterSalesModel();
        $where['aftersales_id'] = input('param.aftersales_id');
        $info = $billAftersalesModel::with('images,items')->where($where)->find();
        if(!$info){
            return error_code(13218);
        }

        if($info->items){
            $info['items_json'] = json_encode($info->items);
        }

        $this->assign('info',$info);
        return [
            'status' => true,
            'data' => $this->fetch('view'),
            'msg' => ''
        ];
    }



}
