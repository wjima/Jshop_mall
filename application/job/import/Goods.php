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
        $ietaskModle = new Ietask();
        $goodsModel = new goodsModel();
        $productModel = new Products();
        $message = [];
        Log::record($params);
        try{
            $file = json_decode($params['params'], true);
            $csv = new \org\Csv();
            $resCsv = $csv->import($file['file_path']);
            if($resCsv['status']){
                $header = $resCsv['data'][0];
                unset($resCsv['data'][0]);
                $title=$goodsModel->csvHeader();
                $fields=[];
                foreach($title as $key=>$val){
                    $index = array_search($val['desc'],$header);
                    if($index>=0){
                        $fields[] = [
                            'index'=>$index,
                            'value'=>$val['id']
                        ];
                    }
                }
                $iData = [];
                if($fields){
                    $i = 0;
                    foreach ($resCsv['data'] as $key=>$val)
                    {
                        foreach($fields as $fkey=>$fval){
                            $iData[$i][$fval['value']]=$val[$fval['index']];
                        }
                        $i++;
                    }
                }
                /**
                 * 组装多规格数据
                 */
                foreach($iData as $key=>$val){
                    $goods['name'] = $val['name'];
                    $goods['bn'] = $val['bn'];
                    $goods['brief'] = $val['brief'];
                    if(!empty($val['cat_name'])){
                        $cat_id = model('common/GoodsCat')->getInfoByName($val['cat_name'],true);
                        $goods['goods_cat_id'] = $cat_id;
                    }
                    if(!empty($val['type_name'])){
                        $type_id = model('common/GoodsType')->getInfoByName($val['type_name'],true);
                        $goods['goods_type_id'] = $type_id;
                    }
                    if(!empty($val['brand_name'])){
                        $brand_id = model('common/Brand')->getInfoByName($val['brand_name'],true);
                        $goods['brand_id'] = $brand_id;
                    }
                    $goods['is_nomal_virtual'] = ($val['is_nomal_virtual']=='是')?$goodsModel::VIRTUAL_NO:$goodsModel::VIRTUAL_YES;
                    $goods['marketable'] = $goodsModel::MARKETABLE_DOWN;
                    $goods['weight'] = $val['weight'];
                    $goods['unit'] = $val['unit'];
                    $goods['intro'] = $val['intro'];
                    $goods['spes_desc'] = $val['spes_desc'];
                    $goods['params'] = $val['params'];
                    $goods['sort'] = $val['sort'];
                    $goods['is_recommend'] = ($val['is_recommend']=='是')?'1':'2';
                    $goods['is_hot'] = ($val['is_hot']=='是')?'1':'2';
                    $goods['label_ids'] = model('common/Label')->getIdsByName($val['label_ids'],true);//todo 标签分隔
                    $product['spes_desc'] = $val['product_spes_desc'];
                    $product['sn'] = $val['sn'];
                    $product['price'] = $val['price'];
                    $product['costprice'] = $val['costprice'];
                    $product['mktprice'] = $val['mktprice'];
                    $product['stock'] = $val['stock'];
                    $product['is_defalut'] = ($val['is_defalut']=='是')?$productModel::DEFALUT_YES:$productModel::DEFALUT_NO;
                    if($val['is_defalut']&&$val['is_defalut']==$productModel::DEFALUT_YES){
                        $goods['price'] = $val['price'];
                        $goods['costprice'] = $val['costprice'];
                        $goods['mktprice'] = $val['mktprice'];
                    }
                    Log::record($goods);
                    $validate                  = new GoodsValidate();
                    if(!$validate->scene('import')->check($goods)) {
                        $message[] = $validate->getError();
                        Log::record($goods['name'].implode(',',$message));
                        continue;
                    }else{
                        $goodsModel->startTrans();
                        //判断商品编码是否存在，如果存在则更新，然后添加货品信息
                        $goodsData=$goodsModel->field('id')->where(['bn'=>$goods['bn']])->find();
                        if(isset($goodsData['id'])&&$goodsData['id']!=''){
                             $res = $goodsModel->updateGoods($goodsData['id'],$goods);
                             if($res===false){
                                 Log::record($goods['name'].'导入失败');
                             }
                            $goods_id = $goodsData['id'];
                        }else{
                            $goods_id = $goodsModel->doAdd($goods);
                        }

                        if(!$goods_id) {
                            $goodsModel->rollback();
                            $message[] = '商品数据保存失败';
                            Log::record($goods['name'].'商品数据保存失败');
                            continue;
                        }else{
                            Log::record("商品id".$goods_id);
                            $product['goods_id']=$goods_id;
                            $product['marketable']=$goodsModel::MARKETABLE_DOWN;
                            $productValidate = new ProductsValidate();
                            if(!$productValidate->check($product)){
                                $message[] = $productValidate->getError();
                                Log::record($goods['name'].implode(',',$message));
                            }
                            $productData = $productModel->field('id')->where(['sn'=>$goods['sn']])->find();
                            if(isset($productData)&&$productData['id']!=''){
                                $res=$productModel->updateProduct($productData['id'],$product);
                            }else{
                                $res=$productModel->doAdd($product);
                            }
                            if($res===false){
                                $goodsModel->rollback();
                                continue;
                            }
                            $goodsModel->commit();
                        }
                    }
                }

                $uData['status'] = $ietaskModle::IMPORT_SUCCESS_STATUS;
                $uData['message'] = '导入成功';
                if($message){
                    $uData['message'] .= json_encode($message);
                }
                $uData['utime'] = time();
                $ietaskModle->update($uData, ['id' => $params['task_id']]);
            }else{
                $uData['status'] = $ietaskModle::IMPORT_FAIL_STATUS;
                $uData['message'] = $resCsv['msg'];
                $uData['utime'] = time();
                $ietaskModle->update($uData, ['id' => $params['task_id']]);
            }
        }catch (Exception $e){
            $message[] = $e->getMessage();
        }
        if ($job->attempts() > 3) {
            $uData['status'] = $ietaskModle::IMPORT_FAIL_STATUS;
            $uData['message'] = '导入执行失败';
            $uData['utime'] = time();
            $ietaskModle->update($uData, ['id' => $params['task_id']]);
            $job->delete();
        }
    }

    public function failed($data)
    {

        // ...任务达到最大重试次数后，失败了
    }
}