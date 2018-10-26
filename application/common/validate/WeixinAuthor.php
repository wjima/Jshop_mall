<?php
/**
 * 微信授权信息验证表
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/17
 * Time: 下午12:36
 */
namespace app\common\validate;

use think\Validate;

class WeixinAuthor extends Validate
{


    public $rule = [
        'nick_name'  => 'require',
        'appid'   => 'require',
        'appsecret'      => 'require',
        'principal_name' => 'require'
    ];

    public $message = [
        'nick_name.require'  => '小程序名称不能为空',
        'appid.require'      => 'AppId不能为空',
        'appsecret.require'  => 'AppSecret不能为空',
        'user_name.require'            => '原始Id不能为空',
        'principal_name.require'    => '主体信息不能为空'
    ];

}