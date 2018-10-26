<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\behavior;

/**
 * 钩子初始化
 * User: mark
 * Date: 2018/9/6
 * Time: 下午12:06
 */

use think\facade\Cache;
use think\facade\Hook;
use app\common\model\Hooks;
use app\common\model\Addons;

class InitHooks
{
    /**
     * 初始化钩子
     * @param $params
     */
    public function run($params)
    {
        //if(isset($_GET['m']) && $_GET['m'] === 'Install') return; //屏蔽安装程序
        $data = Cache::get('hooks');
        $hooksModel  = new Hooks();
        $addonsModel = new Addons();
        if (!$data) {
            $hooks = $hooksModel->field('name,addons')->select();
            if (!$hooks->isEmpty()) {
                $hooks = $hooks->toArray();
                foreach ($hooks as $key => $value) {
                    if ($value) {
                        $where = [];
                        //取出可用插件，然后把可用插件加入钩子
                        $where[] = ['status', 'eq', $addonsModel::INSTALL_STATUS];
                        $names   = explode(',', $value['addons']);
                        $where[] = ['name', 'in', $names];
                        $data    = $addonsModel->where($where)->field('name')->select();
                        if (!$data->isEmpty()) {
                            $data        = $data->toArray();
                            $data        = array_column($data, 'name');
                            $addons      = array_intersect($names, $data);
                            $addons_list = array_filter(array_map('get_addon_class', $addons));
                            Hook::add($value['name'], $addons_list);
                        }
                    }
                }
                Cache::set('hooks', Hook::get());
            }
        } else {

            Hook::import($data, false);
        }
    }

}