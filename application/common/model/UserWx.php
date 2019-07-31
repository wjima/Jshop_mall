<?php
namespace app\common\model;

use org\Curl;
use think\Db;
use app\common\model\Images;

class UserWx extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';


    const TYPE_MINIPROGRAM = 1;         //类型1，微信小程序
    const TYPE_OFFICIAL = 2;            //类型2，微信公众号
    const TYPE_ALIPAY = 3;            //类型3，支付宝小程序
    const TYPE_UNIAPP_WEIXIN = 4;            //类型3，app微信快捷登录
    const TYPE_UNIAPP_QQ = 5;            //类型3，appQQ快捷登录


    //微信小程序登陆第一步，需要现在后台微信配置 小程序配置里面配置好参数
//    public function codeToInfo($code)
//    {
//        //根据code取openid和session_key
//        $wx = new \org\Wx();
//
//        $result = $wx->code_to_sessionkey(getSetting('wx_appid'), getSetting('wx_app_secret'), $code);
//
//        if (!$result['status']) {
//            return $result;
//        }
//        if (isset($result['data']['unionid']) && $result['data']['unionid']) {
//            $where['unionid'] = $result['data']['unionid'];
//        } else {
//            $where['openid'] = $result['data']['openid'];
//        }
//        $where['type'] = self::TYPE_MINIPROGRAM;
//        $info          = $this->where($where)->find();
//        if ($info) {
//            //更新session_key
//            $this->save(['session_key' => $result['data']['session_key']], $where);
//            $user_wx_id = $info['id'];
//        } else {
//            if (isset($result['data']['unionid']) && $result['data']['unionid']) {
//                $data['unionid'] = $result['data']['unionid'];
//            }
//            $data['type']        = self::TYPE_MINIPROGRAM;     //小程序类型
//            $data['openid']      = $result['data']['openid'];
//            $data['session_key'] = $result['data']['session_key'];
//            $this->save($data);
//            $user_wx_id = $this->id;
//        }
//        $result['data'] = $user_wx_id;
//        return $result;
//    }

    //微信小程序登陆第二步，根据微信端传过来的值解析用户数据,更新user_wx表
