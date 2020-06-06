<?php

namespace org\payments;

use org\Curl;
use app\common\model\UserWx;

class alipay implements Payment
{
    private $config = [];

    function __construct($config){
        $this->config = $config;
    }

    public function pay($paymentInfo){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        //其实就是判断是pc端支付，还是h5端支付，pc端传trade_type的值为PC，h5端传trade_type为WAP
        if(isset($paymentInfo['params']) && $paymentInfo['params'] != ""){
            $params = json_decode($paymentInfo['params'],true);
        }else{
            $params = [];
        }
        if(!isset($params['trade_type'])){
            $params['trade_type'] = "WAP";
        }
        //if($params['trade_type'] == 'MWEB'){$params['trade_type'] = "WAP";}        //兼容h5端，其实这一行是不需要的

        if($params['trade_type'] != "PC" && $params['trade_type'] != "WAP" && $params['trade_type'] != "APP" && $params['trade_type'] != "JSAPI" && $params['trade_type'] != 'TT'){
            $result['msg'] = "不支持的支付模式";
            return $result;
        }
        if($params['trade_type'] == "WAP") {
            $method = "alipay.trade.wap.pay";
            $product_code = "QUICK_WAP_WAY";
        }elseif($params['trade_type'] == "APP"){
            $method = "alipay.trade.app.pay";
            $product_code = "QUICK_MSECURITY_PAY";
        }elseif($params['trade_type'] == "JSAPI"){//支付宝小程序支付，调用统一下单接口
            $method = "alipay.trade.create";
            $product_code = "";
        }elseif($params['trade_type'] == "TT"){ //头条小程序的收银台模式
            $method = "alipay.trade.app.pay";
            $product_code = "QUICK_MSECURITY_PAY";
        }else{
            $method = "alipay.trade.page.pay";
            $product_code = "FAST_INSTANT_TRADE_PAY";
        }

        $url = 'https://openapi.alipay.com/gateway.do';

        //组装系统参数
        if($params['trade_type'] == "JSAPI"){
            $data["app_id"] = getAddonsConfigVal("MPAlipay","mp_alipay_appid");
        }else{
            $data["app_id"] = $this->config['appid'];
        }
        $data["version"] = "1.0";
        $data["format"] = "JSON";
        $data["sign_type"] = "RSA2";
        $data["method"] = $method;
        $data["timestamp"] = date("Y-m-d H:i:s");
        $data["notify_url"] = url('b2c/Callback/pay',['code'=>'alipay'],'html',true);

        if($params['trade_type'] == "PC" || $params['trade_type'] == "WAP"){
            $return_url = "";
            if(isset($params['return_url'])){
                $return_url = $params['return_url']."?id=".$paymentInfo['payment_id'];
            }
            $data["return_url"] = $return_url;
        }

        $data["charset"] = "utf-8";


        //业务参数
        $ydata["subject"] = $paymentInfo['pay_title'];
        $ydata["out_trade_no"] = $paymentInfo['payment_id'];     //平台订单号
        $ydata["total_amount"] = $paymentInfo['money'];          //总金额，精确到小数点两位
        if($product_code != ""){
            $ydata["product_code"] = $product_code;
        }

        if($params['trade_type'] == 'JSAPI'){
            //取open_id
            $openid_re = $this->getOpenId($paymentInfo['user_id']);
            if(!$openid_re['status']){
                return $openid_re;
            }
            $ydata["buyer_id"] = $openid_re['data'];
        }

        //$ydata["buyer_id"] = "2088902044999606";

        $data["biz_content"] = json_encode($ydata,JSON_UNESCAPED_UNICODE);


       //待签名字符串
        $preSignStr = $this->getSignContent($data);

        $sign = $this->sign($preSignStr,$data['sign_type']);


        if($params['trade_type'] == "APP") {
            $data = $preSignStr . "&sign=" . urlencode($sign);
        }elseif($params['trade_type'] == "JSAPI"){
            //走统一下单接口
            $data = $preSignStr . "&sign=" . urlencode($sign);
            $re = $this->create($url,$data);
            if(!$re['status']){
                return $re;
            }
            $re['data']['payment_id'] = $paymentInfo['payment_id'];
            return $re;
            //组装数据。
        }elseif($params['trade_type'] == "TT"){
            $data = $preSignStr . "&sign=" . urlencode($sign);
        }else{
            $data['sign'] = $sign;      //wap和pc传所有值，在前端模拟表单提交吧
        }

        $ttOrderInfo = [];
        if($params['trade_type'] == "TT"){
            //头条小程序再签名
            $ttConfig = getAddonsConfig('MPToutiao');
            $ttOrderInfo = [
                'merchant_id' => $ttConfig['mp_toutiao_paymid'],
                'app_id' => $ttConfig['mp_toutiao_payappid'],
                'sign_type' => 'MD5',
                'timestamp' => time(),
                'version' => '2.0',
                'trade_type' => 'H5',
                'product_code' => 'pay',
                'payment_type' => 'direct',
                'out_order_no' => $paymentInfo['payment_id'],
                'uid' => $paymentInfo['user_id'],
                'total_amount' => ceil($paymentInfo['money'] * 100),
                'currency' => 'CNY',
                'subject' => $paymentInfo['pay_title'],
                'body' => $paymentInfo['pay_title'],
                'trade_time' => time(),
                'valid_time' => 3600,
                'notify_url' => 'https://www.jihainet.com/',
                'alipay_url' => $data,
                'wx_url' => '',
            ];

            $ttOrderInfopreSignStr = $this->getSignContent($ttOrderInfo);
            $ttSign = md5($ttOrderInfopreSignStr.$ttConfig['mp_toutiao_paysecret']);
            $ttOrderInfo['sign'] = $ttSign;
        }

        $result['data'] = [
            'payment_id' => $paymentInfo['payment_id'],
            'url' => $url,
            'data' => $data,
            'tt_order_info' => $ttOrderInfo?$ttOrderInfo:[]
        ];
        $result['status'] = true;
        return $result;
    }



