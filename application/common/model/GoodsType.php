<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use app\common\model\GoodsTypeSpec;
use app\common\model\GoodsTypeSpecRel;
use app\common\model\GoodsCat;
use app\common\model\GoodsParams;
use app\common\model\GoodsTypeParams;

/**
 * 商品类型
 * Class GoodsType
 * @package app\common\model
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:09
 */
class GoodsType extends Common
{
    /**
     * 添加商品类型
     * @param $data
     * @return int|string
     */
    public function add($data)
    {
        return $this->insert($data);
    }


    public function specRel()
    {
        return $this->hasMany('GoodsTypeSpecRel','type_id');
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
        $result['order'] = ['id'=>'desc'];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        if(!$list->isEmpty()){
            foreach((array)$list->toArray() as $k => $v)
            {

                $spec = [];
                $specModel = new GoodsTypeSpecRel();
                $specList = $specModel->where(['type_id' => $v['id']])->select();
                $list[$k]['spec'] = [];
                $list[$k]['params'] = [];
                if(!$specList->isEmpty())
                {
                    foreach((array)$specList->toArray() as $sk=>$sv)
                    {
                        if($sv){
                            $goodsTypeSpecModel = new GoodsTypeSpec();
                            $typespec = $goodsTypeSpecModel::get(['id'=>$sv['spec_id']]);
                            if($typespec){
                                $spec[] =$typespec;
                            }
                        }
                    }
                    $list[$k]['spec'] = $spec;
                }
                $params = [];
                $goodsTypeParamsModel = new GoodsTypeParams();
                $paramsList = $goodsTypeParamsModel->where(['type_id' => $v['id']])->select();
                if(!$paramsList->isEmpty())
                {
                    foreach((array)$paramsList->toArray() as $sk=>$sv)
                    {
                        if($sv){
                            $goodsParamsModel = new GoodsParams();
                            $goodsParams = $goodsParamsModel::get(['id'=>$sv['params_id']]);
                            if($goodsParams){
                                $params[] =$goodsParams;
                            }
                        }
                    }
                    $list[$k]['params'] = $params;
                }
            }
        }
        return $list;
    }

    /**
     * 获取全部类型或根据分类ID或者类型
     * @param int $cat_id
     * @return array|\PDOStatement|string|\think\Collection
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-12 16:55
     */
    public function getAllTypes($cat_id = 0)
    {
        $filter = [];
        if($cat_id){
            $filter['id'] = $cat_id;
        }
        if($cat_id) {
            $catModel = new GoodsCat();
            $catInfo  = $catModel->where($filter)->field('type_id,id,name')->find();
            $result   = [
                'id'   => '0',
                'name' => '通用类型',
            ];
            if($catInfo) {
                $result = $this->field('id,name')->where([ 'id' => $catInfo['type_id'] ])->find()->toArray();
            }
            return $result;
        }else {
            $data = $this->where($filter)->field('id,name')->select();
        }

        return $data;
    }

    /**
     * 获取类型相关信息
     * @param int $type_id
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 11:44
     */
    public function getTypeValue($type_id = 0)
    {
        $result   = error_code(12017);
        $typeInfo = $this->field('id,name,params')->where([ 'id' => $type_id ])->find();
        if($typeInfo) {
            $typeInfo['params'] = unserialize($typeInfo['params']);
            $rel                = $typeInfo->specRel;
            if(!$rel->isEmpty()) {
                foreach($rel as $key => $val) {
                    $rel[$key]['spec']       = $val->getSpec;
                    $rel[$key]['spec_value'] = $val->getSpec->getSpecValue;
                }
            }
            $typeInfo['spec'] = $rel;
            $result['msg'] ='获取成功';
            $result['data']   = $typeInfo;
            $result['status'] = true;
        }
        return $result;
    }

    /**
     * 根据名称获取类型信息
     * @param string $name
     * @param bool $isForce 没有名称时，是否添加
     * @return int
     */
    public function getInfoByName($name = '', $isForce = false)
    {
        if (!$name) {
            return false;
        }
        $type_id = 0;
        $type = $this->field('id')->where([['name', 'like', '%' . $name . '%']])->find();
        if (!$type && $isForce) {
            $this->save([
                'name' => $name,
            ]);
            $type_id = $this->getLastInsID();
        } elseif ($type) {
            $type_id = $type['id'];
        }
        return $type_id;
    }


    /**
     * 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $where = [];
        $res = $this->field('id, name')
            ->where($where)
            ->select();
        if($res)
        {
            $return = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res
            ];
        } else {
            return error_code(10025);
        }
        return $return;
    }


    /**
     * 通过ID获取名称
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNameById($id)
    {
        if (!$id) {
            return '';
        }
        $where[] = ['id', 'eq', $id];
        $res     = $this->field('name')->where($where)->find();
        return isset($res['name']) ? $res['name'] : '';
    }
}