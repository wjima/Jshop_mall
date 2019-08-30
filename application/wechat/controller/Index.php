<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/18
 * Time: 下午3:23
 */

namespace app\wechat\controller;

use app\common\controller\Wechat;
use app\common\model\WeixinMessage;
use Request;
use think\facade\Log;


class Index extends Wechat
{


    public function index()
    {
        $wechat = load_wechat('Receive');
        /* 验证接口 */
        if ($wechat->valid() === FALSE) {
            // 接口验证错误，记录错误日志
            // 退出程序
            exit($wechat->errMsg);
        }
        /* 获取粉丝的openid */
        $openid = $wechat->getRev()->getRevFrom();

        Log::info('openid是' . $openid);
        /* 分别执行对应类型的操作 */
        switch ($wechat->getRev()->getRevType()) {
            // 文本类型处理
            case \Wechat\WechatReceive::MSGTYPE_TEXT:
                $keys = $wechat->getRevContent();
                return $this->_keys($keys);
            // 事件类型处理
            case \Wechat\WechatReceive::MSGTYPE_EVENT:
                $event = $wechat->getRevEvent();
                return $this->_event(strtolower($event['event']));
            /*
            // todo 暂时无这些处理，后续更新
             // 图片类型处理
             case \Wechat\WechatReceive::MSGTYPE_IMAGE:
                 return  $this->_image();
             // 发送位置类的处理
             case \Wechat\WechatReceive::MSGTYPE_LOCATION:
                 return  $this->_location();
             // 其它类型的处理，比如卡卷领取、卡卷转赠*/
            default:
                return $this->_default();
        }
    }

    /***
     * 关键词回复
     * @param $keys
     */
    private function _keys($keys)
    {
        if ($keys == '客服') {
            $wechat = &load_wechat('Receive');
            return $wechat->transfer_customer_service()->reply();
        }
        $weixinMessage = new WeixinMessage();
        $weixinMessage->weixinReply('keyword', $keys);
    }

    private function _event($event)
    {
        $wechat = &load_wechat('Receive');
        switch ($event) {
            // 粉丝关注事件
            case 'subscribe':
                $weixinMessage = new WeixinMessage();
                return $weixinMessage->weixinReply('subscribe');
            // 粉丝取消关注
            case 'unsubscribe':
                exit("success");
            // 点击微信菜单的链接
            case 'click':
                $event         = $wechat->getRevEvent();
                $weixinMessage = new WeixinMessage();
                return $weixinMessage->weixinReply('keyword', $event['key']);
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

    private function _image()
    {
        $wechat = &load_wechat('Receive');
        return $wechat->text('您发送了一张图片过来')->reply();
    }

    private function _default()
    {
        $wechat = &load_wechat('Receive');
        $wechat->text('欢迎光临')->reply();//没有默认的时候返回这个
    }


}