<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\job;

use app\common\model\WeixinAuthor;
use app\common\model\MessageCenter;
use think\facade\Log;
use think\queue\Job;
use org\ThirdWx as ThirdwxLib;

class Thirdwx {

    //设置小程序服务器域名 和设置小程序业务域名
    public function exec(Job $job, $params){
        $thirdwxLib = new ThirdwxLib();
        $weixinAuthorModel = new WeixinAuthor();
        $messageModel = new MessageCenter();
        if ($job->attempts() > 3) {//超过3次，删除任务，手动重置
            $job->delete();
        }
        $message['seller_name'] = $params['seller_name'];
        $authorInfo = $weixinAuthorModel->getAuthorInfo($params['seller_id'],$params['authorType']);
        if($authorInfo){
            $res=$thirdwxLib->modify_domain($authorInfo['authorizer_access_token'],$authorInfo,$msg);
            if (!$res) {//服务器域名绑定失败
                //发送站内信
                $message['msg'] = '您的服务器域名设置失败！详细原因：'.$msg;
                Log::error($msg);
            } else {
                $message['msg'] = '您的服务器域名设置成功！';
            }
            $messageModel->sendPlatformMessage($params['seller_id'],'modify_domain',$message);

            $res=$thirdwxLib->setWebViewDomain($authorInfo['authorizer_access_token'],$authorInfo,$msg);
            if (!$res) {//服务器域名绑定失败
                //发送站内信
                Log::error($msg);
                $message['msg'] = '您的业务域名设置失败！详细原因：'.$msg;
            } else {
                $message['msg'] = '您的业务域名设置成功！';
            }
            $messageModel->sendPlatformMessage($params['seller_id'],'modify_domain',$message);
            Log::record("业务域名设置成功");
        }else{
            //延迟5分钟
            $message['msg'] = '未查询到您的授权信息！';
            $messageModel->sendPlatformMessage($params['seller_id'],'modify_domain',$message);
        }
        $job->delete();
    }



    public function failed($data)
    {

        // ...任务达到最大重试次数后，失败了
    }

}