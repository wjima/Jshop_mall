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
        return $areaModel->getArea(input('param.id',0));
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
        $conf['shop_logo'] = _sImage(getSetting('shop_logo'));         //店铺logo
        $conf['shop_name'] = getSetting('shop_name');                   //店铺名称
        $conf['shop_desc'] = getSetting('shop_desc');                   // 店铺描述
        $conf['image_max'] = 5;             //前端上传图片最多几张
        $conf['store_switch'] = getSetting('store_switch');             //开启门店自提状态
        $conf['cate_style'] = getSetting('cate_style');                 //分类样式
        $conf['cate_type'] = getSetting('cate_type');                   // H5分类类型
        $conf['tocash_money_low'] = getSetting('tocash_money_low');                 //最低提现
        $conf['tocash_money_rate'] = getSetting('tocash_money_rate');                 //服务费
        $conf['point_switch'] = getSetting('point_switch');             //是否开启积分功能
        $conf['statistics'] = getSetting('statistics_code');            //获取统计代码
        $recommend_keys = getSetting('recommend_keys');
        $conf['recommend_keys'] = explode(' ', $recommend_keys);    //搜索推荐关键字
        $conf['invoice_switch'] = getSetting('invoice_switch'); //发票功能开关
        $conf['goods_stocks_warn'] = getSetting('goods_stocks_warn'); //库存报警数量
        $conf['shop_default_image'] = _sImage(getSetting('shop_default_image')); //获取默认图片
        $conf['shop_mobile'] = getSetting('shop_mobile'); // 店铺联系电话
        $conf['app_update_auto'] = checkAddons('appupdate');
        return $conf;
    }

}