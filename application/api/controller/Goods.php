<?php

namespace app\api\controller;

use app\common\controller\Api;
use Request;
use app\common\model\Goods as GoodsModel;
use app\common\model\Products;
use app\common\model\Brand;

/***
 * 商品相关接口
 * Class Goods
 * @package app\api\controller
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-23 19:45
 */
class Goods extends Api
{

    //商品允许出现字段
    private $goodsAllowedFields = [
        'id','bn','name','brief','price','costprice','mktprice','image_id','goods_cat_id','goods_type_id','brand_id'
        ,'is_nomal_virtual','marketable','stock','freeze_stock','weight','unit','intro','spes_desc','comments_count','view_count','buy_count','uptime'
        ,'downtime','sort','is_hot','is_recommend','ctime','utime','products','params'
    ];
    //货品允许字段
    private $productAllowedFields = [
        'id','goods_id','barcode','sn','price','costprice','mktprice','marketable','stock','freeze_stock','spes_desc','is_defalut'
    ];

    private function allowedField($data,$type='goods')
    {
        $return_data = [
            'status' => false,
            'msg'    => '有非法查询字段',
            'data'   => []
        ];

        if($data == '' && $data != '*') {
            $return_data['msg'] = '查询字段错误';
            return $return_data;
        }
        if($data != '*') {
            $tmpData = explode(',',$data);
            foreach($tmpData as $key => $value) {
                if($type == 'goods') {
                    if(!in_array($value,$this->goodsAllowedFields)) {
                        $return_data['msg'] .= ':' . $value;
                        return $return_data;
                    }
                }elseif($type == 'product') {
                    if(!in_array($value,$this->productAllowedFields)) {
                        $return_data['msg'] .= ':' . $value;
                        return $return_data;
                    }
                }
            }
        }
        $return_data['status'] = true;
        $return_data['msg']    = '字段校检通过';
        return $return_data;
    }

    /**
     * 检查排序字段
     * @param $order
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-29 16:42
     */
    private function allowedOrder($order)
    {
        $return_data = [
            'status' => false,
            'msg'    => '排序错误',
            'data'   => []
        ];
//        if(is_array($order)) {
//            $return_data['msg'] = '排序字段不能为数组';
//            return $return_data;
//        }
//        if(strpos($order,',') !== false) {
//            $tmp_order = explode(',',$order);
//            foreach($tmp_order as $k => $v) {
//                $field = explode(' ',$v);
//                if(count($field)<2) {
//                    $return_data['msg'] = '排序缺失条件或字段';
//                    return $return_data;
//                }
//                if(!in_array($field,$this->goodsAllowedFields)) {
//                    $return_data['msg'] = '字段：' . $field[0] . '不在可排序字段内';
//                    return $return_data;
//                }
//            }
//
//        }else {
//            $field = explode(' ',$order);
//
//            if(count($field)<2) {
//                $return_data['msg'] = '排序缺失条件或字段';
//                return $return_data;
//            }
//            if(!in_array($field[0],$this->goodsAllowedFields)) {
//                $return_data['msg'] = '字段：' . $field[0] . '不在可排序字段内';
//                return $return_data;
//            }
//        }
        $return_data['status'] = true;
        $return_data['msg']    = '排序校检通过';
        return $return_data;
    }

    /**
     * 获取商品列表
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 19:46
     */
    public function getList()
    {
        $return_data = [
            'status' => false,
            'msg'    => '查询失败',
            'data'   => []
        ];
        $field       = input('field','*');
        $page        = input('page/d',1);
        $limit       = input('limit/d');
        $order       = input('order','sort asc');

        if(input('?param.where')){
            $postWhere = json_decode(input('param.where'),true);
            //判断商品搜索,
            if(isset($postWhere['search_name'])){
                $where[] = ['name|bn|brief','LIKE', '%'.$postWhere['search_name'].'%'];
            }
            //商品分类
            if(isset($postWhere['cat_id'])){
                $where[] = ['goods_cat_id','eq', $postWhere['cat_id']];
            }
            //价格区间
            if(isset($postWhere['price_f']) && isset($postWhere['price_t'])){
                $where[] = ['price',['>=',$postWhere['price_f']], ['<',$postWhere['price_t']],'and'];
            }
            if(isset($postWhere['recommend'])) {
                $where[] = ['is_recommend', 'eq', '1'];
            }
            if(isset($postWhere['hot'])){
                $where[] = ['is_hot','eq', '1'];
            }
        }
        $goodsModel = new GoodsModel();
        $where[] = ['marketable', 'eq',$goodsModel::MARKETABLE_UP];


        $return_data = $this->allowedField($field);
        if(!$return_data['status']) {
            return $return_data;
        }
        $return_data = $this->allowedOrder($order);
        if(!$return_data['status']) {
            return $return_data;
        }


        $page_limit = config('jshop.page_limit');
        $limit      = $limit ? $limit : $page_limit;

        $returnGoods = $goodsModel->getList($field,$where,$order,$page,$limit);

        if($returnGoods['status']) {
            $return_data ['msg']          = '查询成功';
            $return_data ['data']['list'] = $returnGoods['data'];
            $return_data ['data']['total_page'] = $returnGoods['total'];
        }

        $return_data['data']['page']  = $page;
        $return_data['data']['limit'] = $limit;
        $return_data['data']['where'] = $where;
        $return_data['data']['order'] = $order;
        $return_data['sql'] = $returnGoods['sql'];

        return $return_data;
    }

