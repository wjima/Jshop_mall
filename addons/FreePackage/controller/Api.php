<?php

namespace addons\FreePackage\controller;

use addons\FreePackage\model\FreePackage;
use addons\FreePackage\model\FreePackageItems;
use app\common\model\Cart;
use app\common\model\Order;
use app\common\model\UserToken;

class Api extends \app\common\controller\Api
{
    /**
     * 获取配置信息
     * @return \think\response\Json
     */
    public function setting()
    {
        $setting = getAddonsConfig("FreePackage")['setting'];
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => $setting
        ];
        return json($result);
    }

    public function getCartIds(){
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => ''
        ];
        $type = input('param.order_type',8);
        $where[] = ['user_id', 'eq', $this->userId];
        $where[] = ['type', 'eq', $type];
        $vclass = getSetting('virtual_card_class');
        if($vclass)
        {
            $where[] = ['g.goods_cat_id', 'neq', $vclass];
        }
        $cartModel  = new Cart();
        $cartIds = $cartModel
            ->field('c.id')
            ->alias('c')
            ->where($where)
            ->join('products p', 'p.id = c.product_id')
            ->join('goods g', 'g.id = p.goods_id')
            ->select()
            ->toArray();
        if($cartIds){
            $cartIds = array_column($cartIds,'id');
            $cartIds = implode(',',$cartIds);
        }else{
            $cartIds = '';
        }
        $result['data'] = $cartIds;
        return $result;
    }

}
