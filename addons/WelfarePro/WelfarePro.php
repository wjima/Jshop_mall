<?php
namespace addons\WelfarePro;	// 注意命名空间规范

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;

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
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
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
        $obj->data['welfarepro'] = ['hb'];
        return true;
    }

}