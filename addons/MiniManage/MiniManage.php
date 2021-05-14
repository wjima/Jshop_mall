<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace addons\MiniManage;

use addons\ErpSyn\model\AdminToken;
use addons\MiniManage\model\AdminMessage;
use addons\MiniManage\model\AdminMessageCenter;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use think\Db;
use addons\ErpSyn\lib\toLogin;
use think\facade\Hook;


/**
 * ERP
 * Class MiniManage
 * @package addons\Distribution
 */
class MiniManage extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'MiniManage',
        'title' => '小超超前端后台管理',
        'description' => 'app管理后台，可处理订单发货、售后以及查看经营情况',
        'status' => 0,
        'author' => 'lzc',
        'version' => '1.0',
        'dialog_width' => '600px',
        'dialog_height' => '500px',
    ];


    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();
        //token
        $sql = "CREATE TABLE `" . config('database.prefix') . "admin_token` (";
        $sql .= "`token` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,";
        $sql .= "`admin_id` int(10) UNSIGNED NOT NULL,";
        $sql .= "`platform` smallint(2) NOT NULL DEFAULT 1 COMMENT '平台类型，1就是默认，2就是微信小程序',";
        $sql .= "`ctime` bigint(12) UNSIGNED NOT NULL,";
        $sql .= "PRIMARY KEY (`token`)";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';";
        //站内信
        $sql .= "CREATE TABLE `" . config('database.prefix') . "admin_message` (";
        $sql .= "`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,";
        $sql .= "`user_id` int(10) NOT NULL COMMENT '用户id',";
        $sql .= "`code` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '消息编码',";
        $sql .= "`params` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '参数',";
        $sql .= "`content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',";
        $sql .= "`ctime` bigint(12) UNSIGNED NOT NULL COMMENT '创建时间',";
        $sql .= "`utime` bigint(12) UNSIGNED NOT NULL COMMENT '查看时间',";
        $sql .= "PRIMARY KEY (`id`) USING BTREE,";
        $sql .= "INDEX `user_id`(`user_id`) USING BTREE";
        $sql .= ") ENGINE = InnoDB AUTO_INCREMENT = 366 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '站内信' ROW_FORMAT = Compact;";
        //信息配置
        $sql .= "CREATE TABLE `" . config('database.prefix') . "admin_message_center` (";
        $sql .= "`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
        $sql .= "`code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '编码',";
        $sql .= "`message` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '1 启用  2禁用 站内消息',";
        $sql .= "`wx_tpl_message` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '1 启用  2禁用 微信模板消息',";
        $sql .= "`overall` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '1 启用  2禁用 总开关',";
        $sql .= "PRIMARY KEY (`id`) USING BTREE";
        $sql .= ") ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '消息中心表' ROW_FORMAT = Compact;";

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
        $sql = 'DROP TABLE IF EXISTS ' . config('database.prefix') . 'admin_token;';
        $sql .= 'DROP TABLE IF EXISTS ' . config('database.prefix') . 'admin_message;';
        $sql .= 'DROP TABLE IF EXISTS ' . config('database.prefix') . 'admin_message_center;';
        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]) {
                $db::execute($list[$i]);
            }
        }
        return true;
    }


    /**
     * 配置
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }


    /**
     * 实现的menu钩子方法
     * @param $params
     * @return bool
     */
    public function menu($params)
    {
        $addonModel = new addonsModel;
        $setting    = $addonModel->getSetting($this->info['name']);
        if (isset($setting['menu'])) {
            return $setting['menu'];
        }
        return true;
    }


    /**
     * 获取配置
     * @return mixed
     */
    public function getConfigParams()
    {
        $addonsModel = new addonsModel();
        return $addonsModel->getSetting($this->info['name']);
    }

    public function adminmessage($data){
        $messageCenter = new AdminMessage();
        $user_id = $data["user_id"];
        $code = $data["code"];
        $params = $data["params"];
        return $messageCenter->sendMessage($user_id, $code, $params);
    }

}