<?php

namespace addons\WelfarePro\model;
use addons\WelfarePro\lib\Hb;
use app\common\model\Common;
use app\common\model\User;
use app\common\model\UserWx;
use think\Model;
class WelfareproHb extends Common
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    public function toAdd($data){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if(!isset($data['money_all']) || !isset($data['money_start']) || !isset($data['money_end']) || !isset($data['date']) || !isset($data['type'])){
            $result['msg'] = "必填项不能为空";
            return $result;
        }
        //如果是方法，code换成小写
        $theDate = explode(' 到 ', input('param.date'));
        if (count($theDate) != 2) {
            return error_code(10000);
        }
        $data['date_start'] = strtotime($theDate[0]);
        $data['date_end'] = strtotime($theDate[1]);
        //如果选择了用户，那么就解析成一维数组
        if(isset($data['user_id']) && !empty($data['user_id'])){
            $user = explode(',',$data['user_id']);
        }else{
            $user = [0];
        }

        //判断金额
        if($data['money_all'] < 100 || $data['money_start'] < 100 || $data['money_end'] < 100){
            $result['msg'] = "金额不能小于100分";
            return $result;
        }

        $hbuserModel= new WelfareproHbuser();
        if($data['id'] != ""){
            $result = $this->checkUser($user,$data['id']);
            if(!$result['status']){
                return $result;
            }
            $this->save($data,['id'=>$data['id']]);
            //清空老的用户关系
            $hbuserModel->where('hb_id',$data['id'])->where('type',1)->delete();
            //保存用户
            $data = [];
            foreach($user as $v){
                $data[] = [
                    'hb_id' => $this->id,
                    'user_id' => $v
                ];
            }
            $hbuserModel->saveAll($data);
            $result['status'] = true;
            return $result;

        }else{
            $result = $this->checkUser($user);
            if(!$result['status']){
                return $result;
            }
            $this->save($data);
            //保存用户
            $data = [];
            foreach($user as $v){
                $data[] = [
                    'hb_id' => $this->id,
                    'user_id' => $v
                ];
            }
            $hbuserModel->saveAll($data);
            $result['status'] = true;
            return $result;
        }

    }

    public function toDel($id){
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        $this->where('id',$id)->delete();
        $hbuserModel= new WelfareproHbuser();
        $hbuserModel->where('hb_id',$id)->where('type',1)->delete();
        return $result;
    }

    //校验用户是否添加过红包活动
    private function checkUser($user_arr,$hb_id = 0){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $m = new WelfareproHbuser();
        foreach($user_arr as $v){
            $where = [];
            if($hb_id != 0){
                $where[] = ['hb_id', '<>', $hb_id];
            }
            $where[] = ['type','=',1];
            $where[] = ['user_id', '=', $v];
            $info = $m->where($where)->find();
            if($info){
                $result['msg'] = "用户ID：".$v."已经参加活动了";
                return $result;
            }
        }
        $result['status'] = true;

        return $result;
    }

    protected function tableFormat($list)
    {
        $type = [
            1 => '全部用户',
            2 => '新用户',
        ];
        $hbuserModel= new WelfareproHbuser();
        foreach ($list as $k => $v) {
            if ($v['type']) {
                $list[$k]['type_name'] = $type[$v['type']];
            }
            if ($v['date_start']) {
                $list[$k]['date_start'] = getTime($v['date_start']);
            }
            if ($v['date_end']) {
                $list[$k]['date_end'] = getTime($v['date_end']);
            }
            if ($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
            if ($v['utime']) {
                $list[$k]['utime'] = getTime($v['utime']);
            }
            //取用户id

            $hb_users = $hbuserModel->where('hb_id',$v['id'])->select()->toArray();
            if($hb_users && $hb_users[0]['user_id'] == 0){
                unset($hb_users[0]);
            }
            $list[$k]['user_id'] = implode(',',array_column($hb_users,'user_id'));
        }
        return $list;
    }

    //判断某人是否有发红包的活动

    /**
     * @param $userShareCode
     * @param bool $is_new 是否是新用户，如果是只限于新用户，那么就传true，默认是false，不传
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userHb($tj_user_id){

        //取这个人的红包活动
        $where = [];
        $where[] = ['hb.date_start', '<',time()];
        $where[] = ['hb.date_end', '>', time()];
        $where[] = ['hbu.user_id', 'in', [$tj_user_id,0]];

        $m = new WelfareproHb();
        $info = $m
            ->alias('hb')
            ->join('welfarepro_hbuser hbu', 'hbu.hb_id = hb.id')
            ->where($where)
            ->find();

        return $info;
    }

    /**
     * 发放红包
     * @param $user_id      领取人
     * @param $info         红包记录
     */
    public function sendHb($user_id,$userShareCode){
        $result = [
            'status' => false,
            'data' => "",
            'msg' => ''
        ];

        //注册时间到现在小于10分钟的都算新用户
        //判断用户是否是新用户
        $userModel = new User();
        $userInfo = $userModel->where('id',$user_id)->find();
        if(!$userInfo){
            return error_code(10000);
        }
        if(time() - $userInfo['ctime'] > 600){
            $is_new = false;
        }else{
            $is_new = true;     //新用户
        }
        $userModel = new User();
        $tj_user_id = $userModel->getUserIdByShareCode($userShareCode);

        $info = $this->userHb($tj_user_id);
        if(!$info){
            $result['msg'] == "没有活动或者已经结束";
            return $result;
        }

        //判断是否是限于新用户参加
        if($info['type'] == 2 && $is_new){
            $result['msg'] == "没有活动或者已经结束。";         //只限于新用户参与
            return $result;
        }
        //判断是否领取过，
        $hblogModel = new WelfareproHblog();
        $where = [
            ['user_id', '=', $user_id],
            ['tj_user_id', '=', $tj_user_id],
            ['hb_id', '=', $info['id']]
        ];
        if($hblogModel->where($where)->find()){
            $result['msg'] = '您已经领取，不能重复领取';
            return $result;
        }
        //去发券
        return $this->sendHb2($user_id,$info,$tj_user_id);
    }

    /**
     * 实际取发红包
     */
    private function sendHb2($user_id,$info,$tj_user_id){
        $result = [
            'status' => false,
            'data' => "",
            'msg' => ''
        ];
        $hb = new Hb();
        //取openid
        $userWxModel = new UserWx();
        $where[] = ['user_id', '=', $user_id];
        $where[] = ['type', '=', 2];
        $userwxInfo = $userWxModel->where($where)->find();
        if(!$userwxInfo){
            $result['data'] = "请在微信内扫码登陆";
            return $result;
        }
        $openid = $userwxInfo['openid'];

        $money = mt_rand($info['money_start'] , $info['money_end']);
        $result = $hb->send($openid,$money,$info['id']);

        if(!$result['status']){
            return $result;
        }
        //保存到日志表
        $hblog = new WelfareproHblog();
        $data = [
            'user_id' => $user_id,
            'tj_user_id' => $tj_user_id,
            'money' => $money,
            'hb_id' => $info['id'],
        ];
        $hblog->save($data);
        $result['status'] = true;
        $result['msg'] = '领取成功';

        return $result;
    }

}
