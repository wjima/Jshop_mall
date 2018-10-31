<?php
namespace addons\mms1086;	// 注意命名空间规范

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;

/**
 * mms1086短信通道
 */
class Mms1086 extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'mms1086',	// 插件标识
        'title' => 'mms1086短信插件',	// 插件名称
        'description' => 'mms1086发送短信插件，请勿和其它短信通道一起使用',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'mark',
        'version' => '0.1'
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
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function sendsms($params)
    {
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);

        $sms_password = config('?jshop.sms_password') ? config('jshop.sms_password') : $setting['sms_password'];     //为了演示效果，此密码从配置文件中取，如果正式使用，请删除此行，并在后台店铺设置里配置密码即可。
        $content      = $params['params']['content'] . '【' . $setting['sms_prefix'] . '】';
        //$content = iconv("utf-8","gb2312",$content);
        $content = urlencode($content);      //内容
        $str     = "http://sms.mms1086.com:8868/sms.aspx?action=send&userid=" . $setting['sms_user_id'] . "&account=" . $setting['sms_account'] . "&password=" . $sms_password . "&mobile=" . $params['params']['mobile'] . "&content=" . $content . "&sendTime=&extno=";
        $re      = file_get_contents($str);
        return true;
    }

    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        return $this->fetch('config');
    }

}