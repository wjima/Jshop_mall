<?php
namespace addons\Demo;	// 注意命名空间规范

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;

/**
 * Demo演示插件
 */
class Demo extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'Demo',	// 插件标识
        'title' => 'Demo演示插件',	// 插件名称
        'description' => '仅供Demo演示插件功能使用，一些插件的用法会写到这里供大家参考',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'sin',
        'version' => '1.1'
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