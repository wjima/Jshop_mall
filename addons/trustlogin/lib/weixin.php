<?php
/**
 * 微信快捷登录
 */
namespace addons\trustlogin\lib;

class weixin{

    private  $oauth = '';
    public  $name = '微信登录';
    public  $icon = 'http://img1.imgtn.bdimg.com/it/u=2454568623,2405472062&fm=11&gp=0.jpg';//图标地址
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
    public function checkState($state = ''){
        if($state != session('state')){
            return false;
        }
        return true;
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($code = ''){
        $params['code'] = $code;
        $accessToken = $this->oauth->getOauthAccessToken($params);

        if(!$accessToken){
            return false;
        }
        $userInfo=$this->oauth->getOauthUserInfo($access_token, $openid);
        return $userInfo;
    }




}