<?php
namespace app\common\model;

use org\Alipay;
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


    //微信小程序登陆第一步，需要现在后台微信配置 小程序配置里面配置好参数
    public function codeToInfo($code)
    {
        //根据code取openid和session_key
        $wx = new \org\Wx();

        $result = $wx->code_to_sessionkey(getSetting('wx_appid'), getSetting('wx_app_secret'), $code);

        if (!$result['status']) {
            return $result;
        }
        if (isset($result['data']['unionid']) && $result['data']['unionid']) {
            $where['unionid'] = $result['data']['unionid'];
        } else {
            $where['openid'] = $result['data']['openid'];
        }
        $where['type'] = self::TYPE_MINIPROGRAM;
        $info          = $this->where($where)->find();
        if ($info) {
            //更新session_key
            $this->save(['session_key' => $result['data']['session_key']], $where);
            $user_wx_id = $info['id'];
        } else {
            if (isset($result['data']['unionid']) && $result['data']['unionid']) {
                $data['unionid'] = $result['data']['unionid'];
            }
            $data['type']        = self::TYPE_MINIPROGRAM;     //小程序类型
            $data['openid']      = $result['data']['openid'];
            $data['session_key'] = $result['data']['session_key'];
            $this->save($data);
            $user_wx_id = $this->id;
        }
        $result['data'] = $user_wx_id;
        return $result;
    }

    //微信小程序登陆第二步，根据微信端传过来的值解析用户数据,更新user_wx表
    public function updateWxInfo($id, $edata, $iv)
    {
        $info = $this->where(['id' => $id])->find();
        if (!$info) {
            return error_code(11002);
        }
        //解密数据信息
        $wx     = new \org\Wx();
        $result = $wx->decrypt($edata, $iv, $info['session_key']);
        if (!$result['status']) {
            return $result;
        }
        //加密信息里有openid或unionid，前台传过来的值查出来的数据里也有，需要判断是否一致，否则可能会有漏洞
        if ($info['openid'] != $result['data']['openId'] && $info['unionid'] != $result['data']['unionId']) {
            return error_code(10000);
        }
        if (isset($result['data']['unionId']) && $result['data']['unionId']) {
            $where[]    = ['unionid', 'eq', $result['data']['unionId']];
            $where[]    = ['user_id', 'neq', '0'];
            $wxUserInfo = $this->where($where)->find();
            if ($wxUserInfo) {
                $info['user_id'] = $wxUserInfo['user_id'];
                $data['user_id'] = $wxUserInfo['user_id'];
            }
        }
        //有会员的情况下不更新头像

        if (isset($info['avatar']) && !$info['avatar']) {
            $imageModel                  = new Images();
            $image                       = $imageModel->saveImage($result['data']['avatarUrl'], true);//头像都按统一方法保存到本地或者远程图片服务器
            $result['data']['avatarUrl'] = isset($image['data']['id']) ? $image['data']['id'] : _sImage();
        }

        $data['type']     = self::TYPE_MINIPROGRAM;
        $data['avatar']   = $result['data']['avatarUrl'];
        $data['nickname'] = $result['data']['nickName'];
        $data['gender']   = $result['data']['gender'];
        $data['language'] = $result['data']['language'];
        $data['city']     = $result['data']['city'];
        $data['province'] = $result['data']['province'];
        $data['country']  = $result['data']['country'];
        $this->save($data, ['id' => $info['id']]);
        //判断当前记录是否有user_id ,如果有，就是老用户，否则就是新用户
        return [
            'status' => true,
            'data'   => [
                'id'      => $info['id'],
                'user_id' => $info['user_id']
            ],
            'msg'    => ''
        ];
    }


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
            $this->save($data);
            $result['status'] = true;
            $result['data']   = [
                'id' => $this->id
            ];
        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();
            return $result;
        }
        return $result;
    }

    /**
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

}