<?php
namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\BillPayments;
use app\common\model\Payments as PaymentsModel;
use Request;

/**
 * 支付单模块
 * Class Order
 * @package app\api\controller
 * @author keinx
 */
class Payments extends Api
{
    /**
     * 获取店铺所有可用的支付接口
     * @return array
     */
    public function getList()
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        //$paymentsSRModel = new PaymentsSellerRel();
        $paymentsModel = new PaymentsModel();
        $result['data'] = $paymentsModel->getList();
        return $result;
    }

    /**
     * 取支付单信息
     */
    public function getInfo()
    {
        if(!input("?param.payment_id")){
            return error_code(10056);
        }
        $billPayments = new BillPayments();
        return $billPayments->getInfo(input('param.payment_id'),$this->userId);
    }

    /**
     * 支付确认页面取信息
     * @return array|\think\Config
     */
    public function checkPay()
    {
        if (!input("?param.ids")) {
            return error_code(13100);
        }
        if (!input("?param.payment_type")) {
            return error_code(10051);
        }

        //支付的时候，有一些特殊的参数需要传递到支付里面，这里就是干这个事情的,key=>value格式的一维数组
        $data = input('param.');
        if (!isset($data['params'])) {
            $params = [];
        } else {
            $params = $data['params'];
        }
        $billPaymentsModel = new BillPayments();
        return $billPaymentsModel->formatPaymentRel(input('param.ids'), input('payment_type'), $params);



    }



}