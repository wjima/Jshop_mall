<?php
namespace app\common\model;

/**
 * 提货单
 * Class BillLading
 * @package app\common\model
 */
class BillLading extends Common
{
    public function getList($post, $page = 1, $limit = 20)
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => [],
            'count' => 0
        ];

        $where = [];
        if($post['id'])
        {
            $where[] = ['id', 'like', '%'.$post['id'].'%'];
        }
        if($post['order_id'])
        {
            $where[] = ['order_id', 'like', '%'.$post['order_id'].'%'];
        }
        if($post['store_id'])
        {
            $where[] = ['store_id', 'eq', $post['store_id']];
        }
        if($post['name'])
        {
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        if($post['mobile'])
        {
            $where[] = ['mobile', 'like', '%'.$post['mobile'].'%'];
        }

        $return['data'] = $this->where($where)
            ->page($page, $limit)
            ->select();

        $return['count'] = $this->where($where)
            ->count();

        if($return['data'] !== false)
        {
            $storeModel = new Store();
            foreach($return['data'] as &$v)
            {
                $v['store_name'] = $storeModel->getStoreName($v['store_id']);
                $v['status_name'] = config('params.bill_lading.status')[$v['status']];
                $v['ctime'] = getTime($v['ctime']);
            }
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }

        return $return;
    }
}