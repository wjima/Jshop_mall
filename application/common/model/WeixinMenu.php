<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;
class WeixinMenu extends Common
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
        $info           = $this->where(['menu_id' => $data['menu_id'],'pid'=>$data['pid']])->find();
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
        return $result;
    }

    /**
     * 获取微信菜单
     * @param int $pid
     * @return array
     */
    public function getMenu($pid=0){
        $menu = [];
        $list = $this->where(['pid'=>$pid])->order('menu_id asc')->select();
        if(!$list->isEmpty()){
            foreach($list->toArray() as $key=>$value){
                $value['child'] = $this->where(['pid'=>$value['menu_id']])->order('menu_id desc')->select();
                if(!$value['child']->isEmpty()){
                    $value['child'] = $value['child']->toArray();
                }
                $menu[$key] = $value;
            }
        }
        return $menu;
    }

    /**
     * 组装微信菜单
     * @return array
     */
    public function weixinMenu()
    {
        $data       = $this->getMenu();
        $weixinMenu = [];
        if ($data) {
            foreach ($data as $key => $val) {
                $weixinMenu[$key]['type'] = $val['type'];
                $weixinMenu[$key]['name'] = $val['name'];
                $params                   = json_decode($val['params'], true);
                if ($val['type'] == 'click') {
                    $weixinMenu[$key]['key'] = $params['keyword'];
                } elseif ($val['type'] == 'view') {
                    $weixinMenu[$key]['url'] = $params['url'];
                } elseif ($val['type'] == 'miniprogram') {
                    $weixinMenu[$key]['url']      = $params['program_url'];
                    $weixinMenu[$key]['appid']    = $params['appid'];
                    $weixinMenu[$key]['pagepath'] = $params['page'];
                }

                if ($val['child']) {
                    $sub_button = [];
                    foreach ($val['child'] as $ck => $cv) {
                        $sub_button[$ck]['type'] = $cv['type'];
                        $sub_button[$ck]['name'] = $cv['name'];
                        $cvparams                = json_decode($cv['params'], true);
                        if ($cv['type'] == 'click') {
                            $sub_button[$ck]['key'] = $cvparams['keyword'];
                        } elseif ($cv['type'] == 'view') {
                            $sub_button[$ck]['url'] = $cvparams['url'];
                        } elseif ($cv['type'] == 'miniprogram') {
                            $sub_button[$ck]['url']      = $cvparams['program_url'];
                            $sub_button[$ck]['appid']    = $cvparams['appid'];
                            $sub_button[$ck]['pagepath'] = $cvparams['page'];
                        }
                    }
                    if ($sub_button) {
                        $weixinMenu[$key]['sub_button'] = $sub_button;
                    }
                }
            }
        }
        return ['button' => $weixinMenu];
    }

}
