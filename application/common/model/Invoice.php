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

        if ($return['data'] !== false) {
            $return['status'] = true;
            $return['msg'] = '成功';
            if (!isset($return['data']['type']) && empty($return['data']['type'])) {
                $return['data']['type'] = self::TAX_TYPE_NO;
            }
        }
        return $return;
    }
}