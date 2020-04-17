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

    public function tableFormat($list)
    {
        foreach ($list as $key => $value) {
            $list[$key]['avatar']   = _sImage(get_user_info($value['user_id'], 'avatar'));
            $list[$key]['ctime']    = getTime($value['ctime'], false);
            $list[$key]['nickname'] = get_user_info($value['user_id'], nickname);
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

