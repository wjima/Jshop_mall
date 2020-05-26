<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

/**
 * 钩子模型
 * User: mark
 * Date: 2018/9/6
 * Time: 下午12:02
 */
class Hooks extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    const NORMAL_TYPE = 1;

    /**
     * 更新钩子
     * @param $addons_name
     * @return bool
     */
    public function updateHooks($addons_name)
    {
        $addons_class = get_addon_class($addons_name);//获取插件名
        if (!class_exists($addons_class)) {
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->field('name')->select();
        if (!$hooks->isEmpty()) {
            $hooks = $hooks->toArray();
            $hooks = array_column($hooks, 'name');
            $common = array_intersect($hooks, $methods);
            if ($common) {
                foreach ($common as $hook) {
                    $flag = $this->updateAddons($hook, array($addons_name));
                    if (false === $flag) {
                        $this->removeHooks($addons_name);
                        return false;
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新单个钩子处的插件
     */
    public function updateAddons($hook_name, $addons_name)
    {
        $o_addons = $this->where(['name' => $hook_name])->field('addons')->find()->toArray();

        $addons = '';
        if ($o_addons) {
            $addons = explode(',', $o_addons['addons']);
        }
        if ($addons) {
            $addons = array_merge($addons, $addons_name);
            $addons = array_unique($addons);
        } else {
            $addons = $addons_name;
        }
        $addons = array_filter($addons);
        $flag = $this->where(['name' => $hook_name])
            ->setField('addons', implode(',', $addons));

        if (false === $flag) {
            $this->where(['name' => $hook_name])->setField('addons', $o_addons['addons']);
            return false;
        } else {
            return true;
        }
    }

    /**
     * 移除钩子
     * @param $addons_name
     * @return bool
     */
    public function removeHooks($addons_name)
    {
        $addons_class = get_addon_class($addons_name);
        if (!class_exists($addons_class)) {
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->field('name')->select()->toArray();
        $hooks = array_column($hooks, 'name');
        $common = array_intersect($hooks, $methods);

        if ($common) {
            foreach ($common as $hook) {
                $flag = $this->removeAddons($hook, array($addons_name));
                if (false === $flag) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * 去除单个钩子里对应的插件数据
     */
    public function removeAddons($hook_name, $addons_name)
    {
        $o_addons = $this->where(['name' => $hook_name])->field('addons')->find()->toArray();
        $o_addons = explode(',', $o_addons['addons']);
        if ($o_addons) {
            $addons = array_diff($o_addons, $addons_name);
        } else {
            return true;
        }
        $flag = $this->where(['name' => $hook_name])
            ->setField('addons', implode(',', $addons));
        if (false === $flag) {
            $this->where("name='{$hook_name}'")
                ->setField('addons', implode(',', $addons));
            return false;
        } else {
            return true;
        }
    }

    /**
     * 添加钩子
     * User:mark
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $result = [
            'status' => true,
            'msg' => '保存成功',
            'data' => []
        ];
        if (!$this->allowField(true)->save($data)) {
            return  error_code(10004);
        }
        return $result;
    }

    /**
     * 修改钩子
     * User:mark
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $result = [
            'status' => true,
            'msg' => '保存成功',
            'data' => []
        ];
        if (!$this->allowField(true)->save($data, ['id' => $data['id']])) {
            return  error_code(10004);
        }
        return $result;
    }


    /**
     * 根据输入的查询条件，返回所需要的where
     * @param $post
     * @return mixed
     * @author sin
     */
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['name']) && $post['name'] != '') {
            $where[] = ['name|description', 'like', '%'.$post['name'].'%'];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = [];
        return $result;
    }
}