<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use app\common\controller\Api;
use think\facade\Request;
use app\common\model\Cart as CartModel;

/**
 * 购物车
 * Class Cart
 * @package app\api\controller
 * @author keinx
 */
class Cart extends Api
{
    private $cartModel = null;


    protected function initialize()
    {
        parent::initialize();
        $this->cartModel = new CartModel();
    }


    /**
     * 单个加入购物车
     * @return array
     */
    public function add()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];

        if (!input("?param.product_id")) {
            // $result['msg'] = error_code(14011);
            return error_code(14011);
        }
        if (!input("?param.nums")) {
            // $result['msg'] = error_code(14012, true);
            return error_code(14012);
        }
        $type      = input('param.type', 1);          //1是累加，2是覆盖
        $cart_type = input('param.order_type', 1);        //购物车类型，1是普通流程，2是拼团，这里是特例，其他地方都是type，这里是cart_type ，因为type被占住了。
        $params    = json_decode(input('param.params', '{}','safe_filter'), true);        //购物车类型，1是普通流程，2是拼团，这里是特例，其他地方都是type，这里是cart_type ，因为type被占住了。

        return $this->cartModel->add($this->userId, input('product_id'), input('nums'), $type, $cart_type, $params);
    }


    /**
     * 移除购物车
     * @param array ids
     * @return array
     */
    public function del()
    {
        $ids = input('param.ids',"");
        $user_id = $this->userId;
        $type = input('param.order_type',1);

        $result = $this->cartModel->del($user_id,$ids,$type);
        if($result)
        {
            $return_data = array(
                'status' => true,
                'msg' => '移除购物车成功',
                'data' => $result
            );
        } else {
            return error_code(14014);
        }
        return $return_data;
    }


    /**
     * 获取购物车列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $ids = Request::param('ids', '');
        $type = input('param.order_type',1);
        $area_id = Request::param('area_id', false);
        $point = Request::param('point', 0);
        $coupon_code = Request::param('coupon_code', '');
        $receipt_type = Request::param('receipt_type', 1);      //配送方式是否包邮   1=快递配送（要去算运费）生成订单记录快递方式  2=门店自提（不需要计算运费）生成订单记录门店自提信息
        $params = Request::param('params', '','safe_filter');//购物车扩展信息,json对象，传团购秒杀id或其他信息
        if($receipt_type == 1){
            $free_freight = false;
        }else{
            $free_freight = true;
        }
        //是否显示完整的购物车，用在购物车页面
        if(input('?param.display')){
            $display = true;
        }else{
            $display = false;
        }

        $params = json_decode($params,true);
        $result = $this->cartModel->info($this->userId, $ids, $type, $display, $area_id, $point, $coupon_code, $free_freight,1,$params);
        return $result;
    }


    /**
     * 设置购物车数量接口
     * @return array
     */
    public function setNums()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if (!input('?param.id')) {
            // $result['msg'] = error_code(14011, true);
            return error_code(14011);
        } else {
            $id = input('param.id');
        }
        $nums = input('nums', 1);
        if($nums <= 0)
        {
            $nums = 1;
        }
        $order_type = input('param.order_type',1);
        $result = $this->cartModel->setNums($this->userId, $id, $nums, $order_type);
        if(!$result['status'])
        {
            return $result;
        }
        return $this->cartModel->info($this->userId,  input('param.ids',""),$order_type);
    }


    /**
     * 获取购物车数量
     * @return array
     */
    public function getNumber()
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        $type = input('param.order_type',1);
        $where[] = ['user_id', 'eq', $this->userId];
        $where[] = ['type', 'eq', $type];
        $vclass = getSetting('virtual_card_class');
        if($vclass)
        {
            $where[] = ['g.goods_cat_id', 'neq', $vclass];
        }
        $cartNums = $this->cartModel->alias('c')
            ->where($where)
            ->join('products p', 'p.id = c.product_id')
            ->join('goods g', 'g.id = p.goods_id')
            ->sum('nums');
        $result['data'] = $cartNums;
        return $result;
    }


    /**
     * 商品列表页批量快速下单加入购物车
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function batchSetCart()
    {
        $model = new CartModel();
        $input = Request::param('cart/a');
        return $model->batchSetCart($this->userId, $input);
    }
    
/**
 * 批量加入购物车
 *
 * @Author WGG 1490100895@qq.com
 * @DateTime 2021-01-29
 * @return void
 */
    public function batchAdd()
    {
        if (!input("?param.data")) {
            return error_code(14011);
        }
        return $this->cartModel->batchAdd($this->userId, input('data/a'));
    }
    /**
     * 获取全部购物车列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListSelectAll()
    {
        $where[] = ['user_id', 'eq', $this->userId];
        $where[] = ['type', 'eq', 1];
        $list    = $this->cartModel->where($where)->select();
        $ids     = '';
        if (!$list->isEmpty()) {
            $list = $list->toArray();
            $ids  = array_column($list, 'id');
            $ids  = implode(',',$ids);
        }
        $result = $this->cartModel->info($this->userId, $ids, 1, false, 0, '', false);
        return $result;
    }


}