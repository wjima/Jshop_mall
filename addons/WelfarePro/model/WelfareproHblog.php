<?php

namespace addons\WelfarePro\model;
use app\common\model\Common;
use app\common\model\User;
use think\Model;
class WelfareproHblog extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';

    protected function tableWhere($post)
    {

        $where = [];

        if (isset($post['hb_id']) && $post['hb_id'] != "") {
            $where[] = ['hb_id', 'eq', $post['hb_id']];
        }
        if (isset($post['user_name']) && $post['user_name'] != "") {
            $where[] = ['user_id', 'in', $this->getUserId($post['user_name'])];
        }
        if (isset($post['tj_user_name']) && $post['tj_user_name'] != "") {
            $where[] = ['tj_user_id', 'in', $this->getUserId($post['tj_user_name'])];
        }
        if (input('?param.date')) {
            $theDate = explode(' 到 ', input('param.date'));
            if (count($theDate) == 2) {
                $where[] = ['ctime', '>', strtotime($theDate[0])];
                $where[] = ['ctime', '<=', strtotime($theDate[1])];
            }
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id' => 'desc']; //默认最新添加靠前，排序越小越靠前
        return $result;
    }

    private function getUserId($name){
        $userModel = new User();
        $where = [
            ['mobile|username', 'like', '%'.$name.'%']
        ];
        $info = $userModel->where($where)->select();
        if($info->isEmpty()){
            return "9999999999";
        }else{
            $info = $info->toArray();
            return implode(',',array_column($info,'id'));
        }

    }



}
