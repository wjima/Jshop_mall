<?php

namespace app\api\controller;

use app\common\model\Promotion;
use app\common\model\Coupon as couponModel;
use app\common\controller\Api;

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
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '获取失败'
        ];

        $promotionModel = new Promotion();
        $res = $promotionModel->receiveCouponList();

        if ( $res )
        {
            $result['status'] = true;
            $result['data'] = $res;
            $result['msg'] = '获取成功';
        }

        return $result;
    }

    /**
     * 获取优惠券 详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function couponDetail()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '获取失败'
        ];

        if (!input('promotion_id'))
        {
            return error_code(15006);
        }

        $promotionModel = new Promotion();
        $res = $promotionModel->field('id,name,type,status,exclusive,stime,etime')
            ->where('id',input('promotion_id'))
            ->find();

        if ( $res )
        {
            $result['status'] = true;
            $result['data'] = $res;
            $result['msg'] = '获取成功';
        }
        return $result;
    }

    /**
     * 获取用户已领取的优惠券
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userCoupon()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '获取失败'
        ];
        $couponModel = new couponModel();
        $res = $couponModel->getMyCoupon($this->userId, '', input('display', 'all'));

        if ( $res )
        {
            $result['status'] = true;
            $result['data'] = $res;
            $result['msg'] = '获取成功';
        }
        return $result;
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
        $coupon = $couponModel->getMyCoupon($this->userId,input('promotion_id'));
        if ( !$coupon->isEmpty() )
        {
            return error_code(15008);
        }

        return $couponModel->addData($this->userId,input('promotion_id'));
    }
}