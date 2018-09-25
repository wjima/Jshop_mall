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

        $goods                 = $goodsModel->where(['id'=>$product['goods_id']])->field('name,image_id,bn,marketable')->find();//后期调整
        //判断如果是下架状态，就返回false
        if(!($goods && $goods['marketable'] == $goodsModel::MARKETABLE_UP)){
            return $result;
        }


        $product['name']       = $goods['name'];
        $product['image_id']   = $goods['image_id'];
        $product['bn'] = $goods['bn'];
        $product['image_path'] = _sImage($goods['image_id']);


        $product['stock'] = $goodsModel->getStock($product);


        $product['price'] = $goodsModel->getPrice($product);

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