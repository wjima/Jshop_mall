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
use org\Backup;

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

        /**
     * 定时还原
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-02-20
     * @return void
     */
    public function reset()
    {
        $type = input('get.type/s', '');
        $config = array(
            'path'     => './../word/back/', //数据库备份路径
            'part'     => 20971520, //数据库备份卷大小
            'compress' => 0, //数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 9 //数据库备份文件压缩级别 1普通 4 一般  9最高
        );
        // $table_name = 'jshop_addons';
        $db = new Backup($config);
        switch ($type) {
            case 'import':
                $start = 0;
                $file = ['name' => '20210222-155441', 'part' => 1];
                $array = $db->setTimeout(0)->setFile($file)->import($start);
                while ($array[0]) {
                    $array = $db->setTimeout(0)->setFile($file)->import($array[0]);
                }
                return getTime(time()) . '还原标准版数据库成功';
                break;
                // case 'backup':
                //     // $file = ['name' => date('Ymd-His'), 'part' => 1];
                //     $tables = $db->dataList(); //获取数据库所有表的信息
                //     foreach ($tables as $k => $v) {
                //         $db->backup($v['name'], 0); //循环所有表备份表和数据
                //     }
                //     break;
            case 'filelist':
                dump($db->fileList());
                die;
                break;
            case 'datalist':
                dump($db->dataList());
                break;
                // case 'delFile':
                // 删除备份文件
                //     dump($db->delFile($time));
                //     break;
                // case 'downloadFile':
                // 下载备份文件
                //     dump($db->downloadFile($time));
                //     break;
                // case 'repair':
                // 修复表
                //     dump($db->repair($table_name));
                //     break;
                // case 'optimize':
                // 优化表
                //     dump($db->optimize($table_name));
                //     break;
            default:
                return '哈哈哈哈哈';
                break;
        }
        die;
    }
}
