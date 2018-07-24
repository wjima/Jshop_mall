<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/18
 * Time: 下午2:47
 */

namespace app\common\controller;

use think\Container;
use app\common\model\Operation;
class Wechat extends Base
{
    public function index()
    {
        header("Content-type: text/html; charset=utf-8");
        echo '河南吉海网络科技欢迎您~';exit();
    }
}