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
            'msg'    => error_code(10702,true),
            'data'   => '',
        ];
        $addon_name = input('name/s', '');
        if (!$addon_name) {
            return $result;
        }
        $class = get_addon_class($addon_name);
        if (!class_exists($class)) {
            $result['msg'] = error_code(10700,true);
            return $result;
        }

        $addons = new $class;
        $info   = $addons->info;
        if (!$info) {
            $result['msg'] = error_code(10705,true);
            return $result;
        }
        $install_flag = $addons->install();
        if (!$install_flag) {
            $result['msg'] = error_code(10706,true);
            return $result;
        }

        $addonsModel = new addonsModel();
        $res         = $addonsModel->add($info);
        $res         = true;
        if (!$res) {
            $result['msg'] = error_code(10707,true);
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
                $result['msg']    = error_code(10702,true);
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
            'msg'    => error_code(10704,true),
            'data'   => '',
        ];
        $addonsModel = new addonsModel();
        $addon_name  = input('name', '');
        $addons      = $addonsModel->where(['name' => $addon_name])->find();
        if (!$addons) {
            $result['msg'] = error_code(10700,true);
            return $result;
        }
        $addons = $addons->toArray();
        $class  = get_addon_class($addons['name']);

        if (!$addons || !class_exists($class)) {
            $result['msg'] = error_code(10700,true);
            return $result;
        }
        $addon_class    = new $class;
        $uninstall_flag = $addon_class->uninstall();
        if (!$uninstall_flag) {
            $result['msg'] = error_code(10706,true);
            return $result;
        }
        $hookModel    = new Hooks();
        $hooks_update = $hookModel->removeHooks($addons['name']);
        if ($hooks_update === false) {
            $result['msg'] = error_code(10716,true);
            return $result;
        }
        Cache::set('hooks', null);
        $delete = $addonsModel->where(['name' => $addons['name']])->delete();
        if ($delete === false) {
            $result['msg'] = error_code(10704,true);
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
            'msg'    => error_code(10708,true),
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
            $result['msg']    = error_code(10700,true);
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
        $result = [
            'status' => false,
            'msg'    => error_code(10709,true),
            'data'   => '',
        ];
        $data   = input('post.');//配置项

        $addonsModel = new addonsModel();
        $setting     = $addonsModel->getSetting($data['name']);
        $addonName   = $data['name'];
        if ($setting) {
            $uData = array_merge($setting, $data['setting']);//todo 所有插件列表上面的配置，都要以setting为name
        } else {
            $uData = $data['setting'];
        }

        if ($addonsModel->doSetting($uData, $addonName)) {
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
            'msg'    => error_code(10715,true),
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
            'msg'    => error_code(10713,true),
            'data'   => '',
        ];
        $addon_name = input('name/s', '');
        if (!$addon_name) {
            return $result;
        }
        $class = get_addon_class($addon_name);
        if (!class_exists($class)) {
            $result['msg'] = error_code(10700,true);
            return $result;
        }
        $addons = new $class;
        $info   = $addons->info;
        if (!$info) {
            $result['msg'] = error_code(10705,true);
            return $result;
        }
        $addonsModel = new addonsModel();
        $data        = $addonsModel->where(['name' => $addon_name])->find();
        if ($data) {
            $hookModel = new Hooks();
            $config    = $addons->getConfig();
            $oldConfig = json_decode($data['config'], true);
            $menu      = isset($config['menu']) ? $config['menu'] : [];

            $config         = array_merge($config, $oldConfig);
            $config['menu'] = $menu;//菜单使用最新的

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
                $result['msg']    = error_code(10713);
                $result['status'] = false;
                return $result;
            }
        } else {
            $result['msg'] = error_code(10711,true);
        }
        return $result;
    }

}
