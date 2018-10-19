<?php
namespace app\common\model;

use think\Db;

class UserWx extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';


    public function codeToInfo($code){
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        //根据code取openid和session_key
        $wx =   new \org\Wx();      //这里以后要根据seller_id的信息传入对应的微信小程序的参数

        $result = $wx->code_to_sessionkey(getSetting('wx_appid'),getSetting('wx_app_secret'),$code);

        if(!$result['status']){
            return $result;
        }
        $where['openid'] = $result['data']['openid'];
        $info = $this->where($where)->find();
        if($info){
            //更新session_key
            $this->save(['session_key'=>$result['data']['session_key']],$where);
        }else{
            $data['openid'] = $result['data']['openid'];
            $data['session_key'] = $result['data']['session_key'];
            $this->save($data);
        }
        $result['data'] = $result['data']['openid'];
        //unset($result['data']['session_key']);
        return $result;
    }

    //根据微信的信息，创建用户,但是没有登陆，没有手机号码，等他手机号码传过来后再登陆
    public function toCreate($openid,$edata,$iv){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $info = $this->where(['openid'=>$openid])->find();
        if(!$info){
            return error_code(11002);
        }
        //解密数据信息
        $wx =   new \org\Wx();
        $result = $wx->decrypt($edata,$iv,$info['session_key']);
        if(!$result['status']){
            return $result;
        }
        Db::startTrans();
        try {
            if(isset($result['data']['unionId'])){
                $data['unionid'] = $result['data']['unionId'];
            }
            $data['avatar'] = $result['data']['avatarUrl'];
            $data['nickname'] = $result['data']['nickName'];
            $data['gender'] = $result['data']['gender'];
            $data['language'] = $result['data']['language'];
            $data['city'] = $result['data']['city'];
            $data['province'] = $result['data']['province'];
            $data['country'] = $result['data']['country'];
            $this->save($data,['id'=>$info['id']]);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return [
                'status' => false,
                'data' => '',
                'msg' => $e->getMessage(),
            ];
        }
        return [
            'status' => true,
            'data' => '',
            'msg' => '',
        ];
    }
    //根据微信的信息，绑定手机号码
    public function bindMobile($openid,$edata,$iv,$pid){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];

        $info = $this->where(['openid'=>$openid])->find();
        if(!$info){
            return error_code(11002);
        }
        //解密数据信息
        $wx =   new \org\Wx();      //这里以后要根据seller_id的信息传入对应的微信小程序的参数
        $result = $wx->decrypt($edata,$iv,$info['session_key']);
        if(!$result['status']){
            return $result;
        }
        Db::startTrans();
        try {
            //根据手机号码去绑定或者创建用户
            $userModel = new User();
            $userInfo = $userModel->where(['mobile'=>$result['data']['purePhoneNumber']])->find();
            if($userInfo){
                //更新用户表
                $userModel = new User();
                $userData['mobile'] = $result['data']['purePhoneNumber'];
                $userModel->save($userData,['id'=>$userInfo['id']]);
                $data['user_id'] = $userInfo['id'];
            }else{
                //创建用户
//                $userData['sex'] = ($info['gender'] == '0')?3:$info['gender'];
//                $userData['avatar'] = $info['avatar'];
//                $userData['nickname'] = $info['nickname'];
                $userData['mobile'] = $result['data']['purePhoneNumber'];
                $userData['pid'] = $pid;
                $re = $userModel->thirdAdd($userData);
                if(!$re['status']){
                    return $re;
                }
                $data['user_id'] = $re['data'];
            }
            //把手机号码和国家区号存到微信用户表里
            $data['country_code'] = $result['data']['countryCode'];
            $data['mobile'] = $result['data']['purePhoneNumber'];
            $this->save($data,['id'=>$info['id']]);
            Db::commit();
            $result['status'] = true;
            $result['data']['user_id'] = $data['user_id'];
            return $result;
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
    }



}