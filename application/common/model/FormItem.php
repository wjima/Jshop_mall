<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

class FormItem extends common{

    const REQUIRED_YES = 1;//必填
    const REQUIRED_NO = 2;//不是必填

    public function add($data){
        return $this->save($data);
    }

    /**
     * 表单验证
     * @param array $item
     * @param string $value
     * @return bool
     */
    public function validateField($item=[],$value='')
    {
        $function = 'is'.ucfirst($item['validation_type']);
        return $this->$function($value);
    }

    private function isString($vaule = ''){
        return true;
    }

    /***
     * 判断是否数字
     * @param int $value
     * @return bool
     */
    private function isNumber($value = 0)
    {
        return  is_numeric($value)?true:false;
    }

    /**
     * 判断是否整数
     * @param int $value
     * @return bool
     */
    private function isInteger($value = 0)
    {
        return is_int($value)?true:false;
    }

    /**
     * 判断是否价格
     * @param float $value
     * @return bool
     */
    private function isPrice($value = 0.00)
    {
        return is_numeric($value)?true:false;
    }

    /**
     * 判断是否邮箱
     * @param string $value
     * @return bool
     */
    private function isEmail($value = '')
    {
        return isEmail($value);
    }

    /**
     * 判断是否手机号
     * @param string $value
     * @return bool
     */
    private function isMobile($value = '')
    {
        return isMobile($value);
    }

}