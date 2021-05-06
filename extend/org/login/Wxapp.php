<?php

namespace org\login;

use app\common\model\Images;
use app\common\model\UserWx;

class Wxapp
{
    //微信小程序登陆第一步，需要现在后台微信配置 小程序配置里面配置好参数
    public function codeToInfo($code)
    {
        //根据code取openid和session_key
        $wx = new \org\Wx();
        $userWxModel = new UserWx();

        $result = $wx->code_to_sessionkey(getSetting('wx_appid'), getSetting('wx_app_secret'), $code);

        if (!$result['status']) {
            return $result;
        }
        if (isset($result['data']['unionid']) && $result['data']['unionid']) {
            $where['unionid'] = $result['data']['unionid'];
        } else {
            $where['openid'] = $result['data']['openid'];
        }
        $where['type'] = $userWxModel::TYPE_MINIPROGRAM;
        $info          = $userWxModel->where($where)->find();
        if ($info) {
            //更新session_key
            $userWxModel->save(['session_key' => $result['data']['session_key']], $where);
            $user_wx_id = $info['id'];
        } else {
            if (isset($result['data']['unionid']) && $result['data']['unionid']) {
                $data['unionid'] = $result['data']['unionid'];
            }
            $data['type']        = $userWxModel::TYPE_MINIPROGRAM;     //小程序类型
            $data['openid']      = $result['data']['openid'];
            $data['session_key'] = $result['data']['session_key'];
            $userWxModel->save($data);
            $user_wx_id = $userWxModel->id;
        }
        $result['data'] = $user_wx_id;
        return $result;
    }

    //微信小程序登陆第二步，根据微信端传过来的值解析用户数据,更新user_wx表
    public function updateWxInfo($id, $edata, $iv, $pid = 0)
    {
        $userWxModel = new UserWx();
        $info = $userWxModel->where(['id' => $id])->find();
        if (!$info) {
            return error_code(11002);
        }
        //解密数据信息
        $wx     = new \org\Wx();
        $result = $wx->decrypt($edata, $iv, $info['session_key']);
        if (!$result['status']) {
            return $result;
        }
        //加密信息里有openid或unionid，前台传过来的值查出来的数据里也有，需要判断是否一致，否则可能会有漏洞
//        if ($info['openid'] != $result['data']['openId'] && $info['unionid'] != $result['data']['unionId']) {
//            return error_code(10000);
//        }
        //有会员的情况下不更新头像

        if (isset($info['avatar']) && !$info['avatar']) {
            $imageModel                  = new Images();
            $image                       = $imageModel->saveImage($result['data']['avatarUrl'], true); //头像都按统一方法保存到本地或者远程图片服务器
            $result['data']['avatarUrl'] = isset($image['data']['id']) ? $image['data']['id'] : _sImage();
        }

        $data['id']       = $info['id'];
        $data['user_id']       = $info['user_id'];  // user_id
        $data['unionid']       = $info['unionid'];  // unionid
        $data['type']     = $userWxModel::TYPE_MINIPROGRAM;
        $data['avatar']   = $result['data']['avatarUrl'];
        $data['nickname'] = $result['data']['nickName'];
        $data['gender']   = $result['data']['gender'];
        $data['language'] = $result['data']['language'];
        $data['city']     = $result['data']['city'];
        $data['province'] = $result['data']['province'];
        $data['country']  = $result['data']['country'];

        return $userWxModel->toAdd($data, $pid);
    }
}
