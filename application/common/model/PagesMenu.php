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

class PagesMenu extends Common
{
    /**
     * 保存菜单
     * @param array $data
     * @return array
     */
    public function toSave($data = [])
    {
        $result = error_code(10004);
        $data['params'] = json_encode($data['params']);
        $info           = $this->where(['menu_id' => $data['menu_id'], 'pid' => $data['pid']])->find();
        if (!$info) {
            $res = $this->save($data);
        } else {
            $res = $this->save($data, ['id' => $info['id']]);
        }
        if ($res === false) {
            return $result;
        }
        $result['status'] = true;
        $result['msg']    = '保存成功';
        Cache::clear(); //TODO 如果开启其他缓存，记得这里要配置缓存配置信息
        return $result;
    }

    /**
     * 获取微信菜单
     * @param int $pid
     * @return array
     */
    public function getMenu($pid = 0)
    {
        $menu = [];
        $list = $this->where(['pid' => $pid])->order('menu_id asc')->select();
        if (!$list->isEmpty()) {
            foreach ($list->toArray() as $key => $value) {
                $value['child'] = $this->where(['pid' => $value['menu_id']])->order('menu_id desc')->select();
                if (!$value['child']->isEmpty()) {
                    $value['child'] = $value['child']->toArray();
                }
                $value['icon_url']       = convertBase64($value['icon']);//对图片进行base64编码转换，避免前端菜单闪烁
                $value['selecticon_url'] = convertBase64($value['selecticon']);
                $params                  = json_decode($value['params'], true);
                $value['url']            = $params['url'];
                $menu[$key]              = $value;
            }
        }
        return $menu;
    }
}
