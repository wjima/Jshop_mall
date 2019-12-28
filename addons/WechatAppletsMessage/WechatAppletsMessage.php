<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace addons\WechatAppletsMessage;

use app\common\model\BillPayments;
use app\common\model\TemplateMessage;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use org\Wx;
use think\Db;

/**
 * 微信小程序订阅消息插件
 * Class WechatAppletsMessage
 * @package addons\WechatAppletsMessage
 * @author keinx
 */
class WechatAppletsMessage extends Addons
{
    public $info = [
        'name'          => 'WechatAppletsMessage',
        'title'         => '微信小程序订阅消息',
        'description'   => '微信小程序订阅消息配置',
        'status'        => 0,
        'author'        => 'keinx',
        'version'       => '1.0',
        'dialog_width'  => '640px',
        'dialog_height' => '480px'
    ];


    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();

        //
        $sql = "DROP TABLE IF EXISTS `".config('database.prefix')."user_wxmsg_subscription`;";
        $sql .= "CREATE TABLE `".config('database.prefix')."user_wxmsg_subscription` (";
        $sql .= "`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,";
        $sql .= "`template_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '模板ID',";
        $sql .= "`user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',";
        $sql .= "PRIMARY KEY (`id`) USING BTREE,";
        $sql .= "INDEX `inid`(`template_id`, `user_id`) USING BTREE";
        $sql .= ") ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信订阅消息存储表' ROW_FORMAT = Compact;";

        //
        $sql .= "DROP TABLE IF EXISTS `".config('database.prefix')."user_wxmsg_subscription_switch`;";
        $sql .= "CREATE TABLE `".config('database.prefix')."user_wxmsg_subscription_switch` (";
        $sql .= "`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,";
        $sql .= "`user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',";
        $sql .= "`switch` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '1 = 关闭',";
        $sql .= "PRIMARY KEY (`id`) USING BTREE,";
        $sql .= "INDEX `user_id`(`user_id`) USING BTREE";
        $sql .= ") ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户订阅提醒状态' ROW_FORMAT = Compact;";

        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]) {
                $db::execute($list[$i]);
            }
        }
        return true;
    }


    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        $db = new Db();
        $sql = 'DROP TABLE IF EXISTS '.config('database.prefix').'user_wxmsg_subscription;';
        $sql .= 'DROP TABLE IF EXISTS '.config('database.prefix').'user_wxmsg_subscription_switch;';

        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]) {
                $db::execute($list[$i]);
            }
        }
        return true;
    }


    /**
     * 设置配置信息
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * $params['seller_name'] = '张三店铺';
     * $params['order_id'] = '15223133009509';
     * $params['status'] = '待支付';
     * $params['order_amount'] = '50';
     * $params['ctime'] = date('Y-m-d H:i:s',time());
     * sendMessage('4','17','create_order',$params);
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