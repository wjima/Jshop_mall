<?php
namespace addons\WelfarePro\lib;
use app\common\model\Payments;

class Hb
{

    protected $mchid;  //商户号
    protected $appid; //公众账号appid
    protected $apiKey; // API密钥
    protected $apiclient_cert; // api证书1
    protected $apiclient_key; // api证书2
    protected $sendName; // 商户名称
    protected $wishing; // 红包祝福语
    protected $actName; //活动名称
    protected $remark; //备注

    public function __construct()
    {

        $this->mchid = "";                        // 商户号
        $this->appid = "";                //  appid
        $this->apiKey = ""; //  秘钥

        $cert_dir = ROOT_PATH.DS."config".DS."payment_cert".DS."wechatpay".DS;
        $this->apiclient_cert = $cert_dir."apiclient_cert.pem";   //证书
        $this->apiclient_key = $cert_dir."apiclient_key.pem";
        $this->sendName = getSetting('shop_name');               //商户名称
        $this->wishing = "恭喜你，获得一个红包";              //红包祝福语
        $this->actName = "活动名称";                      //活动名称
        $this->remark = "越买越实惠，快来抢购吧！";                             //备注
    }

    /**
     * 发红包
     */
    public function send($openid, $totalFee,$scene_id)
    {
        $result = [
            'status' => true,
            'msg' => '发送成功',
            'data' => ''
        ];

        $scene_id = "PRODUCT_".$scene_id;

        $unified = array(
            'wxappid' => $this->appid,
            'send_name' => $this->sendName,
            'scene_id' =>$scene_id,
            'mch_id' => $this->mchid,
            'nonce_str' => self::createNonceStr(),
            're_openid' => $openid,
            'mch_billno' => time().mt_rand(10000,99999),
            'client_ip' => get_client_ip(0),
            'total_amount' => intval($totalFee),       //单位 转为分
            'total_num'=>1,     //红包发放总人数
            'wishing'=> $this->wishing,      //红包祝福语
            'act_name'=> $this->actName,           //活动名称
            'remark'=> $this->remark,               //备注信息，如为中文注意转为UTF8编码
        );
        $unified['sign'] = self::getSign($unified,$this->apiKey);

        $responseXml = $this->curlPost('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack', self::arrayToXml($unified));
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($unifiedOrder === false) {
            $result ['msg'] = 'xml解析错误';
            $result ['status'] = false;
            return $result;
        }

        $jsonStr = json_encode($unifiedOrder);
        $jsonArray = json_decode($jsonStr,true);

        if ($jsonArray['return_code'] != 'SUCCESS') {
            $result ['msg'] = $jsonArray['return_msg'];
            $result ['status'] = false;
            return $result;
        }
        if ($jsonArray['result_code'] != 'SUCCESS') {
            $result ['msg'] = $jsonArray['err_code_des'];
            $result['data'] = $jsonArray['err_code'];
            $result ['status'] = false;
            return $result;
        }else{
            $result['data'] = bcdiv($jsonArray['total_amount'],100,2);
        }

        return $result;
    }

    public function curlPost($url = '', $postData = '', $options = array())
    {

        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,$this->apiclient_cert);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,$this->apiclient_key);
        //第二种方式，两个文件合成一个.pem文件
        //        curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');
        $data = curl_exec($ch);
        if(!$data){
            $file ="./hb.txt";
            file_put_contents($file, date("Y-m-d H:i:s")."\r\n".curl_error($ch)."\r\n",FILE_APPEND);
        }

        curl_close($ch);
        return $data;
    }
    public static function createNonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }
    public static function getSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }
    protected static function formatQueryParaMap($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
}
