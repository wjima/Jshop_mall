<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace addons\MiniManage\model;

use app\common\model\Manage;
use think\Model;



/**
 * 同步日志
 * Class Api
 * @package addons\MiniManage\model
 */
class AdminToken extends Model
{
    function tologin($data){
        $result = array(
            'status' => false,
            'data'   => '',
            'msg'    => ''
        );
        if (!isset($data['mobile']) || !isset($data['password'])) {
//            $result['msg'] = '请输入手机号码或者密码';
            return error_code(11031);
        }
        $Model = new Manage();
        $userInfo = $Model->where(array('username' => $data['mobile']))->whereOr(array('mobile' => $data['mobile']))->find();

        //判断账号状态
        if ($userInfo["status"] != 1) {
            return error_code(11022);
        }
        //判断是否是用户名登陆
        $userInfo = $Model->where(array('username|mobile' => $data['mobile'], 'password' => $this->enPassword($data['password'], $userInfo->ctime)))->find();
        if($userInfo){
            return $this->setToken($userInfo["id"],$data["platform"]);
        }else{
            return error_code(11033);
        }
    }

    /**
     * 密码加密方法
     * @param string $pw 要加密的字符串
     * @return string
     */
    private function enPassword($password, $ctime)
    {

        return md5(md5($password) . $ctime);
    }
    public function setToken($admin_id, $platform = 1)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $userModel = new Manage();
        $userInfo = $userModel->where(array('id' => $admin_id))->find();
        if ($userInfo) {
            if ($userInfo['status'] == 2) return error_code(11022);

            $data['admin_id'] = $admin_id;
            $data['platform'] = $platform;
            $data['ctime'] = time();
            $data['token'] = $this->algorithm($userInfo['id'], $userInfo['password'], $platform, $data['ctime']);
            $re = $this->save($data);
            if ($re > 0) {
                //删除掉旧的token
                $where[] = ['admin_id', 'eq', $admin_id];
                $where[] = ['platform', 'eq', $platform];
                $where[] = ['token', 'neq', $data['token']];
                $this->where($where)->delete();
                $result['data'] = $data['token'];
                $result['status'] = true;
                return $result;
            } else {
                return error_code(14005);
            }
        } else {
            return error_code(11004);
        }
    }

    public function delToken($token)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        //删除掉旧的token
        //$where[] = ['user_id', 'eq', $user_id];
        $where[] = ['token', 'eq', $token];
        if ($this->where($where)->delete()) {
            $result['status'] = true;
        }
        return $result;
    }


    /**
     * 根据token来获取用户的id
     * @param $token //token的值
     * @param int $status //用户状态，0是所有状态，1是取正常的用户状态
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkToken($token, $status = 1)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if (!$token) {
            return error_code(14006);
        }
        $where[] = ['token', 'eq', $token];
        $tokenInfo = $this->where($where)->cache(true)->find();
        if ($tokenInfo) {
            $info = $this->where("admin_id","eq",$tokenInfo["admin_id"])->order("ctime desc")->find();
            if($info["token"] != $token){
                return error_code(11026);
            }
            //密码有效期半年
            if ($tokenInfo['ctime'] < time() - 60 * 60 * 24 * 3) {
                return error_code(11026);
            }
            $userModel = new Manage();
            $where1[] = ['id', 'eq', $tokenInfo['admin_id']];
            $userInfo = $userModel->where($where1)->find();
            if (!$userInfo) {
                return error_code(11004);
            }
            if ($status == 1 && $userInfo['status'] != 1) {
                return error_code(11022);
            }
            $result['status'] = true;
            $result['data'] = $tokenInfo;
            return $result;
        } else {
            return error_code(14016);
        }
    }
    private function algorithm($user_id, $password, $platform, $createtime)
    {
        return md5(md5($user_id . $password . $platform . $createtime) . rand(1, 10000));
    }
}