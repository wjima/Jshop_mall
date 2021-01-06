<?php

namespace org\login;

use app\common\model\UserWx;
use org\Curl;
use app\common\model\Images;

class Wxofficial
{
    private $scope = [
        1 => 'snsapi_userinfo',
        2 => 'snsapi_base',
    ];



    /**
     * 公众号登陆获取登陆地址
     * @param $url                  此地址是前端的一个地址，此页面地址必须能够获得微信回调回来的参数并请求user.officiallogin接口
     * @param string $scope         应用授权作用域，snsapi_userinfo或者snsapi_base
     * @return array
     */
    public function geturl($url, $scope = 1)
    {

        $url = urlencode($url);
        return  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . getSetting('wx_official_appid') . "&redirect_uri=" . $url . "&response_type=code&scope=" . $this->scope[$scope] . "&state=jshop#wechat_redirect";
    }
    public function codeToInfo($code, $state, $scope = 1, $pid = 0)
    {
        $result     = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . getSetting('wx_official_appid') . "&secret=" . getSetting('wx_official_app_secret') . "&code=" . $code . "&grant_type=authorization_code";
        $curl = new Curl();
        $re = $curl->get($url);
        $data = json_decode($re, true);
        if (isset($data['errcode'])) {
            $result['msg'] = $data['errmsg'];
            return $result;
        }

        //如果是静默登陆的话，到这里就OK了。
        if ($scope == 2) {
            $result['status'] = true;
            $result['data'] = [
                'openid' => $data['openid']
            ];
            return $result;
        }

        //根据access_token拉取用户信息
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $data['access_token'] . "&openid=" . $data['openid'] . "&lang=zh_CN";
        $re = $curl->get($url);
        $data = json_decode($re, true);
        if (isset($data['errcode'])) {
            $result['msg'] = $data['errmsg'];
            return $result;
        }
        //到这里就取到用户的信息了
        return $this->addUserWx($data, $pid);
    }

    //微信授权登陆的时候，取到了微信的信息，创建userWx记录
    private function addUserWx($data, $pid)
    {
        $userWxModel = new UserWx();
        $where[] = ['type', 'eq', $userWxModel::TYPE_OFFICIAL];
        $where[] = ['openid', 'eq', $data['openid']];
        $info = $userWxModel->where($where)->find();
        $data['user_id'] = 0;
        if ($info) {
            $data['id'] = $info['id'];
            $data['user_id'] = $info['user_id'];
        }

        if (isset($data['headimgurl']) && !$info['avatar']) {
            $imageModel                  = new Images();
            $image                       = $imageModel->saveImage($data['headimgurl'], true); //头像都按统一方法保存到本地或者远程图片服务器
            $data['headimgurl'] = isset($image['data']['id']) ? $image['data']['id'] : _sImage();
        }

        $data['type']     = $userWxModel::TYPE_OFFICIAL;
        $data['openid']   = $data['openid'];
        $data['unionid']   = $data['unionid'];  //用户的unionid
        $data['avatar']   = $data['headimgurl'];
        $data['nickname'] = $data['nickname'];
        $data['gender']   = $data['sex'];
        $data['language'] = $data['language'];
        $data['city']     = $data['city'];
        $data['province'] = $data['province'];
        $data['country']  = $data['country'];
        return $userWxModel->toAdd($data, $pid);
    }
}
