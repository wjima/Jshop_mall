<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillAftersales as BillAfterSalesModel;
use app\common\model\Order;
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
        $this->assign('search',input('param.'));
        return $this->fetch('index');
    }

    //售后单处理
    public function audit()
    {
        $this->view->engine->layout(false);
        if(!input('?param.aftersales_id')){
            return error_code(13215);
        }
        $billAftersalesModel = new BillAfterSalesModel();

        if(Request::isPOST()){
            $items = [];
            if(input('?param.order_items_id') && input('?param.aftersaleeItems')){
                $order_items_id = input('param.order_items_id/a');
                $aftersaleeItems = input('param.aftersaleeItems/a');
                foreach($order_items_id as $k => $v){
                    if($aftersaleeItems[$k]){
                        $items[$k] = $aftersaleeItems[$k];
                    }
                }
            }

            if(!input('?param.status')){
                return error_code(13228);
            }
            if(!input('?param.type')){
                return error_code(10000);
            }
            if(!input('?param.refund')){
                return error_code(13216);
            }else{
                $refund = input('param.refund/f');
            }
            $mark = input('param.mark','');

            return $billAftersalesModel->audit(input('param.aftersales_id'), input('param.status'),input('param.type'),$refund,$mark,$items );
        }


        $re = $billAftersalesModel->preAudit(input('param.aftersales_id'));
        if(!$re['status']){
            return $re;
        }

        // $re['data']['orderInfo']['items'] = json_encode($re['data']['orderInfo']['items']);

        $this->assign('info',$re['data']['info']);
        $this->assign('order_info',$re['data']['orderInfo']);
        $this->assign('order_items',$re['data']['orderInfo']['items']);

        return [
            'status' => true,
            'data' => $this->fetch('audit'),
            'msg' => ''
        ];
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
