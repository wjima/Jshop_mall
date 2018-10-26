<?php

namespace app\Manage\controller;

use Request;
use app\common\controller\Manage;
use app\common\model\Payments as PaymentsModel;

class Payments extends Manage
{
    public function index()
    {
        $paymentsModel = new PaymentsModel();
        if(Request::isAjax())
        {
            $data = input('param.');
            return $paymentsModel->tableData($data);
        }
        return $this->fetch();
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

        if(!input('?param.code')){
            return error_code(10051);
        }

        if(Request::isPost()){
            $data = input('param.');
            return $paymentsModel->editData($data);
        }

        $paymentInfo = $paymentsModel->getPayment(input('param.code'));
        if(!$paymentInfo){
            return error_code(10052);
        }
        $paymentInfo['params'] = json_decode($paymentInfo['params'],true);

        $this->assign('paymentInfo',$paymentInfo);
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
        $url = "./static/files/cert/";

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


    public function changeStatus()
    {
        $paymentsModel = new PaymentsModel();
        return $paymentsModel->changeStatus(input('param.id/d'),input('param.status'));
    }
}