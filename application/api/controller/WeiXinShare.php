<?php
namespace app\api\controller;
use app\common\controller\Api;
use org\Curl;

/**
 * 微信分享
 * Class WeiXinShare
 * @package app\api\controller
 */
class WeiXinShare extends Api
{
    private $accessTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token';
    private $jsapiTicketUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi';


    public function share ()
    {
        $url = $this->request->param('url');

        if (!$url) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = "$protocol$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]";
        }
        return $this->getSignPackage($url);
    }

    /**
     *
     *  获取公众号签名
     * @param $url
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSignPackage($url) {
        $jsapiTicket = $this->getJsApiTicket();

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = [
            'appId'     => getSetting('wx_official_appid'),
            'nonceStr'  => $nonceStr,
            'timestamp' => $timestamp,
            'url'       => $url,
            'signature' => $signature,
            'rawString' => $string
        ];

        $result['status'] = true;
        $result['msg'] = '获取成功';
        $result['data'] = $signPackage;
        return $result;
    }


    /**
     *
     *  生成随机字符串
     * @param int $length
     * @return string
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getJsApiTicket ()
    {
        // 判断jsapi_ticket 是否过有效期
        $cache = cache('jsapi_ticket');

        if (!$cache || $cache->expire_time < time()) {
            $accessToken = $this->getAccessToken();

            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = $this->jsapiTicketUrl .= "&access_token=$accessToken";
            $curl = new Curl();
            $res = json_decode($curl->get($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data['expire_time'] = time() + 7000;
                $data['value'] = $ticket;
                cache('jsapi_ticket', json_encode($data));
            }
        } else {
            $ticket = $cache->value;
        }

        return $ticket;
    }


    /**
     * @return array|mixed
     */
    private function getAccessToken()
    {
        // 获取公众号配置
        $appid = getSetting('wx_official_appid');
        $secret = getSetting('wx_official_app_secret');
        if (!$appid || !$secret) return error_code(10015);


        // 判断access_token 是否过有效期
        $cache = cache('access_token');

        if (!$cache || $cache->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = $this->accessTokenUrl.= "?grant_type=client_credential&appid=".$appid."&secret=".$secret;

            $curl = new Curl();
            $res = json_decode($curl->get($url));
            $access_token = $res->access_token;

            if ($access_token) {
                $data['expire_time'] = time() + 7000;
                $data['value'] = $access_token;
                \cache('access_token', json_encode($data));
            }
        } else {
            $access_token = $cache->value;
        }
        return $access_token;
    }
}