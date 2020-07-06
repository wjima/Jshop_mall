<?php

namespace app\common\validate;

use think\Validate;

class UserShip extends Validate
{

    protected $rule = [
        'name' => 'require',
        'mobile' => 'require|mobile',
        'area_id' => 'require|number',
        'address' => 'require'
    ];


    protected $message = [
        'name.require' => '请输入收货人姓名',
        'mobile.require' => '请输入收货人手机号',
        'mobile.mobile' => '收货人手机号格式不正确',
        'area_id.require' => '请选择所在地区信息',
        'area_id.number' => '所在地区信息不正确',
        'address.require' => '请输入详细收货地址信息',
    ];
}