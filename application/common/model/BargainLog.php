<?php

namespace app\common\model;

use think\Validate;
use think\Model;

class BargainLog extends Common
{

    /**
     * 获取日志记录
     * @param string $fields
     * @param array $where
     * @param string $order
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getList($fields = '*', $where = [], $order = ['ctime' => 'desc'], $page = 1, $limit = 10)
    {
        $result                  = [
            'status' => true,
            'msg'    => '',
            'data'   => [],
        ];
        $list                    = $this
            ->field($fields)
            ->where($where)
            ->order($order)
            ->page($page, $limit)
            ->select();
        $total                   = $this
            ->field($fields)
            ->where($where)
            ->count();
        $list                    = $this->tableFormat($list);
        $result['data']['list']  = $list;
        $result['data']['count'] = $total;
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
        if (isset($post['record_id']) && $post['record_id'] != "") {
            $where[] = ['record_id', 'eq', $post['record_id']];
        }
        if (isset($post['username']) && $post['username'] != "") {
            $userModel = new User();
            $user_ids = $userModel->where('nickname|mobile', 'like', '%' . $post['username'] . '%')->column('id');
            if ($user_ids) {
                $where[] = ['user_id', 'in', $user_ids];
            }
        }
        if (isset($post['recordname']) && $post['recordname'] != "") {
            $userModel = new User();
            $user_ids = $userModel->where('nickname|mobile', 'like', '%' . $post['recordname'] . '%')->column('id');
            $bargainRecordModel = new BargainRecord();
            $record_ids = $bargainRecordModel->where('user_id', 'in', $user_ids)->column('id');
            
            if ($user_ids) {
                $where[] = ['record_id', 'in', $record_ids];
            }
        }
        if (isset($post['name']) && $post['name'] != "") {
            $bargainRecordModel = new BargainRecord();
            $bargain_ids = $bargainRecordModel->where('name', 'like', '%' . $post['name'] . '%')->column('bargain_id');
            if ($bargain_ids) {
                $where[] = ['bargain_id', 'in', $bargain_ids];
            }
        }
        if (isset($post['date']) && $post['date'] != "") {
            $date    = explode(' 到 ', $post['date']);
            $where[] = ['ctime', 'between time', [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['ctime'=>'desc'];
        return $result;
    }


    public function tableFormat($list)
    {
        $recordModel = new BargainRecord();

        foreach ($list as $key => $value) {
            $list[$key]['avatar']   = _sImage(get_user_info($value['user_id'], 'avatar'));
            $list[$key]['ctime']    = getTime($value['ctime'], false);
            $list[$key]['nickname'] = get_user_info($value['user_id'], 'nickname');
            if(isset($value['record_id'])&& $value['record_id']){
                $record = $recordModel->field('name,goods_name,image_url,user_id')->get($value['record_id']);
                $list[$key]['bargain_user'] = get_user_info($record['user_id'], 'nickname');
                $list[$key]['bargain_name'] = $record['name'];
            }
        }
        return $list;
    }

    /**
     * 返回砍价总额
     * @param int $bargain_id
     * @param int $buyer_id
     * @return float
     */
    public function getBargainPrice($bargain_id = 0, $buyer_id = 0)
    {
        return $this->where([['bargain_id', '=', $bargain_id], ['buyer_id', '=', $buyer_id]])->sum('bargain_price');
    }
}

