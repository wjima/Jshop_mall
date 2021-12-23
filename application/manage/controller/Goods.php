<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\GoodsExtendCat;
use app\common\model\GoodsGrade;
use app\common\model\UserGrade;
use Request;
use app\common\model\Goods as goodsModel;
use app\common\model\GoodsType;
use app\common\model\GoodsCat;
use app\common\model\Brand;
use app\common\model\Products;
use app\common\model\GoodsImages;
use app\common\model\GoodsTypeParams;
use think\Db;
use app\common\validate\Goods as GoodsValidate;
use app\common\validate\Products as ProductsValidate;


/**
 * 商品
 * Class Goods
 *
 * @package app\Manage\controller
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-11 17:20
 */
class Goods extends Manage
{

    private $spec = [];//规格数组
    static $sku_item;//规格
    static $deep_key;//规格深度
    static $total_item;//总规格


    /**
     * 商品列表
     *
     * @return mixed
     */
    public function index()
    {
        $goodsModel = new goodsModel();
        $statics    = $goodsModel->staticGoods();
        $this->assign('statics', $statics);
        if (Request::isAjax()) {
            $filter = input('request.');
            return $goodsModel->tableData($filter);
        }
        return $this->fetch('index');
    }

    public function add()
    {
        $this->_common();
        //处理会员价
        if ($this->view->gradelist) {
            $gradelist = [];
            foreach ($this->view->gradelist as $key => $value) {
                $gradelist[$key]                = $value;
                $gradelist[$key]['grade_price'] = 0;
            }
        }
        return $this->fetch('add');
    }

    /**
     * 编辑商品公共数据
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-12 17:34
     */
    private function _common()
    {
        //类型
        $goodsTypeModel = new GoodsType();
        $typeList       = $goodsTypeModel->getAllTypes(0);
        $this->assign('typeList', $typeList);

        //分类
        $goodsCatModel = new GoodsCat();
        $list          = $goodsCatModel->order('sort asc')->select()->toArray();
        $tree          = $goodsCatModel->createTree($list, $goodsCatModel::TOP_CLASS_PARENT_ID);
        $this->assign('goodsCatTree', $tree);
        //品牌
        $brandModel = new Brand();
        $brandList  = $brandModel->getAllBrand();
        $this->assign('brandList', $brandList);

        //会员等级
        $gradeModel = new UserGrade();
        $gradelist  = $gradeModel->getAll();
        $this->assign('gradelist', $gradelist);

        //erp同步插件是否开启
        $addonsModel = new \app\common\model\Addons();
        $addons      = $addonsModel->where('name', 'eq', 'ErpSyn')->find();
        if ($addons) {
            $erp_syn_on = true;
        } else {
            $erp_syn_on = false;
        }
        $this->assign('erp_syn_on', $erp_syn_on);
        hook('goodscommon', $this);//商品编辑、添加时增加钩子

    }

    /**
     * 获取子分类信息
     *
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-12 17:51
     */
    public function getCat()
    {

        $id = input('post.cat_id/d');
        if ($id) {

            $goodsCatModel = new GoodsCat();
            $catList       = $goodsCatModel->getCatByParentId($id);

            return [
                'data'   => $catList,
                'msg'    => '获取成功',
                'status' => true,
            ];
        } else {
            return error_code(10051);
        }
    }

