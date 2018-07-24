<?php
/**
 * b2c模块的各种回调方法都写到这里
 */
namespace app\b2c\controller;

use app\common\model\BillPayments;
use app\common\model\Payments;
use app\common\model\PaymentsSellerRel;
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
        $paymentModel = new Payments();
        $paymentInfo = $paymentModel->where(['code'=>input('param.code'),'is_online'=>$paymentModel::PAYMENT_ONLINE])->find();
        if(!$paymentInfo){
            return error_code(10057,true);
        }
        //取得支付单信息
        $billPaymentModel = new BillPayments();
        $billPaymentInfo = $billPaymentModel->where(['payment_id' => input('param.payment_id')])->find();
        if(!$billPaymentInfo){
            return error_code(10056,true);
        }
        //取此支付方式的信息，然后去支付
        $paymentsSellerRelModel = new PaymentsSellerRel();
        $psrInfo = $paymentsSellerRelModel->where(['seller_id'=>$billPaymentInfo['seller_id'],'payment_code'=>input('param.code')])->find();
        if(!$psrInfo){
            return error_code(10050);
        }
        //判断是否是共享店铺，如果是共享店铺，取平台的支付配置信息
        if(getSellerInfoById($billPaymentInfo['seller_id'],'store_type')){
            $conf = config('jshop.payment.'.input('param.code'));
        }else{
            //取此支付方式的配置信息，然后去支付,
            $conf = json_decode($psrInfo['params'],true);
        }

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