<?php

namespace org\share;

class QrShare extends UrlShare implements BaseShare
{
    public function share($client, $page, $userShareCode, $url, $params){
        $re = $this->getCode($client, $page, $userShareCode, $url, $params);
        if(!$re['status']){
            return $re;
        }

        return $this->getQr($url, $re['data']['code']);
    }
    protected function getQr($url,$code){
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        $url = $this->getUrl($url,$code);
        $url = urldecode($url);
        $result['data'] = url('b2c/common/qr',['url' => $url],true,true);
        return $result;
    }
}
