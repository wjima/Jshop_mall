<?php


namespace addons\WelfarePro\model;


use app\common\model\Common;
use app\common\model\Promotion;
use app\common\model\User;
use think\Exception;

class WelfareproCouponLog extends Common
{
    public function couponOver($c_id,$tj_user_id,$maxNums){
        //是否有该推荐人该活动的数据
        $info = $this->where('c_id',$c_id)->where('tj_user_id',$tj_user_id)->count('id');
        if($info >= $maxNums) {
            return false;
        }
        return true;
    }
    public function getList($param){
        $page = isset($param['page']) ? $param['page'] : 1;
        $limit = isset($param['limit']) ? $param['limit'] : 10;
        $query = $this->_where($param);
        $list = $query->page($page,$limit)->select()->toArray();
        return [
          'data'=>empty($list) ? [] : $this->formatTable($list),
          'count'=>$query->count(),
          'msg'=>'success',
          'code'=>0
        ];
    }
    private function formatTable($list){
        foreach ($list as &$v){
            if ($v['user_id']) {
                $v['user_id'] = get_user_info($v['user_id'],'showname');
            }
            if ($v['tj_user_id']) {
                $v['tj_user_id'] = get_user_info($v['tj_user_id'],'showname');
            }
            if ($v['ctime']) {
                $v['ctime'] = getTime($v['ctime']);
            }
        }
        return $list;
    }
    private function _where($post){
        $where = [];
        if (isset($post['c_id']) && $post['c_id'] != "") {
            $where[] = ['c_id', 'eq', $post['c_id']];
        }
        if (isset($post['user_name']) && $post['user_name'] != "") {
            $where[] = ['user_id', 'in', $this->getUserId($post['user_name'])];
        }
        if (isset($post['tj_user_name']) && $post['tj_user_name'] != "") {
            $where[] = ['tj_user_id', 'in', $this->getUserId($post['tj_user_name'])];
        }
        if (isset($post['date']) && $post['date'] != "") {
            $theDate = explode(' 到 ', $post['date']);
            if (count($theDate) == 2) {
                $where[] = ['ctime', '>', strtotime($theDate[0])];
                $where[] = ['ctime', '<=', strtotime($theDate[1])];
            }
        }

        if(!empty($where))  return $this->where($where);
        return $this;
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