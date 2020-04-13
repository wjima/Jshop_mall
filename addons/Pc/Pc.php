<?php
namespace addons\Pc;    // 注意命名空间规范

use myxland\addons\Addons;
use think\Db;
use app\common\model\Addons as addonsModel;

class Pc extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name'         => 'Pc',    // 插件标识
        'title'        => 'PC设置',    // 插件名称
        'description'  => 'PC端首页设置' , // 插件简介
        'status'       => 0,    // 状态
        'author'       => 'xbear',
        'version'      => '0.1',
        'dialog_width' => '600px',
    ];
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();

        //菜单表
        $sql ="CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "pc_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父菜单',
  `code` varchar(20) DEFAULT NULL COMMENT '菜单编码',
  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '类别1=url 2=文章分类id  3=单个文章 4=多个商品   5=商品详情  6=二级菜单',
  `name` varchar(40) NOT NULL COMMENT '菜单名',
  `val` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '菜单数据',
  `sort` tinyint(1) unsigned DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='菜单类型' AUTO_INCREMENT=1 ;";
        //基本设置表
        $sql.="CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "pc_settings` (
  `k` varchar(10) DEFAULT NULL,
  `val` varchar(40) NOT NULL DEFAULT '',
  `name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='基本设置';";
        //友链表
        $sql.="CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "pc_friendship_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `link` varchar(50) DEFAULT NULL,
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='友链' AUTO_INCREMENT=1 ;";
        //楼层广告表
        $sql.="CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "pc_floor_adv` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `floor_id` int(10) unsigned DEFAULT NULL COMMENT '楼层id',
  `type` tinyint(1) unsigned DEFAULT NULL COMMENT '广告类型 1=侧面 2=底部',
  `cat` tinyint(1) unsigned DEFAULT NULL COMMENT '1=Url  2=文章 3=商品',
  `image` varchar(32) DEFAULT NULL COMMENT '广告图',
  `val` varchar(40) DEFAULT NULL,
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '100',
  `utime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='楼层商品及广告' AUTO_INCREMENT=1 ;";
        //楼层表
        $sql.="CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "pc_floor` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL COMMENT '楼层名',
  `val` varchar(60) DEFAULT NULL,
  `keyword` varchar(40) DEFAULT NULL,
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '100' COMMENT '排序  越小越前',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='楼层分类' AUTO_INCREMENT=1 ;";

        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]) {
                $db::execute($list[$i]);
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
        $db  = new Db();
        $sql  = 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'pc_menu_params ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'pc_menu ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'pc_settings ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'pc_friendship_link ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'pc_floor ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'pc_floor_adv ;';
        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]) {
                $db::execute($list[$i]);
            }
        }
        return true;
    }

    /**
     * 实现的menu钩子方法
     * @return mixed
     */
    public function menu($params){
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);
        if(isset($setting['menu'])){
            return $setting['menu'];
        }
        return false;
    }
    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }
}
