<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\controller;

class Wechat extends Base
{
    public function index()
    {
        header("Content-type: text/html; charset=utf-8");
        echo '河南吉海网络科技欢迎您~';exit();
    }
}