    /**
     * 保存商品
     * User:wjima
     * Email:1457529125@qq.com
     *
     * @return array
     */
    public function doAdd()
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => '',
        ];
        //商品数据组装并校检
        $checkData = $this->checkGoodsInfo();
        if (!$checkData['status']) {
            $result['msg'] = $checkData['msg'];
            return $result;
        }
        $data = $checkData['data'];
        //验证商品数据
        $goodsModel    = new goodsModel();
        $productsModel = new Products();
        $goodsModel->startTrans();
        $goods_id = $goodsModel->doAdd($data['goods']);
        if (!$goods_id) {
            $goodsModel->rollback();
            $result['msg'] = error_code(12002,true);
            return $result;
        }
        $open_spec = input('post.open_spec', 0);
        if ($open_spec) {
            //多规格
            $product       = input('post.product/a', []);
            $total_stock   = $price = $costprice = $mktprice = 0;
            $isExitDefalut = false;
            foreach ($product as $key => $val) {
                $tmp_product['goods']['price']        = isset($val['price']) ? $val['price'] : 0;
                $tmp_product['goods']['costprice']    = isset($val['costprice']) ? $val['costprice'] : 0;
                $tmp_product['goods']['mktprice']     = isset($val['mktprice']) ? $val['mktprice'] : 0;
                $tmp_product['goods']['marketable']   = isset($val['marketable']) ? $val['marketable'] : $productsModel::MARKETABLE_DOWN;
                $tmp_product['goods']['stock']        = isset($val['stock']) ? $val['stock'] : 0;
                $sn                                   = get_sn(4);
                $tmp_product['goods']['sn']           = isset($val['sn']) ? $val['sn'] : $sn;
                $tmp_product['goods']['product_spes'] = $key;
                $tmp_product['goods']['image_id']     = isset($val['image_id']) ? $val['image_id'] : '';
                $tmp_product['goods']['is_defalut']   = isset($val['is_defalut']) ? $productsModel::DEFALUT_YES : $productsModel::DEFALUT_NO;

                if ($tmp_product['goods']['is_defalut'] == $productsModel::DEFALUT_YES) {
                    $isExitDefalut = true;
                }
                $checkData = $this->checkProductInfo($tmp_product, $goods_id);
                if (!$checkData['status']) {
                    $result['msg'] = $checkData['msg'];
                    $goodsModel->rollback();
                    return $result;
                }
                $data['product'] = $checkData['data']['product'];
                $product_id      = $productsModel->doAdd($data['product']);
                if (!$product_id) {
                    $goodsModel->rollback();
                    $result['msg'] = error_code(12003,true);
                    return $result;
                }
                if ($tmp_product['goods']['is_defalut'] == $productsModel::DEFALUT_YES) {//todo 取商品默认价格
                    $price     = $tmp_product['goods']['price'];
                    $costprice = $tmp_product['goods']['costprice'];
                    $mktprice  = $tmp_product['goods']['mktprice'];
                }
            }
            if (!$isExitDefalut) {
                $result['msg'] = error_code(12004,true);
                $goodsModel->rollback();
                return $result;
            }
            //更新总库存
            //$upData['stock']     = $total_stock;
            $upData['price']     = $price;
            $upData['costprice'] = $costprice;
            $upData['mktprice']  = $mktprice;
            $goodsModel->updateGoods($goods_id, $upData);
        } else {
            $sn                          = get_sn(4);
            $data['goods']['sn']         = input('post.goods.sn', $sn);//货品编码
            $data['goods']['is_defalut'] = $productsModel::DEFALUT_YES;
            //$data['product'] = $checkData['data']['product'];
            $checkData = $this->checkProductInfo($data, $goods_id);

            if (!$checkData['status']) {
                $result['msg'] = $checkData['msg'];
                $goodsModel->rollback();
                return $result;
            }
            $data       = $checkData['data'];
            $product_id = $productsModel->doAdd($data['product']);
            if (!$product_id) {
                $goodsModel->rollback();
                $result['msg'] = error_code(12003);
                return $result;
            }
        }
        //保存会员价
        $grade_price = input('post.goods.grade_price/a', []);
        if ($grade_price) {
            $grade_price_arr = [];
            foreach ($grade_price as $key => $value) {
                $grade_price_arr[] = [
                    'goods_id'    => $goods_id,
                    'grade_id'    => $key,
                    'grade_price' => $value,
                ];
            }
            $goodsGrade = new GoodsGrade();
            $goodsGrade->where(['goods_id' => $goods_id])->delete();
            if (!$goodsGrade->saveAll($grade_price_arr)) {
                $goodsModel->rollback();
                $result['msg'] = error_code(12005,true);
                return $result;
            }
        }

        //保存图片
        if (isset($data['images']) && count($data['images']) > 1) {
            $imgRelData = [];
            $i          = 0;
            foreach ($data['images'] as $key => $val) {
                if ($key == 0) {
                    continue;
                }
                $imgRelData[$i]['goods_id'] = $goods_id;
                $imgRelData[$i]['image_id'] = $val;
                $imgRelData[$i]['sort']     = $i;
                $i++;
            }
            $goodsImagesModel = new GoodsImages();
            if (!$goodsImagesModel->batchAdd($imgRelData, $goods_id)) {
                $goodsModel->rollback();
                $result['msg'] = error_code(12006,true);
                return $result;
            }
        }
        //保存商品扩展分类
        $goodsExtendCat = new GoodsExtendCat();
        if (!$goodsExtendCat->saveCat($data['extend_cat'], $goods_id)) {
            $goodsModel->rollback();
            $result['msg'] = error_code(12007,true);
            return $result;
        }

        //更新商品总库存
        $total_stock          = $productsModel->where([['goods_id', '=', $goods_id]])->sum('stock');
        $upDataGoods['stock'] = $total_stock;
        $res                  = $goodsModel->updateGoods($goods_id, $upDataGoods);
        if ($res === false) {
            $goodsModel->rollback();
            $result['msg'] = error_code(12008,true);
            return $result;
        }
        $goodsModel->commit();

        array_push($data, ['goods_id' => $goods_id]);
        hook('addgoodsafter', $data);//添加商品后增加钩子
        hook('addgoodsafterdistribution', $data);//添加商品后增加钩子
        hook('addgoodsafterfreepackage', $data);//添加商品后增加钩子
        $result['msg']    = '保存成功';
        $result['status'] = true;
        return $result;
    }

    /**
     * 校检并返回商品信息
     * User:wjima
     * Email:1457529125@qq.com
     *
     * @return mixed
     */
    private function checkGoodsInfo($isEdit = false)
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => ''
        ];

        $bn                             = get_sn(3);
        $data['goods']['name']          = input('post.goods.name', '');
        $data['goods']['goods_cat_id']  = input('post.goods_cat_id', '0');
        $data['goods']['goods_type_id'] = input('post.goods_type_id', 0);
        $data['goods']['brand_id']      = input('post.goods.brand_id', 0);
        $data['goods']['bn']            = input('post.goods.bn', $bn);
        if (empty($data['goods']['bn'])) {
            $data['goods']['bn'] = $bn;
        }
        $data['goods']['brief']        = input('post.goods.brief', '');
        $data['goods']['intro']        = input('post.goods.intro', '','safe_filter');
        $data['goods']['price']        = input('post.goods.price', '');
        $data['goods']['costprice']    = input('post.goods.costprice', '');
        $data['goods']['mktprice']     = input('post.goods.mktprice', '');
        $data['goods']['weight']       = input('post.goods.weight', '');
        $data['goods']['stock']        = input('post.goods.stock', '');
        $data['goods']['unit']         = input('post.goods.unit', '');
        $data['goods']['marketable']   = input('post.goods.marketable', '2');
        $data['goods']['is_recommend'] = input('post.goods.is_recommend', '2');
        $data['goods']['is_hot']       = input('post.goods.is_hot', '2');
        $open_spec                     = input('post.open_spec', 0);
        $specdesc                      = input('post.spec/a', [],'safe_filter');
        $new_spec                      = input('post.goods.new_spec/a', [],'safe_filter');//自定义规格
        $data['extend_cat']            = input('post.goods_cat_extend_id/a', []);//商品扩展分类
        if ($specdesc && $open_spec) {
            if (count($specdesc) == 1) {//优化只一个规格的情况
                $product = input('post.product/a', []);
                foreach ((array)$specdesc as $key => $val) {
                    foreach ($val as $k => $v) {
                        $temp_product_key = $key . ':' . $v;
                        if (!isset($product[$temp_product_key])) {
                            unset($specdesc[$key][$k]);
                        }
                    }
                }
            }
            $data['goods']['spes_desc'] = serialize($specdesc);
            $data['goods']['new_spec']  = serialize($new_spec);
        } else {
            $data['goods']['spes_desc'] = '';
            $data['goods']['new_spec'] = '';
        }
        //商品参数处理
        $params     = [];
        $tempParams = input('post.goods.params/a', []);
        if ($tempParams) {
            foreach ($tempParams as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $vk => $vv) {
                        $params[$key][] = $vk;
                    }
                } else if ($val !== '') {
                    $params[$key] = $val;
                }
            }
            $data['goods']['params'] = serialize($params);
        } else {
            $data['goods']['params'] = '';
        }
        $images = input('post.goods.img/a', []);
        if (count($images) <= 0) {
            $result['msg'] = error_code(10043,true);
            return $result;
        }
        $data['goods']['image_id'] = reset($images);
        $data['images']            = $images;
        $goodsModel                = new goodsModel();

        if ($isEdit) {
            $data['goods']['id'] = input('post.goods.id/d', 0);
            $validate            = new GoodsValidate();
            if (!$validate->scene('edit')->check($data['goods'])) {
                $result['msg'] = $validate->getError();
                return $result;
            }
        } else {
            $validate = new GoodsValidate();
            if (!$validate->check($data['goods'])) {
                $result['msg'] = $validate->getError();
                return $result;
            }
        }

        $result['data']   = $data;
        $result['status'] = true;
        return $result;
    }

    /**
     * 检查并组装货品数据
     * User:wjima
     * Email:1457529125@qq.com
     *
     * @return bool
     */
    private function checkProductInfo($data, $goods_id = 0, $isEdit = false)
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => '',
        ];
        if (!$goods_id) {
//            $result['msg'] = error_code(12009,true);
            return error_log(12009);
        }
        $productsModel = new Products();
        //单规格
        $data['product']['goods_id']   = $goods_id;
        $data['product']['sn']         = $data['goods']['sn'];//货品编码
        $data['product']['price']      = $data['goods']['price'];//货品价格
        $data['product']['costprice']  = $data['goods']['costprice'];//货品成本价
        $data['product']['mktprice']   = $data['goods']['mktprice'];//货品市场价
        $data['product']['marketable'] = $data['goods']['marketable'];//是否上架
        $data['product']['stock']      = $data['goods']['stock'];//货品库存
        $data['product']['image_id']   = $data['goods']['image_id'];//货品图片
        $data['product']['is_defalut'] = $data['goods']['is_defalut'] ? $data['goods']['is_defalut'] : $productsModel::DEFALUT_YES;//是否默认货品
        $open_spec                     = input('post.open_spec', 0);
        if ($open_spec && $data['goods']['product_spes']) {
            $data['product']['spes_desc'] = $data['goods']['product_spes'];
        }
        if (!$data['product']['sn']) {
            $data['product']['sn'] = get_sn(4);
        }
        if ($isEdit) {
            $validate = new ProductsValidate();
            if (!$validate->scene('edit')->check($data['product'])) {
                $result['msg'] = $validate->getError();
                return $result;
            }
        } else {
            $validate = new ProductsValidate();
            if (!$validate->check($data['product'])) {
                $result['msg'] = $validate->getError();
                return $result;
            }
        }

        $result['data']   = $data;
        $result['status'] = true;
        return $result;
    }

    /**
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 11:32
     */
    public function getSpec()
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => '',
        ];
        $this->view->engine->layout(false);
        $type_id = input('post.type_id');
        if ($type_id) {
            $goodsTypeModel = new GoodsType();
            $res            = $goodsTypeModel->getTypeValue($type_id);

            $html       = '';
            $customSpec = false;//是否可以使用自定义规格
            if ($res['status'] == true) {
                $this->assign('typeInfo', $res['data']);
                if (!$res['data']['spec']->isEmpty()) {
                    $spec = [];
                    foreach ($res['data']['spec']->toArray() as $key => $val) {
                        $spec[$key]['name']      = $val['spec']['name'];
                        $spec[$key]['specValue'] = $val['spec']['getSpecValue'];
                    }
                    $this->assign('spec', $spec);
                    if (count($spec) <= 2) {//规格超过2种的，不允许自定义
                        $customSpec = true;
                    }
                }
                $this->assign('customSpec', $customSpec);

                if ($res['data']['spec']->isEmpty()) {
                    $this->assign('canOpenSpec', 'false');
                } else {
                    $this->assign('canOpenSpec', 'true');
                }
                //erp同步插件是否开启
                $addonsModel = new \app\common\model\Addons();
                $addons      = $addonsModel->where('name', 'eq', 'ErpSyn')->find();
                if ($addons) {
                    $erp_syn_on = true;
                } else {
                    $erp_syn_on = false;
                }
                $this->assign('erp_syn_on', $erp_syn_on);
                //获取参数信息
                $goodsTypeParamsModel = new GoodsTypeParams();
                $typeParams           = $goodsTypeParamsModel->getRelParams($type_id);
                $this->assign('typeParams', $typeParams);
                $html             = $this->fetch('getSpec');
                $result['status'] = true;
                $result['msg']    = '获取成功';
                $result['data']   = $html;
            }
        }
        return $result;
    }

    /***
     * 生成多规格html
     *
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 15:34
     */
    public function getSpecHtml()
    {
        $result = [
            'status' => false,
            'msg'    => error_code(10051,true),
            'data'   => '',
        ];
        $this->view->engine->layout(false);
        $spec         = input('post.spec/a');
        $goods_id     = input('post.goods_id/d', 0);
        $goodsDefault = input('post.goods/a', []);
        $products     = [];
        if ($goods_id) {
            $goodsModel = new goodsModel();
            $goods      = $goodsModel->getOne($goods_id, 'id,image_id');
            if (!$goods['status']) {
                return error_code(12700);
            }
            $products = $goods['data']->products;
        }

        if ($spec) {
            $specValue = [];
            $total     = count($spec);
            foreach ($spec as $key => $val) {
                $this->spec[] = $key;
            }
            $items = $this->getSkuItem($spec, -1);

            foreach ((array)$items as $key => $val) {
                $items[$key]['price']     = $goodsDefault['price'];
                $items[$key]['costprice'] = $goodsDefault['costprice'];
                $items[$key]['mktprice']  = $goodsDefault['mktprice'];
                if (isset($goodsDefault['sn']) && $goodsDefault['sn']) {
                    $items[$key]['sn'] = $goodsDefault['sn'] . '-' . ($key + 1);
                }
                $items[$key]['stock'] = $goodsDefault['stock'];
            }
            if ($products) {
                foreach ($items as $key => $val) {
                    foreach ($products as $product) {
                        if ($val['spec_name'] == $product['spes_desc']) {
                            $items[$key]               = array_merge((array)$val, (array)$product);
                            $items[$key]['product_id'] = $product['id'];
                        }
                    }
                }
            }
            $this->assign('items', $items);
        }
        //erp同步插件是否开启
        $addonsModel = new \app\common\model\Addons();
        $addons      = $addonsModel->where('name', 'eq', 'ErpSyn')->find();
        if ($addons) {
            $erp_syn_on = true;
        } else {
            $erp_syn_on = false;
        }
        $this->assign('erp_syn_on', $erp_syn_on);
        $html             = $this->fetch('getSpecHtml');
        $result['data']   = $html;
        $result['status'] = true;
        $result['msg']    = '获取成功';
        return $result;

    }


    private function getSkuItem($data, $index = -1, $sku_item = [])
    {
        self::$total_item = array();
        if ($index < 0) {
            self::$deep_key = count($data) - 1;
            $this->getSkuItem($data, 0, $sku_item);
        } else {
            if ($index == 0) {
                $first = $data[$this->spec[$index]];

                foreach ($first as $key => $value) {
                    self::$total_item[$key] = array(
                        'spec_name' => $this->spec[$index] . ':' . $value,
                        'spec_key'  => $this->spec[$index],
                    );
                }
            } else {
                $first = $data[$this->spec[$index]];

                if (count($sku_item) >= count($first)) {
                    foreach ($first as $key => $value) {
                        foreach ($sku_item as $s => $v) {

                            self::$total_item[] = array(
                                'spec_name' => $v['spec_name'] . ',' . $this->spec[$index] . ':' . $value,
                                'spec_key'  => $v['spec_key'] . '_' . $this->spec[$index],
                            );
                        }
                    }
                } else {
                    if ($sku_item) {
                        foreach ($sku_item as $key => $value) {
                            foreach ($first as $fkey => $fvalue) {
                                self::$total_item[] = array(
                                    'spec_name' => $value['spec_name'] . ',' . $this->spec[$index] . ':' . $fvalue,
                                    'spec_key'  => $value['spec_key'] . '_' . $this->spec[$index],
                                );
                            }
                        }
                    }
                }
            }
            if ($index < self::$deep_key) {
                $this->getSkuItem($data, $index + 1, self::$total_item);
            }
        }
        return self::$total_item;

    }

    /***
     * 编辑商品
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-06 9:52
     */
    public function edit()
    {
        $goods_id      = input("id");
        $goodsModel    = new goodsModel();
        $productsModel = new Products();
        $goods         = $goodsModel->getOne($goods_id, '*');
        if (!$goods['status']) {
            $this->error(error_code(12700,true));
        }
        $this->assign('open_spec', '0');
        $this->assign('data', $goods['data']);
        $this->assign('products', $goods['data']['products']);
        if ($goods['data']['spes_desc'] || $goods['data']['new_spec']) {
            $this->assign('open_spec', '1');
        } else {
            $this->assign('open_spec', '0');
        }
        //类型
        $goodsTypeModel = new GoodsType();
        $res            = $this->getEditSpec($goods['data']['goods_type_id'], $goods['data']);

        $this->assign('spec_html', $res['data']);
        $goodsCatModel = new GoodsCat();
        $catids        = $goodsCatModel->getCatIdsByLastId($goods['data']['goods_cat_id']);
        $this->assign('catids', $catids);

        $this->_common();

        //获取扩展分类
        $goodsExtendCat = new GoodsExtendCat();
        $extends        = $goodsExtendCat->getAll($goods_id);
        $this->assign('extendsCat', $extends);
        //处理会员价
        $goodsGradeModel = new GoodsGrade();
        $goodsGrade      = $goodsGradeModel->getGradePrice($goods_id);
        if ($this->view->gradelist) {
            $gradelist = [];
            foreach ($this->view->gradelist as $key => $value) {
                $gradelist[$key] = $value;
                if ($goodsGrade['status']) {
                    foreach ($goodsGrade['data'] as $k => $v) {
                        if ($value['id'] == $v['grade_id']) {
                            $gradelist[$key]['grade_price'] = $v['grade_price'];
                        }
                    }
                } else {
                    $gradelist[$key]['grade_price'] = 0;
                }
            }
        }
        $this->assign('gradelist', $gradelist);
        return $this->fetch('edit');
    }

    /**
     * 编辑商品
     */
    public function doEdit()
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => '',
        ];
        //商品数据组装并校检
        $checkData = $this->checkGoodsInfo(true);
        if (!$checkData['status']) {
            $result['msg'] = $checkData['msg'];
            return $result;
        }
        $data = $checkData['data'];
        //验证商品数据
        $goodsModel    = new goodsModel();
        $productsModel = new Products();
        $goodsModel->startTrans();
        $updateRes = $goodsModel->updateGoods($data['goods']['id'], $data['goods']);
        $goods_id  = $data['goods']['id'];
        if ($updateRes === false) {
            $goodsModel->rollback();
            // $result['msg'] = '商品数据保存失败';
            return error_code(12002);
        }
        $productIds = [];
        $products   = $productsModel->field('id')->where(['goods_id' => $goods_id])->select()->toArray();
        $productIds = array_column($products, 'id');

        $open_spec = input('post.open_spec', 0);

        if ($open_spec) {
            //多规格
            $product       = input('post.product/a', []);
            $total_stock   = $price = $costprice = $mktprice = 0;
            $isExitDefalut = false;
            $exit_product  = [];
            if (isset($product['id'])) unset($product['id']);

            foreach ($product as $key => $val) {
                $tmp_product['goods']['price']        = !empty($val['price']) ? $val['price'] : 0;
                $tmp_product['goods']['costprice']    = !empty($val['costprice']) ? $val['costprice'] : 0;
                $tmp_product['goods']['mktprice']     = !empty($val['mktprice']) ? $val['mktprice'] : 0;
                $tmp_product['goods']['marketable']   = !empty($val['marketable']) ? $val['marketable'] : $productsModel::MARKETABLE_UP;
                $tmp_product['goods']['stock']        = !empty($val['stock']) ? $val['stock'] : 0;
                $sn                                   = get_sn(4);
                $tmp_product['goods']['sn']           = !empty($val['sn']) ? $val['sn'] : $sn;
                $tmp_product['goods']['product_spes'] = $key;
                $tmp_product['goods']['image_id']     = !empty($val['image_id']) ? $val['image_id'] : '';
                $tmp_product['goods']['is_defalut']   = !empty($val['is_defalut']) ? $productsModel::DEFALUT_YES : $productsModel::DEFALUT_NO;
                if ($tmp_product['goods']['is_defalut'] == $productsModel::DEFALUT_YES) {
                    $isExitDefalut = true;
                }

                if (isset($val['id']) && $val['id']) {
                    $tmp_product['product']['id'] = $val['id'];
                    $checkData                    = $this->checkProductInfo($tmp_product, $goods_id, true);

                } else {
                    unset($tmp_product['product']['id']);
                    $checkData = $this->checkProductInfo($tmp_product, $goods_id);
                }
                if (!$checkData['status']) {
                    $result['msg'] = $checkData['msg'];
                    $goodsModel->rollback();
                    return $result;
                }
                $data['product'] = $checkData['data']['product'];

                if (isset($val['id']) && $val['id']) {
                    $productRes = $productsModel->updateProduct($val['id'], $data['product'],$error_code);
                    if (in_array($val['id'], $productIds)) {
                        $productIds = unsetByValue($productIds, $val['id']);
                    }
                    if ($val['id']) {
                        $exit_product[] = $val['id'];
                    }
                } else {
                    unset($data['product']['id']);
                    $productRes = $productsModel->doAdd($data['product']);
                    if (is_numeric($productRes)) {
                        $exit_product[] = $productRes;
                    }
                }
                if ($productRes === false) {
                    $goodsModel->rollback();
                    return error_code($error_code);
                }

                //$total_stock = $total_stock + $tmp_product['goods']['stock'];
                if ($tmp_product['goods']['is_defalut'] == $productsModel::DEFALUT_YES) {//todo 取商品默认价格
                    $price     = $tmp_product['goods']['price'];
                    $costprice = $tmp_product['goods']['costprice'];
                    $mktprice  = $tmp_product['goods']['mktprice'];
                }
            }
            if (!$isExitDefalut) {
                // $result['msg'] = '请选择默认货品';
                $goodsModel->rollback();
                return error_code(12004);
            }
            //更新商品信息
            //$upData['stock']     = $total_stock;
            $upData['price']     = $price;
            $upData['costprice'] = $costprice;
            $upData['mktprice']  = $mktprice;
            $goodsModel->updateGoods($goods_id, $upData);
            //删除多余规格
            $productsModel->where([['id', 'not in', $exit_product], ['goods_id', '=', $goods_id]])->delete();
        } else {
            $sn                          = get_sn(4);
            $data['goods']['sn']         = input('post.goods.sn', $sn);//货品编码
            $data['goods']['is_defalut'] = $productsModel::DEFALUT_YES;
            //$data['product'] = $checkData['data']['product'];
            $data['product']['id'] = input('post.product.id/d', 0);
            if ($data['product']['id']) {
                $checkData = $this->checkProductInfo($data, $goods_id, true);
            } else {
                $checkData = $this->checkProductInfo($data, $goods_id);
            }
            if (!$checkData['status']) {
                $result['msg'] = $checkData['msg'];
                $goodsModel->rollback();
                return $result;
            }
            $data = $checkData['data'];

            if ($data['product']['id']) {
                if (in_array($data['product']['id'], $productIds)) {
                    $productIds = unsetByValue($productIds, $data['product']['id']);
                }
                $updateRes = $productsModel->updateProduct($data['product']['id'], $data['product'],$error_code);
            } else {
                $updateRes = $productsModel->doAdd($data['product']);
            }

            if ($updateRes === false) {
                $goodsModel->rollback();
                // $result['msg'] = '货品数据保存失败';
                return error_code($error_code);
            }
        }
        //删除多余货品数据
        if ($productIds) {
            $productsModel->deleteProduct($productIds);
        }
        //保存会员价
        $grade_price = input('post.goods.grade_price/a', []);
        if ($grade_price) {
            $grade_price_arr = [];
            foreach ($grade_price as $key => $value) {
                $grade_price_arr[] = [
                    'goods_id'    => $goods_id,
                    'grade_id'    => $key,
                    'grade_price' => $value,
                ];
            }
            $goodsGrade = new GoodsGrade();
            $goodsGrade->where(['goods_id' => $goods_id])->delete();
            if (!$goodsGrade->saveAll($grade_price_arr)) {
                $goodsModel->rollback();
                // $result['msg'] = '会员价保存失败';
                return error_code(12005);
            }
        }

        //保存图片
        if (isset($data['images']) && count($data['images']) >= 1) {
            $imgRelData = [];
            $i          = 0;
            foreach ($data['images'] as $key => $val) {
                if ($key == 0) {
                    continue;
                }
                $imgRelData[$i]['goods_id'] = $goods_id;
                $imgRelData[$i]['image_id'] = $val;
                $imgRelData[$i]['sort']     = $i;
                $i++;
            }
            $goodsImagesModel = new GoodsImages();
            if (!$goodsImagesModel->batchAdd($imgRelData, $goods_id)) {
                $goodsModel->rollback();
                // $result['msg'] = '商品图片保存失败';
                return error_code(12006);
            }
        }
        //保存商品扩展分类
        $goodsExtendCat = new GoodsExtendCat();
        if (!$goodsExtendCat->saveCat($data['extend_cat'], $goods_id)) {
            $goodsModel->rollback();
            // $result['msg'] = '扩展分类保存失败';
            return error_code(12007);
        }

        //更新商品总库存
        $total_stock          = $productsModel->where([['goods_id', '=', $goods_id]])->sum('stock');
        $upDataGoods['stock'] = $total_stock;

        $res = $goodsModel->updateGoods($goods_id, $upDataGoods);
        if ($res === false) {
            $goodsModel->rollback();
            // $result['msg'] = '总库存更新失败';
            return error_code(12008);
        }

        $goodsModel->commit();
        hook('editgoodsafter', $data);//编辑商品后增加钩子
        hook('editgoodsafterdistribution', $data);//编辑商品后增加钩子
        hook('editgoodsafterfreepackage', $data);//编辑商品后增加钩子
        $result['msg']    = '保存成功';
        $result['status'] = true;
        return $result;
    }

    /**
     * 商品删除
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-06 10:42
     */
    public function del()
    {
        $result     = [
            'status' => false,
            'msg'    => '',
            'data'   => '',
        ];
        $goods_id   = input("post.id");
        $goodsModel = new goodsModel();
        if (!$goods_id) {
            return error_code(10051);
        }
        $delRes = $goodsModel->delGoods($goods_id);
        if (!$delRes['status']) {
            $result['msg'] = $delRes['msg'];
            return $result;
        }
        $result['status'] = true;
        $result['msg']    = '删除成功';
        return $result;
    }

    private function getEditSpec($type_id, $goods)
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => '',
        ];
        if (!$type_id) {
            return error_code(10051);
        }
        $spes_desc = unserialize($goods['spes_desc']);
        $new_spec  = unserialize($goods['new_spec']);


        $goodsTypeModel = new GoodsType();
        $res            = $goodsTypeModel->getTypeValue($type_id);


        /**
         * 修复类型值问题，避免修改规格值，导致无法显示
         */
        $temp_new_spec = unserialize($goods['new_spec']);
        if ($temp_new_spec && is_array($temp_new_spec) && $spes_desc == '') {
            //先重新定义new_spec
            $spec = $res['data']['spec']->toArray();
            foreach ((array)$spec as $key => $val) {
                foreach ((array)$temp_new_spec as $tkey => $tval) {
                    $first = array_shift($val['spec']['getSpecValue']);
                    unset($temp_new_spec[$tkey]);
                    $temp_new_spec[$first['id']] = $tval;
                }
            }
            foreach ($goods['products'] as $key => $val) {
                $pspes_desc = $val['spes_desc'];
                list($spec_name, $spec_value) = explode(':', $pspes_desc);
                foreach ($temp_new_spec as $key => $val) {
                    if ($val[0] == $spec_value) {
                        $spes_desc[$spec_name][$key] = $spec_value;
                    }
                }
            }
            $new_spec = $temp_new_spec;
        }

        $this->assign('goods', $goods);

        $html       = '';
        $customSpec = false;//是否可以使用自定义规格

        if ($res['status'] == true) {
            $this->assign('typeInfo', $res['data']);
            if (!$res['data']['spec']->isEmpty()) {
                $spec = [];
                $spec = $this->getSpecData($res['data']['spec']->toArray(), $spes_desc, $new_spec);
                if (count($spec) <= 2) {//规格超过2种的，不允许自定义
                    $customSpec = true;
                }
                //有规格值调整的时候需要操作一遍下面的逻辑
                if ($spes_desc && $customSpec) {
                    $temp_new_spec = unserialize($goods['new_spec']);

                    $spec          = $res['data']['spec']->toArray();
                    foreach ((array)$spec as $key => $val) {
                        foreach ((array)$temp_new_spec as $tkey => $tval) {
                            $first = array_shift($val['spec']['getSpecValue']);
                            if ($first && $tval) {
                                unset($temp_new_spec[$tkey]);
                                $temp_new_spec[$first['id']] = $tval;
                            }
                        }
                    }
                    if($temp_new_spec){
                        $temp_spes_desc = [];
                        foreach ($goods['products'] as $key => $pval) {
                            $pspes_desc = $pval['spes_desc'];
                            $pspes_desc = explode(',', $pspes_desc);
                            foreach ($pspes_desc as $pk => $pv) {
                                list($spec_name, $spec_value) = explode(':', $pv);
                                foreach ($temp_new_spec as $key => $val) {
                                    if ($val[0] == $spec_value) {
                                        $temp_spes_desc[$spec_name][$key] = $spec_value;
                                    }
                                }
                            }
                        }
                    }
                    $new_spec  = $temp_new_spec;
                    $spes_desc = $temp_spes_desc;
                    //避免后面修改规格后无法显示，需要再来一遍
                    $spec = $this->getSpecData($spec, $spes_desc, $new_spec);
                }
                $this->assign('spec', $spec);
            }

            $this->assign('customSpec', $customSpec);
            if ($res['data']['spec']->isEmpty()) {
                $this->assign('canOpenSpec', 'false');
            } else {
                $this->assign('canOpenSpec', 'true');
            }
            //获取参数信息
            $goodsTypeParamsModel = new GoodsTypeParams();
            $typeParams           = $goodsTypeParamsModel->getRelParams($type_id);
            $this->assign('typeParams', $typeParams);
            //解析参数信息
            $params = [];
            if ($goods['params']) {
                $params = unserialize($goods['params']);
            }
            $this->assign('goodsParams', $params);
            $items = [];

            if ($spes_desc) {
                $specValue = [];
                $total     = count($spes_desc);
                foreach ($spes_desc as $key => $val) {
                    $this->spec[] = $key;
                }
                $items = $this->getSkuItem($spes_desc, -1);
                //循环货品
                foreach ($goods['products'] as $product) {
                    foreach ($items as $key => $ispec) {
                        if ($ispec['spec_name'] == $product['spes_desc']) {
                            $items[$key]               = array_merge((array)$ispec, (array)$product);
                            $items[$key]['product_id'] = $product['id'];
                        }
                    }
                }

            } else {
                $this->assign('product', $goods['products'][0]);
            }
            $this->assign('items', $items);

            $this->view->engine->layout(false);

            $html = $this->fetch('editGetSpecHtml');
            $this->view->engine->layout(true);

            $result['status'] = true;
            $result['data']   = $html;
        }
        return $result;
    }


    /**
     * 评论列表
     *
     * @return array|mixed
     */
    public function commentList()
    {
        if (!Request::isAjax()) {
            $goods_id = input('goods_id');
            $this->assign('goods_id', $goods_id);
            return $this->fetch('commentList');
        } else {
            $goods_id = input('goods_id');
            $page     = input('page', 1);
            $limit    = input('limit', 10);
            $res      = model('common/GoodsComment')->getList($goods_id, $page, $limit);
            if ($res['status']) {
                foreach ($res['data']['list'] as $k => $v) {
                    $v['nickname'] = $v['user']['nickname'];
                    $v['evaluate'] = config('params.comment')[$v['score']];
                    $v['ctime']    = date('Y-m-d H:i:s', $v['ctime']);
                }

                $return_data = [
                    'status' => true,
                    'msg'    => '获取评价成功',
                    'count'  => $res['data']['count'],
                    'data'   => $res['data']['list'],
                ];
            } else {
                $return_data = [
                    'status' => false,
                    'msg'    => error_code(12018, true),
                    'count'  => $res['count'],
                    'data'   => $res['data'],
                ];
            }
            return $return_data;
        }
    }


    /**
     * 获取单条评价
     *
     * @return mixed
     */
    public function getCommentInfo()
    {
        $id  = input('id');
        $res = model('common/GoodsComment')->getCommentInfo($id);
        return $res;
    }


    /**
     * 商家回复
     *
     * @return mixed
     */
    public function sellerContent()
    {
        $id             = input('id');
        $seller_content = input('seller_content');
        $res            = model('common/GoodsComment')->sellerComment($id, $seller_content);
        return $res;
    }


    /**
     * 显示不显示
     *
     * @return mixed
     */
    public function setDisplay()
    {
        $id  = input('id');
        $res = model('common/GoodsComment')->setDisplay($id);
        return $res;
    }

    /**
     * 批量上下架
     *
     * @return array
     */
    public function batchMarketable()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $ids    = input('ids/a', []);
        $type   = input('type/s', 'up');
        if (count($ids) <= 0) {
            return error_code(10051);
        }
        $goodsModel = new goodsModel();
        $res        = $goodsModel->batchMarketable($ids, $type);
        if ($res !== false) {
            $result['status'] = true;
            $result['msg']    = '操作成功';
        } else {
            // $result['msg'] = '操作失败';
            return error_code(10018);
        }
        return $result;
    }

    /**
     * 批量删除商品
     *
     * @return array
     */
    public function batchDel()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数丢失',
        ];
        $ids    = input('ids/a', []);
        if (count($ids) <= 0) {
            return error_code(10051);
        }
        $goodsModel = new goodsModel();
        foreach ($ids as $goods_id) {
            $delRes = $goodsModel->delGoods($goods_id);
            if (!$delRes['status']) {
                $result['msg'] = $delRes['msg'];
                return $result;
            }
        }
        $result['status'] = true;
        $result['msg']    = '删除成功';
        return $result;
    }

    /**
     * 商品搜索
     *
     * @return mixed
     */
    public function goodsSearch()
    {
        $result = [
            'status' => false,
            'msg'    => error_code(10037, true),
            'data'   => ''
        ];
        $this->_common();
        $this->view->engine->layout(false);
        $result['status'] = true;
        $result['msg']    = '成功';
        $result['data']   = $this->fetch('goodsSearch');
        return $result;
    }

    /**
     * 更改状态
     *
     * @return array
     */
    public function changeState()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $id     = input('post.id/d', 0);
        $state  = input('post.state/s', 'true');
        $type   = input('post.type/s', 'hot');

        if (!$id) {
            return error_code(10051);
        }
        $iData = [];
        if ($state == 'true') {
            $state = '1';
        } else {
            $state = '2';
        }
        if ($type == 'hot') {
            $iData['is_hot'] = $state;
        } else if ($type == 'rec') {
            $iData['is_recommend'] = $state;
        } else if ($type == 'able') {
            $iData['marketable'] = $state;
        }
        if (!$iData) {
            return $result;
        }
        $goodsModel = new goodsModel();
        if ($goodsModel->save($iData, ['id' => $id])) {
            $result['msg']    = '设置成功';
            $result['status'] = true;
        } else {
            // $result['msg']    = '设置失败';
            // $result['status'] = false;
            return error_code(10081);
        }
        return $result;
    }

    /**
     * 更新排序
     *
     * @return array
     */
    public function updateSort()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $field  = input('post.field/s');
        $value  = input('post.value/d');
        $id     = input('post.id/d', '0');
        if (!$field || !$value || !$id) {
            // $result['msg']    = '参数丢失';
            // $result['status'] = false;
            return error_code(10081);
        }
        $goodsModel = new goodsModel();
        if ($goodsModel->updateGoods($id, [$field => $value])) {
            $result['msg']    = '更新成功';
            $result['status'] = true;
        } else {
            return error_code(10021);
            // $result['status'] = false;
        }
        return $result;
    }

    /**
     * 批量修改价格
     *
     * @return array
     */
    public function batchModifyPrice()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $ids    = input('ids/a', []);
        if (count($ids) <= 0) {
            return error_code(10051);
        }
        $price_type = [
            'price'     => '销售价',
            'mktprice'  => '市场价',
            'costprice' => '成本价',
        ];
        $gradeModel = new UserGrade();
        $gradelist  = $gradeModel->getAll();
        foreach ((array)$gradelist as $key => $value) {
            $price_type['grade_price_' . $value['id']] = $value['name'];
        }
        $this->assign('price_type', $price_type);
        $this->assign('total_goods', count($ids));
        $this->assign('ids', implode(',', $ids));
        $this->view->engine->layout(false);
        $content = $this->fetch('batchModifyPrice');
        return [
            'status' => true,
            'data'   => $content,
            'msg'    => '获取成功',
        ];
    }

    /**
     * 保存批量操作价格
     *
     * @return array
     */
    public function doBatchModifyPrice()
    {
        $result      = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $ids         = input('ids/s', '');
        $price_type  = input('price_type/s', '');
        $modify_type = input('modify_type/s', '');
        $price_value = input('price_value', '0');
        $ids         = explode(',', $ids);
        if (!$ids) {
            return error_code(10051);
        }
        if (!$price_value) {
            // $result['msg'] = '请输入调整值';
            return error_code(12019);
        }

        $goodsModel    = new goodsModel();
        $productsModel = new Products();

        switch ($price_type) {
            case 'price':
                if ($modify_type == '=') {
                    $uData = [
                        'price' => $price_value
                    ];
                } else {
                    $uData = [
                        'price' => Db::raw('if ((price ' . $modify_type . ' ' . $price_value.') >0,(price ' . $modify_type . ' ' . $price_value.'),0 )')
                    ];
                }
                $goodsModel->where([['id', 'in', $ids]])->update($uData);
                $productsModel->where([['goods_id', 'in', $ids]])->update($uData);
                break;
            case 'mktprice':
                if ($modify_type == '=') {
                    $uData = [
                        'mktprice' => $price_value
                    ];
                } else {
                    $uData = [
                        'mktprice' => Db::raw('if ((mktprice ' . $modify_type . ' ' . $price_value.') >0,(mktprice ' . $modify_type . ' ' . $price_value.'),0 )')
                    ];
                }
                $goodsModel->where([['id', 'in', $ids]])->update($uData);
                $productsModel->where([['goods_id', 'in', $ids]])->update($uData);
                break;
            case 'costprice':
                if ($modify_type == '=') {
                    $uData = [
                        'costprice' => $price_value
                    ];
                } else {
                    $uData = [
                        'costprice' => Db::raw('if ((costprice ' . $modify_type . ' ' . $price_value.') >0,(costprice ' . $modify_type . ' ' . $price_value.'),0 )')
                    ];
                }
                $goodsModel->where([['id', 'in', $ids]])->update($uData);
                $productsModel->where([['goods_id', 'in', $ids]])->update($uData);
                break;
            default:
                $grade_id   = str_replace('grade_price_', '', $price_type);//会员等级id
                $goodsGrade = new GoodsGrade();
                foreach ((array)$ids as $key => $value) {
                    if ($modify_type == '=') {
                        $price = $price_value;
                    } else {
                        $price = Db::raw('if ((grade_price ' . $modify_type . ' ' . $price_value.') >0,(grade_price ' . $modify_type . ' ' . $price_value.'),0 )');
                    }
                    $goodsGrade->setGradePrice($value, $grade_id, $price);
                }
        }

        return [
            'status' => true,
            'data'   => '',
            'msg'    => '调整成功',
        ];
    }

    /**
     * 批量修改价格
     *
     * @return array
     */
    public function batchModifyStock()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $ids    = input('ids/a', []);
        if (count($ids) <= 0) {
            return error_code(10051);
        }

        $this->assign('total_goods', count($ids));
        $this->assign('ids', implode(',', $ids));
        $this->view->engine->layout(false);
        $content = $this->fetch('batchModifyStock');
        return [
            'status' => true,
            'data'   => $content,
            'msg'    => '获取成功',
        ];
    }

    /**
     * 保存批量操作价格
     *
     * @return array
     */
    public function doBatchModifyStock()
    {
        $result      = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $ids         = input('ids/s', '');
        $modify_type = input('modify_type/s', '');
        $stock_value = input('stock_value', '0');
        $ids         = explode(',', $ids);
        if (!$ids) {
            return error_code(10051);
        }
        if (!$stock_value) {
            // $result['msg'] = '请输入调整值';
            return error_code(12019);
        }

        $goodsModel    = new goodsModel();
        $productsModel = new Products();

        if ($modify_type == '=') {
            $uData = [
                'stock' => $stock_value
            ];
        } else {
            $uData = [
                'stock' => Db::raw('(cast(stock as signed) ' . $modify_type . ' ' . $stock_value . ')')
            ];
        }
        $goodsModel->where([['id', 'in', $ids]])->update($uData);
        $productsModel->where([['goods_id', 'in', $ids]])->update($uData);
        return [
            'status' => true,
            'data'   => '',
            'msg'    => '调整成功',
        ];
    }

    /**
     * 获取新的spec
     *
     * @param array $data
     * @param array $spes_desc
     * @param array $new_spec
     *
     * @return array
     */
    public function getSpecData($data = [], $spes_desc = [], $new_spec = [])
    {
        $spec = [];
        foreach ((array)$data as $key => $val) {
            $spec[$key]['name']      = $val['spec']['name'];
            $spec[$key]['specValue'] = $val['spec']['getSpecValue'];
            if ($spes_desc) {
                foreach ((array)$spec[$key]['specValue'] as $vkey => $vval) {
                    $spec[$key]['specValue'][$vkey]['isSelected'] = 'false';
                    if (isset($new_spec[$vval['id']])) {
                        $vval['value']                           = $new_spec[$vval['id']][0];
                        $spec[$key]['specValue'][$vkey]['value'] = $new_spec[$vval['id']][0];
                    }
                    foreach ($spes_desc as $gk => $gv) {
                        foreach ($gv as $v) {
                            if ($v == $vval['value']) {
                                $spec[$key]['specValue'][$vkey]['isSelected'] = 'true';
                            }
                        }
                    }
                }
            }
        }
        return $spec;
    }
    /**
     * 标签列表
     */

    public function labellist()
    {
        $label = new \app\common\model\Label();
        if (Request::isAjax()) {
            return $label->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /**
     * 标签删除
     */
    public function  labeldel(){
        $label = new \app\common\model\Label();
        $result  = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        if (!$label->del(input('param.id/d'))) {
            return error_code(10023);
        }
        return $result;
    }


}
