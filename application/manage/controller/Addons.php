<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use Request;
use app\common\model\Addons as addonsModel;
use app\common\model\Hooks;
use think\facade\Cache;

/**
 * 插件中心
 * Class Addons
 * @package app\Manage\controller
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:07
 */
class Addons extends Manage
{


    /**
     * 插件列表
     * @return mixed
     */
    public function index()
    {
        if (Request::isAjax()) {
            $addonsModel = new addonsModel();
            $filter      = input('request.');
            $addons      = $addonsModel->getList($filter);
            return $addons;
        }
        return $this->fetch('index');
    }

    /**
     * 安装插件
     */
    public function install()
    {
        $result     = [
            'status' => false,
            'msg'    => '安装失败',
            'data'   => '',
        ];
        $addon_name = input('name/s', '');
        if (!$addon_name) {
            return $result;
        }
        $class = get_addon_class($addon_name);
        if (!class_exists($class)) {
            $result['msg'] = '插件不存在';
            return $result;
        }

        $addons = new $class;
        $info   = $addons->info;
        if (!$info) {
            $result['msg'] = '插件信息缺失';
            return $result;
        }
        $install_flag = $addons->install();
        if (!$install_flag) {
            $result['msg'] = '执行插件预安装失败';
            return $result;
        }

        $addonsModel = new addonsModel();
        $res         = $addonsModel->add($info);
        $res         = true;
        if (!$res) {
            $result['msg'] = '插件安装失败';
            return $result;
        } else {
            $hookModel = new Hooks();
            $config    = [
                'config' => json_encode($addons->getConfig())
            ];
            $addonsModel->save($config, ['name' => $addon_name]);//更新配置

            $hooks_update = $hookModel->updateHooks($addon_name);//更新钩子
            if ($hooks_update) {
                Cache::set('hooks', null);
                $result['msg']    = '安装成功';
                $result['status'] = true;
                return $result;
            } else {
                $addonsModel->where(['name' => $addon_name])->delete();
                $result['msg']    = '安装失败，请重试';
                $result['status'] = true;
                return $result;
            }
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall()
    {
        $result      = [
            'status' => false,
            'msg'    => '卸载失败',
            'data'   => '',
        ];
        $addonsModel = new addonsModel();
        $addon_name  = input('name', '');
        $addons      = $addonsModel->where(['name' => $addon_name])->find();
        if (!$addons) {
            $result['msg'] = '插件不存在';
            return $result;
        }
        $addons = $addons->toArray();
        $class  = get_addon_class($addons['name']);

        if (!$addons || !class_exists($class)) {
            $result['msg'] = '插件不存在';
            return $result;
        }
        $addon_class    = new $class;
        $uninstall_flag = $addon_class->uninstall();
        if (!$uninstall_flag) {
            $result['msg'] = '插件预卸载失败';
            return $result;
        }
        $hookModel    = new Hooks();
        $hooks_update = $hookModel->removeHooks($addons['name']);
        if ($hooks_update === false) {
            $result['msg'] = '插件预卸载失败';
            return $result;
        }
        Cache::set('hooks', null);
        $delete = $addonsModel->where(['name' => $addons['name']])->delete();
        if ($delete === false) {
            $result['msg'] = '卸载插件失败';
            return $result;
        } else {
            $result['msg']    = '卸载成功';
            $result['status'] = true;
            return $result;
        }
    }


    public function setting()
    {
        $result = [
            'status' => false,
            'msg'    => '获取配置信息失败',
            'data'   => '',
            'dialog' => [],
        ];
        $name   = input('name/s', '');
        if (!$name) {
            return $result;
        }
        $this->view->engine->layout(false);

        $addonsModel = new addonsModel();

        $addon      = $addonsModel->getAddonInfo($name);
        $setting    = $addonsModel->getSetting($name);
        $class_name = '\\addons\\' . $addon['name'] . '\\' . ucfirst($addon['name']);
        if (!class_exists($class_name)) {
            $result['status'] = false;
            $result['msg']    = '插件不存在';
            return $result;
        }
        $addonObject      = new $class_name();
        $result['status'] = true;
        $result['data']   = $addonObject->config($setting);
        $result['dialog'] = $addonObject->getDialog();
        return $result;
    }

    /**
     * 保存配置信息
     * @return array
     */
    public function doSetting()
    {
        $result      = [
            'status' => false,
            'msg'    => '配置信息保存失败',
            'data'   => '',
        ];
        $data        = input('post.');
        $addonsModel = new addonsModel();
        $setting    = $addonsModel->getSetting($data['name']);
        if(isset($setting['menu']) && $setting['menu']){
            $data['setting']['menu'] = $setting['menu'];
        }
        if ($addonsModel->doSetting($data)) {
            $result['status'] = true;
            $result['msg']    = '配置信息保存成功';
        }
        return $result;
    }

    /**
     * 停用插件
     * @return array
     */
    public function changeStatus()
    {
        $result      = [
            'status' => false,
            'msg'    => '操作失败',
            'data'   => '',
        ];
        $name        = input('post.name/s', '');
        $addonsModel = new addonsModel();
        if ($addonsModel->changeStatus($name)) {
            $result['status'] = true;
            $result['msg']    = '操作成功';
        }
        return $result;
    }

    /**
     * 刷新插件
     * @return array
     */
    public function refresh()
    {
        $result     = [
            'status' => false,
            'msg'    => '刷新失败',
            'data'   => '',
        ];
        $addon_name = input('name/s', '');
        if (!$addon_name) {
            return $result;
        }
        $class = get_addon_class($addon_name);
        if (!class_exists($class)) {
            $result['msg'] = '插件不存在';
            return $result;
        }
        $addons = new $class;
        $info   = $addons->info;
        if (!$info) {
            $result['msg'] = '插件信息缺失';
            return $result;
        }
        $addonsModel = new addonsModel();
        $data        = $addonsModel->where(['name' => $addon_name])->find();
        if ($data) {
            $hookModel = new Hooks();
            $config    = $addons->getConfig();
            $oldConfig = json_decode($data['config'], true);
            $config    = array_merge($config, $oldConfig);
            $iData     = [
                'title'       => $info['title'],
                'description' => $info['description'],
                'author'      => $info['author'],
                'version'     => $info['version'],
                'config'      => json_encode($config),
            ];
            //合并插件配置
            $addonsModel->save($iData, ['name' => $addon_name]);//更新配置
            $hooks_update = $hookModel->updateHooks($addon_name);//更新钩子
            if ($hooks_update) {
                Cache::set('hooks', null);
                $result['msg']    = '刷新成功';
                $result['status'] = true;
                return $result;
            } else {
                $result['msg']    = '刷新失败，请重试';
                $result['status'] = true;
                return $result;
            }
        } else {
            $result['msg'] = '请先安装插件';
        }
        return $result;
    }

}
