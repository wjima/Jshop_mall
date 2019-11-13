<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace org\login;

use app\common\model\User;
use app\common\model\UserToken;
use app\common\model\UserWx;
use org\Tt;

class Ttapp
{
    /**
     * 头条小程序
     * @param $code
     * @param $userInfo
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function codeToInfo($code, $userInfo)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        $userWxModel = new UserWx();
        $tt = new Tt();
        $res = $tt->code_to_token($code);
        if (!$res['status']) {
            return $res;
        }

        $where[] = ['openid', 'eq', $res['data']['openid']];
        $where[] = ['type', 'eq', $userWxModel::TYPE_TOUTIAO];
        $info = $userWxModel->where($where)->find();
        if ($info) {
            //更新session_key
            $userWxModel->save(['session_key' => $res['data']['session_key']], $where);
            $user_id = $info['id'];
            if ($info['user_id']) {
                //绑定好手机号码了,去登陆,去取user_token
                $userTokenModel = new UserToken();
                $token_res = $userTokenModel->setToken($info['user_id'], $userWxModel::TYPE_TOUTIAO);
                if ($token_res['status']) {
                    $result['data']['token'] = $token_res['data'];
                }
            }
        } else {
            $data = [
                'type' => $userWxModel::TYPE_TOUTIAO,
                'openid' => $res['data']['openid'],
                'session_key' => $res['data']['session_key'],
                'avatar' => $userInfo['avatar'],
                'nickname' => $userInfo['nickname'],
                'gender' => $userInfo['gender'],
                'language' => $userInfo['language'],
                'city' => $userInfo['city'],
                'province' => $userInfo['province'],
                'country' => $userInfo['country']
            ];

            if (getSetting('is_bind_mobile') == '2') {
                $user['nickname'] = $data['nickname'];
                $user['avatar'] = $data['avatar'];
                $user['sex'] = $data['gender'];

                $userModel = new User();
                $user_res = $userModel->manageAdd($user);
                if (!$user_res['status']) {
                    return $user_res;
                }
                //这时候还需要把新的user_id绑定到user_wx表上，否则就每次登陆都用新用户了
                $data['user_id'] = $user_res['data'];

                $userTokenModel = new UserToken();
                $token_res = $userTokenModel->setToken($data['user_id'], $userWxModel::TYPE_TOUTIAO);
                if ($token_res['status']) {
                    $result['data']['token'] = $token_res['data'];
                }
            }

            $userWxModel->save($data);
            $user_id = $userWxModel->id;
        }
        $result['status'] = true;
        $result['data']['user_id'] = $user_id;
        return $result;
    }
}