<?php
/**
 * 微信快捷登录
 */
namespace addons\Trustlogin\lib;

use think\facade\Cache;
use think\facade\Log;

class weixin{

    private  $oauth = '';
    public  $name = '微信登录';
    public  $icon = 'https://b2c.jihainet.com/static/images/wechat_login.png';//图标地址
    public function __construct()
    {
        $this->oauth = load_wechat('Oauth');
    }

    /**
     * 获取跳转地址
     * @param $redirect_uri
     * @return string
     */
    public function getOauthUrl($redirect_uri,$uuid = ''){
        $state = rand(10000,99999);
        Cache::set("user_".$uuid,$state);//用户id和session对应
        return  $this->oauth->getOauthRedirect($redirect_uri,$state,'snsapi_userinfo');
    }

    /**
     * 检查state是否一致，校检是否同一个用户
     * @param string $state
     * @return bool
     */
    private function checkState($state = '',$uuid = ''){
        if($state != Cache::get('user_'.$uuid)){
            return false;
        }
        return true;
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($params)
    {
        $data = [
            'status' => false,
            'msg'    => '获取失败',
            'data'   => []
        ];
        if (!$this->checkState($params['state'], $params['uuid'])) {
            $data['msg'] = 'state错误';
            return $data;
        }
        $accessToken = $this->oauth->getOauthAccessToken($params);
        if (!$accessToken) {
            $data['msg'] = '获取授权失败';
            return $data;
        }
        $userInfo = $this->oauth->getOauthUserInfo($accessToken['access_token'], $accessToken['openid']);
        Log::info("用户信息：" . json_encode($userInfo));
        $user = [];
        if (!$userInfo) {
            $data['msg'] = '获取用户信息失败';
            return $data;
        }
        $data['status'] = true;
        $data['msg']    = '获取成功';
        $data['data']   = $this->getUserData($userInfo);
        return $data;
    }

    /**
     * 字段转换
     * @param array $params
     * @return mixed
     */
    public function getUserData($params = [])
    {
        $userData['openid']    = $params['openid'];
        $userData['unionId']   = $params['unionid'];
        $userData['privilege'] = $params['privilege'];
        $userData['avatar']    = $params['headimgurl'];
        $userData['country']   = $params['country'];
        $userData['language']  = $params['language'];
        $userData['province']  = $params['province'];
        $userData['city']      = $params['city'];
        $userData['gender']    = $params['sex'];
        $userData['nickName']  = $params['nickname'];
        $userData['username']  = $params['nickname'];
        return $userData;
    }



}