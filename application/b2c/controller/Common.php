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
use org\QRcode;
use org\share\PosterShare;
use Request;
use app\common\model\User;
use app\common\controller\Base;

class Common extends Base
{

//以下代码登录注册等是为了不是前后端分离的时候使用的。
//    /**
//     * 用户登陆页面
//     * @author sin
//     */
//    public function login()
//    {
//        if (session('?user')) {
//            $this->success('已经登录成功，跳转中...',redirect_url());
//        }
//        if(Request::isPost()){
//            $userModel = new User();
//            $result = $userModel->toLogin(input('param.'));
//            if($result['status']){
//                if(Request::isAjax()){
//                    $result['data'] = redirect_url();
//                    return $result;
//                }else{
//                    $this->redirect(redirect_url());
//                }
//            }else{
//                return $result;
//            }
//        }else{
//            return $this->fetch('login');
//        }
//
//    }
//    /**
//     * 用户短信登陆
//     * @author sin
//     */
//    public function smsLogin()
//    {
//        $result = [
//            'status' => false,
//            'data' => '',
//            'msg' => ''
//        ];
//        if (session('?user')) {
//            $result['msg'] = "用户已经登录，请刷新页面，直接登陆";
//            return $result;
//        }
//        if(!input("?post.mobile")){
//            $result['msg'] = '请输入手机号码';
//            return $result;
//        }
//        if(!input("?post.code")){
//            $result['msg'] = '请输入手机短信验证码';
//            return $result;
//        }
//
//        $userModel = new User();
//        $result = $userModel->smsLogin(input('post.'));
//        if($result['status']){
//            if(Request::isAjax()){
//                $result['data'] = redirect_url();
//                return $result;
//            }else{
//                $this->redirect(redirect_url());
//            }
//        }else{
//            return $result;
//        }
//
//    }
//    /**
//     * 用户注册页面，此页面是通过页面直接登陆注册的，没有走接口，存的是session，暂时没啥用
//     * @author sin
//     */
//    public function reg()
//    {
//        if (session('?user')) {
//            $this->success('已经登录成功，跳转中...',redirect_url());
//        }
//        if(Request::isPost()){
//            $userModel = new User();
//            $result = $userModel->smsLogin(input('post.'));
//            if($result['status']){
//                if(Request::isAjax()){
//                    $result['data'] = url('seller/index/index');
//                    return $result;
//                }else{
//                    if(cookie('?redirect_url')){
//                        $redirect_url = cookie('redirect_url');
//                    }else{
//                        $redirect_url = '/';
//                    }
//                    if(Request::isAjax()){
//                        $result['data'] = $redirect_url;
//                        return $result;
//                    }else{
//                        $this->redirect($redirect_url);
//                    }
//                }
//            }else{
//                return $result;
//            }
//
//            //dump($result);
//        }else{
//            return $this->fetch('reg');
//        }
//
//    }
//    /**
//     * 用户退出
//     * @author sin
//     */
//    public function logout()
//    {
//        if(session('?user')){
//            $userLogModel = new UserLog();
//            $userLogModel->setLog(session('user.id'),$userLogModel::USER_LOGOUT);
//        }
//        session('user', null);
//        session('seller', null);
//        $this->success('退出成功','/');
//    }
//
//    /**
//     * 发送短信验证码，这里只能发送登陆，注册和短信校验的时候的验证码
//     */
//    public function sms()
//    {
//        $result = [
//            'status' => false,
//            'data' => '',
//            'msg' => error_code(10038,true)
//        ];
//        $userModel = new User();
//        if(!input("?post.mobile")){
//            $result['msg'] = '请输入手机号码';
//            return $result;
//        }
//        if(!input("?post.code")){
//            $result['msg'] = '缺少核心参数';
//            return $result;
//        }
//        $params = input('param.params',[]);
//
//        $code = input('post.code');
//        return $userModel->sms(input('post.mobile'),$code);
//    }

    /**
     * 二维码
     */
    public function qr()
    {
        ob_end_clean();
        header('Content-type:image/png'); //mime信息
        include_once ROOT_PATH . 'extend/org/phpqrcode.php';
        if (input('?param.url')) {
            $url = urldecode(input('param.url'));
        } else {
            $url = 'https://www.ji'.'hai'.'net.com';
        }
        $url = urldecode($url);
        $level = input('param.level', 'L');
        $size = input('param.size', 10);
        QRcode::png($url, false, $level, $size, 2);
    }


    /**
     * 海报
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function poster()
    {
        ob_end_clean();
        header('Content-type:image/png'); //mime信息
        if (input('?param.url')) {
            $url = urldecode(input('param.url'));
        } else {
            $url = 'https://www.ji'.'hai'.'net.com';
        }
        $code = input('param.code');
        $client = input('param.client');
        $posterShare = new PosterShare();
        echo $posterShare->poster($url, $code, $client);
    }

}
