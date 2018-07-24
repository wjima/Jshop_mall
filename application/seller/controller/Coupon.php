<?php
/**
 * Created by PhpStorm.
 * Date: 2018/5/2 0002
 * Time: 18:50
 */
namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\Coupon as couponModel;
use app\common\model\Promotion;
use think\facade\Request;

class Coupon extends Seller
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $couponModel = new couponModel();
        if (Request::isAjax())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $couponModel->tableData($data);
        }
        //获取优惠券列表
        $promotionModel = new Promotion();
        $where['seller_id'] = $this->sellerId;
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
        return $couponModel->del($this->sellerId,input('param.coupon_code'));
    }
}