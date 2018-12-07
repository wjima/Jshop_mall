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
        if(isset($result['data']['unionid']) && $result['data']['unionid']){
            $where['unionid'] = $result['data']['unionid'];
        }else{
            $where['openid'] = $result['data']['openid'];
        }
        $where['type'] = self::TYPE_MINIPROGRAM;
        $info = $this->where($where)->find();
        if($info){
            //更新session_key
            $this->save(['session_key'=>$result['data']['session_key']],$where);
        }else{
            if(isset($result['data']['unionid']) && $result['data']['unionid']){
                $data['unionid'] = $result['data']['unionid'];
            }
            $data['type'] = self::TYPE_MINIPROGRAM;     //小程序类型
            $data['openid'] = $result['data']['openid'];
            $data['session_key'] = $result['data']['session_key'];
            $this->save($data);
        }
        $result['data'] = $result['data']['openid'];
        //unset($result['data']['session_key']);
        return $result;
    }

    //微信小程序登陆第二步，根据微信端传过来的值解析用户数据,更新user_wx表
    public function updateWxInfo($openid,$edata,$iv,$pid){
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
        //加密信息里有openid或unionid，前台传过来的值查出来的数据里也有，需要判断是否一致，否则可能会有漏洞
        if($info['openid'] != $result['data']['openId'] && $info['unionid'] != $result['data']['unionId']){
            return error_code(10000);

        }
        //更新微信信息
        $data['avatar'] = $result['data']['avatarUrl'];
        $data['nickname'] = $result['data']['nickName'];
        $data['gender'] = $result['data']['gender'];
        $data['language'] = $result['data']['language'];
        $data['city'] = $result['data']['city'];
        $data['province'] = $result['data']['province'];
        $data['country'] = $result['data']['country'];
        $this->save($data,['id'=>$info['id']]);
        //判断当前记录是否有user_id ,如果有，就是老用户，否则就是新用户
        return [
            'status' => true,
            'data' => [
                'id' => $info['id'],
                'user_id' => $info['user_id']
            ],
            'msg' => ''
        ];
    }


    //微信第三登陆根据微信的信息，创建用户,但是没有登陆，没有手机号码，等他手机号码传过来后再登陆
    public function toAddWx($params)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
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
        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();
            return $result;
        }
        return $result;
    }
}