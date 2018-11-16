<?php
namespace app\common\model;

use think\Db;

class UserWx extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';


    const TYPE_MINIPROGRAM = 1;         //类型1，微信小程序
    const TYPE_OFFICIAL = 2;            //类型2，微信公众号


    //微信小程序登陆第一步，需要现在后台微信配置 小程序配置里面配置好参数
    public function codeToInfo($code){
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        //根据code取openid和session_key
        $wx = new \org\Wx();

        $result = $wx->code_to_sessionkey(getSetting('wx_appid'),getSetting('wx_app_secret'),$code);

        if(!$result['status']){
            return $result;
        }
        $where['openid'] = $result['data']['openid'];
        $where['type'] = self::TYPE_MINIPROGRAM;
        $info = $this->where($where)->find();
        if($info){
            //更新session_key
            $this->save(['session_key'=>$result['data']['session_key']],$where);
        }else{
            $data['type'] = self::TYPE_MINIPROGRAM;     //小程序类型
            $data['openid'] = $result['data']['openid'];
            $data['session_key'] = $result['data']['session_key'];
            $this->save($data);
        }
        $result['data'] = $result['data']['openid'];
        //unset($result['data']['session_key']);
        return $result;
    }

    //根据微信的信息，创建用户,但是没有登陆，没有手机号码，等他手机号码传过来后再登陆,此方法是调用微信小程序账号登陆的时候调用的方法，暂时没用，现在小程序是直接获取手机号码来登陆
//    public function toCreate($openid,$edata,$iv){
//        $result = [
//            'status' => false,
//            'data' => '',
//            'msg' => ''
//        ];
//        $info = $this->where(['openid'=>$openid])->find();
//        if(!$info){
//            return error_code(11002);
//        }
//        //解密数据信息
//        $wx =   new \org\Wx();
//        $result = $wx->decrypt($edata,$iv,$info['session_key']);
//        if(!$result['status']){
//            return $result;
//        }
//        Db::startTrans();
//        try {
//            if(isset($result['data']['unionId'])){
//                $data['unionid'] = $result['data']['unionId'];
//            }
//            $data['avatar'] = $result['data']['avatarUrl'];
//            $data['nickname'] = $result['data']['nickName'];
//            $data['gender'] = $result['data']['gender'];
//            $data['language'] = $result['data']['language'];
//            $data['city'] = $result['data']['city'];
//            $data['province'] = $result['data']['province'];
//            $data['country'] = $result['data']['country'];
//            $this->save($data,['id'=>$info['id']]);
//            Db::commit();
//        } catch (\Exception $e) {
//            Db::rollback();
//            return [
//                'status' => false,
//                'data' => '',
//                'msg' => $e->getMessage(),
//            ];
//        }
//        return [
//            'status' => true,
//            'data' => '',
//            'msg' => '',
//        ];
//    }
    //微信小程序登陆第二步，根据微信端传过来的值解析用户数据，，绑定手机号码
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
                $data['user_id'] = $userInfo['id'];
            }else{
                //创建用户
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


    //微信第三登陆根据微信的信息，创建用户,但是没有登陆，没有手机号码，等他手机号码传过来后再登陆
    public function toAddWx($params)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        Db::startTrans();
        try {
            $data['type'] = self::TYPE_OFFICIAL;            //公众号类型
            if (isset($params['unionId'])) {
                $data['unionid'] = $params['unionId'];
            }
            $data['avatar'] = $params['avatar'];
            $data['openid'] = $params['openid'];
            $data['nickname'] = $params['nickName'];
            $data['gender'] = $params['gender'];
            $data['language'] = $params['language'];
            $data['city'] = $params['city'];
            $data['province'] = $params['province'];
            $data['country'] = $params['country'];
            $this->save($data);
            $result['status'] = true;
            $result['data']['id'] = $this->getLastInsID();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
        return $result;
    }
}