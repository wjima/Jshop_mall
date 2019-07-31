<?php

namespace org\login;
use org\Alipay;
use app\common\model\UserWx;

class Alipayapp
{
//微信小程序登陆第一步，需要现在后台微信配置 小程序配置里面配置好参数
    public function codeToInfo($code)
    {
        //暂不适配
        die();
        $result     = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        $userWxModel = new UserWx();
        $alipay     = new Alipay();

        $result = $alipay->code_to_token(getSetting('mp_alipay_appid'), $code);
        if (!$result['status']) {
            return $result;
        }
        if (isset($result['data']['alipay_user_id']) && $result['data']['alipay_user_id']) {
            $where['unionid'] = $result['data']['alipay_user_id'];
        }
        $where['openid'] = $result['data']['user_id'];
        $where['type']   = $userWxModel::TYPE_ALIPAY;
        $info            = $userWxModel->where($where)->find();
        if ($info) {
            //更新session_key
            $this->save(['session_key' => $result['data']['access_token']], $where);
            $user_alipay_id = $info['id'];
            if ($info['user_id']) {
                //绑定好手机号码了，去登陆,去取user_token
                $userTokenModel = new UserToken();
                $re             = $userTokenModel->setToken($info['user_id'], 3);
                if ($re['status']) {
                    $result['data'] = ['token' => $re['data']];
                }
            }
        } else {
            $userinfo = $alipay->get_user_info(getSetting('mp_alipay_appid'), $result['data']['access_token']);
            if (!$userinfo['status']) {
                return $userinfo;
            }
            if (isset($result['data']['alipay_user_id']) && $result['data']['alipay_user_id']) {
                $data['unionid'] = $result['data']['alipay_user_id'];
            }
            $data['type']        = self::TYPE_ALIPAY;     //小程序类型
            $data['openid']      = $result['data']['user_id'];
            $data['session_key'] = $result['data']['access_token'];

            $data['avatar']   = $userinfo['data']['avatar'];
            $data['openid']   = $userinfo['data']['openid'];
            $data['nickname'] = $userinfo['data']['nickname'];
            $data['gender']   = $userinfo['data']['gender'];
            $data['language'] = $userinfo['data']['language'];
            $data['city']     = $userinfo['data']['city'];
            $data['province'] = $userinfo['data']['province'];
            $data['country']  = $userinfo['data']['country'];
            $this->save($data);
            $user_alipay_id = $this->id;
        }
        $result['data']['user_wx_id'] = $user_alipay_id;
        return $result;

        return $userWxModel->toAdd($data,$pid);
    }
}
