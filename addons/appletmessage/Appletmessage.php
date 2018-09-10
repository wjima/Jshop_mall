<?php

namespace addons\appletmessage;    // 注意命名空间规范

use app\common\model\WeixinAuthor;
use myxland\addons\Addons;
use app\common\model\SellerAddons;
use app\common\model\Addons as addonsModel;

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
        if (!$params['params']['seller_id'] || !$params['params']['user_id']) {
            return false;
        }
        $addonModel        = new addonsModel();
        $addon             = $addonModel->getAddonByName($this->info['name']);
        $sellerAddonsModel = new SellerAddons();
        $setting           = $sellerAddonsModel->getSetting($addon['id'], $params['params']['seller_id']);
        if(!$setting){
            return false;
        }
        $template          = $setting[$params['params']['code']];
        //发送消息，取出会员open_id，然后发送
        $wxUserinfo = getUserWxInfo($params['params']['seller_id'], $params['params']['user_id']);
        if (!$wxUserinfo) {
            return false;
        }
        $authorModel = new WeixinAuthor();
        $appinfo     = $authorModel->getAuthorInfo($params['params']['seller_id'], 'b2c');
        if (!$appinfo) {
            return false;
        }
        $template_id = $template['template_id'];
        unset($template['template_id']);
        $message['data']        = $this->replaceWord($params['params']['params'], $template);
        $message['touser']      = $wxUserinfo['openid'];
        $message['template_id'] = $template_id;
        $message['url']         = '';
        $message['form_id']     = md5(time());//formid
        $wechat = &load_wechat('Message', $appinfo['appid']);
        $wechat->sendTemplateMessage($message);
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