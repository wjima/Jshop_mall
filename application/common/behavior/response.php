<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\behavior;


class response
{

    public function run($params)
    {
        $class = is_object($params) ? get_class($params) : $params;
        $classname = basename(str_replace('\\', '/', $class));
        $data = $params->getData();
        if($classname =='Json' && is_array($data) &&!isset($data['token'])){
            $data['token'] = \think\facade\Request::token('__Jshop_Token__', 'sha1');
            $params->data($data);
        }
    }
}