<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: sin <sin@jihainet.com>
// +----------------------------------------------------------------------

/**
 * 此控制器不用api校验，不需要token登陆，就是单纯的标准的接口数据,用于取一些通用的信息
 */

namespace app\api\controller;

use app\common\model\Area;

use app\common\controller\Base;

use app\common\model\Seller;

class Common extends Base
{

    protected function initialize()
    {
        parent::initialize();
        //解决跨域问题
        header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式
    }

    /**
     * 取省市区的详细信息
     * @return array
     */
    public function area()
    {
        $areaModel = new Area();
        return $areaModel->getArea(input('param.id'));
    }

    /**
     * 取地区的下级列表
     * @return array
     */
    public function areaChildren()
    {
        $areaModel = new Area();
        return $areaModel->getAllChild(input('param.id'));
    }

    /**
     * 获取验证码，如果登陆失败次数太多，就需要验证码了
     * @return string
     */
    public function verify()
    {
        if(input('?param.id')){
            $id = input('param.id');
        }else {
            $id = "";
        }
        return captcha_src($id);
    }

    //获取店铺设置，此接口用于统一返回店铺的公开设置，然后本地缓存即可
    public function jshopConf()
    {
        $conf = [];

        $conf['abc'] = '123';


        return $conf;
    }

}