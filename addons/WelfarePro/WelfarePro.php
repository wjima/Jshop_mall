<?php
namespace addons\WelfarePro;	// 注意命名空间规范

use addons\WelfarePro\model\WelfareproCoupon;
use addons\WelfarePro\model\WelfareproCouponLog;
use addons\WelfarePro\model\WelfareproHb;
use app\common\model\User;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use think\Db;

/**
 * 领红包和领优惠券功能
 */
class WelfarePro extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'WelfarePro',	// 插件标识
        'title' => '扫码领红包和领优惠券',	// 插件名称
        'description' => '扫描推荐人的二维码可以领取微信红包和优惠券',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'sin',
        'version' => '1.0'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        //先判断表是否存在，如果存在，就不安装了
        $db = new Db();
        $sql = "show tables like '" . config('database.prefix') . "welfarepro_hb';";
        if($db::execute($sql)){
            return true;
        }
        //表不存在，开始安装
        $sql = file_get_contents(ADDON_PATH . 'WelfarePro/sql/install.sql');
        $sql = str_replace("`jshop_",'`'.config('database.prefix'),$sql);
        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if (trim($list[$i])) {
                $db::execute(trim($list[$i]));
            }
        }
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        //卸载就不去删除表了，防止数据丢失
        return true;
    }

    /**
     * 实现的menu钩子方法
     * @return mixed
     */
    public function menu($params)
    {
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);
        if(isset($setting['menu'])){
            return $setting['menu'];
        }
        return true;
    }
    public function apiAddonsConf($obj){
        $obj->data[$this->info['name']] = true;
        return true;
    }

    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }
    public function deshare($obj){
        $obj->data['welfarepro'] = [];      //'hb','coupon'

        if(isset($obj->data['userShareCode']) && $obj->data['userShareCode'] != ""){
            $userModel = new User();
            $tj_user_id = $userModel->getUserIdByShareCode($obj->data['userShareCode']);

            //判断当前推荐人是否有红包活动
            $hb = new WelfareproHb();
            $info = $hb->userHb($tj_user_id);
            if($info){
                $obj->data['welfarepro'][] = 'hb';
            }

            //判断优惠券
            $coupon = new WelfareproCoupon();
            $info = $coupon->userCoupon($tj_user_id);
            if($info){
                //判断该推荐人推荐数量是否已发完
                $couponLogModel = new WelfareproCouponLog();
                $is_over =  $couponLogModel->couponOver($info['id'],$tj_user_id,$info['sendnum']);
                if($is_over){
                    $obj->data['welfarepro'][] = 'coupon';
                }
            }

        }




        return true;
    }

}