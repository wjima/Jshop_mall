<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use think\Db;

class GoodsGrade extends Common
{
    /***
     * 获取会员等级优惠价
     * @param int $goods_id
     * @param int $grade_id
     * @return array
     */
    public function getGradePrice($goods_id = 0, $grade_id = 0)
    {
        $returnData = [
            'data'   => [],
            'msg'    => '获取失败',
            'status' => false,
        ];
        if (!$goods_id) {
            $returnData['msg'] = '关键参数失败';
            return $returnData;
        }
        $where   = [];
        $where[] = ['goods_id', '=', $goods_id];
        if ($grade_id) {
            $where[] = ['grade_id', '=', $grade_id];
        }
        $data = $this->where($where)->select();
        if ($data->isEmpty()) {
            return $returnData;
        }
        $data = $data->toArray();
        if ($grade_id) {
            $returnData['status'] = true;
            $returnData['msg']    = '获取成功';
            $returnData['data']   = $data[0];
            return $returnData;
        }
        $returnData['status'] = true;
        $returnData['msg']    = '获取成功';
        $returnData['data']   = $data;
        return $returnData;
    }

    /**
     * 设置会员价
     * @param int $goods_id
     * @param int $grade_id
     * @param float $price
     * @return array
     */
    public function setGradePrice($goods_id = 0, $grade_id = 0, $price = 0.00)
    {
        $returnData = [
            'data'   => [],
            'msg'    => '获取失败',
            'status' => false,
        ];
        if (!$goods_id || !$grade_id) {
            $returnData['msg'] = '关键参数失败';
            return $returnData;
        }

        $where   = [];
        $where[] = ['goods_id', '=', $goods_id];
        $where[] = ['grade_id', '=', $grade_id];

        $data = $this->where($where)->select();
        if ($data) {
            $uData['grade_price'] = $price;
            $res                  = $this->where($where)->update($uData);
            if (!$res) {
                $returnData['msg'] = '更新失败';
                return $returnData;
            }
        } else {
            $iData['goods_id']    = $goods_id;
            $iData['grade_id']    = $grade_id;
            $iData['grade_price'] = $price;
            $res                  = $this->where($where)->insertGetId($iData);
            if (!$res) {
                $returnData['msg'] = '更新失败';
                return $returnData;
            }
        }
        $returnData['status'] = true;
        $returnData['msg']    = '更新成功';
        return $returnData;
    }

}