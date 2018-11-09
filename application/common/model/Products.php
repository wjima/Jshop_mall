<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use app\common\model\Goods;

/**
 * 产品类型
 * Class Products
 * @package app\common\model
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:09
 */
class Products extends Common
{

    #use SoftDelete;
    #protected $deleteTime = 'isdel';

    const MARKETABLE_UP = 1; //上架
    const MARKETABLE_DOWN = 2;//下架
    const DEFALUT_NO = 2;//非默认货品
    const DEFALUT_YES = 1;//默认货品


    /**
     * 保存货品
     * User:wjima
     * Email:1457529125@qq.com
     * @param array $data
     * @return mixed
     */
    public function doAdd($data = [])
    {
        $result=$this->insert($data);
        if($result)
        {
            return $this->getLastInsID();
        }
        return $result;
    }

    public function goods()
    {
        return $this->hasOne('Goods', 'id','goods_id');
    }

    /**
     * 根据货品ID获取货品信息
     * @param array  $where
     * @param bool $isPromotion 默认是true，如果为true的时候，就去算此商品的促销信息，否则，就不算
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-08 11:14
     */
    public function getProductInfo($id,$isPromotion = true)
    {
        $result  = [
            'status' => false,
            'msg'    => '获取失败',
            'data'   => [ ],
        ];
        $product = $this->where(['id'=>$id])->field('*')->find();
        if(!$product) {
            return $result;
        }
        $goodsModel = new Goods();

        $goods      = $goodsModel->where(['id'=>$product['goods_id']])->field('name,image_id,bn,marketable,spes_desc')->find();//后期调整
        //判断如果没有商品，就返回false
        if(!($goods)){
            return $result;
        }


        $product['name']       = $goods['name'];
        $product['image_id']   = $goods['image_id'];
        $product['bn'] = $goods['bn'];
        $product['image_path'] = _sImage($goods['image_id']);


        $product['stock'] = $goodsModel->getStock($product);

        $product['price'] = $goodsModel->getPrice($product);

        //如果是多规格商品，算多规格
        if($goods['spes_desc']) {
            $defaultSpec = [];
            $spesDesc     = unserialize($goods['spes_desc']);

            //设置on的效果
            $productSpesDesc = getProductSpesDesc($product['spes_desc']);
            foreach($spesDesc as $k => $v){
                foreach($v as $j){
                    $defaultSpec[$k][$j]['name'] = $j;
                    if($productSpesDesc[$k] == $j){
                        $defaultSpec[$k][$j]['is_default'] = true;
                    }else{
                        $defaultSpec[$k][$j]['product_id'] = 0;
                    }
                }
            }
            //取出刨除去当前id的其他兄弟货品
            $product_list = $this->where(
                [
                    ['goods_id','eq',$product['goods_id']],
                    ['id','neq',$id],
                ]
            )->select();
            if(!$product_list->isEmpty()){
                $product_list = $product_list->toArray();
            }else{
                $product_list = [];
            }
            foreach($product_list as $k => $v){
                $product_list[$k]['temp_spes_desc'] = getProductSpesDesc($v['spes_desc']);
            }
            //遍历二维多规格信息，设置货品id
            foreach($defaultSpec as $k => $j){
                foreach($j as $v => $l){
                    //如果是默认选中的，不需要找货品信息
                    if(isset($l['is_default'])){
                        continue;
                    }
                    $tempProductSpesDesc = $productSpesDesc;
                    $tempProductSpesDesc[$k] = $v;
                    //循环所有货品，找到对应的多规格
                    foreach($product_list as $a){
                        if(!array_diff_assoc($a['temp_spes_desc'],$tempProductSpesDesc)){
                            $defaultSpec[$k][$v]['product_id'] = $a['id'];
                            break;           //找到了，就不循环剩下的货品了，没意义
                        }
                    }
                }
            }
            $product['default_spes_desc'] = $defaultSpec;
        }

        $product['amount'] = $product['price'];       //商品总价格,商品单价乘以数量
        $product['promotion_list'] = [];             //促销列表
        $product['promotion_amount'] = 0;         //如果商品促销应用了，那么促销的金额
        //算促销信息
        if($isPromotion){
            $product['amount'] = $product['price'];
            //模拟购物车数据库结构，去取促销信息
            $miniCart =[
                'goods_amount' =>$product['amount'],         //商品总金额
                'amount' => $product['amount'],              //总金额
                'order_pmt' => 0,           //订单促销金额            单纯的订单促销的金额
                'goods_pmt' => 0,           //商品促销金额            所有的商品促销的总计
                'coupon_pmt' => 0,          //优惠券优惠金额
                'promotion_list' => [],      //促销列表
                'cost_freight' => 0,        //运费
                'weight' => 0,               //商品总重
                'coupon' => [],
                'point' => 0,
                'point_money' => 0,
                'list' => [
                    [
                        'id'=> 0,
                        'user_id' => '',
                        'product_id' => $id,
                        'nums' => 1,
                        'products' => $product->toArray(),
                        'is_select' => true
                    ]
                ]
            ];
            $promotionModel = new Promotion();
            $cart = $promotionModel->toPromotion($miniCart);

            //把促销信息和新的价格等，覆盖到这里
            if($cart['list'][0]['products']['promotion_list']){
                $newProduct = $cart['list'][0]['products'];
                //把订单促销和商品促销合并,都让他显示
                $promotionList = $cart['promotion_list'];
                foreach($newProduct['promotion_list'] as $k => $v){
                    $promotionList[$k] = $v;
                }
                $product['price'] = $newProduct['price'];                               //新的商品单价
                $product['amount'] = $newProduct['amount'];                             //商品总价格
                $product['promotion_list'] = $promotionList;             //促销列表
                $product['promotion_amount'] = $newProduct['promotion_amount'];         //如果商品促销应用了，那么促销的金额

            }
        }
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $product->toArray(),
        ];
        return $result;
    }

    public function updateProduct($product_id,$data=[])
    {
        return $this->save($data,['id'=>$product_id]);
    }

    public function deleteProduct($ids = [])
    {
        return $this->where('id', 'in', $ids)->delete(true);
    }

}