<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/18
 * Time: 下午3:23
 */

namespace app\wechat\controller;

use app\common\controller\Wechat;
use org\ThirdWx;
use Request;
use think\facade\Log;


class Index extends Wechat
{
    public function index()
    {

        $input = file_get_contents('php://input');
        $file  = ROOT_PATH . 'runtime/' . basename(__FILE__) . '.log';
        //error_log(var_export($input,true),3,$file);
        // $input = file_get_contents('/Applications/MAMP/htdocs/www.jshop.com/application/wechat/controller/test.xml');
        $wx = new ThirdWx();
        if (!$input) {
            Log::record('未接收到微信平台消息');
            echo 'success';
            exit();
        }
        $res = $wx->decrypt($input);
        if ($res['status']) {
            echo 'success';
            exit();
        } else {
            error_log(var_export('错误码:' . $errCode . '数据报文' . $input, true), 3, __FILE__ . '.log');
        }
    }
}