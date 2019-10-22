<?php

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
     */
    public function process($id, $data)
    {
        $this->save($data, ['id', 'eq', $id]);
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
        $return = [
            'status' => false,
            'msg' => '失败',
            'data' => []
        ];

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
}