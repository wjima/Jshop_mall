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
     * 用户注册页面，此页面是通过页面直接登陆注册的，没有走接口，存的是session，暂时没啥用
     * @author sin
     */
    public function reg()
    {
        if (session('?user')) {
            $this->success('已经登录成功，跳转中...',redirect_url());
        }
        if(Request::isPost()){
            $userModel = new User();
            $result = $userModel->smsLogin(input('post.'));
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
}
