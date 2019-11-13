<?php
namespace org;

use app\common\model\Payments;

class Alipay
{
    public function code_to_token($appid, $code)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        $url    = 'https://openapi.alipay.com/gateway.do';
        //组装系统参数
        $data["app_id"]     = $appid;
        $data["version"]    = "1.0";
        $data["format"]     = "JSON";
        $data["sign_type"]  = "RSA2";
        $data["method"]     = 'alipay.system.oauth.token';
        $data["timestamp"]  = date("Y-m-d H:i:s");
        $data["charset"]    = "utf-8";
        $data['grant_type'] = 'authorization_code';
        $data['code']       = $code;
        //待签名字符串
        $preSignStr   = $this->getSignContent($data);
        $sign         = $this->sign($preSignStr, $data['sign_type']);
        $data['sign'] = $sign;
        $curl         = new Curl();
        $re           = $curl->post($url, $data);
        $re           = json_decode($re, true);
        if (!isset($re['error_response'])) {
            $result['data']   = $re['alipay_system_oauth_token_response'];
            $result['status'] = true;
        } else {
            $result['msg'] = $re['error_response']['code'] . ":" . $re['error_response']['sub_msg'];
        }
        return $result;
    }

//    public function get_user_info($appid, $token)
//    {
//        $result = [
//            'status' => false,
//            'data'   => '',
//            'msg'    => ''
//        ];
//        $url    = 'https://openapi.alipay.com/gateway.do';
//        //组装系统参数
//        $data["app_id"]     = $appid;
//        $data["version"]    = "1.0";
//        $data["format"]     = "JSON";
//        $data["sign_type"]  = "RSA2";
//        $data["method"]     = 'alipay.user.info.share';
//        $data["timestamp"]  = date("Y-m-d H:i:s");
//        $data["charset"]    = "utf-8";
//        $data['auth_token'] = $token;
//        //待签名字符串
//        $preSignStr   = $this->getSignContent($data);
//        $sign         = $this->sign($preSignStr, $data['sign_type']);
//        $data['sign'] = $sign;
//        $curl         = new Curl();
//        $re           = $curl->post($url, $data);
//        $re           = json_decode($re, true);
//        if (!isset($re['error_response'])) {
//            $userinfo = $this->getData($re['alipay_user_info_share_response']);//数据转换下
//            if (isset($userinfo['status']) && !$userinfo['status']) {
//                return $userinfo;
//            }
//            $result['data']   = $userinfo;
//            $result['status'] = true;
//        } else {
//            $result['msg'] = $re['error_response']['code'] . ":" . $re['error_response']['sub_msg'];
//        }
//        return $result;
//    }

//    /**
//     * 数据转换
//     * @param $params
//     * @return array
//     */
//    private function getData($params)
//    {
//
//        $imageModel = new Images();
//        $image      = $imageModel->saveImage($params['avatar'], true);//头像都按统一方法保存到本地或者远程图片服务器
//        if (!$image['status']) {
//            return $image;
//        }
//        $data['avatar']   = isset($image['data']['id']) ? $image['data']['id'] : _sImage();
//        $data['openid']   = $params['user_id'];
//        $data['nickname'] = isset($params['nick_name']) ? $params['nick_name'] : '企业用户';
//        $gender           = '0';
//        if (isset($params['gender']) && $params['gender'] == 'M') {
//            $gender = '1';
//        } elseif (isset($params['gender']) && $params['gender'] == 'F') {
//            $gender = '2';
//        }
//        $data['gender']   = $gender;
//        $data['language'] = '';
//        $data['city']     = $params['city'];
//        $data['province'] = $params['province'];
//        $data['country']  = '中国';//todo 海外用户需要调整
//        return $data;
//    }

    protected function getSignContent($params)
    {
        ksort($params);

        $stringToBeSigned = "";
        $i                = 0;
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


    private function sign($data, $signType = "RSA")
    {
        $parmentModel = new Payments();
        $alipayInfo   = $parmentModel->getPayment('alipay');

        $params = json_decode($alipayInfo['params'], true);
        $priKey = $params['rsa_private_key'];//todo 小程序私钥
        $res    = "-----BEGIN RSA PRIVATE KEY-----\n" .
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

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }
}