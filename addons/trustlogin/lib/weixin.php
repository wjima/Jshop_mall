<?php
/**
 * 微信快捷登录
 */
namespace addons\trustlogin\lib;

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
    public function getOauthUrl($redirect_uri){
        $state = rand(10000,99999);
        session('state',$state);
        return  $this->oauth->getOauthRedirect($redirect_uri,$state);
    }

    /**
     * 检查state是否一致，校检是否同一个用户
     * @param string $state
     * @return bool
     */
    private function checkState($state = ''){
        if($state != session('state')){
            return false;
        }
        return true;
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($params)
    {
        //TODO 临时注释掉,没有获取到
        /*if (!$this->checkState($params['state'])) {
            return false;
        }*/
        $accessToken = $this->oauth->getOauthAccessToken($params);
        if (!$accessToken) {
            return false;
        }
        $userInfo = $this->oauth->getOauthUserInfo($accessToken['access_token'], $accessToken['openid']);
        Log::info("用户信息：" . json_encode($userInfo));
        $user = [];
        if(!$userInfo) {
            return false;
        }
        return $this->getUserData($userInfo);
    }

    /**
     * 字段转换
     * @param array $params
     * @return mixed
     */
    public function getUserData($params = [])
    {
        $userData['openid'] = $params['openid'];
        $userData['unionId'] = $params['unionid'];
        $userData['privilege'] = $params['privilege'];
        $userData['avatar'] = $params['headimgurl'];
        $userData['country'] = $params['country'];
        $userData['language'] = $params['language'];
        $userData['province'] = $params['province'];
        $userData['city'] = $params['city'];
        $userData['gender'] = $params['sex'];
        $userData['nickName'] = $params['nickname'];
        $userData['username'] = $params['nickname'];
        return $userData;
    }



}