<?php

namespace org\payments;

class wechatpay implements Payment
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
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

        $data['appid'] = $this->config['appid'];
        $data['mch_id'] = $this->config['mch_id'];//商户号
        $data['nonce_str'] = self::getNonceStr();//$this->config['nonce_str'];//32位随机数
        $data['body'] = "商品详情";
        $data['out_trade_no'] = $paymentInfo['payment_id'];                    //商户订单号
        $data['total_fee'] = $paymentInfo['money']*100;                        //总金额
        $data['spbill_create_ip'] = $paymentInfo['ip'];                 //终端ip
        $data['notify_url'] = url('b2c/Callback/pay',['code'=>'wechatpay','payment_id'=>$paymentInfo['payment_id']],'html',true);                       //异步通知地址

        if(isset($paymentInfo['params']) && $paymentInfo['params'] != ""){
            $params = json_decode($paymentInfo['params'],true);

        }
        if(!isset($params['trade_type'])){
            $params['trade_type'] = 'JSAPI';
        }

        $data['trade_type'] = $params['trade_type'];                       //交易类型JSAPI微信小程序，MWEB是H5支付

        //当时JSAPI的时候，也就是小程序的时候，openid必传
        if($params['trade_type'] == 'JSAPI'){
            //取openid
            $userWxInfo = model('common/UserWx')->where(['user_id'=>$paymentInfo['user_id']])->find();
            if(!$userWxInfo){
                return error_code(11002);
            }
            $data['openid'] = $userWxInfo['openid'];
        }
        if($params['trade_type'] == 'MWEB'){
            $data['scene_info'] = [
                'h5_info' => [
                    'type' => 'Wap',
                    'wap_url' => $params['wap_url'],
                    'wap_name' => 'mysite',//$params['wap_name'],
                ]
            ];
            $data['scene_info'] = json_encode($data['scene_info']);
        }

        $data['sign'] = $this->makeSign($data);
        
        $xml = $this->toXml($data);

        $response = $this->postXmlCurl($xml, $url, false, 6);
        $re = $this->fromXml($response);
        if(!isset($re['return_code'])){
            $result['msg'] = "出错了";
            return $result;
        }
        if($re['return_code'] == 'SUCCESS'){
            if($re['result_code'] == 'SUCCESS'){
                $result['status'] = true;
                $data = $this->wxapppay($re);
                //支付单传到前台
                $data['payment_id'] = $paymentInfo['payment_id'];
                $result['data'] = $data;
            }else{
                $result['data'] = $re['err_code'];
                $result['msg'] = $re['err_code_des'];
            }
        }else{
            $result['data'] = '';
            $result['msg'] = $re['return_msg'];
        }
        return $result;
    }

    //根据统一下单接口的数据拼装调起支付所需要的参数
    private function wxapppay($data){
        $app_data = [];

        switch ($data['trade_type'])
        {
            case 'JSAPI':                   //微信小程序组建数据
                $app_data['timeStamp'] = time();
                $app_data['nonceStr'] = $data['nonce_str'];
                $app_data['package'] = 'prepay_id='.$data['prepay_id'];
                $app_data['signType'] = 'MD5';
                $app_data['paySign'] = md5(
                    'appId='.$data['appid'].
                    '&nonceStr='.$app_data['nonceStr'].
                    '&package='.$app_data['package'].
                    '&signType='.$app_data['signType'].
                    '&timeStamp='.$app_data['timeStamp'].
                    '&key='.$this->config['key']
                );
                break;
            case 'MWEB':                //微信H5支付
                $app_data['mweb_url'] = $data['mweb_url'];
        }

        return $app_data;
    }

    public function callback(){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        //获取通知的数据
        $xml = file_get_contents('php://input');
        $data = $this->xmlToArray($xml);

        if($data && $data['return_code'] == 'SUCCESS' && $data['sign'] == $this->makeSign($data)){
            //说明值没问题，并且验证签名通过
            if($data['result_code'] == "SUCCESS"){
                $result['status'] = true;
                $result['data']['status'] = 2;          //1未支付，2支付成功，3其他
                $result['data']['payed_msg'] = $data['result_code'];
                $result['data']['trade_no'] = $data['transaction_id'];
            }else{
                //如果未支付成功，也更新支付单
                $result['status'] = true;
                $result['data']['status'] = 3;          //1未支付，2支付成功，3其他
                $result['data']['payed_msg'] = $data['err_code'].':'.$data['err_code_des'];
                $result['data']['trade_no'] = '';
            }
        }else{
            return $result;
        }

        return $result;
    }
    public function refund($refundInfo,$paymentInfo){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if(isset($this->config['sslcert']) && $this->config['sslcert'] != ''){

        }else{
            $result['msg'] = "微信支付cert证书没有上传，不能在线退款";
            return $result;
        }
        if(isset($this->config['sslkey']) && $this->config['sslkey'] != ''){

        }else{
            $result['msg'] = "微信支付key证书没有上传，不能在线退款";
            return $result;
        }


        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

        $data['appid'] = $this->config['appid'];
        $data['mch_id'] = $this->config['mch_id'];//商户号
        $data['nonce_str'] = self::getNonceStr();
        $data['out_trade_no'] = $paymentInfo['payment_id'];      //平台支付单号
        $data['out_refund_no'] = $refundInfo['refund_id'];            //退款单号

        $data['total_fee'] = $paymentInfo['money']*100;                        //订单总金额
        $data['refund_fee'] = $refundInfo['money']*100;         //退款金额


        //$data['notify_url'] = url('b2c/Callback/pay',['code'=>'wechatpay','payment_id'=>$paymentInfo['payment_id']],'html',true);                       //异步通知地址

        $data['sign'] = $this->makeSign($data);
        $xml = $this->toXml($data);
        $response = $this->postXmlCurl($xml, $url, true, 6);
        if($response == ""){
            //出错了
        }
        $re = $this->fromXml($response);

        if(!isset($re['return_code'])){
            return "";
        }
        if($re['return_code'] == 'SUCCESS'){
            if($re['result_code'] == 'SUCCESS'){
                $result['status'] = true;
            }else{
                $result['msg'] = $re['err_code'].'-'.$re['err_code_des'];
            }
        }else{
            $result['msg'] = $re['return_msg'];
        }
        return $result;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function xmlToArray($xml)
    {
        if(!$xml){
            return [];
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    public function makeSign($values)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->ToUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->config['key'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    /**
     * 格式化参数格式化成url参数
     */
    private function toUrlParams($value)
    {
        $buff = "";
        foreach ($value as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
    /**
     * 输出xml字符
     **/
    public function toXml($data)
    {
        if(!is_array($data)
            || count($data) <= 0)
        {
            return "";
        }

        $xml = "<xml>";
        foreach ($data as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     */
    private function fromXml($xml)
    {
        if(!$xml){
            return "";
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $this->values;
    }
    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     */
    public function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
//        if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
//            && WxPayConfig::CURL_PROXY_PORT != 0){
//            curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
//            curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
//        }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            $cert_dir = ROOT_PATH.DS."public".DS."static".DS."files".DS."cert".DS;
            if(
                $this->config['sslcert'] == "" ||
                !file_exists($cert_dir.$this->config['sslcert']) ||
                $this->config['sslkey'] == "" ||
                !file_exists($cert_dir.$this->config['sslkey'])
            ){
                return "";
            }
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $cert_dir.$this->config['sslcert']);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $cert_dir.$this->config['sslkey']);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return "";
        }
    }
    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
}
