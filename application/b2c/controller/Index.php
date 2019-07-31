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
use app\common\model\Payments;
use app\common\model\User;
use app\common\model\UserWx;
use org\login\Wxofficial;
use think\Hook;


class Index extends Base
{
    public function index()
    {
        $this->redirect('/wap/index','302');
    }
}
