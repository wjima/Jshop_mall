<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Coupon as couponModel;
use app\common\model\Promotion;
use think\facade\Request;

class Coupon extends Manage
{
    public function index()
    {
        $couponModel = new couponModel();
        if (Request::isAjax())
        {
            return $couponModel->tableData(input('param.'));
        }
        //获取优惠券列表
        $promotionModel = new Promotion();
        $where['type'] = $promotionModel::TYPE_COUPON;
        $list = $promotionModel->tableData($where);
        return $this->fetch('',['list'=>$list]);
    }


    /**
     * 删除用户优惠券
     * @return array
     */
    public function  del()
    {
        $couponModel = new couponModel();
        return $couponModel->del(input('param.coupon_code'));
    }
}