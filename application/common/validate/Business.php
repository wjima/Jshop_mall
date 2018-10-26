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

class Business extends Validate{

    protected $rule =[
        'name'  =>      'require|chs',
        'phone' =>      'require|mobile',
        'industry' =>   'require'
    ];

    protected $message =[
        'name.require'  =>  '请填写您的姓名',
        'name.chs'      =>  '请输入正确的姓名',
        'phone.require' =>  '请输入您的联系方式',
        'phone.mobile'  =>  '请输入正确的手机号',
        'industry'      =>  '请选择您所在的行业'
    ];
}