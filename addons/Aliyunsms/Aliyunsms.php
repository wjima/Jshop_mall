<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace addons\Aliyunsms;    // 注意命名空间规范

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use think\facade\Log;

/**
 * 阿里云短信插件
 */
class Aliyunsms extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name'        => 'Aliyunsms',    // 插件标识
        'title'       => '阿里云短信通道',    // 插件名称
        'description' => '阿里云发送短信插件，请勿和其它短信通道一起使用',    // 插件简介
        'status'      => 0,    // 状态
        'author'      => 'mark',
        'version'     => '0.2',
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现发送短信
     * @return mixed
     */
    public function sendsms($data)
    {
        $result     = [
            'status' => false,
            'data'   => [],
            'msg'    => '发送失败'
        ];
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);
        $params     = array();
        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId     = trim($setting['accessKeyId']);
        $accessKeySecret = trim($setting['accessKeySecret']);

        if ($data['params']['code'] == 'seller_order_notice') {
            $data['params']['mobile'] = trim(getSetting('shop_mobile'));
            if (!$data['params']['mobile']) {
                $result['msg'] = '商户手机号不存在';
                return $result;
            }
        }

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = trim($data['params']['mobile']);

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = trim($setting['aliyunPrefix']);

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        if (!isset($setting[$data['params']['code']]['data']['title']['value'])) {
            $result['msg'] = '请先配置后台短信接口';
            return $result;
        }
        $params["TemplateCode"] = trim($setting[$data['params']['code']]['data']['title']['value']);

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = $this->getTemplateData($data['params']);

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        Log::info('aliyunsms:' . json_encode($params));
        try {
            $content = $this->request(
                $accessKeyId,
                $accessKeySecret,
                "dysmsapi.aliyuncs.com",
                array_merge($params, array(
                    "RegionId" => "cn-hangzhou",
                    "Action"   => "SendSms",
                    "Version"  => "2017-05-25",
                )),
                $security
            );
            if ($content->Code != 'OK') {
                Log::error($content->Message);
                $result['msg'] = $content->Message;
                return $result;

            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $result['msg'] = $e->getMessage();
            return $result;
        }
        $result['status'] = true;
        $result['msg']    = '发送成功';
        return $result;
    }

    /**
     * 短信接口配置
     * @param array $params
     * @return mixed
     */
    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        $this->assign('config_params', $params);
        return $this->fetch('config');
    }

    /**
     * 模板转换
     * @param $params
     * @return mixed
     */
    function getTemplateData($params)
    {
        $config   = $this->getConfig();
        $variable = $config['template'][$params['code']]['data']['template']['variable'];
        foreach ($variable as $key => $val) {
            $variable[$key] = $params['params'][$val];
        }
        return $variable;
    }


    /**
     * 生成签名并发起请求
     *
     * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $accessKeySecret string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @param $method boolean 使用GET或POST方法请求，VPC仅支持POST
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    private function request($accessKeyId, $accessKeySecret, $domain, $params, $security = false, $method = 'POST')
    {
        $apiParams = array_merge(array(
            "SignatureMethod"  => "HMAC-SHA1",
            "SignatureNonce"   => uniqid(mt_rand(0, 0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId"      => $accessKeyId,
            "Timestamp"        => gmdate("Y-m-d\TH:i:s\Z"),
            "Format"           => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "${method}&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http') . "://{$domain}/";

        try {
            $content = $this->fetchContent($url, $method, "Signature={$signature}{$sortedQueryStringTmp}");
            return json_decode($content);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url, $method, $body)
    {
        $ch = curl_init();

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            $url .= '?' . $body;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0",
        ));

        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if ($rtn === false) {
            // 大多由设置等原因引起，一般无法保障后续逻辑正常执行，
            // 所以这里触发的是E_USER_ERROR，会终止脚本执行，无法被try...catch捕获，需要用户排查环境、网络等故障
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }
}