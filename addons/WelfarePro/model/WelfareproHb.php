<?php

namespace addons\WelfarePro\model;
use app\common\model\Common;
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
        if(isset($data['user_id'])){
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
            $hbuserModel->where('hb_id',$data['id'])->delete();
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
        $hbuserModel->where('hb_id',$id)->delete();
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
}
