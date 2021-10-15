<?php
namespace addons\StockControl;	// 注意命名空间规范

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
class StockControl extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'StockControl',	// 插件标识
        'title' => '库存管理',	// 插件名称
        'description' => '库存管理、入库、出库、库存明细、库存盘点',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'wgg',
        'version' => '1.0'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();
        $sql = file_get_contents(ADDON_PATH . 'StockControl/SQL/install.sql');
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
        $db = new Db();
        $sql = file_get_contents(ADDON_PATH . 'StockControl/SQL/uninstall.sql');
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
     * 实现的menu钩子方法
     * @return mixed
     */
    public function menu($params)
    {
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);
        if (isset($setting['menu'])) {
            return $setting['menu'];
        }
        return true;
    }
    public function apiAddonsConf($obj)
    {
        $obj->data[$this->info['name']] = true;
        return true;
    }

    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }

    public function afterShip($delivery_id)
    {
        return true;
    }
}