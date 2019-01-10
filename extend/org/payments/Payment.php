<?php

namespace org\payments;

interface Payment
{
    function pay($paymentInfo);


    /**
     * 异步回调
     * @return [
     *  'status' => true,
     *  'data' => [
     *      'payment_id' => '', //支付单
     *      'status' => '',     //支付结果，2支付成功，3支付异常
     *      'payed_msg' => '',  //支付结果标示符
     *      'trade_no' => '',   //支付平台订单号
     *      'money' => '',      //支付的金额，单位元
     *  ],
     *  'msg' => ''
     * ]
     */
    function callback();
    function refund($refundInfo,$paymentInfo);
}
