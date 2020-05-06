<?php
// +----------------------------------------------------------------------
// | Author: lisgroup  https://github.com/lisgroup  407505297@qq.com
// +----------------------------------------------------------------------
namespace addons\Juhesms;
// 注意命名空间规范

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use org\Curl;
use think\facade\Log;

/**
 * 聚合短信插件
 */
class Juhesms extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'Juhesms',    // 插件标识
        'title' => '聚合短信平台',    // 插件名称
        'description' => '聚合短信平台的插件，请勿和其它短信通道一起使用',    // 插件简介
        'status' => 0,    // 状态
        'author' => 'lisgroup',
        'version' => '1.0',
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
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function sendsms($params)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '发送失败'
        ];
        $addonModel = new addonsModel();
        $setting = $addonModel->getSetting($this->info['name']);
        if ('seller_order_notice' == $params['params']['code']) {
            $params['params']['mobile'] = getSetting('shop_mobile');
            if (!$params['params']['mobile']) {
                $result['msg'] = '商户手机号不存在';
                return $result;
            }
        }
        // 发送模板内容，未完成订单通知类短信模板
        // $tpl_value = urlencode('#code#='.$params['params']['params']['code']);
        $tpl_value = urlencode($this->getTplValue($params['params']));

        $tpl_id = $setting[$params['params']['code']]['data']['title']['value'];
        $curl = new Curl();
        $str = "http://v.juhe.cn/sms/send?key=".$setting['key']."&tpl_id=".$tpl_id."&tpl_value=".$tpl_value."&mobile=".$params['params']['mobile'];
        //$re      = file_get_contents($str);
        $re = $curl->get($str);
        $data = json_decode($re, true);

        Log::debug('setting', $setting);
        Log::debug('params', $params);
        Log::debug('send sms', $data);
        if (!isset($data['error_code']) || $data['error_code'] != '0') {
            $result['msg'] = $data['reason'];
            return $result;
        }
        $result['msg'] = '发送成功';
        $result['status'] = true;
        return $result;
    }

    /**
     * 短信接口配置
     * @param array $params
     * @return mixed
     */
    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        $this->assign('config_params', $params);
        return $this->fetch('config');
    }

    /**
     * 模板转换
     * @param $params
     * @return mixed
     */
    private function getTplValue($params)
    {
        $config = $this->getConfig();
        $variable = $config['template'][$params['code']]['data']['template']['variable'];
        if ($variable) {
            $tpl_value = '';
            foreach ($variable as $key => $val) {
                $variable[$key] = $params['params'][$val];
                $tpl_value .= '#'.$key.'#='.$params['params'][$val];
            }
            return $tpl_value;
        }
        return '';
    }
}