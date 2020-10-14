<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\b2c\controller;

use app\common\controller\Base;
use app\common\model\BillAftersales;
use app\common\model\Payments;
use app\common\model\User;
use app\common\model\UserWx;
use org\login\Wxofficial;
use org\Wx;
use think\Hook;


class Index extends Base
{
    public function index()
    {
        $this->redirect('/wap/','302');
    }


    /**
     * 外部web-view调用客服页面
     * url: 域名+/b2c/index/kefu.html
     * @return mixed
     */
    public function kefu()
    {
        $ent_id = getSetting('ent_id');
        $this->assign('ent_id', $ent_id);
        return $this->fetch();
    }

    /**
     * 收货地址地图经纬度逆向解析
     */
    public function addressMap()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
        $key      = input('key');
        $location = input('location');
        $poi      = input('get_poi');
        $url      = 'https://apis.map.qq.com/ws/geocoder/v1/?location=' . $location . '&key=' . $key . '&get_poi=' . $poi;
        $data     = $this->map_curl($url);
        echo json_encode($data, 320);
        exit();
    }

    /**
     * 收货地址地图curl方法，增加来源页面
     * @param $url
     * @return mixed
     */
    private function map_curl($url)
    {
        $ch = curl_init(); //初始化
        curl_setopt($ch, CURLOPT_URL, $url); //你要访问的页面
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']); //伪造来路页面
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //是否显示内容
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }
}