<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use app\common\model\Promotion;
use app\common\model\Coupon as couponModel;
use app\common\controller\Api;
use think\facade\Request;

/**
 * 优惠券
 * Class Coupon
 * @package app\api\controller
 */
class Coupon extends Api
{
    /**
     * 获取 商户可领取的优惠券
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function couponList()
    {
        $result =  error_code(10025);
        $promotionModel = new Promotion();
        $limit = Request::param('limit', 3);
        $res = $promotionModel->receiveCouponList($limit);
        if($res)
        {
            $result['status'] = true;
            $result['data'] = $res;
            $result['msg'] = '获取成功';
        }
        return $result;
    }


    /**
     * 获取优惠券 详情
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function couponDetail()
    {
        $result =  error_code(10025);
        if (!input('promotion_id')) {
            return error_code(15006);
        }
        $promotionModel = new Promotion();
        $res = $promotionModel->field('id,name,type,status,exclusive,stime,etime')
            ->where('id',input('promotion_id'))
            ->find();
        if($res)
        {
            $result['status'] = true;
            $result['data'] = $res;
            $result['msg'] = '获取成功';
        }
        return $result;
    }


    /**
     * 获取用户已领取的优惠券
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userCoupon()
    {
        $couponModel = new couponModel();
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $display = Request::param('display', 'all');
        $res = $couponModel->getMyCoupon($this->userId, '', $display, $page, $limit);
        return $res;
    }


    /**
     * 用户领取优惠券
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCoupon()
    {
        if(!input('promotion_id'))
        {
            return error_code(15006);
        }
        //判断优惠券是否可以领取?
        $promotionModel = new Promotion();
        if (!$promotionModel->receiveCoupon(input('promotion_id')))
        {
            return error_code(15007);
        }
        //判断用户是否已领取?
        $couponModel = new couponModel();
        $coupon = $couponModel->getMyCoupon($this->userId, input('promotion_id'));
        if(count($coupon['data']['list']) > 0)
        {
            return error_code(15008);
        }
        return $couponModel->addData($this->userId,input('promotion_id'));
    }


    /**
     * 输入优惠券号领取优惠券
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCouponKey()
    {
        $key = Request::param('key', false);
        if(!$key)
        {
            return error_code(15006);
        }
        $couponModel = new couponModel();
        $where[] = ['coupon_code', 'eq', $key];
        $coupon = $couponModel->field('promotion_id')->where($where)->find();
        if(!$coupon['promotion_id'])
        {
            return error_code(15009);
        }
        //判断优惠券是否可以领取?
        $promotionModel = new Promotion();
        if(!$promotionModel->receiveCoupon($coupon['promotion_id']))
        {
            return error_code(15007);
        }
        //判断用户是否已领取?
        $coupon = $couponModel->getMyCoupon($this->userId, $coupon['promotion_id']);
        if(!$coupon->isEmpty())
        {
            return error_code(15008);
        }
        return $couponModel->receiveCoupon($this->userId, $key);
    }
}