<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
/**
 * 校检token
 */
namespace app\manage\behavior;

use think\Db;
use think\facade\Request;

class ValidateToken
{
    //需要校检的控制器和方法
    private function getConf()
    {
        //结构：控制器@方法名
        return [
            'ship@add',
            'ship@edit',
            'goods@doadd',
            'goods@doedit',
            'categories@add',
            'categories@edit',
            'brand@add',
            'brand@edit',
            'goodstype@add',
            'goodstype@edit',
            'goodstypespec@add',
            'goodstypespec@edit',
            'goodsparams@add',
            'goodsparams@edit',
            'order@edit',
            'billpayments@topay',
            'billaftersales@audit',
            'carousel@edit',
            'carousel@add',
            'notice@add',
            'notice@edit',
            'user@edituser',
            'user@adduser',
            'user@editmoney',
            'user@editpoint',
            'user@gradeadd',
            'role@add',
            'administrator@add',
            'administrator@edit',
            'article@add',
            'article@edit',
            'articletype@add',
            'articletype@edit',
            'promotion@add',
            'promotion@edit',
            'promotion@groupadd',
            'promotion@groupedit',
            'balance@tocashexamine',
            'payments@edit',
            'billrefund@refund',
            'hooks@add',
            'hooks@edit',
            'area@add',
            'store@add',
            'store@edit',
            'setting@index',
            'logistics@add',
            'logistics@edit',
            'operation@add',
            'wechat@doedit',
            'wechat@addmessage',
            'wechat@doeditmediamessage',
            'wechat@doeditmenu',
            'wechat@doedit',
            'form@add',
            'form@edit',
            'common@login'
        ];
    }

    public function run($params)
    {
        $validateToken = config('app.validate_token');
        if (Request::isPost() && $validateToken) {
            $validateMethod = $this->getConf();
            $ctl          = strtolower(Request::controller());
            $act          = strtolower(Request::action());
            if (in_array($ctl . '@' . $act, $validateMethod)) {
                validateJshopToken();
            }
        }
    }

}