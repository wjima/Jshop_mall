<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\wechat\controller;

use app\common\controller\Wechat;
use think\facade\Log;


class Message extends Wechat
{
    public function callback()
    {

        $file   = ROOT_PATH . 'runtime/' . basename(__FILE__) . '.log';
        $params = input('param.');
        $wechat = load_wechat('Receive');
        if ($wechat->valid() === FALSE) {
            // 接口验证错误，记录错误日志
            Log::record("微信被动接口验证失败，{$wechat->errMsg}[{$wechat->errCode}]");
            // 退出程序
            exit($wechat->errMsg);
        }

        /* 获取粉丝的openid */
        $openid = $wechat->getRev()->getRevFrom();
        $to = $wechat->getRev()->getRevTo();
        /* 分别执行对应类型的操作 */
        switch ($wechat->getRev()->getRevType()) {
            // 文本类型处理
            case \Wechat\WechatReceive::MSGTYPE_TEXT:
                //$keys            = $wechat->getRevContent();
                return $wechat->transfer_customer_service()->reply();//自动转接多客服
            // 事件类型处理
            case \Wechat\WechatReceive::MSGTYPE_EVENT:
                $event = $wechat->getRevEvent();
                return $this->_event(strtolower($event['event']));
            // 图片类型处理
            case \Wechat\WechatReceive::MSGTYPE_IMAGE:
                return $this->_image();
            // 发送位置类的处理
            case \Wechat\WechatReceive::MSGTYPE_LOCATION:
                return $this->_location();
            // 其它类型的处理，比如卡卷领取、卡卷转赠
            default:
                return $this->_default();
        }

    }

    function _keys($keys, $appid)
    {
        $wechat = &load_wechat('Receive');
        // 这里直接原样回复给微信(当然你需要根据业务需求来定制的
        return $wechat->text($keys)->reply();
    }

    function _event($event, $appid)
    {
        $wechat = &load_wechat('Receive');
        switch ($event) {
            // 粉丝关注事件
            case 'subscribe':
                return $wechat->text('欢迎关注公众号！')->reply();
            // 粉丝取消关注
            case 'unsubscribe':
                exit("success");
            // 点击微信菜单的链接
            case 'click':
                return $wechat->text('你点了菜单链接！')->reply();
            // 微信扫码推事件
            case 'scancode_push':
            case 'scancode_waitmsg':
                $scanInfo = $wechat->getRev()->getRevScanInfo();
                return $wechat->text("你扫码的内容是:{$scanInfo['ScanResult']}")->reply();
            // 扫码关注公众号事件（一般用来做分销）
            case 'scan':
                return $wechat->text('欢迎关注公众号！')->reply();
        }
    }

    function _image($appid)
    {
        $wechat = &load_wechat('Receive');
        // $wechat 中有获取图片的方法
        return $wechat->text('您发送了一张图片过来')->reply();
    }


}