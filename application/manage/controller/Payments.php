<?php

namespace app\Manage\controller;

use Request;
use app\common\controller\Manage;
use app\common\model\Payments as PaymentsModel;
use app\common\model\PaymentsSellerRel ;

class Payments extends Manage
{
    public function index()
    {
        $paymentsModel = new PaymentsModel();
        $paymentsSellerRelModel = new PaymentsSellerRel();
        if(Request::isAjax())
        {
            $data = input('param.');
            return $paymentsSellerRelModel->tableData($data);
        }
        $this->assign('sellerList',getSellerList());
        $paymentList = $paymentsModel->getList();
        $this->assign('paymentList', $paymentList);
        return $this->fetch();
    }

    //支付方式添加
    public function add()
    {
        $this->view->engine->layout(false);
        $paymentsModel = new PaymentsModel();
        $paymentsSellerRelModel = new paymentsSellerRel();
        if(Request::isPost())
        {
            $data = input('param.');
            return $paymentsSellerRelModel->addData($data);
        }
        $data = $paymentsModel->getList();
        $this->assign('data',$data);
        $this->assign('sellerList',getSellerList());
        return $this->fetch('add');
    }

    //支付方式第二步
    public function edit()
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $this->view->engine->layout(false);
        $paymentsModel = new PaymentsModel();
        $paymentsSellerRelModel = new PaymentsSellerRel();

        if(!input('?param.code')){
            return error_code(10051);
        }
        if(!input('?param.seller_id')){
            return error_code(10011);
        }
        //判断此支付方式是否可用
        $paymentInfo = $paymentsModel->where(['code'=>input('param.code'),'status'=>$paymentsModel::PAYMENT_STATUS_YES])->find();
        if(!$paymentInfo){
            return error_code(10052);
        }
        //判断商户是否已经开过此支付方式
        $paymentSRInfo = $paymentsSellerRelModel->where(['payment_code'=>input('param.code'),'seller_id'=>input('param.seller_id')])->find();
        if($paymentSRInfo){
            //如果是新增，就报错，因为有了此支付方式
            if(input('?param.add')){
                return error_code(10053);
            }
            $paymentSRInfo['params'] = json_decode($paymentSRInfo['params'],true);
        }
        $this->assign('paymentInfo',$paymentInfo);
        $this->assign('paymentSRInfo',$paymentSRInfo);
        $this->assign('seller_id',input('param.seller_id'));
        $this->assign('code',input('param.code'));
        $result['data'] = $this->fetch();
        $result['status'] = true;
        return $result;
    }




    /*
     * 支付方式删除
     * */
    public function del()
    {
        $paymentsSellerRel = new paymentsSellerRel();
        return $paymentsSellerRel->del(input('post.id/d'));
    }


    public function changeStatus()
    {
        $paymentsSellerRelModel = new PaymentsSellerRel();
        return $paymentsSellerRelModel->changeStatus(input('param.id/d'),input('param.status'));
    }
}