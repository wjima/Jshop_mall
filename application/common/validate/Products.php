<?php
/**
 * 货品验证规则
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/17
 * Time: 下午12:36
 */
namespace app\common\validate;

use think\Validate;

class Products extends Validate
{


    public $rule = [
        'goods_id'   => 'require|number',
        'costprice'  => 'float',
        'mktprice'   => 'float',
        //'price'      => 'require|float',
        'is_defalut' => 'in:1,2',
        'marketable' => 'in:1,2',
        'stock'      => 'regex:/^-?[0-9]\d*$/',
        'sn'         => 'unique:products',
    ];

    protected $scene = [
        'edit' => ['goods_id', 'costprice', 'mktprice', 'price', 'is_defalut', 'marketable', 'stock', 'sn' => 'unique:products,sn^id'],
    ];

    public $message = [
        'goods_id.require' => '商品ID不能为空',
        'goods_id.number'  => '商品ID非法',
        'costprice'        => '请输入正确的成本价',
        'mktprice'         => '请输入正确的市场价',
        //'price.float'      => '请输入正确的销售价',
        //'price.require'    => '销售价不能为空',
        'is_defalut.in'    => '是否默认商品超出范围',
        'marketable.in'    => '上下架状态超出范围',
        'stock.regex'      => '库存非法',
        'sn.unique'        => '货品编号不能重复',
    ];

}