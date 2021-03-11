<?php
namespace addons\WeRun;	// 注意命名空间规范

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;

/**
 * Demo演示插件
 */
class WeRun extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'WeRun',	// 插件标识
        'title' => '每日运动',	// 插件名称
        'description' => '每日运动，同步微信运动的数据',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'wgg',
        'version' => '1.0',
        'dialog_width'  => '750px',//配置弹窗宽
        'dialog_height' => '520px',//配置弹窗高
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {

        // CREATE TABLE `jshop_werun_log` (
        //     `id` int(10) NOT NULL AUTO_INCREMENT,
        //     `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
        //     `steps` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '兑换总步数',
        //     `points` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '兑换总积分',
        //     `date_str` char(25) NOT NULL DEFAULT '' COMMENT '日期格式 2021-03-05 到 2021-03-08',
        //     `ctime` bigint(12) unsigned NOT NULL COMMENT '兑换时间',
        //     PRIMARY KEY (`id`) USING BTREE
        //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='微信运动积分兑换记录';
        // CREATE TABLE `jshop_werun` (
        //     `id` int(10) NOT NULL AUTO_INCREMENT,
        //     `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
        //     `step` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当日步数',
        //     `date` bigint(12) unsigned NOT NULL COMMENT '日期',
        //     `date_str` char(10) NOT NULL COMMENT '日期格式 2021-03-05',
        //     `status` tinyint(1) NOT NULL COMMENT '兑换状态：1 待兑换 2 已兑换 3 已无效',
        //     `point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '兑换积分',
        //     `ctime` bigint(12) unsigned NOT NULL COMMENT '首次更新时间',
        //     `utime` bigint(12) unsigned NOT NULL COMMENT '最新更新时间',
        //     PRIMARY KEY (`id`) USING BTREE
        //   ) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='微信运动记录';
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
        $data = [
            'key'=>'3',
            'val' =>'yxq'
        ];
        $obj->data[$this->info['name']] = $data;
        return true;
    }

    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }

}