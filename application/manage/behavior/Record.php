<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
/**
 * 记录操作日志
 */
namespace app\manage\behavior;

use app\common\model\Operation;
use app\common\model\OperationLog;
use app\common\model\User;
use think\Db;
use think\facade\Log;
use think\facade\Request;

class Record
{
    //记录不需要保存的控制器和方法
    private function getConf()
    {
        //结构：控制器@方法名
        return [
            'categories@index',
            'user@comment',
            'wechat@verifylist',
            'goods@index',
            'categories@index',
            'brand@index',
            'goodstype@index',
            'goodsrypeSpec@index',
            'billrefund@index',
            'order@index',
            'billaftersales@index',
            'billreship@index',
            'articletype@index',
            'article@index',
            'notice@index',
            'advertposition@index',
            'advertisement@index',
            'promotion@index',
            'coupon@index',
            'images@index',
            'ship@index',
            'setting@index',
            'payments@index',
            'ietask@index',
            'store@index',
        ];
    }

    public function run($params)
    {
        $user       = session('manage');
        $opname     = $user['username'] ? $user['username'] : $user['mobile'];
        if (Request::isPost()) {
            $forbidMethod = $this->getConf();
            $ctl          = strtolower(Request::controller());
            $act          = strtolower(Request::action());

            if (!in_array($ctl . '@' . $act, $forbidMethod)) {
                $operation = new Operation();
                $opinfo    = $operation->getOperationInfo($ctl, $act, $operation::MENU_MANAGE);     //这里第三个参数要传模块的id，这里是商户端，以后还可能有总管理端，add by wht
                if ($opinfo['status']) {
                    $postData = input('post.');
                    $decs     = $opinfo['data']['act']['name'];
                    if ($postData['name'] || $postData['id']) {
                        $decs = $decs . '：' . ($postData['name'] ? $postData['name'] : $postData['id']);
                    }
                    $log      = [
                        'manage_id'  => $user['id'],
                        'controller' => $ctl,
                        'method'     => $act,
                        'desc'       => $decs,
                        'content'    => json_encode($postData),
                        'ip'         => get_client_ip(0,true),
                    ];
                    $logModel = new OperationLog();
                    $logModel->doAdd($log);
                } else {
                    Log::record(json_encode($opinfo));
                }
            }
        }
    }

}