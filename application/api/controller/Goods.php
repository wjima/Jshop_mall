<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\GoodsBrowsing;
use app\common\model\GoodsCat;
use app\common\model\GoodsComment;
use app\common\model\GoodsExtendCat;
use think\Db;
use think\facade\Cache;
use think\facade\Request;
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
    //商品允许出现字段，允许出现的字段跟查询的字段不太一样，允许查询的只能不能有：album、isfav、product、image_url
    private $goodsAllowedFields = [
        'id', 'bn', 'name', 'brief', 'price', 'mktprice', 'image_id', 'video_id', 'goods_cat_id', 'goods_type_id', 'brand_id', 'label_ids', 'is_nomal_virtual', 'marketable', 'stock', 'weight', 'unit', 'intro', 'spes_desc', 'comments_count', 'view_count', 'buy_count', 'uptime', 'downtime', 'sort', 'is_hot', 'is_recommend', 'ctime', 'utime', 'params'
    ];
    //货品允许字段
    private $productAllowedFields = [
        'id', 'goods_id', 'barcode', 'sn', 'price', 'mktprice', 'marketable', 'stock', 'spes_desc', 'is_defalut'
    ];

    private function allowedField($data, $type = 'goods')
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10028, true),
            'data'   => []
        ];

        if ($data == '' && $data != '*') {
            // $return_data['msg'] = error_code(10029,true);
            return error_code(10029);
        }
        if ($data != '*') {
            $tmpData = explode(',', $data);
            foreach ($tmpData as $key => $value) {
                if ($type == 'goods') {
                    if (!in_array($value, $this->goodsAllowedFields)) {
                        $return_data['msg'] .= ':' . $value;
                        return $return_data;
                    }
                } elseif ($type == 'product') {
                    if (!in_array($value, $this->productAllowedFields)) {
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
            'msg'    => error_code(10031, true),
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 19:46
     */
    public function getList()
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10027, true),
            'data'   => []
        ];
        $field       = input('field', '*');
        $page        = input('page/d', 1);
        $limit       = input('limit/d');
        $order       = input('order', 'sort asc,id desc');
        $filter      = []; //过滤条件
        $class_name['data']  = '';
        $where  = [];
        $whereRaw = ' 1=1 '; //扩展sql
        if (input('?param.where','','safe_filter')) {
            $postWhere = json_decode(input('param.where','','safe_filter'), true);

            //套餐商品
            if(isset($postWhere['is_combo']) && $postWhere['is_combo']){
                $where[] = ['g.is_combo', 'eq', $postWhere['is_combo']];
            }

            //判断商品搜索,
            if (isset($postWhere['search_name']) && $postWhere['search_name']) {
                $where[] = ['g.name|g.bn|g.brief', 'LIKE', '%' . $postWhere['search_name'] . '%'];
            }
            if (isset($postWhere['bn']) && $postWhere['bn']) {
                $where[] = ['g.bn', '=', $postWhere['bn']];
            }
            //商品分类,同时取所有子分类 todo 无限极分类时要注意
            if (isset($postWhere['cat_id']) && $postWhere['cat_id']) {
                $goodsCatModel = new GoodsCat();
                $cat_ids        = [];
                $childCats     = $goodsCatModel->getCatByParentId($postWhere['cat_id']);
                if (!$childCats->isEmpty()) {
                    $filter['child_cats'] = $childCats;
                }
                $cat_ids   = array_column($childCats->toArray(), 'id');
                $cat_ids[] = $postWhere['cat_id'];
                //$where[]  = ['g.goods_cat_id', 'in', $catIds];
                $class_name = $goodsCatModel->getNameById($postWhere['cat_id']);

                $goodsExtendCat = new GoodsExtendCat();
                $goods_ids = $goodsExtendCat->getGoodsIdByCat($cat_ids, true);
                if ($goods_ids) {
                    $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $cat_ids) . ') or g.id in (' . implode(',', $goods_ids) . ') ) ';
                } else {
                    $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $cat_ids) . ') ) ';
                }
            }
            //价格区间
            if (isset($postWhere['price_f']) && $postWhere['price_f']) {
                $where[] = ['g.price', '>=', $postWhere['price_f']];
            }
            if (isset($postWhere['price_t']) && $postWhere['price_t']) {
                $where[] = ['g.price', '<', $postWhere['price_t']];
            }
            if (isset($postWhere['recommend'])) {
                $where[] = ['g.is_recommend', 'eq', '1'];
            }
            if (isset($postWhere['hot'])) {
                $where[] = ['g.is_hot', 'eq', '1'];
            }
            //品牌筛选
            if (isset($postWhere['brand_id']) && $postWhere['brand_id']) {
                $where[] = ['g.brand_id', 'in', $postWhere['brand_id']];
            }
            //标签筛选
            if (isset($postWhere['label_id']) && $postWhere['label_id']) {
                $where[] = ['', 'exp', Db::raw('FIND_IN_SET(' . remove_xss($postWhere['label_id']) . ',g.label_ids)')];
            }
        }

        $goodsModel = new GoodsModel();
        $where[]    = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];

        $return_data = $this->allowedField($field);
        if (!$return_data['status']) {
            return $return_data;
        }
        $return_data = $this->allowedOrder($order);
        if (!$return_data['status']) {
            return $return_data;
        }


        $page_limit = config('jshop.page_limit');
        $limit      = $limit ? $limit : $page_limit;

        $returnGoods = $goodsModel->getList($field, $where, $order, $page, $limit, $whereRaw);
        if ($returnGoods['status']) {
            $return_data['msg']                = '查询成功';
            $return_data['data']['list']       = $returnGoods['data'];
            $return_data['data']['total_page'] = $returnGoods['total'];
            $return_data['data']['filter']      = isset($returnGoods['filter']) ? array_merge($returnGoods['filter'], $filter) : [];
        }
        $return_data['data']['page']  = $page;
        $return_data['data']['limit'] = $limit;
        $return_data['data']['where'] = $postWhere;
        $return_data['data']['order'] = $order;
        $return_data['data']['class_name'] = $class_name['data'] ? $class_name['data'] : '';
        return $return_data;
    }

    /**
     * 获取商品明细
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 19:47
     */
    public function getDetial()
    {
        //$return_data = [
        //  'status' => false,
        //  'msg'    => error_code(10027, true),
        //  'data'   => []
        // ];
        $goods_id    = input('id/d', 0); //商品ID
        $token       = input('token', ''); //token值 会员登录后传
        if (!$goods_id) {
            return error_code(10027);
        }
        $field       = input('field', '*');
        $return_data = $this->allowedField($field);
        $goodsModel  = new GoodsModel();
        $returnGoods = $goodsModel->getGoodsDetial($goods_id, $field, $token);

        // if ($returnGoods['status']) {
        //     // $return_data['msg']  = '';
        //     // $return_data['data'] = $returnGoods['data'];
        //     return $returnGoods;
        // } else {
        //     $return_data['msg']    = $returnGoods['msg'];
        //     $return_data['status'] = false;
        // }
        return $returnGoods;
    }

    //app版的获取商品明细接口，因为多规格的传值问题，导致java解析不了多规格数据，在此做了转化
    public function appGetDetail()
    {
        $re = $this->getDetial();
        if ($re['data']['product']['default_spes_desc']) {
            $arr                                        = $re['data']['product']['default_spes_desc'];
            $re['data']['product']['default_spes_desc'] = [];
            foreach ($arr as $k => $v) {
                $n_v = [];
                foreach ($v as $vv) {
                    if (!isset($vv['is_default'])) {
                        $vv['is_default'] = false;
                    }
                    if (!isset($vv['product_id'])) {
                        $vv['product_id'] = 0;
                    }
                    $n_v[] = $vv;
                }
                $re['data']['product']['default_spes_desc'][] = [
                    'items'  => $k,
                    'fenlei' => $n_v
                ];
            }
        } else {
            $re['data']['product']['default_spes_desc'] = [];
        }
        return $re;
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
            'data' => [],
            'msg' => ''
        ];
        $spec_value  = input('spec', '');
        $goods_id    = input('id/d', 0); //商品ID
        $token       = input('token', ''); //token值 会员登录后传

        if (!$goods_id || !$spec_value) {
            return error_code(12701);
        }

        $goodsModel  = new GoodsModel();
        $returnGoods = $goodsModel->getGoodsDetial($goods_id, 'id,bn,name,image_id,goods_cat_id,goods_type_id,brand_id,spes_desc', $token);
        if ($returnGoods['status']) {
            $goods = $returnGoods['data'];
            if ($goods['products']) {
                $products = $goods['products'];
                foreach ($products as $key => $val) {
                    if ($val['spes_desc'] == $spec_value) {
                        //获取价格
                        $val['price']          = $goodsModel->getPrice($val);
                        $val['stock']          = $goodsModel->getStock($val);
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
            'msg'    => error_code(10033, true),
            'data'   => []
        ];
        $goods_id    = input('id/d', 0); //商品ID
        $goodsModel  = new GoodsModel();
        $brandModel  = new Brand();
        $returnGoods = $goodsModel->getOne($goods_id, 'id,bn,name,brand_id,image_id,params,spes_desc');

        if ($returnGoods['status']) {
            $params = [];
            $data   = $returnGoods['data'];
            if ($data['params']) {
                $goodsParams = unserialize($data['params']);
                $goodsParams = array_filter($goodsParams);
                if ($goodsParams) {
                    foreach ($goodsParams as $key => $val) {
                        if (is_array($val)) {
                            $val      = implode('、', $val);
                            $params[] = [
                                'name'  => $key,
                                'value' => $val ? $val : ''
                            ];
                        } else {
                            $params[] = [
                                'name'  => $key,
                                'value' => $val ? $val : ''
                            ];
                        }
                    }
                }
            }
            $return_data['data']   = $params;
            $return_data['status'] = true;
            $return_data['msg']    = '查询成功';
        }
        return $return_data;
    }

    /**
     * 获取该货品相关信息
     * @param int $user_id
     * @return array
     */
    public function getProductInfo()
    {
        // $return_data = error_code(10033);
        $product_id  = input('id/d', 0); //货品ID
        $token       = input('token', ''); //token值 会员登录后传
        $type       = input('type', 'goods'); //商品类型,默认是商品
        if (!$product_id) {
            // $return_data['msg'] = error_code(14011, true);
            return error_code(14011);
        }

        $productsModel      = new Products();
        $user_id            = getUserIdByToken($token); //获取user_id
        $product            = $productsModel->getProductInfo($product_id, true, $user_id, $type);
        // $return_data['msg'] = $product['msg'];
        // if (!$product['status']) {
        //     return $return_data;
        // }
        // $return_data['data']   = $product['data'];
        // $return_data['status'] = true;
        return $product;
    }

    //app版的获取商品明细接口，因为多规格的传值问题，导致java解析不了多规格数据，在此做了转化
    public function appGetProductInfo()
    {
        $re = $this->getProductInfo();
        if ($re['data']['default_spes_desc']) {
            $arr                             = $re['data']['default_spes_desc'];
            $re['data']['default_spes_desc'] = [];
            foreach ($arr as $k => $v) {
                $n_v = [];
                foreach ($v as $vv) {
                    if (!isset($vv['is_default'])) {
                        $vv['is_default'] = false;
                    }
                    if (!isset($vv['product_id'])) {
                        $vv['product_id'] = 0;
                    }
                    $n_v[] = $vv;
                }
                $re['data']['default_spes_desc'][] = [
                    'items'  => $k,
                    'fenlei' => $n_v
                ];
            }
        } else {
            $re['data']['default_spes_desc'] = [];
        }
        return $re;
    }


    /**
     * 获取商品评价
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsComment()
    {
        $goods_id = input('goods_id');
        $page     = input('page', 1);
        $limit    = input('limit', 10);
        $order    = input('order');
        if (empty($goods_id)) {
            return error_code(13403);
        }
        $model = new GoodsComment();
        $res   = $model->getList($goods_id, $page, $limit, 1, $order);
        return $res;
    }


    /**
     * 获取某个分类的热卖商品
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsCatHotGoods()
    {
        $cat_id = Request::param('cat_id');
        $limit  = Request::param('limit', 6);
        $model  = new GoodsModel();
        $res    = $model->getGoodsCatHotGoods($cat_id, $limit);
        return $res;
    }

    /**
     * 获取推荐商品
     */
    public function getPickGoods()
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10027, true),
            'data'   => []
        ];
        $field       = input('field', 'id,bn,name,brief,price,mktprice,image_id,goods_cat_id,goods_type_id,brand_id,stock,unit,spes_desc,view_count,buy_count,label_ids');
        $page        = input('page/d', 1);
        $limit       = input('limit/d', 10);
        $order       = input('order', 'buy_count desc');
        $token       = input('token/s', '');
        $user_id     = getUserIdByToken($token); //获取user_id
        $lastLimit = 0;
        if ($user_id != 0) {
            //取浏览记录
            $goodsBrowsing = new GoodsBrowsing();
            $browsing      = $goodsBrowsing->getList($user_id, 1, $limit);
            if ($browsing['status'] && $browsing['data']['count'] > 0) {
                if (count($browsing['data']['list']) < $limit) {
                    $lastLimit = $limit - count($browsing['data']['list']);
                }
                $goodsIds = array_column($browsing['data']['list'], 'goods_id');
                $where[]  = ['g.id', 'in', $goodsIds];
            }
        }
        $filter      = []; //过滤条件
        $class_name['data']  = '';
        $whereRaw = '1 = 1';
        if (input('?param.where','','safe_filter')) {
            $postWhere = json_decode(input('param.where','','safe_filter'), true);
            //判断商品搜索,
            if (isset($postWhere['search_name']) && $postWhere['search_name']) {
                $where[] = ['g.name|g.bn|g.brief', 'LIKE', '%' . $postWhere['search_name'] . '%'];
            }
            if (isset($postWhere['bn']) && $postWhere['bn']) {
                $where[] = ['g.bn', '=', $postWhere['bn']];
            }
            //商品分类,同时取所有子分类 todo 无限极分类时要注意
            if (isset($postWhere['cat_id']) && $postWhere['cat_id']) {
                $goodsCatModel = new GoodsCat();
                $cat_ids        = [];
                $childCats     = $goodsCatModel->getCatByParentId($postWhere['cat_id']);
                if (!$childCats->isEmpty()) {
                    $filter['child_cats'] = $childCats;
                }
                $cat_ids   = array_column($childCats->toArray(), 'id');
                $cat_ids[] = $postWhere['cat_id'];
                //$where[]  = ['g.goods_cat_id', 'in', $catIds];
                $class_name = $goodsCatModel->getNameById($postWhere['cat_id']);

                $goodsExtendCat = new GoodsExtendCat();
                $goods_ids = $goodsExtendCat->getGoodsIdByCat($cat_ids, true);
                if ($goods_ids) {
                    $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $cat_ids) . ') or g.id in (' . implode(',', $goods_ids) . ') ) ';
                } else {
                    $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $cat_ids) . ') ) ';
                }
            }
            //价格区间
            if (isset($postWhere['price_f']) && $postWhere['price_f']) {
                $where[] = ['g.price', '>=', $postWhere['price_f']];
            }
            if (isset($postWhere['price_t']) && $postWhere['price_t']) {
                $where[] = ['g.price', '<', $postWhere['price_t']];
            }
            if (isset($postWhere['recommend'])) {
                $where[] = ['g.is_recommend', 'eq', '1'];
            }
            if (isset($postWhere['hot'])) {
                $where[] = ['g.is_hot', 'eq', '1'];
            }
            //品牌筛选
            if (isset($postWhere['brand_id']) && $postWhere['brand_id']) {
                $where[] = ['g.brand_id', 'in', $postWhere['brand_id']];
            }
            //标签筛选
            if (isset($postWhere['label_id']) && $postWhere['label_id']) {
                $where[] = ['', 'exp', Db::raw('FIND_IN_SET(' . remove_xss($postWhere['label_id']) . ',g.label_ids)')];
            }
        }

        $goodsModel = new GoodsModel();
        $where[]    = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];

        $return_data = $this->allowedField($field);
        if (!$return_data['status']) {
            return $return_data;
        }
        $return_data = $this->allowedOrder($order);
        if (!$return_data['status']) {
            return $return_data;
        }

        $page_limit = config('jshop.page_limit');
        $limit      = $limit ? $limit : $page_limit;
        $returnGoods = $goodsModel->getList($field, $where, $order, $page, $limit, $whereRaw);
        if ($lastLimit > 0) {
            $where[]    = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];
            $otherGoods = $goodsModel->getList($field, $where, $order, $page, $lastLimit, $whereRaw);
            $returnGoods['data'] = array_merge($returnGoods['data'], $otherGoods['data']);
        }
        if ($returnGoods['status']) {
            $return_data['msg']                = '查询成功';
            $return_data['data']['list']       = $returnGoods['data'];
            $return_data['data']['total_page'] = $returnGoods['total'];
            $return_data['data']['filter']      = isset($returnGoods['filter']) ? array_merge($returnGoods['filter'], $filter) : [];
        }
        $return_data['data']['page']  = $page;
        $return_data['data']['limit'] = $limit;
        $return_data['data']['where'] = $postWhere;
        $return_data['data']['order'] = $order;
        $return_data['data']['class_name'] = $class_name['data'] ? $class_name['data'] : '';
        return $return_data;
    }


    /*
     * 全部商品
     * */
    public function goodsall()
    {
        $page       = input('page', 1);
        $limit      = input('limit', 10);
        $order      = input('order', 10);
        $goodsModel = new GoodsModel();
        return $goodsModel->goods_all($page, $limit, $order);
    }


    /*
     * 上新商品
     * */
    public function newgoods()
    {
        $page       = input('page', 1);
        $limit      = input('limit', 10);
        $order      = input('order', 10);
        $goodsModel = new GoodsModel();
        return $goodsModel->newgoods($page, $limit, $order);
    }


    /*
     * 促销数据
     * */
    public function promotiongoods()
    {
        $page       = input('page', 1);
        $limit      = input('limit', 10);
        $order      = input('order', 10);
        $goodsModel = new GoodsModel();
        return $goodsModel->promotiongoods($page, $limit, $order);
    }

    /**
     * 获取销量排名前十的商品
     */
    public function salesRanking()
    {
        $goodsModel = new GoodsModel();
        return $goodsModel->salesRanking();
    }
}
