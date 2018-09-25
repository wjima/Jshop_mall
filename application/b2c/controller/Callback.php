<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
/**
 * b2c模块的各种回调方法都写到这里
 */
namespace app\b2c\controller;

use app\common\model\BillPayments;
use app\common\model\Payments;
use Request;
use app\common\model\User;
use app\common\controller\Base;


class Callback extends Base
{
    public function pay()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if(!input('?param.code')){
            return error_code(10051,true);
        }
        if(!input('?param.payment_id')){
            return error_code(10051,true);
        }
        //判断支付方式合法性
        $paymentsModel = new Payments();
        $paymentInfo = $paymentsModel->getPayment(input('param.code'));
        if(!$paymentInfo){
            return error_code(10057);
        }
        //取得支付单信息
        $billPaymentModel = new BillPayments();
        $billPaymentInfo = $billPaymentModel->where(['payment_id' => input('param.payment_id')])->find();
        if(!$billPaymentInfo){
            return error_code(10056,true);
        }

        //取此支付方式的配置信息，然后去支付,
        $conf = json_decode($paymentInfo['params'],true);

        //校验合法性
        $payment = \org\Payment::create(input('param.code'),$conf);
        $result = $payment->callback();
        if($result['status']){
            //到这里就说明校验成功了，去更新支付单
            $resultBillPayment = $billPaymentModel->toUpdate(input('param.payment_id'),$result['data']['status'],input('param.code'),$result['data']['payed_msg'],$result['data']['trade_no']);
            if($resultBillPayment['status']){
                return $result['msg'];
            }else{
                return $resultBillPayment['msg'];
            }
        }else{
            return $result['msg'];
        }


    }
}