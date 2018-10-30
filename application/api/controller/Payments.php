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
        return $billPayments->getInfo(input('param.payment_id'),$this->sellerId,$this->userId);
    }



}