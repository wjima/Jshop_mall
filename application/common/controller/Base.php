<?php

/**
 * 控制器基类
 *
 * @author sin
 *
 */

namespace app\common\controller;

use think\Controller;

class Base extends Controller
{

    protected function initialize()
    {
        parent::initialize();
        error_reporting(E_ALL ^ E_NOTICE);              //错误等级
        //初始化配置参数，用于在模板中使用
        $this->assign('params',config('params.'));
    }

}