    public function callback(){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        trace(input('post.'),'alipay');
        //获取通知的数据
        $data = input('post.');
        $sign = $data['sign'];
        $data['sign_type'] = null;
        $data['sign'] = null;

        $re = $this->verify($this->getSignContent($data), $sign,"RSA2");

        if($re){
            if ($data['trade_status'] == 'TRADE_SUCCESS' || $data['trade_status'] == 'TRADE_FINISHED') {
                $result['status'] = true;
                $result['data']['payment_id'] = $data['out_trade_no'];
                $result['data']['status'] = 2;          //1未支付，2支付成功，3其他
                $result['data']['payed_msg'] = $data['trade_status'];
                $result['data']['trade_no'] = $data['trade_no'];
                $result['data']['money'] = $data['total_amount'];
            }else{
                //如果未支付成功，也更新支付单
                $result['status'] = true;
                $result['data']['payment_id'] = $data['out_trade_no'];
                $result['data']['status'] = 3;          //1未支付，2支付成功，3其他
                $result['data']['payed_msg'] = $data['trade_status'];
                $result['data']['trade_no'] = '';
                $result['data']['money'] = $data['total_amount'];
            }
            $result['msg'] = 'success';
        }else{
            $result['msg'] = "fail";
        }
        return $result;
    }
    public function refund($refundInfo,$paymentInfo){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '出错了'
        ];
        //取appid,如果是支付宝小程序的话，单独取appid
        if(isset($paymentInfo['params']) && $paymentInfo['params'] != ""){
            $params = json_decode($paymentInfo['params'],true);
            if(isset($params['trade_type']) && $params['trade_type'] == "JSAPI"){
                $app_id = getAddonsConfigVal("MPAlipay","mp_alipay_appid");
            }else{
                $app_id = $this->config['appid'];
            }
        }else{
            $app_id = $this->config['appid'];
        }

        $url = 'https://openapi.alipay.com/gateway.do';



