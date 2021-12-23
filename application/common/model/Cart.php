<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use addons\FreePackage\model\FreePackage;
use addons\KdniaoExpress\lib\kdniao;
use think\Db;


/**
 * 购物车
 * Class Cart
 * @package app\common\model
 * @author keinx
 */
class Cart extends Common
{
    const TYPE_COMMON = 1;      //普通模式          //这些都是系统内置的type类型，如果二开新增购物车类型的话，建议从二位数开始
    const TYPE_PINTUAN = 2;      //拼团模式
    const TYPE_GROUP = 3;      //团购模式
    const TYPE_SKILL = 4;      //秒杀模式
    const TYPE_BARGAIN = 6;      //砍价模式
    const TYPE_GIVEAWAY = 7;        //赠品，在cart表里不会存在，但是会在计算促销过之后，动态的加上去
    const TYPE_COMBO = 8;        //套餐，套餐内最低价的商品免单

    /**
     * 关联货品
     * @return \think\model\relation\HasOne
     */
    public function products()
    {
        return $this->hasOne('Products', 'id', 'product_id');
    }


    /**
     * 单个商品加入购物车
     * @param $user_id //用户id
     * @param $product_id //货品id
     * @param $nums //数量
     * @param $num_type //数量类型，1是直接增加，2是赋值
     * @param int $type
     * @param array $params //扩展参数
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function add($user_id, $product_id, $nums, $num_type, $type = 1,$params = [])
    {
        $result        = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        $productsModel = new Products();
        $productInfo   = $productsModel->getProductInfo($product_id, false);           //第二个参数是不算促销信息,否则促销信息就算重复了
        if (!$productInfo['status']) {
            return $productInfo;
        }

        //判断货品是否已经下架
        $flag = $productsModel->getShelfStatus($product_id);
        if (!$flag['status']) {
            return $flag;
        }

        $canBuyNum = $productInfo['data']['stock'];

        $where[]  = array('product_id', 'eq', $product_id);
        $where[]  = array('user_id', 'eq', $user_id);
        $where[]  = ['type', 'eq', $type];
        $cat_info = $this->where($where)->find();

        switch ($type) {
            case self::TYPE_COMMON:

                break;
            case self::TYPE_PINTUAN:
                $num_type = 2;
                //拼团模式去判断是否开启拼团，是否存在
                $pintuanRuleModel = new PintuanRule();
                $re               = $pintuanRuleModel->addCart($product_id, $user_id, $nums);
                if (!$re['status']) {
                    return $re;
                }
                //此人的购物车中的所有购物车拼团商品都删掉，因为立即购买也是要加入购物车的，所以需要清空之前历史的加入过购物车的商品
                $delWhere[] = ['user_id', 'eq', $user_id];
                $delWhere[] = ['type', 'eq', 2];
                $this->where($delWhere)->delete();
                unset($cat_info);
                break;
            case self::TYPE_GROUP:
            case self::TYPE_SKILL:
                //判断商品是否做团购秒杀
                if (isInGroup($productInfo['data']['goods_id'], $params['group_id'], $promotion)) {
                    //此人的购物车中的所有购物车拼团商品都删掉，因为立即购买也是要加入购物车的，所以需要清空之前历史的加入过购物车的商品
                    $delWhere[] = ['user_id', 'eq', $user_id];
                    $delWhere[] = ['type', 'in', [self::TYPE_GROUP,self::TYPE_SKILL]];
                    $delWhere[] = ['product_id', 'eq', $product_id];
                    $this->where($delWhere)->delete();

                    $params      = json_decode($promotion['params'], true);
                    $orderModel  = new Order();
                    $check_order = $orderModel->findLimitOrder($product_id, $user_id, $promotion, self::TYPE_GROUP);
                    //应该里面方法判断，以后优化吧
                    if (isset($params['max_goods_nums']) && $params['max_goods_nums'] != 0) {
                        if (($check_order['data']['total_orders'] + $nums) > $params['max_goods_nums']) {
//                            $result['msg'] = '该商品已超过当前活动最大购买量';
                            return error_code(15610);
                        }
                    }
                    if (isset($params['max_nums']) && $params['max_nums'] != 0) {
                        if (($nums + $check_order['data']['total_user_orders']) > $params['max_nums']) {
//                            $result['msg'] = '您已超过该活动最大购买量';
                            return error_code(15611);
                        }
                    }
                    unset($cat_info);
                }
                break;
            case self::TYPE_BARGAIN://砍价
                $num_type = 2;
                //砍价
                $bargainModel = new Bargain();
                $re           = $bargainModel->addCart($product_id, $user_id, $nums);
                if (!$re['status']) {
                    return $re;
                }
                //此人的购物车中的所有购物车拼团商品都删掉，因为立即购买也是要加入购物车的，所以需要清空之前历史的加入过购物车的商品
                $delWhere[] = ['user_id', 'eq', $user_id];
                $delWhere[] = ['type', 'eq', self::TYPE_BARGAIN];
                $this->where($delWhere)->delete();
                unset($cat_info);
                break;
            case self::TYPE_COMBO:  // 商品套餐活动
                break;
            default:
                return error_code(10000);
        }

        if ($cat_info) {
            if ($num_type == 1) {
                $cat_info->nums = $nums + $cat_info['nums'];
            } else {
                $cat_info->nums = $nums;
            }

            if ($cat_info->nums > $canBuyNum) {
//                $result['msg'] = '库存不足';
                return error_code(12702);
            }
            $cat_info->save();

            $result['data'] = $cat_info->id;
        } else {
            if ($nums > $canBuyNum) {
//                $result['msg'] = '库存不足';
                return error_code(12702);
            }

            $data['product_id'] = $product_id;
            $data['nums']       = $nums;
            $data['user_id']    = $user_id;
            $data['type']       = $type;

            $result['data'] = $this->insertGetId($data);
        }
        $result['msg']    = '加入成功';
        $result['status'] = true;

        return $result;
    }


    /**
     * 移除购物车
     * @param $user_id
     * @param string $ids
     * @param int $type
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del($user_id, $ids = "", $type = 1)
    {
        $where[] = array('user_id', 'eq', $user_id);
        if ($ids != "") {
            $where[] = array('id', 'in', $ids);
        }
        $where[] = ['type', 'eq', $type];

        $res = $this->where($where)
            ->delete();
        return $res;
    }


    /**
     * 获取购物车列表
     * @param $userId //用户id
     * @param $ids //购物车信息
     * @param int $type //购物车类型
     * @param array $params 扩展字段信息，传数组
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($userId, $ids, $type = 1, $display = false, $params = [])
    {
        $result  = array(
            'status' => false,
            'data'   => [],
            'msg'    => ''
        );
        $where[] = ['user_id', 'eq', $userId];
        $where[] = ['type', 'eq', $type];
        if(!$display){
            $where[] = ['id', 'in',$ids];
        }
        $list    = $this->where($where)->select();
        if (!$list->isEmpty()) {
            $list = $list->toArray();
        }

        $productsModel = new Products();
        $goodsModel    = new Goods();
        foreach ($list as $k => $v) {
            //如果没有此商品，就在购物车里删掉
            $productInfo = $productsModel->getProductInfo($v['product_id'], false, $userId);           //第二个参数是不算促销信息,否则促销信息就算重复了
            if (!$productInfo['status']) {
                unset($list[$k]);
                $this::destroy($v['id']);
                continue;
            }
            //商品下架，就从购物车里面删除
            $ps = $productsModel->getShelfStatus($v['product_id']);
            if (!$ps['status']) {
                unset($list[$k]);
                $this::destroy($v['id']);
                continue;
            }

            $goodsWeight        = $goodsModel->getWeight($v['product_id']);
            $list[$k]['weight'] = $goodsWeight;

            $list[$k]['products'] = $productInfo['data'];
            //如果传过来了购物车数据，就算指定的购物车的数据，否则，就算全部购物车的数据
            $array_ids = explode(',', $ids);
            if (in_array($v['id'], $array_ids)) {
                $list[$k]['is_select'] = true;
            } else {
                $list[$k]['is_select'] = false;
            }
            //判断商品是否已收藏
            $list[$k]['isCollection'] = model('common/GoodsCollection')->check($v['user_id'], $list[$k]['products']['goods_id']);
        }

        //如果不同的购物车类型，可能会做一些不同的操作。
        switch ($type) {
            case self::TYPE_COMMON:
                //标准模式不需要修改订单数据和商品数据
                break;
            case self::TYPE_PINTUAN;
                //拼团模式走拼团价，去修改商品价格
                $pintuanRuleModel = new PintuanRule();
                $result           = $pintuanRuleModel->pintuanInfo($list);
                if (!$result['status']) {
                    return $result;
                }
                break;
            case self::TYPE_GROUP:
                //团购模式不需要修改订单数据和商品数据
                break;
            case self::TYPE_SKILL:
                //秒杀模式不需要修改订单数据和商品数据
                break;
            case self::TYPE_BARGAIN:
                $bargainModel = new Bargain();
                $result       = $bargainModel->bargainInfo($list, $userId);
                if (!$result['status']) {
                    return $result;
                }
                break;
            case self::TYPE_COMBO:
                //套餐 下单再走验证，购物车选购页面不限制
                if(isset($params['is_order']) && $params['is_order'] == 1 ){
                    $combo_status = get_addons_status('freepackage');
                    if(!$combo_status){
                        return error_code(10711);   // 请先安装插件
                    }
                    $packageModel = new FreePackage();
                    $result       = $packageModel->comboInfo($list, $userId);
                    if (!$result['status']) {
                        return $result;
                    }
                }
                break;
            default:
                return error_code(10000);
        }
        $data['list']     = $list;
        $result['data']   = $data;
        $result['status'] = true;

        return $result;
    }


    /**
     * @param $userId
     * @param $ids
     * @param string $order_type //订单类型
     * @param bool $display         //是否显示所有购物车信息
     * @param int $area_id //收货地址id
     * @param int $point //消费的积分
     * @param string $coupon_code
     * @param bool $free_freight //是否免运费
     * @param int $delivery_type
     * @param array $params 扩展字段信息，传数组
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info($userId, $ids, $order_type = '1',$display = false, $area_id = 0, $point = 0, $coupon_code = "", $free_freight = false, $delivery_type = 1,$params = [])
    {
        $result   = [
            'status' => false,
            'data'   => [
                'user_id'        => $userId,
                'type'           => $order_type,
                'list'           => [],
                'goods_amount'   => 0,         //商品总金额
                'amount'         => 0,              //总金额
                'order_pmt'      => 0,           //订单促销金额            单纯的订单促销的金额
                'goods_pmt'      => 0,           //商品促销金额            所有的商品促销的总计
                'coupon_pmt'     => 0,          //优惠券优惠金额
                'promotion_list' => [],      //促销列表
                'cost_freight'   => 0,        //运费
                'weight'         => 0,               //商品总重
                'coupon'         => [],
                'point'          => $point,              //在刚开始一定要校验积分是否可以使用，
                'point_money'    => 0,              //积分可以抵扣多少金额
                'params'         => [],              //一些可以放到购物车中的参数
            ],
            'msg'    => ""
        ];

        $cartList = $this->getList($userId, $ids, $order_type, $display, $params);
        if (!$cartList['status']) {
            $result['msg'] = $cartList['msg'];
            return $result;
        } else {
            $result['data']['list'] = $cartList['data']['list'];

            //如果没有商品，那么就返回
            if(count($result['data']['list']) == 0){
                $result['status'] = true;
                return $result;
            }

        }

        //算订单总金额
        foreach ($result['data']['list'] as $k => $v) {
            //库存不足不计算金额不可以选择
            if ($v['nums'] > $v['products']['stock']) {
                $result['data']['list'][$k]['is_select'] = false;
                $v['is_select']                          = false;
            }

            //单条商品总价
            $result['data']['list'][$k]['products']['amount'] = bcmul($v['nums'], $v['products']['price'], 2);

            if ($v['is_select']) {
                //算订单总商品价格
                $result['data']['goods_amount'] = bcadd($result['data']['goods_amount'], $result['data']['list'][$k]['products']['amount'], 2);

                //计算促销应用之前的商品优惠
                $result['data']['goods_pmt'] = bcadd($result['data']['goods_pmt'], $result['data']['list'][$k]['products']['promotion_amount'], 2);

                //算订单总价格
                $result['data']['amount'] = bcadd($result['data']['amount'], $result['data']['list'][$k]['products']['amount'], 2);

                // 若为免单订单需要在这里减去免单商品的价格
                if($order_type == self::TYPE_COMBO && $v['products']['is_free'] == 1){
                    $result['data']['amount'] = bcsub($result['data']['amount'], $v['products']['amount'], 2);
                    $result['data']['order_pmt'] = bcadd($result['data']['order_pmt'], $v['products']['amount'], 2);
                }

                //计算总重量
                $result['data']['weight'] = bcadd($result['data']['weight'], bcmul($v['weight'], $v['nums'], 2), 2);
            }
        }

        //门店订单，强制无运费
        if ($delivery_type == 2) {
            $free_freight = true;
        }
        //接下来算订单促销金额,有些模式不需要计算促销信息，这里就增加判断
        if ($order_type == self::TYPE_COMMON) {
            $promotionModel = new Promotion();
            $promotionModel->toPromotion($result['data']);
        } elseif (($order_type == self::TYPE_SKILL || $order_type == self::TYPE_GROUP) && $params) {
            //团购秒杀默认时间过期后，不可以下单
            $promotionModel = new Promotion();
            $promotionInfo  = $promotionModel->getInfo($params['group_id'], true);
            $checkRes       = $promotionModel->setPromotion($promotionInfo, $result['data']);
            //如果依然可以下单，但是是正常销售价，请注释下面的判断
            if (!$checkRes) {
                return error_code(15600);
            }
        } elseif ($order_type == self::TYPE_PINTUAN) {//拼团也计算促销信息
            $promotionModel = new Promotion();
            $promotionModel->toPromotion($result['data']);
        }

        //运费判断
        if (!$this->cartFreight($result, $area_id, $free_freight)) {
            return $result;
        }

        //使用优惠券，判断优惠券是否可用
        if (!$this->cartCoupon($result, $coupon_code)) {
            return $result;
        }

        //使用积分
        if (!$this->cartPoint($result, $userId, $point)) {
            return $result;
        }

        $result['status'] = true;
        return $result;
    }


    /**
     * 设置购物车数量
     * @param $user_id
     * @param $id
     * @param $nums
     * @param int $type
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function setNums($user_id, $id, $nums, $type = 1)
    {
        $result =  error_code(10037);

        $where[] = ['id', 'eq', $id];
        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['type', 'eq', $type];
        $info    = $this->field('product_id')
            ->where($where)
            ->find();

        if (isset($info['product_id']) && !empty($info['product_id'])) {
            $result = $this->add($user_id, $info['product_id'], $nums, 2, $type);
        }

        return $result;
    }


    /**
     * 算运费
     * @param $result //购物车
     * @param $area_id //收货地址id
     * @param bool $free //是否包邮，默认false
     * @return bool
     */
    private function cartFreight(&$result, $area_id, $free = false)
    {
        if (!$free) {
            if ($area_id) {
                $shipModel                      = new Ship();
                $result['data']['cost_freight'] = $shipModel->getShipCost($area_id, $result['data']['weight'], $result['data']['goods_amount']);//运费是商品金额-优惠有金额
                $result['data']['amount']       = bcadd($result['data']['amount'], $result['data']['cost_freight'], 2);
            }
        }
        return true;
    }


