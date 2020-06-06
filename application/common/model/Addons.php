<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\facade\Cache;
use think\facade\Log;

/**
 * 插件模型
 * Class Area
 * @package app\common\model
 * @author keinx
 */
class Addons extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const INSTALL_STATUS = 1;//已安装
    const STATUS_DISENABLE = 2; //禁用

    /**
     * 获取插件列表
     * @return array
     */
    public function getList()
    {
        $result = [
            'code'   => 0,
            'msg'    => error_code(10025,true),
            'status' => false,
        ];

        if (!defined('ADDON_PATH')) {
//            $result['msg'] = '插件路径缺失';
            return error_code(10718);
        }
        $dirs = array_map('basename', glob(ADDON_PATH . '*', GLOB_ONLYDIR));
        if ($dirs === FALSE || !file_exists(ADDON_PATH)) {
//            $result['msg'] = '插件目录不可读或者不存在';
            return error_code(10719);
        }
        $addons  = [];
        $where[] = ['name', 'in', $dirs];
        $list    = $this->where($where)->field('*')->select();
        if (!$list->isEmpty()) {
            $list = $list->toArray();
        }

        foreach ($dirs as $value) {
            foreach ($list as $addon) {
                if($addon['name'] == $value){
                    $addon['install']       = $addon['status'];
                    $addons[$addon['name']] = $addon;
                }
            }
            if (!isset($addons[$value])) {
                $class = get_addon_class($value);
                if (!class_exists($class)) { // 实例化插件失败忽略执行
                    Log::record('插件' . $value . '的入口文件不存在！');
                    continue;
                }
                $obj            = new $class;
                $addons[$value] = $obj->info;

                if ($addons[$value]) {
                    $addons[$value]['install'] = 0;
                    unset($addons[$value]['status']);
                }
            }
        }
        $result['msg']  = '获取成功';
        $result['code'] = 0;
        $result['data'] = $addons;
        $re['count']    = count($addons);
        return $result;
    }

    /**
     * 数据转换
     * @param array $data
     * @return array
     */
    public function listData($data = [])
    {
        return $data;
    }

    /**
     * 获取插件信息
     * @param $name
     * @return array
     */
    public function getAddonInfo($name)
    {
        $info = $this->where(['name' => $name])->cache('addon_'.$name)->find();
        if ($info) {
            return $info->toArray();
        } else {
            return [];
        }
    }


    /**
     * 获取配置信息
     * @param $name
     * @return mixed
     */
    public function getSetting($name)
    {
        $info = $this->where(['name' => $name])->cache('addon_'.$name)->find();
        if($info){
            return json_decode($info['config'], true);
        }else{
            return [];
        }

    }

    /**
     * 保存插件信息
     * @param $params 插件参数
     * @param string $name 插件名称
     * @return bool
     */
    public function doSetting($params, $name)
    {
        if (!$name) {
            return false;
        }
        $addon = $this->where(['name' => $name])->cache('addon_' . $name)->find();
        if (!$addon) {
            return false;
        }
        $uData = [
            'config' => json_encode($params),
        ];
        $res   = $this->where(['id' => $addon['id']])->cache('addon_' . $name)->update($uData);
        if ($res !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据名称获取插件信息
     */
    public function getAddonByName($name = '')
    {
        $addonInfo = Cache::get('addon_' . $name);

        if ($addonInfo) {
            return json_decode($addonInfo, true);
        } else {
            $info = $this->where(['name' => $name])->find();
            if ($info) {
                $tmp_info = $info->toArray();
                Cache::set('addon_' . $name, json_encode($tmp_info));
                return $tmp_info;
            } else {
                return [];
            }
        }
    }

    /**
     * 添加插件
     * @param $data
     * @return bool
     */
    public function add($data)
    {
        $iData = [
            'name'        => $data['name'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'status'      => self::INSTALL_STATUS,
            'author'      => $data['author'],
            'version'     => $data['version'],
        ];
        return $this->save($iData);
    }


    /**
     * 插件启用，停用
     * @param $name
     * @return bool
     */
    public function changeStatus($name)
    {
        if (!$name) {
            return false;
        }
        $addon = $this->field('id,status')->where(['name' => $name])->find();
        if (!$addon) {
            return false;
        }
        if ($addon['status'] == self::INSTALL_STATUS) {
            $uData['status'] = self::STATUS_DISENABLE;
        } else {
            $uData['status'] = self::INSTALL_STATUS;
        }
        $res = $this->save($uData, ['id' => $addon['id']]);
        if ($res !== false) {
            //刷新cache
            Cache::set('hooks', null);
            return true;
        } else {
            return false;
        }
    }

}