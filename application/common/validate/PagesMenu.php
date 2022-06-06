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

class PagesMenu extends Validate
{

    protected $rule = [
        'name'       => 'require|max:10',
        'ptype'      => 'require|in:redirectTo,navigateTo',
        'params'     => 'require',
        'selecticon' => 'require',
        'icon'       => 'require',
    ];

    protected $message = [
        'name.require'       => '请填写菜单名称',
        'name.max'           => '菜单名称字数不超过5个汉字或10个字母',
        'ptype.require'      => '动作类型不能为空',
        'type.in'            => '动作类型错误',
        'selecticon.require' => '选中图标不能为空',
        'icon.require'       => '未选中图标',
    ];
}