<?php
//余额支付
namespace org\payments;
use app\common\model\BillPayments;
use app\common\model\Order;
use app\common\model\User;

class balancepay implements Payment
{
    private $config = [];

    function __construct($config)
    {
        $this->config = $config;
    }

    //去余额支付，减去用户余额
    public function pay($paymentInfo)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $balance = new \app\common\model\Balance();
        $res = $balance->change($paymentInfo['user_id'], $balance::TYPE_PAY, $paymentInfo['money'], $paymentInfo['payment_id']);
        if(!$res['status']){
            $result['msg'] = $res['msg'];
            return $result;
        }

        //改变支付单状态
        $billPaymentModel = new BillPayments();
        $billPaymentInfo = $billPaymentModel->where(['payment_id' => $paymentInfo['payment_id']])->find();
        if(!$billPaymentInfo){
            return error_code(10056,true);
        }
        $resultBillPayment = $billPaymentModel->toUpdate( $paymentInfo['payment_id'],$billPaymentModel::STATUS_PAYED,'balancepay',$paymentInfo['money'],$res['data']['memo'],$res['data']['id']);
        if($resultBillPayment['status']){
            $result['msg'] = $resultBillPayment['msg'];
            $result['status'] = true;
            $result['data'] = $paymentInfo;
            return $result;
        }else{
            $result['msg'] = $resultBillPayment['msg'];
            return $result;
        }
    }

    public function callback()
    {

    }

    //用户余额退款
    public function refund($refundInfo, $paymentInfo)
    {
        $result  = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        if(!$refundInfo['money'] || $refundInfo['money'] == 0){
            $result['status'] = true;
            $result['msg']    = '退款成功';
            return $result;
        }
        $balance = new \app\common\model\Balance();
        $res     = $balance->change($paymentInfo['user_id'], $balance::TYPE_REFUND, $refundInfo['money'], $paymentInfo['payment_id']);
        if (!$res['status']) {
            $result['msg'] = $res['msg'];
            return $result;
        }
        $result['status'] = true;
        $result['msg']    = '退款成功';
        return $result;
    }
}
