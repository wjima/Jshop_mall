<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace org;

use think\facade\Cache;

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
            'data' => [],
            'msg' => ''
        ];

        $config = getAddonsConfig('MPToutiao');
        if (!$config) {
            $result['msg'] = '请开启头条小程序插件';
            return $result;
        }
        $url = 'https://developer.toutiao.com/api/apps/jscode2session?appid=' . $config['mp_toutiao_appid'] . '&secret=' . $config['mp_toutiao_secret'] . '&code=' . $code;

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


    /**
     * 获取AccessToken
     * @param string $grant_type
     * @return array
     */
    public function getAccessToken($grant_type = 'client_credential')
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $config = getAddonsConfig('MPToutiao');
        if (!$config) {
            $result['msg'] = '请开启头条小程序插件';
            return $result;
        }

        $key = $config['mp_toutiao_appid'] . '_' . $config['mp_toutiao_secret'];
        $val = Cache::get($key);
        if (!$val) {
            $url = 'https://developer.toutiao.com/api/apps/token?appid=' . $config['mp_toutiao_appid'] . '&secret=' . $config['mp_toutiao_secret'] . '&grant_type=' . $grant_type;

            $curl = new Curl();
            $res = $curl->get($url);
            $res = json_decode($res, true);

            if ($res['error'] == 0) {
                $result['data'] = $res['access_token'];
                Cache::set($key, $res['access_token'], 3600);
                $result['status'] = true;
            } else {
                $result['msg'] = $res['errcode'] . ':' . $res['errmsg'];
            }
        } else {
            $result['status'] = true;
            $result['data'] = $val;
        }

        return $result;
    }


    /**
     * 二维码生成
     * @param $access_token
     * @param $tt_platform
     * @param $page
     * @param $parameter
     * @param $width
     * @param $set_icon
     * @return array
     */
    public function getParameterQRCode($access_token, $tt_platform, $page, $parameter, $width, $set_icon)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $config = getAddonsConfig('MPToutiao');
        if (!$config) {
            $result['msg'] = '请开启头条小程序插件';
            return $result;
        }

        $filename = "static/qrcode/toutiao/" . md5($config['mp_toutiao_appid'] . '_' . $config['mp_toutiao_secret'] . '_' . $tt_platform . '_' . $page . '_' . $parameter . '_' . $width . '_' . $set_icon) . '.jpg';

        if (file_exists($filename)) {
            //有这个二维码了
            $return['status'] = true;
            $return['msg'] = '二维码获取成功';
            $return['data'] = $filename;
        } else {
            $url = 'https://developer.toutiao.com/api/apps/qrcode';
            $data = [
                'access_token' => $access_token,
                'appname' => $tt_platform,
                'path' => $page.'%3f'.'scene%3d'.$parameter,
                'width' => $width,
                'set_icon' => $set_icon,
            ];

            $curl = new Curl();
            $res = $curl->post($url, $data);
            $flag = json_decode($res, true);

            if ($flag && $flag['errcode'] == -1) {
                $return['msg'] = '后台生成小程序二维码出现系统错误';
                return $return;
            } else if ($flag && $flag['errcode'] == 40002) {
                $return['msg'] = '后台生成小程序二维码access_token错误';
                return $return;
            } else if ($flag && $flag['errcode'] == 40016) {
                $return['msg'] = '后台生成小程序二维码appname错误';
                return $return;
            } else if ($flag && $flag['errcode'] == 40021) {
                $return['msg'] = '后台生成小程序二维码宽度超过指定范围';
                return $return;
            } else if ($flag && $flag['errcode'] == 60003) {
                $return['msg'] = '后台生成小程序二维码频率超过限制';
                return $return;
            } else {
                $return['msg'] = '后台生成小程序二维码参数错误：'.$flag['errcode'].'：'.$flag['errmsg'];
                return $return;
            }

            $file = fopen($filename, "w");//打开文件准备写入
            fwrite($file, $res);//写入
            fclose($file);//关闭

            if (file_exists($filename)) {
                $return['status'] = true;
                $return['msg'] = '二维码获取成功';
                $return['data'] = $filename;
            }
        }

        return $result;
    }
}