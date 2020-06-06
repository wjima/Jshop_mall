<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

/**
 * 发票
 * Class Invoice
 * @package app\common\model
 */
class Invoice extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const TAX_CLASS_ORDER = 1; //订单类型
    const TAX_TYPE_NO = 1; //不开发票
    const TAX_TYPE_PERSONAL = 2; //个人发票
    const TAX_TYPE_COMPANY = 3; //企业发票
    const TAX_STATUS_NO = 1; //未开票
    const TAX_STATUS_YES = 2; //已开票


    /**
     * 存储发票信息
     * @param $data
     */
    public function add($data)
    {
        $this->save($data);
    }


    /**
     * 处理发票
     * @param $id
     * @param $data
     * @return array
     */
    public function process($id, $data)
    {
        $return = error_code(10037);
        $where[] = ['id', 'eq', $id];
        $return['data'] = $this->save($data, $where);
        if ($return['data']) {
            $return['status'] = true;
            $return['msg'] = '成功';
        }
        return $return;
    }


    /**
     * 获取订单的发票信息
     * @param $order_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderInvoiceInfo($order_id)
    {
        $return = error_code(10037);

        $where[] = ['class', 'eq', self::TAX_CLASS_ORDER];
        $where[] = ['source_id', 'eq', $order_id];
        $return['data'] = $this->where($where)->find();

        if ($return['data']) {
            $return['status'] = true;
            $return['msg'] = '成功';
        }
        return $return;
    }


    /**
     * 根据输入的查询条件，返回所需要的where
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['class']) && !empty($post['class'])) {
            $where[] = ['class', 'eq', $post['class']];
        }
        if (isset($post['source_id']) && !empty($post['source_id'])) {
            $where[] = ['source_id', 'eq', $post['source_id']];
        }
        if (isset($post['status']) && !empty($post['status'])) {
            $where[] = ['status', 'eq', $post['status']];
        }
        if (isset($post['tax_number']) && !empty($post['tax_number'])) {
            $where[] = ['tax_number', 'eq', $post['tax_number']];
        }
        if (isset($post['title']) && !empty($post['title'])) {
            $where[] = ['title', 'like', '%' . $post['title'] . '%'];
        }
        if (isset($post['type']) && !empty($post['type'])) {
            $where[] = ['type', 'eq', $post['type']];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = 'ctime DESC';
        return $result;
    }


    /**
     * 根据查询结果，格式化数据
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as &$v) {
            $v['class'] = config('params.order')['tax_class'][$v['class']];
            $v['type'] = config('params.order')['tax_type'][$v['type']];
            $v['status'] = config('params.order')['tax_status'][$v['status']];
            $v['ctime'] = getTime($v['ctime']);
        }
        return $list;
    }


    /**
     * 获取发票信息
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($id)
    {
        $return = error_code(10037);

        if (!$id) {
            return error_code(10051);
        }

        $where[] = ['id', 'eq', $id];
        $return['data'] = $this->where($where)->find();

        if ($return['data']) {
            $return['data']['class'] = config('params.order')['tax_class'][$return['data']['class']];
            $return['status'] = true;
            $return['msg'] = '成功';
        }

        return $return;
    }


    /**
     * 删除发票
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function del($id)
    {
        $return = error_code(10037);

        $where[] = ['id', 'eq', $id];
        $return['data'] = $this->where($where)
            ->delete();
        if ($return['data'] !== false) {
            $return['status'] = true;
            $return['msg'] = '成功';
        }

        return $return;
    }


    /**
     * 获取我的发票列表
     * @param $user_id
     * @param $page
     * @param $limit
     * @param bool $status
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function myInvoiceList($user_id, $page, $limit, $status = false, $id = 0)
    {
        $return = [
            'status' => false,
            'msg' => error_code(10025,true),
            'data' => [
                'list' => [],
                'count' => 0,
                'page' => $page,
                'limit' => $limit
            ]
        ];

        $where[] = ['user_id', 'eq', $user_id];
        if ($status) {
            $where[] = ['status', 'eq', $status];
        }
        if ($id != 0) {
            $where[] = ['id', 'eq', $id];
        }
        $return['data']['list'] = $this->where($where)
            ->order('id DESC')
            ->page($page, $limit)
            ->select()
            ->toArray();

        $return['data']['count'] = $this->where($where)
            ->count();

        if ($return['data']['list'] !== false) {
            $return['status'] = true;
            $return['msg'] = '获取成功';
            foreach ($return['data']['list'] as &$v) {
                $v['class_text'] = config('params.order')['tax_class'][$v['class']];
                $v['type_text'] = config('params.order')['tax_type'][$v['type']];
                $v['status_text'] = config('params.order')['tax_status'][$v['status']];
                $v['ctime_text'] = getTime($v['ctime']);
            }
        }

        return $return;
    }
}