<?php

namespace addons\WeRun\model;

use app\common\model\Common;

class WerunLog extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';

        /**
     * 后台获取计步列表
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-03-08
     * @param [type] $data
     * @return void
     */
    public function getList($data)
    {
        $page = isset($data['page']) ? $data['page'] : 1;
        $limit = isset($data['limit']) ? $data['limit'] : config('jshop.page_limit');
        $where = $this->_where($data);
        $query = $this->alias('rl')
            ->leftJoin('user u', 'rl.user_id=u.id');
        if ($where) $query->where($where);

        $datas = $query->field(['rl.*', 'u.username'])->order('rl.id', 'desc')->page($page, $limit)->select();
        $count = $query->count();

        foreach ($datas as &$data) {
            $data['ctime'] = getTime($data['ctime']);
        }

        return [
            'code' => 0,
            'data' => $datas,
            'count' => $count,
            'msg' => '获取成功'
        ];
    }

    private function _where($data)
    {
        $where = [];
        if ($data) {
            if(isset($data['date']) && $data['date'] != ''){
                $theDate = explode(' 到 ', $data['date']);
                if (count($theDate) == 2) {
                    $where[] = ['rl.ctime', '>', strtotime($theDate[0].'  00:00:00')];
                    $where[] = ['rl.ctime', '<=', strtotime($theDate[1].'  23:59:59')];
                }
            }
            if(isset($data['username']) && $data['username'] != ""){
                $where[] = ['u.username|nickname|mobile', 'like', '%'.$data['username'].'%'];
            }
        }
        return $where;
    }

    public function log($userId,$data)
    {
        $page = isset($data['page']) ? $data['page'] : 1;
        $limit = isset($data['limit']) ? $data['limit'] : config('jshop.page_limit');

        $data = $this->where('user_id',$userId)->order('ctime','desc')->page($page,$limit)->select();
        $count = $this->where('user_id',$userId)->count();
        foreach ($data as &$val) {
            $val['ctime'] = getTime($val['ctime']);
        }
        return [
            'code'=>0,
            'data'=>$data,
            'count'=>$count,
            'msg'=>''
        ];
    }

}