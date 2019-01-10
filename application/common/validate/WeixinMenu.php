<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\validate;

use think\Validate;

class WeixinMenu extends Validate
{

    protected $rule = [
        'name'    => 'require|max:40',
        'type'    => 'require|in:click,view,miniprogram',
        'menu_id' => 'require|number',
    ];

    protected $message = [
        'name.require' => '请填写菜单名称',
        'name.max'     => '菜单名称字数不超过13个汉字或40个字母',
        'type.require' => '菜单类型不能为空',
        'type.in'      => '菜单类型错误',
        'menu_id'      => '关键参数丢失',
    ];
}