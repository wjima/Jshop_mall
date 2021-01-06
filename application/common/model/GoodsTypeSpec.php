<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use app\common\model\GoodsTypeSpecValue;

/**
 * 商品属性
 * Class GoodsTypeSpec
 * @package app\common\model
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:10
 */
class GoodsTypeSpec extends Common
{

    public function getSpecValue()
    {
        return $this->hasMany('GoodsTypeSpecValue','spec_id','id');
    }

    /**
     * 默认排序
     * @param $post
     * @return mixed
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-11 16:32
     */
    protected function tableWhere($post)
    {
        $where = [];
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }

    /***
     * 查询所有属性
     * @return array|\PDOStatement|string|\think\Collection
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-11 16:44
     */
    public function getAllSpec(){
        return $this->where([])->select();

    }

    /**
     * 获取属性值
     * User:wjima
     * Email:1457529125@qq.com
     * @param $list
     * @return array
     */
    protected function tableFormat($list)
    {
        $goodsTypeSpecValue = new GoodsTypeSpecValue();
        if($list)
        {
            foreach((array)$list->toArray() as $key=>$val)
            {
                $spec_value = $goodsTypeSpecValue::all([
                    'spec_id'=>$val['id']
                ]);
                $list[$key]['spec_value'] = $spec_value;

            }
        }
        return $list;
    }

    /**
     * 添加商品属性
     * @param $data
     * @return int|string
     */
    public function add($data)
    {
        return $this->insert($data);
    }

    /**
     * 获取参数信息
     * @param int $spec_id
     * @return array|bool
     */
    public function getSpecInfo($spec_id=0)
    {
        if(!$spec_id){
            return false;
        }
        $filter = [];
        if($spec_id){
            $filter['id'] = $spec_id;
        }

        $info = $this->where($filter)->field('id,name')->find();
        if($info){
            return $info->toArray();
        }else{
            return false;
        }
    }

}
