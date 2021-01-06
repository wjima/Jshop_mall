<?php

namespace app\common\model;


class UserToken extends Common
{
    /**
     * 登陆存token
     * @param $user_id
     * @param int $platform     //1就是h5登陆（h5端和微信公众号端），2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc
     * @return array
     */
    public function setToken($user_id, $platform = 1)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $userModel = new User();
        $userInfo = $userModel->where(array('id' => $user_id))->find();
        if ($userInfo) {
            if ($userInfo['status'] == $userModel::STATUS_DISABLE) return error_code(11022);

            $data['user_id'] = $user_id;
            $data['platform'] = $platform;
            $data['ctime'] = time();
            $data['token'] = $this->algorithm($userInfo['id'], $userInfo['password'], $platform, $data['ctime']);
            $re = $this->save($data);
            if ($re > 0) {
                //删除掉旧的token
                $where[] = ['user_id', 'eq', $user_id];
                $where[] = ['platform', 'eq', $platform];
                $where[] = ['token', 'neq', $data['token']];
                $this->where($where)->delete();

                $result['data'] = $data['token'];
                $result['status'] = true;

                hook('loginAfter', $userInfo); //登录后事件

                //如果需要绑定手机号码，但是用户的手机号码为空，那么就需要绑定手机号码
                if (getSetting('is_bind_mobile') == 1 && !$userInfo['mobile']) {
                    $result['status'] = false;
                    $result['token'] = $data['token'];
                    $result['data'] = '11027';
                    $result['msg'] = error_code(11027, true);
                }
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
            //密码有效期半年
            if ($tokenInfo['ctime'] < time() - 60 * 60 * 24 * 180) {
                return error_code(11026);
            }
            $userModel = new User();
            $where1[] = ['id', 'eq', $tokenInfo['user_id']];
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
