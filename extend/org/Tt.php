<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace org;

/**
 * Class Tt
 * @package org
 */
class Tt
{
    /**
     * @param $code
     * @return array
     */
    public function code_to_token($code)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];

        $config = getAddonsConfig('MPToutiao');
        if (!$config) {
            $result['msg'] = '请开启头条小程序插件';
            return $result;
        }
        $url = 'https://developer.toutiao.com/api/apps/jscode2session?appid='.$config['mp_toutiao_appid'].'&secret='.$config['mp_toutiao_secret'].'&code='.$code;

        $curl = new Curl();
        $res = $curl->get($url);
        $res = json_decode($res, true);

        if ($res['error'] == 0) {
            $result['data'] = $res;
            $result['status'] = true;
        } else {
            $result['msg'] = $res['errcode'] . ':' . $res['errmsg'];
        }

        return $result;
    }
}