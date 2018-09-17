<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\b2c\controller;

use app\common\model\BillRefund;
use app\common\model\BillReship;
use app\common\model\UserLog;
use app\common\model\Sms;
use Request;
use app\common\model\User;
use app\common\controller\Base;

class Common extends Base
{

    /**
     * 用户登陆页面
     * @author sin
     */
    public function login()
    {
        if (session('?user')) {
            $this->success('已经登录成功，跳转中...',redirect_url());
        }
        if(Request::isPost()){
            $userModel = new User();
            $result = $userModel->toLogin(input('param.'));
            if($result['status']){
                if(Request::isAjax()){
                    $result['data'] = redirect_url();
                    return $result;
                }else{
                    $this->redirect(redirect_url());
                }
            }else{
                return $result;
            }
        }else{
            return $this->fetch('login');
        }

    }
    /**
     * 用户短信登陆
     * @author sin
     */
    public function smsLogin()
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if (session('?user')) {
            $result['msg'] = "用户已经登录，请刷新页面，直接登陆";
            return $result;
        }
        if(!input("?post.mobile")){
            $result['msg'] = '请输入手机号码';
            return $result;
        }
        if(!input("?post.code")){
            $result['msg'] = '请输入手机短信验证码';
            return $result;
        }

        $userModel = new User();
        $result = $userModel->smsLogin(input('post.'));
        if($result['status']){
            if(Request::isAjax()){
                $result['data'] = redirect_url();
                return $result;
            }else{
                $this->redirect(redirect_url());
            }
        }else{
            return $result;
        }

    }
    /**
     * 用户注册页面
     * @author sin
     */
    public function reg()
    {
        if (session('?user')) {
            $this->success('已经登录成功，跳转中...',redirect_url());
        }
        if(Request::isPost()){
            $userModel = new User();
            $result = $userModel->toAdd(input('post.'));
            if($result['status']){
                if(Request::isAjax()){
                    $result['data'] = url('seller/index/index');
                    return $result;
                }else{
                    if(cookie('?redirect_url')){
                        $redirect_url = cookie('redirect_url');
                    }else{
                        $redirect_url = '/';
                    }
                    if(Request::isAjax()){
                        $result['data'] = $redirect_url;
                        return $result;
                    }else{
                        $this->redirect($redirect_url);
                    }
                }
            }else{
                return $result;
            }

            //dump($result);
        }else{
            return $this->fetch('reg');
        }

    }
    /**
     * 用户退出
     * @author sin
     */
    public function logout()
    {
        if(session('?user')){
            $userLogModel = new UserLog();
            $userLogModel->setLog(session('user.id'),$userLogModel::USER_LOGOUT);
        }
        session('user', null);
        session('seller', null);
        $this->success('退出成功','/');
    }

    /**
     * 发送短信验证码
     */
    public function sms()
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => '成功'
        ];
        $userModel = new User();
        if(!input("?post.mobile")){
            $result['msg'] = '请输入手机号码';
            return $result;
        }
        if(!input("?post.code")){
            $result['msg'] = '缺少核心参数';
            return $result;
        }
        $params = input('param.params',[]);

        $code = input('post.code');
        if($code == 'reg' || $code == 'login' || $code == 'seller_reg'){
            //此三个短信编码需要做特殊校验，所以先去use里做判断
            return $userModel->sms(input('post.mobile'),$code);
        }else{
            $smsModel = new Sms();
            return $smsModel->send(input('post.mobile'), $code,$params);
        }


    }
    public function topay()
    {
        $config = [
            'appid' => '2018060560313727',
            'rsa_private_key' => "MIIEowIBAAKCAQEA18Km/YKsi6vcwLHYarzlILEuXEkYzDXX8JCTM9/4wJNKY+X4kq0HskiAu1eOhMUFRGCM3jb6xpSKL47kjkR6gf4aM+er2pHBWyzNiBWaWJtLMr31HGBquGhbcV28aCCMrA81WVy6TJolzGg+FeHrwPngoLormkCgeG9TMywPjqXDZgsrmO5yz9jvQWkHc0NsnvhZGqBw8sDD5P6ZP/yoVM/5ny3TNKDB6gAuu/aProSLVlptn982vblmL6fcWTfxbXg/Kc7JWDj908ChloJZlIuPmMP+yb2btuuv3R9sExFY25lZF7GU/+FP13rRQVc6E/v/SUl4WuNU+8qg5Pq7HQIDAQABAoIBADzfZDG9/mRhlqSfCIoExThWBNhwnxhuUIBEqeNG222poKFSrrDjNHquTfONHl6DH5tNCjFfnZ0NJ3eHfyBMXavzPv1J7Yh6+ux4VXyBa8x3891bDp507Wcduj7fzVorZMTefZS6vq0CCIwFFQ8jcFo/pYpzO/4HIb8Y2YZkdfcudLFFzBeypTn91f5y49Tb1iAENPUoV/HRiUhG7b9Tjs0tMJq47niGkrWb6IuOMAcFS1qtfIM1jBhAj8ftMqMc6KKXdt04qAjSRFxi3T8pH8I7EukPwBQsiSxNZETg53Sc+gm44WbaX7BLbgB+132mVuxSRLsWrMLlOUEApriXWlkCgYEA72bJapc76Uw7UgQ9oLQT7GBQV2kY0Rj8F7poZVIBL91BMMCIxgQ6MMma8S+FgSPP/jyNCc5+32cyEyt7QR74aNtKuE70GTLW2EY8WgpqvAtdj+M/rVmk2VbVNHog9OPoB6vZknevgCa/+c6zSx2JnOOXhrxZa06Su09UpU3Lj2sCgYEA5rhAUqOC5X5eEHvEDlWFzI/seNroxo9XzixM9fRVRQH0qVvlXEDctK3GfOJwSYiqYi5BCYloU2GKV5QYvo52MsO+IoWvOVxDFs/1EYzVsuwGFLJZ2kImQPALLSES87c6K/XrGJ7nUySSXXvu32FHXKILynfmqnmxbrHVjAkTKZcCgYB0o0WasxgzcDqumPZVp6bVUY+TJtc6eMvJ274lLzD42vl6ATQiFcksH+1neNm4s3QQz/t/PaY+vg59nH+umKI6pCzhY0Y2SLi4InAhBlY+S+NHyVYq5TQV1+KEcMRBzBAuPKICZGGTNE3wLa7Y+Akl8RTQRk/ioiOHMv1wlFiLBQKBgQC9YtlJ/HZiM4y7Mx0INyORe0K9IdSZyOhmkO7LpjioYKaYrppjU8rXdR3NPYK2mbxiw10XmvdMGnEY2rRFF2Pm386fP+VJzHY1r5aXRWfVavDC5HPlOQ91lpVWAiQwcmMJ2t+UYju4f/i1TBoUuyF85HeRR0LNBP+kwc1tGbXlGQKBgAnlO8bPx3viY8pLNMsgclorGVX1j/glCeygvTjS1L3cXAa8u4gdSxdLa6Qw3eJh3inDNL4IZk1Hz4OcZZVTKSONvv8HS6sUs+S0sJPlSPzktVoX89TpK9Ygwj6pPCt2oQfuclMRzVL/akR3DJupjE+ic/z3wowMR5oWftIUQ7Qj",
        ];
        $alipay = new \org\payments\alipay($config);
        $paymentInfo = [
            'payment_id' => time(),
            'money' => '0.01',
        ];
        $re = $alipay->pay($paymentInfo);
        dump($re);

    }
}
