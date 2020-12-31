<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\job\import;

use think\exception\ErrorException;
use think\queue\Job;
use app\common\model\Goods as goodsModel;
use app\common\validate\Goods as GoodsValidate;
use app\common\validate\Products as ProductsValidate;
use app\common\model\Products;
use app\common\model\Ietask;
use think\facade\Log;

class Goods
{
    //执行导入任务
    public function exec(Job $job, $params)
    {
        $ietaskModle  = new Ietask();
        $goodsModel   = new goodsModel();
        $productModel = new Products();
        $message      = [];
        Log::record($params);
        try {
            $file   = json_decode($params['params'], true);
            $csv    = new \org\Csv();
            $resCsv = $csv->import($file['file_path']);
            if ($resCsv['status']) {
                $header = $resCsv['data'][0];
                unset($resCsv['data'][0]);
                $title  = $goodsModel->csvHeader();
                $fields = [];
                foreach ($title as $key => $val) {
                    $index = array_search($val['desc'], $header);
                    if ($index >= 0) {
                        $fields[] = [
                            'index' => $index,
                            'value' => $val['id']
                        ];
                    }
                }
                $iData = [];
                if ($fields) {
                    $i = 0;
                    foreach ($resCsv['data'] as $key => $val) {
                        foreach ($fields as $fkey => $fval) {
                            $iData[$i][$fval['value']] = $val[$fval['index']];
                            if ($iData[$i]['is_spec'] == '是' && !$iData[$i]['bn'] && $iData[$i - 1]['is_spec'] == '是') {
                                $iData[$i]['bn'] = $iData[$i - 1]['bn'];
                            }
                        }
                        $i++;
                    }
                }
                //按编码重新构造数据
                $lastData = [];
                foreach ($iData as $key => $val) {
                    if (isset($lastData[$val['bn']]) && $lastData[$val['bn']]) {
                        $lastData[$val['bn']]['product'][] = $val;
                    } else {
                        $lastData[$val['bn']] = $val;
                    }
                }
                #print_r($lastData);die();
                /**
                 * 组装多规格数据
                 */
                foreach ($lastData as $key => $val) {
                    $goods['name']     = $val['name'];
                    $goods['image_id'] = $val['image_id'];
                    $goods['bn']       = $val['bn'];
                    $goods['brief']    = $val['brief'];
                    if (!empty($val['cat_name'])) {
                        $cat_id                = model('common/GoodsCat')->getInfoByName($val['cat_name'], true);
                        $goods['goods_cat_id'] = $cat_id;
                    }
                    if (!empty($val['type_name'])) {
                        $type_id                = model('common/GoodsType')->getInfoByName($val['type_name'], true);
                        $goods['goods_type_id'] = $type_id;
                    }
                    if (!empty($val['brand_name'])) {
                        $brand_id          = model('common/Brand')->getInfoByName($val['brand_name'], true);
                        $goods['brand_id'] = $brand_id;
                    }
                    $goods['is_nomal_virtual'] = ($val['is_nomal_virtual'] == '是') ? $goodsModel::VIRTUAL_NO : $goodsModel::VIRTUAL_YES;
                    $goods['marketable']       = $goodsModel::MARKETABLE_DOWN;
                    $goods['weight']           = $val['weight'];
                    $goods['unit']             = $val['unit'];
                    $goods['intro']            = $val['intro'];
                    if (isset($val['spes_desc']) && $val['spes_desc']) {
                        $spec_desc_array = explode('|', $val['spes_desc']);
                        foreach ((array)$spec_desc_array as $ssk => $ssv) {
                            $spec_desc                 = explode(':', $ssv);
                            $spec_value[$spec_desc[0]] = explode(',', $spec_desc[1]);
                        }
                        $goods['spes_desc'] = serialize($spec_value);
                    } else {
                        $goods['spes_desc'] = $val['spes_desc'];
                    }
                    $goods['params']          = $val['params'];
                    $goods['sort']            = $val['sort'];
                    $goods['is_recommend']    = ($val['is_recommend'] == '是') ? '1' : '2';
                    $goods['is_hot']          = ($val['is_hot'] == '是') ? '1' : '2';
                    $goods['label_ids']       = model('common/Label')->getIdsByName($val['label_ids'], true);//todo 标签分隔
                    $product                  = [];
                    $product[0]['spes_desc']  = $val['product_spes_desc'];
                    $val['sn']                = (isset($val['sn']) && $val['sn']) ? $val['sn'] : get_sn(4);
                    $product[0]['sn']         = $val['sn'];
                    $product[0]['price']      = $val['price'];
                    $product[0]['costprice']  = $val['costprice'];
                    $product[0]['mktprice']   = $val['mktprice'];
                    $product[0]['stock']      = $val['stock'];
                    $product[0]['is_defalut'] = ($val['is_defalut'] == '是') ? $productModel::DEFALUT_YES : $productModel::DEFALUT_NO;
                    if ($val['product']) {
                        $keyIndex = count($product);
                        foreach ($val['product'] as $pk => $pv) {
                            $product[$keyIndex]['spes_desc']  = isset($pv['product_spes_desc']) ? $pv['product_spes_desc'] : '';
                            $pv['sn']                         = (isset($pv['sn']) && $pv['sn']) ? $pv['sn'] : get_sn(4);
                            $product[$keyIndex]['sn']         = $pv['sn'];
                            $product[$keyIndex]['price']      = $pv['price'];
                            $product[$keyIndex]['costprice']  = $pv['costprice'];
                            $product[$keyIndex]['mktprice']   = $pv['mktprice'];
                            $product[$keyIndex]['stock']      = $pv['stock'];
                            $product[$keyIndex]['is_defalut'] = ($pv['is_defalut'] == '是') ? $productModel::DEFALUT_YES : $productModel::DEFALUT_NO;
                            $keyIndex++;
                        }
                    }

                    if ($val['is_defalut'] && $val['is_defalut'] == $productModel::DEFALUT_YES) {//取默认货品价格
                        $goods['price']     = $val['price'];
                        $goods['costprice'] = $val['costprice'];
                        $goods['mktprice']  = $val['mktprice'];
                    } else {
                        //取最后一条货品的价格
                        $goods['price']     = $val['price'];
                        $goods['costprice'] = $val['costprice'];
                        $goods['mktprice']  = $val['mktprice'];
                    }
                    Log::record($goods);


                    $validate = new GoodsValidate();
                    if (!$validate->scene('import')->check($goods)) {
                        $message[] = $validate->getError();
                        Log::record($goods['name'] . implode(',', $message));
                        continue;
                    } else {
                        $goodsModel->startTrans();
                        //判断商品编码是否存在，如果存在则更新，然后添加货品信息
                        $goodsData = $goodsModel->field('id')->where(['bn' => $goods['bn']])->find();
                        if (isset($goodsData['id']) && $goodsData['id'] != '') {
                            $res = $goodsModel->updateGoods($goodsData['id'], $goods);
                            if ($res === false) {
                                Log::record($goods['name'] . '导入失败');
                            }
                            $goods_id = $goodsData['id'];
                        } else {
                            $goods_id = $goodsModel->doAdd($goods);
                        }

                        if (!$goods_id) {
                            $goodsModel->rollback();
                            $message[] = error_code(12002, true);
                            Log::record($goods['name'] . '商品数据保存失败');
                            continue;
                        } else {
                            Log::record("商品id" . $goods_id);

                            foreach ($product as $pkey => $pval) {
                                $pval['goods_id']   = $goods_id;
                                $pval['marketable'] = $goodsModel::MARKETABLE_DOWN;
                                $productValidate    = new ProductsValidate();
                                if (!$productValidate->check($pval)) {
                                    $message[] = $productValidate->getError();
                                    Log::record($goods['name'] . implode(',', $message));
                                }
                                $productModel = new Products();

                                $productData = $productModel->field('id')->where(['sn' => $pval['sn']])->find();

                                if (isset($productData) && $productData['id'] != '') {
                                    #print_r($pval);echo '---';
                                    $res = $productModel->updateProduct($productData['id'], $pval,$error_code);
                                } else {
                                    #print_r($pval);
                                    $res = $productModel->doAdd($pval);
                                }
                                if ($res === false) {
                                    $goodsModel->rollback();
                                    continue;
                                }
                            }
                            $goodsModel->commit();
                        }
                    }
                }

                $uData['status']  = $ietaskModle::IMPORT_SUCCESS_STATUS;
                $uData['message'] = '导入成功';
                if ($message) {
                    $uData['message'] .= json_encode($message);
                }
                $uData['utime'] = time();
                $ietaskModle->update($uData, ['id' => $params['task_id']]);
            } else {
                $uData['status']  = $ietaskModle::IMPORT_FAIL_STATUS;
                $uData['message'] = $resCsv['msg'];
                $uData['utime']   = time();
                $ietaskModle->update($uData, ['id' => $params['task_id']]);
            }
        } catch (Exception $e) {
            $message[] = $e->getMessage();
        }
        if ($job->attempts() > 3) {
            $uData['status']  = $ietaskModle::IMPORT_FAIL_STATUS;
            $uData['message'] = error_code(10041, true);
            $uData['utime']   = time();
            $ietaskModle->update($uData, ['id' => $params['task_id']]);
            $job->delete();
        }
    }

    public function failed($data)
    {

        // ...任务达到最大重试次数后，失败了
    }
}
