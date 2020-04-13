<?php
namespace org\share;
use org\Wx;

/**
 * Class QrShare
 * @package org\share
 */
class QrShare extends UrlShare implements BaseShare
{
    /**
     * @param $client
     * @param $page
     * @param $userShareCode
     * @param $url
     * @param $params
     * @return array|mixed
     */
    public function share($client, $page, $userShareCode, $url, $params)
    {
        $re = $this->getCode($client, $page, $userShareCode, $url, $params);
        if (!$re['status']) {
            return $re;
        }
        return $this->getQr($url, $re['data']['code'], $client);
    }


    /**
     * @param $url
     * @param $code
     * @param $client
     * @return array
     */
    protected function getQr($url, $code, $client)
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        switch ($client) {
            case self::CLIENT_WXMNAPP:
                //微信小程序二维码
                $wx = new Wx();
                $result = $wx->getQRCode($code, $url);
                break;
            case self::CLIENT_ALIMNAPP:
                //支付宝小程序二维码
                //todo::待开发
                break;
            case self::CLIENT_TTMNAPP:
                //头条系小程序二维码
                //todo::待开发
            default:
                $url = $this->getUrl($url, $code);
                $url = urlencode($url);
                $result['data'] = url('b2c/common/qr', [], true, true)."?url=".$url;
        }
        return $result;
    }
}