//    public function updateWxInfo($id, $edata, $iv,$pid = 0)
//    {
//        $info = $this->where(['id' => $id])->find();
//        if (!$info) {
//            return error_code(11002);
//        }
//        //解密数据信息
//        $wx     = new \org\Wx();
//        $result = $wx->decrypt($edata, $iv, $info['session_key']);
//        if (!$result['status']) {
//            return $result;
//        }
//        //加密信息里有openid或unionid，前台传过来的值查出来的数据里也有，需要判断是否一致，否则可能会有漏洞
//        if ($info['openid'] != $result['data']['openId'] && $info['unionid'] != $result['data']['unionId']) {
//            return error_code(10000);
//        }
//        if (isset($result['data']['unionId']) && $result['data']['unionId']) {
//            $where[]    = ['unionid', 'eq', $result['data']['unionId']];
//            $where[]    = ['user_id', 'neq', '0'];
//            $wxUserInfo = $this->where($where)->find();
//            if ($wxUserInfo) {
//                $info['user_id'] = $wxUserInfo['user_id'];
//                $data['user_id'] = $wxUserInfo['user_id'];
//            }
//        }
//        //有会员的情况下不更新头像
//
//        if (isset($info['avatar']) && !$info['avatar']) {
//            $imageModel                  = new Images();
//            $image                       = $imageModel->saveImage($result['data']['avatarUrl'], true);//头像都按统一方法保存到本地或者远程图片服务器
//            $result['data']['avatarUrl'] = isset($image['data']['id']) ? $image['data']['id'] : _sImage();
//        }
//
//        $data['type']     = self::TYPE_MINIPROGRAM;
//        $data['avatar']   = $result['data']['avatarUrl'];
//        $data['nickname'] = $result['data']['nickName'];
//        $data['gender']   = $result['data']['gender'];
//        $data['language'] = $result['data']['language'];
//        $data['city']     = $result['data']['city'];
//        $data['province'] = $result['data']['province'];
//        $data['country']  = $result['data']['country'];
//        $this->save($data, ['id' => $info['id']]);
//
//
//        //如果是新用户，并且不需要绑定手机号码的话，就创建用户
//        if($info['user_id'] == 0 && getSetting('is_bind_mobile') == '2'){
//            $user['nickname'] = $data['nickname'];
//            $user['avatar'] = $data['avatar'];
//            $user['sex'] = $data['gender'];
//            $user['pid'] = $pid;
//
//            $userModel = new \app\common\model\User();
//            $user_re = $userModel->manageAdd($user);
//            if(!$user_re['status']){
//                return $user_re;
//            }
//            //$re['data']['user_id'] = $user_re['data'];
//            //这时候还需要把新的user_id绑定到user_wx表上，否则就每次登陆都用新用户了
//
//            $this->user_id = $user_re['data'];
//            $this->save();
//            $info['user_id'] = $user_re['data'];
//        }
//
//
//        //判断当前记录是否有user_id ,如果等于0，就是新用户，否则就是老用户
//        return [
//            'status' => true,
//            'data'   => [
//                'id'      => $info['id'],
//                'user_id' => $info['user_id']
//            ],
//            'msg'    => ''
//        ];
//    }


    //微信第三登陆根据微信的信息，创建用户,但是没有登陆，没有手机号码，等他手机号码传过来后再登陆
    public function toAddWx($params, $type = self::TYPE_OFFICIAL)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        try {
            $data['type'] = $type;            //类型
            if (isset($params['unionId'])) {
                $data['unionid'] = $params['unionId'];
            }
            if (isset($params['user_id']) && $params['user_id']) {
                $data['user_id'] = $params['user_id'];
            } else {
                $imageModel     = new Images();
                $image          = $imageModel->saveImage($params['avatar'], true);//头像都按统一方法保存到本地或者远程图片服务器
                $data['avatar'] = isset($image['data']['id']) ? $image['data']['id'] : _sImage();
            }
            $data['openid']   = $params['openid'];
            $data['nickname'] = $params['nickName'];
            $data['gender']   = $params['gender'];
            $data['language'] = $params['language'];
            $data['city']     = $params['city'];
            $data['province'] = $params['province'];
            $data['country']  = $params['country'];

            //如果是新用户，并且不需要绑定手机号码的话，就创建用户
            if(getSetting('is_bind_mobile') == '2'){
                $user['nickname'] = $data['nickname'];
                $user['avatar'] = $data['avatar'];
                $user['sex'] = $data['gender'];
                //$user['pid'] = $pid;

                $userModel = new \app\common\model\User();
                $user_re = $userModel->manageAdd($user);
                if(!$user_re['status']){
                    return $user_re;
                }
                //这时候还需要把新的user_id绑定到user_wx表上，否则就每次登陆都用新用户了
                $data['user_id'] = $user_re['data'];
            }

            $this->save($data);
            $result['status'] = true;
            $result['data']   = [
                'id' => $this->id,
                'user_id' => $this->user_id
            ];

        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();
            return $result;
        }
        return $result;
    }

    /**
     * 待废除
     * 支付宝code解析获取用户授权
     */
    public function alipayCodeToInfo($code)
    {
        $result     = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        $alipay     = new Alipay();
        $alipayInfo = getAddonsConfig('MPAlipay');
        if (!$alipayInfo) {
            $result['msg'] = '请先安装支付宝小程序插件';
            return $result;
        }
        $result = $alipay->code_to_token($alipayInfo['mp_alipay_appid'], $code);
        if (!$result['status']) {
            return $result;
        }
        if (isset($result['data']['alipay_user_id']) && $result['data']['alipay_user_id']) {
            $where['unionid'] = $result['data']['alipay_user_id'];
        }
        $where['openid'] = $result['data']['user_id'];
        $where['type']   = self::TYPE_ALIPAY;
        $info            = $this->where($where)->find();
        if ($info) {
            //更新session_key
            $this->save(['session_key' => $result['data']['access_token']], $where);
            $user_alipay_id = $info['id'];
            if ($info['user_id']) {
                //绑定好手机号码了，去登陆,去取user_token
                $userTokenModel = new UserToken();
                $re             = $userTokenModel->setToken($info['user_id'], 3);
                if ($re['status']) {
                    $result['data'] = ['token' => $re['data']];
                }
            }
        } else {
            $userinfo = $alipay->get_user_info($alipayInfo['mp_alipay_appid'], $result['data']['access_token']);
            if (!$userinfo['status']) {
                return $userinfo;
            }
            if (isset($result['data']['alipay_user_id']) && $result['data']['alipay_user_id']) {
                $data['unionid'] = $result['data']['alipay_user_id'];
            }
            $data['type']        = self::TYPE_ALIPAY;     //小程序类型
            $data['openid']      = $result['data']['user_id'];
            $data['session_key'] = $result['data']['access_token'];

            $data['avatar']   = $userinfo['data']['avatar'];
            $data['openid']   = $userinfo['data']['openid'];
            $data['nickname'] = $userinfo['data']['nickname'];
            $data['gender']   = $userinfo['data']['gender'];
            $data['language'] = $userinfo['data']['language'];
            $data['city']     = $userinfo['data']['city'];
            $data['province'] = $userinfo['data']['province'];
            $data['country']  = $userinfo['data']['country'];
            $this->save($data);
            $user_alipay_id = $this->id;
        }
        $result['data']['user_wx_id'] = $user_alipay_id;
        return $result;
    }

    //第三方登录保存&创建记录，并判断是否手机号码绑定，并返回前端最终状态
    public  function toAdd($data,$pid){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        if(isset($data['id'])){
            $this->save($data, ['id' => $data['id']]);
            $id = $data['id'];
        }else{
            //如果是新用户，并且外面没有传进来user_id的话，这里就赋个初始值
            if(!isset($data['user_id'])){
                $data['user_id'] = 0;
            }
            $this->save($data);
            $id = $this->id;
        }
        $info = self::get($id);


        //如果是新用户，并且不需要绑定手机号码的话，就创建用户
        if($info->user_id == 0 && getSetting('is_bind_mobile') == '2'){
            $user['nickname'] = $data['nickname'];
            $user['avatar'] = $data['avatar'];
            $user['sex'] = $data['gender'];
            $user['pid'] = $pid;

            $userModel = new \app\common\model\User();
            $user_re = $userModel->manageAdd($user);
            if(!$user_re['status']){
                return $user_re;
            }

            $info->user_id = $user_re['data'];
            $info->save();

        }

        //到这里，如果没有用户id，就需要去绑定手机号码了。

        if ($info->user_id == 0) {
            //未绑定用户，需要先绑定手机号码
            $result['status'] = true;
            $result['data']   = [
                'user_wx_id' => $info->id
            ];
            return $result;
        } else {
            //绑定好手机号码了，去登陆,去取user_token
            $userTokenModel = new UserToken();
            $re             = $userTokenModel->setToken($info->user_id, 2);
            if (!$re['status']) {
                return $re;
            }
            $result['status'] = true;
            $result['data'] = [
                'token' => $re['data']
            ];
        }
        return $result;
    }


    //公众号无感登陆
    public function officialMiniLogin($url){
        $result     = [
            'status' => false,
            'data'   => 10066,
            'msg'    => ''
        ];
        $url = urlencode($url);
        $result['msg'] =  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".getSetting('wx_official_appid')."&redirect_uri=".$url."&response_type=code&scope=snsapi_base&state=jshop#wechat_redirect";
        return $result;
    }
    public function officialMiniLogin2($code){
        $result     = [
            'status' => true,
            'data'   => '',
            'msg'    => ''
        ];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".getSetting('wx_official_appid')."&secret=".getSetting('wx_official_app_secret')."&code=".$code."&grant_type=authorization_code";
        $curl = new Curl();
        $re = $curl->get($url);
        $data = json_decode($re,true);
        if(!isset($data['openid'])){
            return error_code(10000);
        }
        $result['data'] = $data['openid'];
        return $result;
    }


}