    /**
     * 获取商品明细
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 19:47
     */
    public function getDetial()
    {
        $return_data = [
            'status' => false,
            'msg'    => '查询失败',
            'data'   => [ ]
        ];
        $goods_id    = input('id/d',0);//商品ID
        $token    = input('token','');//token值 会员登录后传
        if(!$goods_id) {
            return $return_data;
        }
        $field       = input('field','*');
        $return_data = $this->allowedField($field);
        $goodsModel  = new GoodsModel();
        $returnGoods = $goodsModel->getGoodsDetial($goods_id,$field,$token);
        if($returnGoods['status']) {
            $return_data ['msg']  = '查询成功';
            $return_data ['data'] = $returnGoods['data'];
        }else{
            $return_data['msg'] = $returnGoods['msg'];
            $return_data['status'] = false;
        }
        return $return_data;
    }

    /**
     * 根据sku获取相关价格，库存等信息
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-02 10:09
     */
    public function getSkuDetial()
    {
        $return_data = [
            'status' => false,
            'msg'    => '无此规格信息',
            'data'   => [ ]
        ];
        $spec_value  = input('spec','');
        $goods_id    = input('id/d',0);//商品ID
        if(!$goods_id) {
            return $return_data;
        }
        if(!$spec_value) {
            return $return_data;
        }
        $goodsModel  = new GoodsModel();
        $returnGoods = $goodsModel->getGoodsDetial($goods_id,'id,bn,name,image_id,goods_cat_id,goods_type_id,brand_id,spes_desc');
        if($returnGoods['status']) {
            $goods = $returnGoods['data'];
            if($goods['products']) {
                $products = $goods['products'];
                foreach($products as $key => $val) {
                    if($val['spes_desc'] == $spec_value) {
                        //获取价格
                        $val['price'] = $goodsModel->getPrice($val);
                        $val['stock'] = $goodsModel->getStock($val);
                        $return_data['data']   = $val;
                        $return_data['msg']    = '获取成功';
                        $return_data['status'] = true;
                    }
                }
            }
        }
        return $return_data;
    }

    /**
     * 获取参数接口
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-02 11:18
     */
    public function getGoodsParams()
    {
        $return_data = [
            'status' => false,
            'msg'    => '无参数相关信息',
            'data'   => [ ]
        ];
        $goods_id    = input('id/d',0);//商品ID
        $goodsModel  = new GoodsModel();
        $brandModel  = new Brand();
        $returnGoods = $goodsModel->getOne($goods_id,'id,bn,name,brand_id,image_id,params,spes_desc');

        if($returnGoods['status']){
            $params = [];
            $data = $returnGoods['data'];
            if(isset($data['brand_id']))
            {
                $brand = $brandModel::get($data['brand_id']);
                $params[] = [
                    'name'=>'品牌',
                    'value'=>$brand['name'],
                ];

            }
            if($data['params'])
            {
                $goodsParams=unserialize($data['params']);
                $goodsParams = array_filter($goodsParams);
                if($goodsParams){
                    foreach($goodsParams as $key=>$val){
                        if(is_array($val)){
                            $val = implode('、',$val);
                            $params[]=[
                                'name'=>$key,
                                'value'=>$val
                            ];
                        }else{
                            $params[]=[
                                'name'=>$key,
                                'value'=>$val
                            ];
                        }

                    }
                }
            }
            $return_data['data'] = $params;
            $return_data['status'] = true;
            $return_data['msg'] = '查询成功';
        }
        return $return_data;
    }

    /**
     * 获取该货品相关信息
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-08 10:18
     */
    public function getProductInfo()
    {
        $return_data = [
            'status' => false,
            'msg'    => '无参数相关信息',
            'data'   => [ ]
        ];
        $product_id  = input('id/d',0);//货品ID
        if(!$product_id) {
            $return_data['msg'] = '货品ID缺失';
            return $return_data;
        }

        $productsModel = new Products();
        $product       = $productsModel->getProductInfo($product_id);
        $return_data['msg'] = $product['msg'];
        if(!$product['status']){
            return $return_data;
        }
        $return_data['data'] = $product['data'];
        $return_data['status'] = true;
        return $return_data;
    }


    /**
     * 获取商品评价
     */
    public function getGoodsComment()
    {
        $goods_id = input('goods_id');
        $page     = input('page', 1);
        $limit    = input('limit', 10);
        if (empty($goods_id)) {
            return error_code(13403);
        }
        $res = model('common/GoodsComment')->getList($goods_id, $page, $limit, 1);
        return $res;
    }
}