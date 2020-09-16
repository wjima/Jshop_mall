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
    const TYPE_TOUTIAO = 6;             //头条小程序

    /**
     * 根据unionid获取用户ID
     *  wgg
     *  1490100895@qq.com
     * @param string $unionid
     * @return int
     */
    private function getUserIdByUnionid($unionid)
    {
        $info = $this->where([
            ['unionid', '=', $unionid],
            ['type', 'in', [self::TYPE_MINIPROGRAM, self::TYPE_OFFICIAL]],
            ['user_id', 'neq', '0']
        ])->find();
        if (!$info) return 0;
        return $info['user_id'];
    }

    //第三方登录保存&创建记录，并判断是否手机号码绑定，并返回前端最终状态
    public function toAdd($data, $pid)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        // 如果是微信小程序或公众号登录，且有绑定开发平台
        if (in_array($data['type'], [self::TYPE_MINIPROGRAM, self::TYPE_OFFICIAL]) && $data['unionid']) {
            // 且当前的第三方账号没有绑定用户ID
            if (!isset($data['user_id']) || $data['user_id'] == '0') {
                // 则根据union_id 取是否其他方式登录过
                $data['user_id'] = $this->getUserIdByUnionid($data['unionid']);
            }
        }

        if (isset($data['id'])) {
            $this->save($data, ['id' => $data['id']]);
            $id = $data['id'];
        } else {
            //如果是新用户，并且外面没有传进来user_id的话，这里就赋个初始值
            if (!isset($data['user_id'])) {
                $data['user_id'] = 0;
            }
            $this->save($data);
            $id = $this->id;
        }
        $info = self::get($id);
        $user['pid'] = $pid;
        //如果是新用户，并且不需要绑定手机号码的话，就创建用户
        if ($info->user_id == 0 && getSetting('is_bind_mobile') == '2') {
            $user['nickname'] = $data['nickname'];
            $user['avatar']   = $data['avatar'];
            $user['sex']      = $data['gender'];
            $userModel        = new \app\common\model\User();
            $user_re          = $userModel->manageAdd($user);
            if (!$user_re['status']) {
                return $user_re;
            }
            $info->user_id = $user_re['data'];
            $info->save();
        }

        //到这里，如果没有用户id，就需要去绑定user了。
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
            $result['data']   = [
                'token' => $re['data']
            ];
        }
        return $result;
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['gender']) && $post['gender'] != "") {
            $where[] = ['gender', 'eq', $post['gender']];
        }
        if (isset($post['nickname']) && $post['nickname'] != "") {
            $where[] = ['nickname', 'like', '%' . $post['nickname'] . '%'];
        }
        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', 'eq', $post['type']];
        }
        if (isset($post['user_mobile']) && $post['user_mobile'] != "") {
            $pwhere[] = ['mobile|username', 'like', "%" . $post['user_mobile'] . "%"];
            $userModel = new User();
            $user      = $userModel->field('id')->where($pwhere)->select();
            if (!$user->isEmpty()) {
                $user = array_column($user->toArray(), 'id');
                $where[] = ['user_id', 'in', $user];
            } else {
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }
        if (isset($post['openid']) && $post['openid'] != "") {
            $where[] = ['openid', 'like', '%' . $post['openid'] . '%'];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "id desc";
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if (isset($v['gender'])) {
                $list[$k]['gender'] = config('params.user_wx')['gender'][$v['gender']];
            }
            $list[$k]['user_name'] = get_user_info($v['user_id'], 'showname');
            if ($v['type']) {
                $list[$k]['type_name'] = config('params.user_wx')['type'][$v['type']];
            }
            if ($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
            if ($v['utime']) {
                $list[$k]['utime'] = getTime($v['utime']);
            }
            if (isset($v['avatar']) && $v['avatar']) {
                $list[$k]['avatar'] = _sImage($v['avatar']);
            }
        }
        return $list;
    }
}