    /**
     * 购物车中使用优惠券
     * @param $result
     * @param $coupon_code
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function cartCoupon(&$result, $coupon_code)
    {
        if ($coupon_code != "") {
            $couponModel = new Coupon();
            $couponInfo  = $couponModel->codeToInfo($coupon_code, true);
            if (!$couponInfo['status']) {
                $result = $couponInfo;
                return false;
            }
            $promotionModel = new Promotion();
            $re             = $promotionModel->toCoupon($result['data'], $couponInfo['data']);
            if (!$re['status']) {
                $result = $re;
                return false;
            }
        }
        return true;
    }


    /**
     * 购物车中使用积分
     * @param $result
     * @param $userId
     * @param $point
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function cartPoint(&$result, $userId, $point)
    {
        if ($point != 0) {
            //判断用户是否有这么多积分
            $userModel = new User();
            $oPoint    = $userModel->getUserPoint($userId);
            if ($oPoint['data'] < $point) {
                $result['msg'] = error_code(11600,true);
                return false;
            }
            //判断积分值多少钱
            $settingModel                = new Setting();
            $orders_point_proportion     = $settingModel->getValue('orders_point_proportion'); //订单积分使用比例
            $max_point_deducted_money    = $result['data']['amount'] * ($orders_point_proportion / 100); //最大积分抵扣的钱
            $point_discounted_proportion = $settingModel->getValue('point_discounted_proportion'); //积分兑换比例
            $point_deducted_money        = (int)$point / (int)$point_discounted_proportion; //积分可以抵扣的钱
            if ($max_point_deducted_money < $point_deducted_money) {
                $result['msg'] = error_code(11601,true);
                return false;
            }

            $result['data']['point']       = $point;
            $result['data']['point_money'] = $point_deducted_money;
            $result['data']['amount']      = $result['data']['amount'] - $point_deducted_money;
        }
        return true;
    }

    
    /**
     * 购物车数据
     * @param $user_id
     * @param $input
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function batchSetCart($user_id, $input, $type = 1, $display = false)
    {
        $return = error_code(10037);
        $ids    = [];
        Db::startTrans();
        try {
            //删除用户的所有购物车数据
            $where[] = ['user_id', 'eq', $user_id];
            //$this->where($where)->delete();

            //判断数量是否可以加入购物车
            $product_ids = [];
            foreach ($input as $v) {
                $product_ids[] = $v['product_id'];
            }
            $productsModel    = new Products();
            $products_where[] = ['id', 'in', $product_ids];
            //$products_where[] = ['marketable', 'eq', $productsModel::MARKETABLE_UP];
            $productsList    = $productsModel->field('id,marketable,stock,freeze_stock')
                ->where($products_where)
                ->select();
            $newProductsList = [];
            foreach ($productsList as $v) {
                $newProductsList[$v['id']] = $v;
            }

            //添加购物车数据
            $insert_data = [];
            foreach ($input as $v) {
                $stock = $newProductsList[$v['product_id']]['stock'] - $newProductsList[$v['product_id']]['freeze_stock'];
                if ($stock < $v['nums']) {
                    //数量不足回滚sql
                    Db::rollback();
                    return error_code(12702);
                }
                unset($where);
                $where[]  = array('product_id', 'eq', $v['product_id']);
                $where[]  = array('user_id', 'eq', $user_id);
                $where[]  = ['type', 'eq', $type];
                $cat_info = $this->where($where)->find();

                if (!$cat_info) {
                    $insert_data[] = [
                        'user_id'    => $user_id,
                        'product_id' => $v['product_id'],
                        'nums'       => $v['nums'],
                        'type'       => $type
                    ];
                } else {
                    $cat_info->nums = $v['nums'] + $cat_info['nums'];
                    $cat_info->save();
                    $ids[] = ['id' => $cat_info->id];
                }
            }
            if ($insert_data) {
                $ids = $this->saveAll($insert_data);
                $ids = $ids->toArray();
            }
            //提交数据库
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $return['msg'] = $e->getMessage();
            return $return;
        }

        //查询购物车数据
        $list             = $this->getList($user_id, '', $type, $display);
        $return['status'] = true;
        if ($list['status']) {
            $list['data']['count']  = 0;
            $list['data']['amount'] = 0;
            $list['data']['ids']    = $ids ? array_column($ids, 'id') : [];
            foreach ($list['data']['list'] as $v) {
                //总数量
                $list['data']['count'] += $v['nums'];

                //总价格
                $list['data']['amount'] = bcadd($list['data']['amount'], bcmul($v['nums'], $v['products']['amount'], 2), 2);
            }

            $return['msg']  = '成功';
            $return['data'] = $list['data'];
        } else {
            return error_code(10020);
        }

        return $return;
    }


    /**
     * 批量加入购物车
     *
     * @Author WGG 1490100895@qq.com
     * @DateTime 2021-01-29
     * @param int $user_id
     * @param array $data
     * @return void
     */
    public function batchAdd($user_id, $data)
    {
        try {
            Db::startTrans();
            $cat_ids = [];
            foreach ($data as $product_id => $num) {
                if (!$product_id || !$num) throw new Exception("请选择货品及购买数量");
                $res = $this->add($user_id, $product_id, $num, 2);
                if (!$res['status']) throw new Exception($res['msg']);
                $cat_ids[] = $res['data'];
            }
            Db::commit();
            return [
                'status' => true,
                'data'   => implode(",", $cat_ids),
                'msg'    => ''
            ];
        } catch (Exception $e) {
            Db::rollback();
            return [
                'status' => false,
                'data'   => '',
                'msg'    => $e->getMessage()
            ];
        }
    }
}
