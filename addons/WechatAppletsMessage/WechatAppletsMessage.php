<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace addons\WechatAppletsMessage;

use addons\WechatAppletsMessage\model\UserWxmsgSubscription;
use app\common\model\UserWx;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use org\Curl;
use org\Wx;
use think\Db;
use think\facade\Log;

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
        'description'   => '微信小程序订阅消息配置，安装后记得开启消息配置里面的微信消息哦',
        'status'        => 0,
        'author'        => 'keinx,mark',
        'version'       => '2.0',
        'dialog_width'  => '640px',
        'dialog_height' => '570px'
    ];


    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();

        //
        $sql = "DROP TABLE IF EXISTS `" . config('database.prefix') . "user_wxmsg_subscription`;";
        $sql .= "CREATE TABLE `" . config('database.prefix') . "user_wxmsg_subscription` (";
        $sql .= "`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,";
        $sql .= "`template_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '模板ID',";
        $sql .= "`user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',";
        $sql .= "`type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订阅类型',";
        $sql .= "PRIMARY KEY (`id`) USING BTREE,";
        $sql .= "INDEX `inid`(`template_id`, `user_id`) USING BTREE";
        $sql .= ") ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信订阅消息存储表' ROW_FORMAT = Compact;";

        //
        $sql .= "DROP TABLE IF EXISTS `" . config('database.prefix') . "user_wxmsg_subscription_switch`;";
        $sql .= "CREATE TABLE `" . config('database.prefix') . "user_wxmsg_subscription_switch` (";
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
        $db  = new Db();
        $sql = 'DROP TABLE IF EXISTS ' . config('database.prefix') . 'user_wxmsg_subscription;';
        $sql .= 'DROP TABLE IF EXISTS ' . config('database.prefix') . 'user_wxmsg_subscription_switch;';

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
     */
    public function sendwxmessage($params)
    {
        if ($params['params']['code'] == 'seller_order_notice') {
            //商家不通知
            return true;
        }
        $template_id = $this->getUserIsTip($params['params']['user_id'], $params['params']['code']);
        if (!$template_id) {
            //不需要通知
            return true;
        }

        switch ($params['params']['code']) {
            case 'create_order':
                $this->orderTip($params, $template_id);
                break;
            case 'remind_order_pay':
                $this->remindTip($params, $template_id);
                break;
            case 'order_cancle':
                $this->cancleTip($params, $template_id);
                break;
            case 'order_payed':
                $this->payedTip($params, $template_id);
                break;
            case 'delivery_notice':
                $this->shipTip($params, $template_id);
                break;
            case 'aftersales_pass':
                $this->aftersalesTip($params, $template_id);
                break;
            case 'refund_success':
                $this->refundTip($params, $template_id);
                break;
        }
        return true;
    }


    /**
     * 下单通知
     * @param $params
     * @param $template_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function orderTip($params, $template_id)
    {
        $user_id = $params['params']['user_id'];
        $info    = $params['params']['params'];
        $data    = [
            'order_id'     => [
                'value' => $info['order_id']
            ],
            'order_amount' => [
                'value' => $info['order_amount']
            ],
            'ship_name'    => [
                'value' => $info['ship_name']
            ],
            'ship_mobile'  => [
                'value' => $info['ship_mobile']
            ],
            'ship_addr'    => [
                'value' => $info['ship_addr']
            ]
        ];

        $addonModel = new addonsModel();
        $con        = $addonModel->getSetting('WechatAppletsMessage');

        $newData = [];
        foreach ($data as $k => $v) {
            if ($con['order'][$k] && $con['order'][$k] != '') {
                $con['order'][$k]           = trim($con['order'][$k]);
                $con['order'][$k]           = ltrim($con['order'][$k], "{{");
                $con['order'][$k]           = rtrim($con['order'][$k], ".DATA}}");
                $newData[$con['order'][$k]] = $v;
            }
        }

        $this->send($user_id, $template_id, $newData);
    }


    /**
     * 催付通知
     * @param $params
     * @param $template_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function remindTip($params, $template_id)
    {
        $user_id = $params['params']['user_id'];
        $info    = $params['params']['params'];
        $data    = [
            'order_id'     => [
                'value' => $info['order_id']
            ],
            'order_amount' => [
                'value' => $info['order_amount']
            ],
            'ctime'        => [
                'value' => getTime($info['ctime'])
            ],
            'tip'          => [
                'value' => '订单未支付请及时支付'
            ]
        ];

        $addonModel = new addonsModel();
        $con        = $addonModel->getSetting('WechatAppletsMessage');

        $newData = [];
        foreach ($data as $k => $v) {
            if ($con['remind'][$k] && $con['remind'][$k] != '') {
                $con['remind'][$k]           = trim($con['remind'][$k]);
                $con['remind'][$k]           = ltrim($con['remind'][$k], "{{");
                $con['remind'][$k]           = rtrim($con['remind'][$k], ".DATA}}");
                $newData[$con['remind'][$k]] = $v;
            }
        }

        $this->send($user_id, $template_id, $newData);
    }


    /**
     * 订单取消通知
     * @param $params
     * @param $template_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function cancleTip($params, $template_id)
    {
        $user_id = $params['params']['user_id'];
        $info    = $params['params']['params'];
        $data    = [
            'order_id' => [
                'value' => $info['order_id']
            ],
            'reason'   => [
                'value' => $info['reason']
            ],
            'ctime'    => [
                'value' => getTime($info['ctime'])
            ]
        ];

        $addonModel = new addonsModel();
        $con        = $addonModel->getSetting('WechatAppletsMessage');

        $newData = [];
        foreach ($data as $k => $v) {
            if ($con['cancel'][$k] && $con['cancel'][$k] != '') {
                $con['cancel'][$k]           = trim($con['cancel'][$k]);
                $con['cancel'][$k]           = ltrim($con['cancel'][$k], "{{");
                $con['cancel'][$k]           = rtrim($con['cancel'][$k], ".DATA}}");
                $newData[$con['cancel'][$k]] = $v;
            }
        }

        $this->send($user_id, $template_id, $newData);
    }


    /**
     * 支付通知
     * @param $params
     * @param $template_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function payedTip($params, $template_id)
    {
        $user_id = $params['params']['user_id'];
        $info    = $params['params']['params'];
        $data    = [
            'order_id' => [
                'value' => $info['order_id']
            ],
            'money'    => [
                'value' => $info['money']
            ],
            'pay_time' => [
                'value' => $info['pay_time']
            ]
        ];

        $addonModel = new addonsModel();
        $con        = $addonModel->getSetting('WechatAppletsMessage');

        $newData = [];
        foreach ($data as $k => $v) {
            if ($con['pay'][$k] && $con['pay'][$k] != '') {
                $con['pay'][$k]           = trim($con['pay'][$k]);
                $con['pay'][$k]           = ltrim($con['pay'][$k], "{{");
                $con['pay'][$k]           = rtrim($con['pay'][$k], ".DATA}}");
                $newData[$con['pay'][$k]] = $v;
            }
        }

        $this->send($user_id, $template_id, $newData);
    }


    /**
     * 发货通知
     * @param $params
     * @param $template_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function shipTip($params, $template_id)
    {
        $user_id = $params['params']['user_id'];
        $info    = $params['params']['params'];
        $data    = [
            'logistics_name' => [
                'value' => $info['logistics_name']
            ],
            'logi_no'   => [
                'value' => $info['logi_no']
            ],
            'order_id'  => [
                'value' => $info['order_id']
            ],
        ];

        $addonModel = new addonsModel();
        $con        = $addonModel->getSetting('WechatAppletsMessage');

        $newData = [];
        foreach ($data as $k => $v) {
            if ($con['ship'][$k] && $con['ship'][$k] != '') {
                $con['ship'][$k]           = trim($con['ship'][$k]);
                $con['ship'][$k]           = ltrim($con['ship'][$k], "{{");
                $con['ship'][$k]           = rtrim($con['ship'][$k], ".DATA}}");
                $newData[$con['ship'][$k]] = $v;
            }
        }

        $this->send($user_id, $template_id, $newData);
    }


    /**
     * 售后通知
     * @param $params
     * @param $template_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function aftersalesTip($params, $template_id)
    {
        $user_id = $params['params']['user_id'];
        $info    = $params['params']['params'];
        $data    = [
            'id'            => [
                'value' => $info['order_id']
            ],
            'amount'        => [
                'value' => $info['order_amount']
            ],
            'aftersales_id' => [
                'value' => $info['aftersales_id']
            ],
            'status'        => [
                'value' => $info['aftersales_status']
            ]
        ];

        $addonModel = new addonsModel();
        $con        = $addonModel->getSetting('WechatAppletsMessage');

        $newData = [];
        foreach ($data as $k => $v) {
            if ($con['after_sale'][$k] && $con['after_sale'][$k] != '') {
                $con['after_sale'][$k]           = trim($con['after_sale'][$k]);
                $con['after_sale'][$k]           = ltrim($con['after_sale'][$k], "{{");
                $con['after_sale'][$k]           = rtrim($con['after_sale'][$k], ".DATA}}");
                $newData[$con['after_sale'][$k]] = $v;
            }
        }

        $this->send($user_id, $template_id, $newData);
    }


    /**
     * 退款通知
     * @param $params
     * @param $template_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function refundTip($params, $template_id)
    {
        $user_id = $params['params']['user_id'];
        $info    = $params['params']['params'];
        $data    = [
            'source_id'            => [
                'value' => $info['source_id']
            ],
            'aftersales_id' => [
                'value' => $info['aftersales_id']
            ],
            'money'         => [
                'value' => $info['money']
            ],
            'payment_code'  => [
                'value' => config('params.payment_type')[$info['payment_code']]
            ],
            'ctime'         => [
                'value' => getTime($info['ctime'])
            ]
        ];

        $addonModel = new addonsModel();
        $con        = $addonModel->getSetting('WechatAppletsMessage');

        $newData = [];
        foreach ($data as $k => $v) {
            if ($con['refund'][$k] && $con['refund'][$k] != '') {
                $con['refund'][$k]           = trim($con['refund'][$k]);
                $con['refund'][$k]           = ltrim($con['refund'][$k], "{{");
                $con['refund'][$k]           = rtrim($con['refund'][$k], ".DATA}}");
                $newData[$con['refund'][$k]] = $v;
            }
        }

        $this->send($user_id, $template_id, $newData);
    }


    /**
     * 判断是否需要通知用户
     * @param $user_id
     * @param $code
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getUserIsTip($user_id, $code)
    {
        $newCode = [
            'create_order'     => 'order',
            'remind_order_pay' => 'remind',
            'order_payed'      => 'pay',
            'delivery_notice'  => 'ship',
            'aftersales_pass'  => 'after_sale',
            'refund_success'   => 'refund',
            'order_cancle'     => 'cancel'//订单取消
        ];
        $suModel = new UserWxmsgSubscription();
        $where[] = ['user_id', '=', $user_id];
        $where[] = ['type', '=', $newCode[$code]];
        $info    = $suModel->where($where)
            ->find();

        $flag = false;
        if ($info) {
            $addonModel = new addonsModel();
            $con        = $addonModel->getSetting('WechatAppletsMessage');
            foreach ($con as $k => $v) {
                if ($k == $newCode[$code]) {
                    if ($info['template_id'] == $v['template_id']) {
                        $flag = trim($v['template_id']);
                        break;
                    }
                }
            }
        }

        return $flag;
    }


    /**
     * 发送信息
     * @param $user_id
     * @param $template_id
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function send($user_id, $template_id, $data)
    {
        $appid       = getSetting('wx_appid');
        $secret      = getSetting('wx_app_secret');
        $wx          = new Wx();
        $accessToken = $wx->getAccessToken($appid, $secret);
        $curl        = new Curl();
        $url         = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $accessToken;

        $userWxModel = new UserWx();
        $useropenid  = $userWxModel->where('user_id', '=', $user_id)->find();
        $sendData    = [
            'touser'      => $useropenid['openid'],
            'template_id' => $template_id,
            'page'        => '/pages/index/index',//todo页面路径，如果有需要，记得调整为对应页面
            'data'        => $data
        ];

        $sendData = json_encode($sendData);
        Log::info('订阅模板消息发送：' . $sendData);
        $res = $curl->post($url, $sendData);
        Log::info('订阅模板消息返回：' . $res);
        $res = json_decode($res, true);
        if (isset($res['errcode']) && $res['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }
}