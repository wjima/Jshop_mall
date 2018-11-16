<?php
namespace addons\trustlogin;

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
/**
 * 信任登录插件
 */
class Trustlogin extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'trustlogin',
        'title' => 'H5信任登录插件',
        'description' => '信任登录插件，目前只有微信快捷登录，后续增加其他快捷登录',
        'status' => 0,
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
    //信任登录
    public function trustlogin($params = []){
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);
        $trustData = [];
        foreach($setting as $key=>$value){
            if($value){
                $class = "\\addons\\trustlogin\\lib\\$key";
                $lib = new $class();
                $trustData[$key] = [
                    'url'=>$lib->getOauthUrl($params['url'].'?type='.$key,$params['uuid']),
                    'ico'=>$lib->icon,
                ];
            }
        }
        return $trustData;
    }
    //获取用户信息
    public function trustcallback($params = [])
    {
        $data = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];
        if (!isset($params['type']) && !$params['type']) {
            return $data;
        }
        $class = "\\addons\\trustlogin\\lib\\" . $params['type'];
        $lib = new $class();
        $res = $lib->getUserInfo($params);
        if (!$res) {
            return $data;
        }
        $data['status'] = true;
        $data['msg'] = '获取成功';
        $data['data'] = $res;
        return $data;
    }

    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        $this->assign('config_params', $params);
        return $this->fetch('config');
    }

}