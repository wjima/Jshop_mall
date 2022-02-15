<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: sin <sin@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Area;
use app\common\controller\Base;
use app\common\model\PagesMenu;
use think\facade\Cache;
use think\facade\Hook;


/**
 * old 此控制器不用api校验，不需要token登陆，就是单纯的标准的接口数据,用于取一些通用的信息
 * new 此控制器不校验token登陆但是最好也放到api体系了里面吧
 * Class Common
 * @package app\api\controller
 */
class Common extends Api
{
    /**
     * 加载方法
     */
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function area()
    {
        $areaModel = new Area();
        return $areaModel->getArea(input('param.id', 0));
    }


    /**
     * 取地区的下级列表
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
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
        if (input('?param.id')) {
            $id = input('param.id');
        } else {
            $id = "";
        }
        return captcha_src($id);
    }


    /**
     * 获取店铺设置，此接口用于统一返回店铺的公开设置，然后本地缓存即可
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function jshopConf()
    {

        if (!Cache::has("jshop_jshopconf")) {
            $config = getMultipleSetting('shop_logo,shop_name,shop_desc,store_switch,cate_style,cate_type,tocash_money_low,tocash_money_rate,tocash_money_rate,point_switch,statistics_code,recommend_keys,invoice_switch,goods_stocks_warn,shop_default_image,shop_mobile,show_inviter,share_title,share_desc,share_image,about_article_id,ent_id,user_agreement_id,privacy_policy_id,goods_show_word1,goods_show_word2,shop_beian');

            $conf['shop_logo']          = _sImage($config['shop_logo']); //店铺logo
            $conf['shop_name']          = $config['shop_name'];  //店铺名称
            $conf['shop_desc']          = $config['shop_desc'];  //店铺描述
            $conf['image_max']          = 5; //前端上传图片最多几张
            $conf['store_switch']       = $config['store_switch'];    //开启门店自提状态
            $conf['cate_style']         = $config['cate_style'];    //分类样式
            $conf['cate_type']          = $config['cate_type'];  //H5分类类型
            $conf['tocash_money_low']   = $config['tocash_money_low'];    //最低提现
            $conf['tocash_money_rate']  = $config['tocash_money_rate'];  //服务费
            $conf['point_switch']       = $config['point_switch'];    //是否开启积分功能
            $conf['statistics']         = $config['statistics_code'];   //获取统计代码
            $recommend_keys             = $config['recommend_keys'];
            $conf['recommend_keys']     = explode(' ', $recommend_keys);    //搜索推荐关键字
            $conf['invoice_switch']     = $config['invoice_switch'];    //发票功能开关
            $conf['goods_stocks_warn']  = $config['goods_stocks_warn'];  //库存报警数量
            $conf['shop_default_image'] = _sImage($config['shop_default_image']);   //获取默认图片
            $conf['shop_mobile']        = $config['shop_mobile'];  //店铺联系电话
            $conf['open_distribution']  = get_addons_status('DistributionCenter');   //是否开启分销
            if ($conf['open_distribution']) {
                $distributionSetting            = getAddonsConfigVal('DistributionCenter', 'setting');
                $conf['distribution_notes']     = isset($distributionSetting['notes']) ? $distributionSetting['notes'] : '';    //用户须知
                $conf['distribution_agreement'] = isset($distributionSetting['agreement']) ? $distributionSetting['agreement'] : '';    //分销协议
                $conf['distribution_store']     = isset($distributionSetting['distribution_store']) ? $distributionSetting['distribution_store'] : '';    //是否开启店铺
            }
            $conf['show_inviter']      = $config['show_inviter'];    //是否显示邀请人信息
            $conf['share_title']       = $config['share_title'];  //分享标题
            $conf['share_desc']        = $config['share_desc'];    //分享描述
            $conf['share_image']       = _sImage($config['share_image']); //分享图片
            $conf['about_article_id']  = $config['about_article_id'];    //关于我们文章
            $conf['ent_id']            = $config['ent_id'];    //客服ID
            $conf['user_agreement_id'] = $config['user_agreement_id']; //用户协议
            $conf['privacy_policy_id'] = $config['privacy_policy_id']; //隐私政策
            $conf['shop_beian']        = $config['shop_beian']; //备案
            $conf['language']          = getSetting('language');        //语言包，预留的口

            //手机端商品详情页文字说明，如果为空就不显示
            $goods_show_word = [];
            if ($config['goods_show_word1'] != '') {
                $goods_show_word[] = $config['goods_show_word1'];
            }
            if ($config['goods_show_word2'] != '') {
                $goods_show_word[] = $config['goods_show_word2'];
            }
            $conf['goods_show_word'] = $goods_show_word;
            
            $pagesMenuModel = new PagesMenu();
            $front_menu = $pagesMenuModel->getMenu();
            $conf['front_menu'] = $front_menu;

            Cache::set("jshop_jshopconf", $conf, 3600 * 12);
        } else {
            $conf = Cache::get("jshop_jshopconf");
        }
        return $conf;
    }

    //插件配置列表，插件是否开启，也通过此接口判断
    public function addons(){
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        $obj = new \stdClass;
        Hook('apiAddonsConf', $obj);
        $result['data'] = $obj->data;
        return $result;
    }
}