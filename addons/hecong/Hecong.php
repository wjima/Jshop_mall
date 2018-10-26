<?php

namespace addons\hecong;    // 注意命名空间规范
use myxland\addons\Addons;

/**
 * 合从在线客服插件
 */
class Hecong extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name'          => 'hecong',    // 插件标识
        'title'         => '合从在线客服插件',    // 插件名称
        'description'   => '合从在线客服，申请地址https://aihecong.com/',    // 插件简介
        'status'        => 1,    // 状态
        'author'        => 'mark',
        'version'       => '0.1',
        'dialog_width'  => '380px',//配置弹窗宽
        'dialog_height' => '300px',//配置弹窗高
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

    //设置配置信息
    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        $this->assign('params', $params);
        return $this->fetch('config');
    }

    /***
     * 实现方法
     * @param $params
     * @return array
     */
    public function kefu($params)
    {
        return  getAddonsConfig('hecong');
    }

}