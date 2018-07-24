<?php

namespace app\seller\controller;

use Request;
use app\common\controller\Seller;
use app\common\model\Payments as PaymentsModel;
use app\common\model\PaymentsSellerRel ;

class Payments extends Seller
{
    public function index()
    {
        $paymentsModel = new PaymentsModel();
        $paymentsSellerRelModel = new PaymentsSellerRel();
        if(Request::isAjax())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $paymentsSellerRelModel->tableData($data);
        }
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
            $data['seller_id'] = $this->sellerId;
            return $paymentsSellerRelModel->addData($data);
        }
        $data = $paymentsModel->getList();
        $this->assign('data',$data);
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
        //判断此支付方式是否可用
        $paymentInfo = $paymentsModel->where(['code'=>input('param.code'),'status'=>$paymentsModel::PAYMENT_STATUS_YES])->find();
        if(!$paymentInfo){
            return error_code(10052);
        }
        //判断商户是否已经开过此支付方式
        $paymentSRInfo = $paymentsSellerRelModel->where(['payment_code'=>input('param.code'),'seller_id'=>$this->sellerId])->find();
        if($paymentSRInfo){
            //如果是新增，就报错，因为有了此支付方式
            if(input('?param.add')){
                return error_code(10053);
            }
            $paymentSRInfo['params'] = json_decode($paymentSRInfo['params'],true);
        }
        $this->assign('paymentInfo',$paymentInfo);
        $this->assign('paymentSRInfo',$paymentSRInfo);
        $this->assign('seller_id',$this->sellerId);
        $this->assign('code',input('param.code'));
        $result['data'] = $this->fetch();
        $result['status'] = true;
        return $result;
    }

    //上传证书
    public function uploadCert()
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        //上传路径为 /public/static/files/cert/商户code/
        $url = "./static/files/cert/".session('seller.token')."/";

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');

        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size'=>15678,'ext'=>'pem'])->rule('uniqid')->move($url);
        if($info){
            $result['data'] = $info->getSaveName();
            $result['status'] = true;
        }else{
            $result['msg'] = $file->getError();
        }
        return $result;

    }


    /*
     * 支付方式修改
     * */
//    public function edit()
//    {
//        $result = [
//            'status' => false,
//            'data' => '',
//            'msg' => ''
//        ];
//        $this->view->engine->layout(false);
//        $paymentsModel = new PaymentsModel();
//        $paymentsSellerRelModel = new PaymentsSellerRel();
//
//        if(!input('?param.code')){
//            return error_code(10051);
//        }
//        //判断此支付方式是否可用
//        $paymentInfo = $paymentsModel->where(['code'=>input('param.code'),'status'=>$paymentsModel::PAYMENT_STATUS_YES])->find();
//        if(!$paymentInfo){
//            return error_code(10052);
//        }
//        //判断商户是否已经开过此支付方式
//        $paymentSRInfo = $paymentsSellerRelModel->where(['payment_code'=>input('param.code'),'seller_id'=>$this->sellerId])->find();
//        if($paymentSRInfo){
//            return error_code(10053);
//        }
//        $this->assign('paymentInfo',$paymentInfo);
//        $this->assign('code',input('param.code'));
//        $result['data'] = $this->fetch();
//        $result['status'] = true;
//        return $result;
//    }


    /*
     * 支付方式删除
     * */
    public function del()
    {
        $paymentsSellerRel = new paymentsSellerRel();
        return $paymentsSellerRel->del(input('post.id/d'),$this->sellerId);
    }


    public function changeStatus()
    {
        $paymentsSellerRelModel = new PaymentsSellerRel();
        return $paymentsSellerRelModel->changeStatus(input('param.id/d'),input('param.status'),$this->sellerId);
    }
}