<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;
use addons\KdniaoExpress\lib\kdniao;

/**
 * 购物车
 * Class Cart
 * @package app\common\model
 * @author keinx
 */
class Cart extends Common
{

    const TYPE_COMMON = 1;      //普通模式          //这些都是系统内置的type类型，如果二开新增购物车类型的话，建议从二位数开始
    const TYPE_PINTUAN= 2;      //拼团模式


    /**
     * 关联货品
     * @return \think\model\relation\HasOne
     */
    public function products()
    {
        return $this->hasOne('Products', 'id','product_id');
    }

    /**
     * 单个商品加入购物车
     * @param $user_id          用户id
     * @param $product_id       货品id
     * @param $nums             数量
     * @param $num_type         数量类型，1是直接增加，2是赋值
     * @param int $type
     * @return array
     */
    public function add($user_id,$product_id,$nums,$num_type,$type = 1)
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

        switch ($type){
            case self::TYPE_COMMON:
                //标准模式不需要做什么判断
                break;
            case self::TYPE_PINTUAN;
                //拼团模式去判断是否开启拼团，是否存在
                if(!$this->pCartAdd($user_id,$product_id,$nums,$num_type,$result)){
                    return $result;
                }
                break;
            default:
                return error_code(10000);
        }

        $canBuyNum = $productInfo['data']['stock'];

        $where[] = array('product_id', 'eq', $product_id);
        $where[] = array('user_id', 'eq', $user_id);
        $where[] = ['type', 'eq', $type];

        $cat_info = $this->where($where)->find();

