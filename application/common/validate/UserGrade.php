<?php
/**
 * 用户等级验证表
 */
namespace app\common\validate;

use think\Validate;

class UserGrade extends Validate
{


    public $rule = [
        'id'  => 'require|number|between:1,99',
        'name'   => 'require|max:60'
    ];

    public $message = [
        'id.require' => '请填写编号',
        'id.number'  => '编号请填写数字',
        'id.between'     => '请填写1~100的数字',
        'name.require'  => '请输入用户等级名称',
        'name.max'      => '等级名称最大60个字符',
    ];

}