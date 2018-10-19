<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

/**
 * 购物车
 * Class Cart
 * @package app\common\model
 * @author keinx
 */
class Cart extends Common
{
    /**
     * 关联货品
     * @return \think\model\relation\HasOne
     */
    public function products()
    {
        return $this->hasOne('Products', 'id','product_id');
    }

    /**
     * 单个加入购物车
     * @param $user_id
     * @param $product_id
     * @param $nums
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add($user_id,$product_id,$nums,$type)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $productsModel = new Products();
        $productInfo = $productsModel->getProductInfo($product_id,false);           //第二个参数是不算促销信息,否则促销信息就算重复了
        if(!$productInfo['status']){
            return $productInfo;

        }

        $canBuyNum = $productInfo['data']['stock'];

        $where[] = array('product_id', 'eq', $product_id);
        $where[] = array('user_id', 'eq', $user_id);

        $cat_info = $this->where($where)->find();

        if($cat_info)
        {
            if($type == 1){
                $cat_info->nums = $nums + $cat_info['nums'];
            }else{
                $cat_info->nums =  $nums;
            }

            if($cat_info->nums > $canBuyNum)
            {
                $result['msg'] = '库存不足';
                return $result;
            }
            $cat_info->save();

            $result['data'] = $cat_info->id;

        }
        else
        {
            if($nums > $canBuyNum)
            {
                $result['msg'] = '库存不足';
                return $result;
            }

            $data['product_id'] = $product_id;
            $data['nums'] = $nums;
            $data['user_id'] = $user_id;
            $result['data'] = $this->insertGetId($data);

        }
        $result['msg'] = '加入成功';
        $result['status'] = true;

        return $result;
    }

    /**
     * 移除购物车
     * @param $ids
     * @param bool $user_id
     * @return int
     */
    public function del($user_id, $ids = "")
    {
        $where[] = array('user_id', 'eq', $user_id);
        if($ids != ""){
            $where[] = array('id', 'in', $ids);
        }

        $res = $this->where($where)
            ->delete();
        return $res;
    }

    /**
     * 获取购物车列表
     * @param $userId
     * @param string $id
     * @param string $display
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($userId, $id = '', $display = '')
    {
        $result = array(
            'status' => false,
            'data' => [],
            'msg' => ''
        );
        $where[] = ['user_id', 'eq', $userId];
        if($id != '' && $display == '')
        {
            $where[] = ['id', 'in', $id];
        }

        $list = $this->where($where)->select()->toArray();

        $productsModel = new Products();
        $goodsModel = new Goods();
        foreach($list as $k => $v){
            //如果没有此商品，就在购物车里删掉
            $productInfo = $productsModel->getProductInfo($v['product_id'],false);           //第二个参数是不算促销信息,否则促销信息就算重复了
            if(!$productInfo['status']){
                unset($list[$k]);
                $this::destroy($v['id']);
                continue;
            }

            $goodsWeight = $goodsModel->getWeight($v['product_id']);
            $list[$k]['weight'] = $goodsWeight;

            $list[$k]['products'] = $productInfo['data'];
            //如果传过来了购物车数据，就算指定的购物车的数据，否则，就算全部购物车的数据
            if($id != ''){
                $array_ids = explode(',',$id);
                if(in_array($v['id'],$array_ids)){
                    $list[$k]['is_select'] = true;
                }else{
                    $list[$k]['is_select'] = false;
                }
            }else{
                $list[$k]['is_select'] = true;
            }
            //判断商品是否已收藏
            $list[$k]['isCollection'] = model('common/GoodsCollection')->check($v['user_id'],$list[$k]['products']['goods_id']);
        }
        $data['list'] = $list;
        $result['data'] = $data;
        $result['status'] = true;
        return $result;
    }

    /**
     * @param $sellerId
     * @param $userId
     * @param string $id
     * @param string $display
     * @param bool $area_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($userId, $id = '', $display = '', $area_id = false,$point = 0,$coupon_code = "")
    {
        $result = [
            'status' => false,
            'data' => [
                'list' => [],
                'goods_amount' =>0,         //商品总金额
                'amount' => 0,              //总金额
                'order_pmt' => 0,           //订单促销金额            单纯的订单促销的金额
                'goods_pmt' => 0,           //商品促销金额            所有的商品促销的总计
                'coupon_pmt' => 0,          //优惠券优惠金额
                'promotion_list' => [],      //促销列表
                'cost_freight' => 0,        //运费
                'weight' => 0,               //商品总重
                'coupon' => [],
                'point' => $point,              //在刚开始一定要校验积分是否可以使用，
                'point_money' => 0              //在结尾一定要算积分可以抵扣多少金额

            ],
            'msg' => ""
        ];
        $cartList = $this->getList($userId, $id, $display);

        if(!$cartList['status']){
            $result['msg'] = $cartList['msg'];
            return $result;
        }else{
            $result['data']['list'] = $cartList['data']['list'];
        }
        //算订单总金额
        foreach($result['data']['list']as $k=>$v){
            //单条商品总价
            $result['data']['list'][$k]['products']['amount'] = $v['nums']*$v['products']['price'];
            if($v['is_select']){
                //算订单总商品价格
                $result['data']['goods_amount'] += $result['data']['list'][$k]['products']['amount'];
                //算订单总价格
                $result['data']['amount'] += $result['data']['list'][$k]['products']['amount'];
                //计算总重量
                $result['data']['weight'] += $v['weight']*$v['nums'];
            }
        }
        if($area_id)
        {
            $shipModel = new Ship();
            $result['data']['cost_freight'] = $shipModel->getShipCost($area_id, $result['data']['weight']);
            $result['data']['amount'] += $result['data']['cost_freight'];
        }

        //接下来算订单促销金额
        $promotionModel = new Promotion();
        $result['data'] = $promotionModel->toPromotion($result['data']);

        //加入有优惠券，判断优惠券是否可用
        if($coupon_code != ""){
            $couponModel = new Coupon();
            $couponInfo = $couponModel->codeToInfo($coupon_code,true);
            if(!$couponInfo['status']){
                return $couponInfo;
            }
            $re = $promotionModel->toCoupon($result['data'],$couponInfo['data']);
            if(!$re['status']){
                return $re;       //优惠券不符合使用规则，后期会把不符合的原因写出来
            }
        }

        $result['status'] = true;
        return $result;
    }

    /**
     * 设置购物车数量
     * @param $input
     * @return array
     */
    public function setNums($input)
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];

        $where[] = ['id', 'eq', $input['id']];
        $where[] = ['user_id', 'eq', $input['user_id']];
        $res = $this->where($where)
            ->update(['nums'=>$input['nums']]);

        $result['data'] = $res;
        if($res !== false)
        {
            $result['status'] = true;
            $result['msg'] = '设置成功';
        }
        else
        {
            $result['msg'] = '设置失败';
        }
        return $result;
    }
}