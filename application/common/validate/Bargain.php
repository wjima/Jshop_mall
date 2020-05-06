<?php

namespace app\common\validate;

use think\Validate;

class Bargain extends Validate
{

    protected $rule = [
        'name'                 => 'require',
        'intro'                => 'require',
        'goods_id'             => 'require',
        'status'               => 'in:1,2',
        'date'                 => 'require',
        'start_price'          => 'require',
        'end_price'            => 'require',
       /* 'bargain_max_price'    => 'require',
        'bargain_min_price'    => 'require',*/
        'significant_interval' => 'require',
        'total_times'          => 'require',
        'total_times'          => 'gt:0',
    ];


    protected $message = [
        'name.require'                 => '请输入活动名称',
        'intro.require'                => '请输入活动简介',
        'goods_id.require'             => '请选择单规格商品',
        'status.in'                    => '砍价活动状态错误',
        'date.require'                 => '请选择活动时间',
        'start_price.require'          => '请输入起始金额',
        'end_price.require'            => '请输入成交金额',
        'bargain_max_price.require'    => '请输入最大价',
        'bargain_min_price.require'    => '请输入最小价',
        'significant_interval.require' => '请输入有效时长',
        'total_times.require'          => '请输入砍价次数',
        'total_times.gt'               => '砍价总次数必须大于0',
    ];
}