<?php
/**
 * 线下收款
 */
namespace org\payments;

class offline implements Payment
{
    private $config = [];

    function __construct($config){
        $this->config = $config;
    }

    public function pay($paymentInfo){
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        return $result;
    }
    public function callback(){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        return $result;
    }

    public function refund($refundInfo,$paymentInfo){
        return true;
    }
}
