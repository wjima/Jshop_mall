<?php

namespace org\share;

use org\Wx;

class QrShare extends UrlShare implements BaseShare
{
    public function share($client, $page, $userShareCode, $url, $params){
        $re = $this->getCode($client, $page, $userShareCode, $url, $params);
        if(!$re['status']){
            return $re;
        }

        return $this->getQr($url, $re['data']['code'],$client);
    }
    protected function getQr($url,$code,$client){
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        switch($client){
            case self::CLIENT_WXMNAPP:
                $wx = new Wx();
                $result = $wx->getQRCode($code,$url);
                break;
            default:
                $url = $this->getUrl($url,$code);
                $url = urlencode($url);
                $result['data'] = url('b2c/common/qr',[],true,true)."?url=".$url;
        }
        return $result;
    }
}