        if($cat_info)
        {
            if($num_type == 1){
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
            $data['type'] = $type;

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
    public function del($user_id, $ids = "",$type = 1)
    {
        $where[] = array('user_id', 'eq', $user_id);
        if($ids != ""){
            $where[] = array('id', 'in', $ids);
        }
        $where[] = ['type', 'eq',$type];

        $res = $this->where($where)
            ->delete();
        return $res;
    }

    /**
     * 获取购物车列表
     * @param $userId           用户id
     * @param $ids              购物车信息
     * @param $type             购物车类型
     * @return array
     */
    public function getList($userId, $ids,$type = 1)
    {
        $result = array(
            'status' => false,
            'data' => [],
            'msg' => ''
        );
        $where[] = ['user_id', 'eq', $userId];

        $where[] = ['type', 'eq' , $type];

        $list = $this->where($where)->select();


        if(!$list->isEmpty()){
            $list = $list->toArray();
        }

        $productsModel = new Products();
        $goodsModel = new Goods();
        foreach($list as $k => $v){
            //如果没有此商品，就在购物车里删掉
            $productInfo = $productsModel->getProductInfo($v['product_id'],false,$userId);           //第二个参数是不算促销信息,否则促销信息就算重复了
            if(!$productInfo['status']){
                unset($list[$k]);
                $this::destroy($v['id']);
                continue;
            }
            $goodsWeight = $goodsModel->getWeight($v['product_id']);
            $list[$k]['weight'] = $goodsWeight;

            $list[$k]['products'] = $productInfo['data'];
            //如果传过来了购物车数据，就算指定的购物车的数据，否则，就算全部购物车的数据
            $array_ids = explode(',',$ids);
            if(in_array($v['id'],$array_ids)){
                $list[$k]['is_select'] = true;
            }else{
                $list[$k]['is_select'] = false;
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
     * @param $userId
     * @param string $id
     * @param string $type          没用的值，以后会拿掉
     * @param bool $area_id             收货地址id
     * @param int $point                消费的积分
     * @param string $coupon_code
     * @param bool free_freight 是否免运费
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($userId, $ids, $type = '1', $area_id = 0, $point = 0, $coupon_code = "", $free_freight = false)
    {
        $result = [
            'status' => false,
            'data' => [
                'user_id' => $userId,
                'type' => $type,
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
                'point_money' => 0,              //积分可以抵扣多少金额
                'params' => []              //一些可以放到购物车中的参数
            ],
            'msg' => ""
        ];
        $cartList = $this->getList($userId, $ids,$type);
        if(!$cartList['status']){
            $result['msg'] = $cartList['msg'];
            return $result;
        }else{
            $result['data']['list'] = $cartList['data']['list'];
        }

        switch ($type){
            case self::TYPE_COMMON:
                //标准模式不需要修改订单数据和商品数据
                break;
            case self::TYPE_PINTUAN;
                //拼团模式去修改商品价格
                if(!$this->pCartInfo($result)){
                    return $result;
                }
                break;
            default:
                return error_code(10000);
        }



        //算订单总金额
        foreach($result['data']['list']as $k=>$v){
            //库存不足不计算金额不可以选择
            if($v['nums'] > $v['products']['stock'])
            {
                $result['data']['list'][$k]['is_select'] = false;
                $v['is_select'] = false;
            }

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

        //echo json_encode($result['data']['list']);exit;

        //运费判断

        if(!$this->cartFreight($result, $area_id, $free_freight)){
            return $result;
        }

        //接下来算订单促销金额,有些模式不需要计算促销信息，这里就增加判断
        if($type == self::TYPE_COMMON){
            $promotionModel = new Promotion();
            $result['data'] = $promotionModel->toPromotion($result['data']);
        }


        //使用优惠券，判断优惠券是否可用
        if(!$this->cartCoupon($result, $coupon_code)){
            return $result;
        }

        //使用积分
        if(!$this->cartPoint($result,$userId,$point)){
            return $result;
        }

        $result['status'] = true;
        return $result;
    }

    /**
     * 设置购物车数量
     * @param $input
     * @return array
     */
    public function setNums($user_id,$id,$nums,$type = 1)
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];

        $where[] = ['id', 'eq', $id];
        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['type', 'eq', $type];
        $res = $this->where($where)
            ->update(['nums'=>$nums]);

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


    /**
     * 算运费
     * @param $result       购物车
     * @param $area_id      收货地址id
     * @param bool $free    是否包邮，默认false
     * @return bool
     */
    private function cartFreight(&$result, $area_id, $free = false){
        if(!$free)
        {
            if($area_id)
            {
                $shipModel = new Ship();
                $result['data']['cost_freight'] = $shipModel->getShipCost($area_id, $result['data']['weight'],$result['data']['goods_amount']);
                $result['data']['amount'] += $result['data']['cost_freight'];
            }
        }
        return true;
    }

    //购物车中使用优惠券
    private function cartCoupon(&$result,$coupon_code){
        if($coupon_code != ""){
            $couponModel = new Coupon();
            $couponInfo = $couponModel->codeToInfo($coupon_code,true);
            if(!$couponInfo['status']){
                $result['msg'] = $couponInfo['msg'];
                return false;
            }
            $promotionModel = new Promotion();
            $re = $promotionModel->toCoupon($result['data'],$couponInfo['data']);
            if(!$re['status']){
                $result['msg'] = $re['msg'];
                return false;
            }
        }
        return true;
    }

    //购物车中使用积分
    private function cartPoint(&$result,$userId,$point){
        if($point != 0)
        {
            //判断用户是否有这么多积分
            $userModel = new User();
            $oPoint = $userModel->getUserPoint($userId);
            if($oPoint['data'] < $point)
            {
                $result['msg'] = "积分不足，无法使用积分";
                return false;
            }
            //判断积分值多少钱
            $settingModel = new Setting();
            $orders_point_proportion = $settingModel->getValue('orders_point_proportion'); //订单积分使用比例
            $max_point_deducted_money = $result['data']['amount']*($orders_point_proportion/100); //最大积分抵扣的钱
            $point_discounted_proportion = $settingModel->getValue('point_discounted_proportion'); //积分兑换比例
            $point_deducted_money = (int)$point/(int)$point_discounted_proportion; //积分可以抵扣的钱
            if($max_point_deducted_money < $point_deducted_money)
            {
                $result['msg'] = "积分超过订单可使用的积分数量";
                return false;
            }

            $result['data']['point'] = $point;
            $result['data']['point_money'] = $point_deducted_money;
            $result['data']['amount'] = $result['data']['amount']-$point_deducted_money;
        }
        return true;

    }

    //计算拼团的各种信息
    private function pCartInfo(&$result){
        //一定要先安装拼团插件
        if(checkAddons('Pintuan')){
            $result['msg'] = "请安装拼团插件";
            return false;
        }

        $ptlib = new \addons\Pintuan\lib\ptlib();

        return $ptlib->cartInfo($result);
    }
    private function pCartAdd($user_id,$product_id,$nums,$num_type,&$result){
        //一定要先安装拼团插件
        if(checkAddons('Pintuan')){
            $result['msg'] = "请安装拼团插件";
            return false;
        }

        $ptlib = new \addons\Pintuan\lib\ptlib();
        return $ptlib->cartAdd($user_id,$product_id,$nums,$num_type,$result);
    }



}