        $url = "https://openapi.alipay.com/gateway.do";
        $data = [
            'app_id' => $app_id,
            'method' => "alipay.trade.refund",
            'format' => "JSON",
            'charset' => "utf-8",
            'sign_type' => "RSA2",
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => "1.0",
            'biz_content' => [
                'out_trade_no' => $paymentInfo['payment_id'],
                'out_request_no' => $refundInfo['refund_id'],
                'refund_amount' => $refundInfo['money']
            ]
        ];

        $data["biz_content"] = json_encode($data["biz_content"],JSON_UNESCAPED_UNICODE);

        //待签名字符串
        $preSignStr = $this->getSignContent($data);
        $sign = $this->sign($preSignStr,$data['sign_type']);
        $data['sign'] = $sign;

        $curl = new Curl();
        $re = $curl->post($url,$data);
        // 判断是否退款成功
        $re = json_decode($re,true);

        if(isset($re['alipay_trade_refund_response']['code']) && $re['alipay_trade_refund_response']['code'] == '10000'){
            $result['msg'] = $re['alipay_trade_refund_response']['msg'];

            if($re['alipay_trade_refund_response']['msg'] == "Success" && $re['alipay_trade_refund_response']['fund_change'] == 'Y'){
                $result['status'] = true;
            }
        }
        return $result;

    }

    //支付宝统一下单接口
    private function create($url,$data){
        $curl = new Curl();
        $re = $curl::post($url,$data);
        $re = json_decode($re,true);

        if(!isset($re['alipay_trade_create_response']['code'])){
            return error_code(10000);
        }
        if($re['alipay_trade_create_response']['code'] != '10000'){
            return [
                'status' => false,
                'data' => $re['alipay_trade_create_response'],
                'msg' => $re['alipay_trade_create_response']['sub_msg']
            ];
        }
        return [
            'status' => true,
            'data' => [
                'out_trade_no' => $re['alipay_trade_create_response']['out_trade_no'],
                'trade_no' => $re['alipay_trade_create_response']['trade_no']
            ],
            'msg' => ''
        ];


    }

    /**
     * 获取seller_id
     * @param $user_id      用户id
     * @return array
     */
    private function getOpenId($user_id){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $userWxModel = new UserWx();

        $type = $userWxModel::TYPE_ALIPAY;

        $userWxInfo = $userWxModel->where(['type'=>$type,'user_id'=>$user_id])->find();
        if(!$userWxInfo){
            return error_code(10063);
        }
        $result['data'] = $userWxInfo['openid'];
        $result['status'] = true;
        return $result;
    }



    private function verify($data, $sign, $signType = 'RSA') {
        $pubKey=$this->config['alipay_public_key'];
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        //调用openssl内置方法验签，返回bool值

        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        trace($result, 'alipay');
        return $result;
    }


    private function sign($data, $signType = "RSA") {
        $priKey=$this->config['rsa_private_key'];
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        $sign = base64_encode($sign);
        return $sign;
    }

    private function getSignContent($params) {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }
    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    private function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }


    /**
     * 加密方法
     * @param string $str
     * @return string
     */
    private function encrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $screct_key = base64_decode($screct_key);
        $str = trim($str);
        $str = $this->addPKCS7Padding($str);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        return base64_encode($encrypt_str);
    }

    /**
     * 解密方法
     * @param string $str
     * @return string
     */
    private function decrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $str = base64_decode($str);
        $screct_key = base64_decode($screct_key);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        $encrypt_str = trim($encrypt_str);

        $encrypt_str = $this->stripPKSC7Padding($encrypt_str);
        return $encrypt_str;

    }

    /**
     * 填充算法
     * @param string $source
     * @return string
     */
    private function addPKCS7Padding($source){
        $source = trim($source);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }
    /**
     * 移去填充算法
     * @param string $source
     * @return string
     */
    private function stripPKSC7Padding($source){
        $source = trim($source);
        $char = substr($source, -1);
        $num = ord($char);
        if($num==62)return $source;
        $source = substr($source,0,-$num);
        return $source;
    }


}
