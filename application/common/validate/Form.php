<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\validate;

use think\Validate;

class Form extends Validate
{

    protected $rule = [
        'name'         => 'require',
        'sort'         => 'require|number',
        'type'         => 'require',
        'head_type'    => 'require',
        'button_name'  => 'require',
        'button_color' => 'require',
    ];

    protected $message = [
        'name.require'         => '请填写您的姓名',
        'sort.require'         => '请输入排序',
        'sort.number'          => '请输入正确的排序',
        'head_type.require'    => '请选择表头类型',
        'type.require'         => '请选择类型',
        'button_name.require'  => '请输入按钮名称',
        'button_color.require' => '请输入按钮颜色'
    ];
}