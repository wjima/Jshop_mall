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


class Index extends Base
{
    public function index()
    {
        $this->redirect('/wap/index','302');
    }
    public function t(){
        $cert_dir = ROOT_PATH.DS."config".DS."payment_cert".DS."wechatpay".DS;
        if(
            !file_exists($cert_dir."apiclient_cert.pem") ||
            !file_exists($cert_dir."apiclient_key.pem")
        ){
            echo 'dddd';
        }else{
            echo 'yes';
        }
    }
}
