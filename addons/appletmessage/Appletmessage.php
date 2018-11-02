<?php

namespace addons\appletmessage;    // 注意命名空间规范

use app\common\model\TemplateMessage;
use app\common\model\WeixinAuthor;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use org\Wx;

/**
 * 微信小程序模板消息插件
 */
class Appletmessage extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name'          => 'appletmessage',    // 插件标识
        'title'         => '微信小程序模板消息',    // 插件名称
        'description'   => '微信小程序模板消息',    // 插件简介
        'status'        => 1,    // 状态
        'author'        => 'mark',
        'version'       => '0.1',
        'dialog_width'  => '600px',//配置弹窗宽
        'dialog_height' => '520px',//配置弹窗高
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

    //设置配置信息
    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('template', $config['template']);
        $this->assign('template_params', $params);
        return $this->fetch('config');
    }

    /**
     * 发送小程序消息
     * @param $params
     * @return bool
     *
     *
    $params['seller_name'] = '张三店铺';
    $params['order_id'] = '15223133009509';
    $params['status'] = '待支付';
    $params['order_amount'] = '50';
    $params['ctime'] = date('Y-m-d H:i:s',time());
    sendMessage('4','17','create_order',$params);
     */
    public function sendwxmessage($params)
    {
        if (!$params['params']['user_id']) {
            return false;
        }
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);

        if (!$setting) {
            return false;
        }
        $template = $setting[$params['params']['code']];

        //发送消息，取出会员open_id，然后发送
        $wxUserinfo = getUserWxInfo($params['params']['user_id']);
        if (!$wxUserinfo) {
            return false;
        }
        $templateMessageModel = new TemplateMessage();

        if ($params['params']['code'] == 'create_order') {
            $id                                   = $params['params']['params']['order_id'];
            $closeOrder                           = getSetting('order_cancel_time') * 24;
            $params['params']['params']['notice'] = '您的订单将在' . $closeOrder . '小时候取消，请及时付款哦';
            $formInfo                             = $templateMessageModel->where(['type' => $params['params']['code'], 'code' => $id, 'status' => '1'])->find();

        }
        if ($params['params']['code'] == 'delivery_notice') {
            $formInfo = $templateMessageModel->where(['type' => 'order_payed', 'code' => $id, 'status' => '1'])->find();
        }
        $appid       = getSetting('wx_appid');
        $secret      = getSetting('wx_app_secret');
        $template_id = $template['template_id'];
        unset($template['template_id']);
        $params['params']['params']['seller_name'] = getSetting('shop_name');
        $message['data']                           = $this->replaceWord($params['params']['params'], $template);
        $message['touser']                         = $wxUserinfo['openid'];
        $message['template_id']                    = $template_id;
        $message['page']                           = 'index/index';
        $message['form_id']                        = $formInfo['from_id'];//formid
        $wx                                        = new Wx();
        $res                                       = $wx->sendTemplateMessage($appid, $secret, $message);
        if ($res) {
            $templateMessageModel->sendSuccess($formInfo['id']);
        }
    }

    /**
     * 模板消息替换
     * @param $data
     * @param array $template
     * @return array
     */
    public function replaceWord($data, $template = [])
    {
        $msgData = [];
        foreach ($template as $key => $value) {
            $mkey           = str_replace("{{", "", $value);
            $mkey           = str_replace(".DATA}}", "", $mkey);
            $msgData[$mkey] = [
                'value' => $data[$key],
                'color' => '#173177',
            ];
        }
        return $msgData;

    }

    public function testhook($param){
        #echo '第二个';
    }
}