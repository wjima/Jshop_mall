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

    public function changeStatus()
    {
        $paymentsModel = new PaymentsModel();
        return $paymentsModel->changeStatus(input('param.id/d'),input('param.status'));
    }
}