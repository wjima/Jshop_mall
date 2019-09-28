<?php

namespace org\login;

use app\common\model\Images;
use app\common\model\UserWx;

class Uniapp
{
    /**
     * app的微信登录
     * @param array $info
     * @return array|mixed
     */
    public function login($info = [])
    {
        $userWxModel = new UserWx();

        if ($info['type'] == 'weixin') {
            $userInfo         = $this->getWeixinUserData($info['user']);
            $userInfo['type'] = $userWxModel::TYPE_UNIAPP_WEIXIN;
        } elseif ($info['type'] == 'QQ') {
            $userInfo         = $this->getQQUserData($info['user']);
            $userInfo['type'] = $userWxModel::TYPE_UNIAPP_QQ;
        }
        //openid必须
        if (!$userInfo['openid']) {
            return error_code(10000);
        }
        //判断用户是否存在
        $where[] = ['openid', 'eq', $userInfo['openid']];
        if (isset($userInfo['unionid']) && $userInfo['unionid']) {
            $where[]    = ['unionid', 'eq', $userInfo['unionid']];
            $wxUserInfo = $userWxModel->where($where)->find();
            if ($wxUserInfo) {
                $userInfo['user_id'] = $wxUserInfo['user_id'];
                $userInfo['id']      = $wxUserInfo['id'];
            }
        }
        //有会员的情况下不更新头像
        if (isset($userInfo['avatar']) && $userInfo['avatar'] && !$info['user_id']) {
            $imageModel         = new Images();
            $image              = $imageModel->saveImage($userInfo['avatar'], true);//头像都按统一方法保存到本地或者远程图片服务器
            $userInfo['avatar'] = isset($image['data']['id']) ? $image['data']['id'] : _sImage();
        }
        return $userWxModel->toAdd($userInfo, $info['pid']);
    }


    private function getQQUserData()
    {

    }

    /**
     * 获取微信信任登录信息
     * 字段转换
     * @param array $params
     * @return mixed
     */
    public function getWeixinUserData($params = [])
    {
        $userData['openid']    = $params['openId'];
        $userData['unionid']   = $params['unionId'];
        $userData['privilege'] = $params['privilege'];
        $userData['avatar']    = $params['avatarUrl'];
        $userData['country']   = $params['country'];
        $userData['language']  = $params['language'];
        $userData['province']  = $params['province'];
        $userData['city']      = $params['city'];
        $userData['gender']    = $params['gender'];
        $userData['nickname']  = $params['nickName'];
        return $userData;
    }

}
