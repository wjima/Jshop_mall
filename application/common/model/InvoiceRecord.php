<?php
namespace app\common\model;

/**
 * 发票记录
 * Class InvoiceRecord
 * @package app\common\model
 */
class InvoiceRecord extends Common
{
    /**
     * 记录企业发票信息
     * @param $data
     * @return false|int|true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add($data)
    {
        $where[] = ['name', 'eq', $data['name']];
        $where[] = ['code', 'eq', $data['code']];
        $flag = $this->where($where)->find();

        if($flag)
        {
            $res = $this->where($where)->setInc('frequency', 1);
        }
        else
        {
            $res = $this->save($data);
        }

        return $res;
    }


    /**
     * 获取税号
     * @param $name
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCodeByName($name)
    {
        $return = [
            'status' => true,
            'msg' => '',
            'data' => ''
        ];
        $where[] = ['name', 'like', '%'.$name.'%'];
        //$where[] = ['frequency', '>=', 2];
        $info = $this->field('name,code,frequency')
            ->where($where)
            ->order('frequency', 'DESC')
            ->page(1, 4)
            ->select();
        if($info)
        {
            $return['data'] = $info;
        }

        return $return;
    }
}