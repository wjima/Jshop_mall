<?php

namespace org\payments;

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
            $params['trade_type'] = "WAP";
        }
        if(!isset($params['trade_type'])){
            $params['trade_type'] = "WAP";
        }
        //if($params['trade_type'] == 'MWEB'){$params['trade_type'] = "WAP";}        //兼容h5端，其实这一行是不需要的

        if($params['trade_type'] != "PC" && $params['trade_type'] != "WAP"){
            $result['msg'] = $params;
            return $result;
        }
        if($params['trade_type'] == "WAP"){
            $method = "alipay.trade.wap.pay";
            $product_code = "QUICK_WAP_WAY";
        }else{
            $method = "alipay.trade.page.pay";
            $product_code = "FAST_INSTANT_TRADE_PAY";
        }



        $url = 'https://openapi.alipay.com/gateway.do';

        //组装系统参数
        $data["app_id"] = $this->config['appid'];
        $data["version"] = "1.0";
        $data["format"] = "json";
        $data["sign_type"] = "RSA2";
        $data["method"] = $method;
        $data["timestamp"] = date("Y-m-d H:i:s");
        $data["notify_url"] = url('b2c/Callback/pay',['code'=>'alipay'],'html',true);

        $return_url = "";
        if(isset($paymentInfo['params']) && $paymentInfo['params'] != ""){
            $params = json_decode($paymentInfo['params'],true);
            if(isset($params['return_url'])){
                $return_url = $params['return_url'];
            }
        }

        $data["return_url"] = $return_url;
        $data["charset"] = "utf-8";


        //业务参数
        $ydata["subject"] = 'jshopgoods';          //商品名称,此处用商户名称代替
        $ydata["out_trade_no"] = $paymentInfo['payment_id'];     //平台订单号
        $ydata["total_amount"] = $paymentInfo['money'];          //总金额，精确到小数点两位
        $ydata["product_code"] = $product_code;

        $data["biz_content"] = json_encode($ydata,JSON_UNESCAPED_UNICODE);


       //待签名字符串
        $preSignStr = $this->getSignContent($data);
        

        $data['sign'] = $this->sign($preSignStr,$data['sign_type']);


        //$sign = $this->sign($preSignStr,$data['sign_type']);

        //$requestUrl = $url."?".$preSignStr."&sign=".urlencode($sign);

        $result['data'] = [
            'payment_id' => $paymentInfo['payment_id'],
            'url' => $url,
            'data' => $data
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
            'msg' => ''
        ];

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

    protected function getSignContent($params) {
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
    protected function checkEmpty($value) {
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
    function encrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $screct_key = base64_decode($screct_key);
        $str = trim($str);
        $str = addPKCS7Padding($str);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        return base64_encode($encrypt_str);
    }

    /**
     * 解密方法
     * @param string $str
     * @return string
     */
    function decrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $str = base64_decode($str);
        $screct_key = base64_decode($screct_key);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        $encrypt_str = trim($encrypt_str);

        $encrypt_str = stripPKSC7Padding($encrypt_str);
        return $encrypt_str;

    }

    /**
     * 填充算法
     * @param string $source
     * @return string
     */
    function addPKCS7Padding($source){
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
    function stripPKSC7Padding($source){
        $source = trim($source);
        $char = substr($source, -1);
        $num = ord($char);
        if($num==62)return $source;
        $source = substr($source,0,-$num);
        return $source;
    }


}
