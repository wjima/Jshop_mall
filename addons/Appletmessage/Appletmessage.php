<?php

namespace addons\Appletmessage;    // 注意命名空间规范

use app\common\model\BillPayments;
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
        'name'          => 'Appletmessage',    // 插件标识
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
        //创建订单
        if ($params['params']['code'] == 'create_order') {
            if ($params['params']['params']['form_id']) {
                $templateMessageModel = new TemplateMessage();
                $message              = [
                    'type'    => $templateMessageModel::TYPE_ORDER,
                    'code'    => $params['params']['params']['order_id'],
                    'form_id' => $params['params']['params']['form_id'],
                    'status'  => $templateMessageModel::SEND_STATUS_NO
                ];
                $templateMessageModel->addSend($message);
            }
            return true;
        } else if ($params['params']['code'] == 'order_payed') { //订单付款
            $order_id             = $params['params']['params']['order_id'];
            $billPaymentsRelModel = new BillPayments();
            $billPaymentInfo      = $billPaymentsRelModel->getSuccessPaymentInfo($order_id, $billPaymentsRelModel::TYPE_ORDER);
            //存储微信消息模板
            if (isset($billPaymentInfo['params'])) {
                $billParams = json_decode($billPaymentInfo['params'], true);
                if (isset($billParams['formid']) && $billParams['formid']) {
                    $formid = $billParams['formid'];
                    if ($formid) {
                        //获取orderid
                        $templateMessageModel = new TemplateMessage();
                        $message              = [
                            'type'    => $templateMessageModel::TYPE_PAYMENT,
                            'code'    => $order_id,
                            'form_id' => $formid,
                            'status'  => $templateMessageModel::SEND_STATUS_NO
                        ];
                        $templateMessageModel->addSend($message);
                    }
                }
            }
            return true;
        }


        if (!$params['params']['user_id']) {
            return false;
        }
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);

        if (!$setting) {
            return false;
        }

        $templateMessageModel = new TemplateMessage();
        $formInfo             = [];

        if ($params['params']['code'] == 'remind_order_pay') {
            $id = $params['params']['params']['order_id'];

            $remind_order_time = getSetting('remind_order_time');//催付时间

            $lastdesc = secondConversion($remind_order_time*3600);
            $params['params']['params']['notice'] = '您的订单将在'.$lastdesc.'后取消，请及时付款哦';
            $formInfo                             = $templateMessageModel->where(['type' =>'create_order', 'code' => $id, 'status' =>$templateMessageModel::SEND_STATUS_NO])->find();
        } else if ($params['params']['code'] == 'delivery_notice') {//发货
            $id = $params['params']['params']['order_id'];
            $formInfo = $templateMessageModel->where(['type' => 'order_payed', 'code' => $id, 'status' => $templateMessageModel::SEND_STATUS_NO])->find();
        } else if ($params['params']['code'] == 'refund_success') {//退款成功
            $id                                          = $params['params']['params']['source_id'];
            $params['params']['params']['refund_time']   = getTime($params['params']['params']['utime']);
            $params['params']['params']['refund_status'] = '退款成功';
            $params['params']['params']['refund_reason'] = '退款已经原路返回，具体到账时间可能会有1-3天延迟';
            $formInfo                                    = $templateMessageModel->where(['type' => 'after_sale', 'code' => $id, 'status' => '1'])->find();
        }
        $params['params']['params']['seller_name'] = getSetting('shop_name');//店铺名称

        //查询不到时，不发送模板消息
        if (!$formInfo) {
            return false;
        }
        //发送消息，取出会员open_id，然后发送
        $wxUserinfo = getUserWxInfo($params['params']['user_id']);
        if (!$wxUserinfo) {
            return false;
        }
        if (isset($setting[$params['params']['code']]) && $setting[$params['params']['code']]) {
            $template               = $setting[$params['params']['code']];
            $appid                  = getSetting('wx_appid');
            $secret                 = getSetting('wx_app_secret');
            $message['data']        = $this->replaceWord($params['params']['params'], $template);
            $message['touser']      = $wxUserinfo['openid'];
            $message['template_id'] = $template['template_id'];
            $message['page']        = 'pages/index/index';
            $message['form_id']     = $formInfo['form_id'];
            $wx                     = new Wx();
            $res                    = $wx->sendTemplateMessage($appid, $secret, $message);
            if ($res) {
                $templateMessageModel->sendSuccess($formInfo['id']);
            }
        } else {
            return false;
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
            if ($key != 'template_id') {

                $mkey = str_replace("{{", "", $value);
                $mkey = str_replace(".DATA}}", "", $mkey);

                $data[$key] = is_string($data[$key]) ? $data[$key] : "$data[$key]";

                $msgData[$mkey] = [
                    'value' => $data[$key],
                    'color' => '#173177',
                ];
            }

        }
        return $msgData;

    }
}