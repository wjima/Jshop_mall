<?php
namespace app\common\model;

class Payments extends Common
{

    const PAYMENT_STATUS_YES = 1;      //启用
    const PAYMENT_STATUS_NO  = 2;      //禁用

    const PAYMENT_ONLINE = 1;           //线上支付
    const PAYMENT_OFFLINE = 2;          //线下支付


    //判断支付方式是否可用
    public function enable($payment_code)
    {
        return Payments::where(array('payment_code' => $payment_code))->find();
    }

    /**
     * 取所有的支付方式
     */
    public function getList($enable = 1){
        $where = [];
        if($enable != 0){
            $where['status'] = $enable;
        }
        return $this->where($where)->select();
    }
}