<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 商品属性
 * Class GoodsTypeSpec
 * @package app\common\model
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:10
 */
class GoodsTypeSpecRel extends Common
{
    /***
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 14:10
     */
    public function getSpec()
    {
        return $this->hasOne('GoodsTypeSpec','id','spec_id');
    }


    /**
     * 更新类型和参数
     * @param int $type_id
     * @param array $specs_id
     * @return bool|\think\Collection
     */
    public function updateTypeSpec($type_id=0,$specs_id=[])
    {
        if(!$type_id||!$specs_id)
        {
            return false;
        }
        $typeSpecRelModel = $this->where(['type_id' => $type_id])->field('type_id');
        if($typeSpecRelModel->select()){
            $typeSpecRelModel->delete();
        }
        $iData = [];
        foreach($specs_id as $key=>$val)
        {
            $iData[] = [
                'type_id'=>$type_id,
                'spec_id'=>$val
            ];
        }
        return $this->saveAll($iData);

    }


    /**
     * 获取类型关联参数
     * @param int $type_id
     * @return array|bool
     */
    public function getRelTypeSpec($type_id=0)
    {
        if(!$type_id){
            return false;
        }
        $data = [];
        $goodsTypeSpecModel = new GoodsTypeSpec();
        $typeSpecs = $this->where(['type_id' => $type_id])->select();

        if(!$typeSpecs->isEmpty()){
            foreach ((array)$typeSpecs->toArray() as $k=>$v)
            {
                $data[$k]['type_id'] = $v['type_id'];
                $data[$k]['spec_id'] = $v['spec_id'];
                $data[$k]['spec'] = $goodsTypeSpecModel->getSpecInfo($v['spec_id']);
            }
        }
        return $data;
    }
}
