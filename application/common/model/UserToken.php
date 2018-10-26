<?php
namespace app\common\model;


class UserToken extends Common
{
    /**
     * 登陆存token
     * @param $user_id
     * @param int $platform     如果是1，就是前端普通登陆，如果是2就是微信小程序登陆
     * @return array
     */
    public function setToken($user_id,$platform=1){
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $userModel = new User();
        $userInfo = $userModel->where(array('id'=>$user_id))->find();
        if($userInfo){
            $data['user_id'] = $user_id;
            $data['platform'] = $platform;
            $data['ctime'] = time();
            $data['token'] = $this->algorithm($userInfo['id'],$userInfo['password'],$platform,$data['ctime']);
            $re = $this->save($data);
            if($re >0){
                //删除掉旧的token
                $where[] = ['user_id', 'eq', $user_id];
                $where[] = ['platform', 'eq', $platform];
                $where[] = ['token', 'neq', $data['token']];
                $this->where($where)->delete();

                $result['data'] = $data['token'];
                $result['status'] = true;


                return $result;
            }else{
                $result['msg'] = "生成token失败";
                return $result;
            }
        }else{
            if(!$userInfo){
                $result['msg'] = "用户不存在";
            }
            return $result;
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
        if($this->where($where)->delete()){
            $result['status'] = true;
        }
        return $result;
    }
    public function checkToken($token){
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        $where[] = ['token', 'eq', $token];
        $where[] = ['ctime', 'gt', time()-60*60*24*180];     //有效期180天
        $tokenInfo = $this->where($where)->find();
        if($tokenInfo){
            $result['status'] = true;
            $result['data'] = $tokenInfo;
            return $result;
        }else{
            $result['msg'] = "不是有效的token";
            return $result;
        }

    }
    private function algorithm($user_id,$password,$platform,$createtime){
        return md5(md5($user_id.$password.$platform.$createtime).rand(1,10000));
    }
}