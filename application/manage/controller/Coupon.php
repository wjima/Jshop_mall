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
        if (!input('param.id')) {
            return $this->error(error_code(15018,true));
        } else {
            $promotion = new Promotion();
            $promotionlist = $promotion->field("id,name")->select();
            $this->assign('promotion', $promotionlist);
            $this->assign('promotion_id', input('param.id'));
            return $this->fetch('');
        }
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


    public function bindUser()
    {
        $couponModel = new couponModel();
        return $couponModel->bindUser(input('param.user_id'), input('param.coupon_code'));
    }
}