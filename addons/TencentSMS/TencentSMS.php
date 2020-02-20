<?php

namespace addons\TencentSMS;

require __DIR__ . "/lib/index.php";

use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use Qcloud\Sms\SmsSingleSender;


/**
 * 腾讯云短信插件
 * Class TencentSMS
 * @package addons\TencentSMS
 * @author keinx
 * @github https://github.com/keinx/tencent-sms-for-jshop
 */
class TencentSMS extends Addons
{
    /**
     * 插件基本信息
     * @var array
     */
    public $info = [
        'name' => 'TencentSMS',
        'title' => '腾讯云短信插件',
        'description' => '腾讯云短信通道插件，使用腾讯云短信通道发送短信，请勿多个短信通道一起使用',
        'status' => 0,
        'author' => 'keinx',
        'version' => '1.2',
        'dialog_width' => '500px',
        'dialog_height' => '570px',
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
     * 实现发送短信
     * @param $data
     * @return array
     */
    public function sendsms($data)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '发送失败'
        ];

        $addonModel = new addonsModel();
        $setting = $addonModel->getSetting($this->info['name']);

        if ($data['params']['code'] == 'seller_order_notice') {
            $data['params']['mobile'] = trim(getSetting('shop_mobile'));
            if (!$data['params']['mobile']) {
                $result['msg'] = '商户手机号不存在';
                return $result;
            }
        }

        if (!isset($setting[$data['params']['code']]['data']['title']['value']) || $setting[$data['params']['code']]['data']['title']['value'] == "") {
            $result['msg'] = '请先配置后台短信接口';
            return $result;
        }

        $appID = $setting['AppID'];
        $appKey = $setting['AppKey'];
        $signName = $setting['SignName'];
        $mobile = $data['params']['mobile'];
        $params = $this->getSmsParams($data['params']['code'], $data['params']['params']);
        try {
            $ssender = new SmsSingleSender(trim($appID), trim($appKey));
            $res = $ssender->sendWithParam("86", trim($mobile), trim($setting[$data['params']['code']]['data']['title']['value']), $params, trim($signName), "", "");
            $rsp = json_decode($res, true);
            if ($rsp['result'] != 0) {
                if ($rsp['errmsg'] == 'package format error, template params format error') {
                    $result['msg'] = '短信模板参数配置错误';
                } else {
                    $result['msg'] = $rsp['errmsg'];
                }
                return $result;
            }
        } catch (\Exception $e) {
            $result['msg'] = json_encode($e);
            return $result;
        }

        $result['status'] = true;
        $result['msg'] = '发送成功';
        return $result;
    }


    /**
     * 短信接口配置
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        $this->assign('config_params', $params);
        return $this->fetch('config');
    }


    /**
     * 获取短信参数
     * @param $code
     * @param $params
     * @return mixed
     */
    public function getSmsParams($code, $params)
    {
        $config = $this->configMerge();
        $variable = $config['template'][$code]['data']['template']['variable'];
        $new = [];
        foreach ($variable as $v) {
            if (isset($v['order']) && $v['order'] != '' && $v['order'] > 0) {
                $order = $v['order'] - 1;
                $new[(int)$order] = trim($params[$v['name']]);
            }
        }

        $newData = [];
        for ($i = 0; $i < count($new); $i++) {
            $newData[] = $new[$i];
        }

        return $newData;
    }


    /**
     * 配置合并
     * @return array|mixed|null
     */
    protected function configMerge()
    {
        $config = $this->getConfig();
        $addonModel = new addonsModel();
        $config_params = $addonModel->getSetting($this->info['name']);

        foreach ($config['template'] as $k => &$v) {
            foreach ($v['data']['template']['variable'] as $kk => &$vv) {
                $vv['order'] = (isset($config_params[$k]['data']['template']['variable'][$kk]['order']) && $config_params[$k]['data']['template']['variable'][$kk]['order'] != '') ? $config_params[$k]['data']['template']['variable'][$kk]['order'] : $vv['order'];
            }
        }

        return $config;
    }
}