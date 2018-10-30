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
 * 商品分类
 * Class GoodsCat
 * @package app\common\model
 * @author keinx
 */
class GoodsCat extends Common
{
    const PLATFORM_ID = 0;                  //平台ID
    const TOP_CLASS_PARENT_ID = 0;          //顶级分类父类ID
    const TOP_CLASS = 1;                    //顶级分类
    const SUB_CLASS = 2;                    //子分类
    const DEFAULT_TYPE = 0;                 //默认类型
    const DEFAULT_TYPE_NAME = '通用类型';   //默认类型名称

    protected $autoWriteTimestamp = true;
    protected $createTime = 'utime';
    protected $updateTime = 'utime';


    /**
     * 获取商品分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {

        $data = $this->field('id, parent_id, name, type_id, sort, image_id')
            ->order([ 'sort' => 'asc'])
            ->select();

        $return_data = $this->getTree($data);
        return $return_data;
    }


    /**
     * 转换成树状
     * @param $data
     * @return array
     */
    protected function getTree($data)
    {
        $new_data = array();
        foreach($data as $v)
        {
            if($v['parent_id'] == self::TOP_CLASS_PARENT_ID)
            {
                $new_data[$v['id']]['id'] = $v['id'];
                $new_data[$v['id']]['name_1'] = $v['name'];
                $new_data[$v['id']]['name_2'] = '';
                $new_data[$v['id']]['type_id'] = $v['type_id'];
                $new_data[$v['id']]['image_id'] = $v['image_id'];
                $new_data[$v['id']]['sort'] = $v['sort'];
                $new_data[$v['id']]['operating'] = $this->getOperating($v['id'], self::TOP_CLASS);
            }
            else
            {
                $new_data[$v['parent_id']]['subclass'][] = array(
                    'id' => $v['id'],
                    'name_1' => '',
                    'name_2' => $v['name'],
                    'type_id' => $v['type_id'],
                    'image_id' => $v['image_id'],
                    'sort' => $v['sort'],
                    'operating' => $this->getOperating($v['id'], self::SUB_CLASS)
                );
            }
        }

        $return_data = array();
        foreach($new_data as $v)
        {
            $return_data[] = array(
                'id' => $v['id'],
                'name_1' => $v['name_1'],
                'name_2' => $v['name_2'],
                'type_id' => $this->getTypeName($v['type_id']),
                'image_id' => $this->getImage($v['image_id']),
                'sort' => $v['sort'],
                'operating' => $v['operating']
            );
            if(isset($v['subclass']) && count($v['subclass']) > 0)
            {
                foreach($v['subclass'] as $vv)
                {
                    $return_data[] = array(
                        'id' => $vv['id'],
                        'name_1' => $vv['name_1'],
                        'name_2' => $vv['name_2'],
                        'type_id' => $this->getTypeName($vv['type_id']),
                        'image_id' => $this->getImage($vv['image_id']),
                        'sort' => $vv['sort'],
                        'operating' => $vv['operating']
                    );
                }
            }
        }
        return $return_data;
    }


    /**
     * 获取全部分类
     * @param bool $id //排除分类ID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllCat( $id = false)
    {
        if($id)
        {
            $where[] = ['id', 'neq', $id];
            $where[] = ['parent_id', 'neq', $id];
        }

        $data = $this->field('id, parent_id, name, sort, image_id')
            ->where($where)
            ->order('sort asc')
            ->select();
        $return_data = $this->getTreeApi($data);
        return $return_data;
    }


    /**
     * API使用的树装
     * @param $data
     * @return array
     */
    protected function getTreeApi($data)
    {
        $new_data = array();
        foreach($data as $v)
        {
            if($v['parent_id'] == self::TOP_CLASS_PARENT_ID)
            {
                $new_data[$v['id']]['id'] = $v['id'];
                $new_data[$v['id']]['name'] = $v['name'];
                $new_data[$v['id']]['image_id'] = $v['image_id'];
                if($v['image_id'])
                {
                    $new_data[$v['id']]['image_url'] = _sImage($v['image_id']);
                }
                $new_data[$v['id']]['sort'] = $v['sort'];
            }
            else
            {
                if($v['image_id'])
                {
                    $new_data[$v['parent_id']]['child'][] = array(
                        'id' => $v['id'],
                        'name' => $v['name'],
                        'image_id' => $v['image_id'],
                        'image_url' => _sImage($v['image_id']),
                        'sort' => $v['sort']
                    );
                }
                else
                {
                    $new_data[$v['parent_id']]['child'][] = array(
                        'id' => $v['id'],
                        'name' => $v['name'],
                        'image_id' => $v['image_id'],
                        'image_url' => '',
                        'sort' => $v['sort']
                    );
                }
            }
        }
        return $new_data;
    }


    /**
     * 获取图片
     * @param $image_id
     * @return string
     */
    protected function getImage($image_id)
    {
        if($image_id)
        {
            return _sImage($image_id);
        }
        else
        {
            return '';
        }
    }


    /**
     * 获取类型名称
     * @param $type_id
     * @return string
     */
    protected function getTypeName($type_id)
    {
        if($type_id === self::DEFAULT_TYPE)
        {
            return self::DEFAULT_TYPE_NAME;
        }
        else
        {
            return model('common/GoodsType')->getNameById($type_id);
        }
    }


    /**
     * 生成操作按钮
     * @param $id
     * @param int $type
     * @return string
     */
    protected function getOperating($id, $type = self::TOP_CLASS)
    {
        $html = '';
        if($type == self::TOP_CLASS)
        {
            $html .= '<a class="layui-btn layui-btn-primary layui-btn-xs add-class" data-id="'.$id.'">添加</a>';
            $html .= '<a class="layui-btn layui-btn-xs edit-class" data-id="'.$id.'">编辑</a>';
            $html .= '<a class="layui-btn layui-btn-danger layui-btn-xs del-class" data-id="'.$id.'">删除</a>';
        }
        elseif($type == self::SUB_CLASS)
        {
            $html .= '<a class="layui-btn layui-btn-xs edit-class" data-id="'.$id.'">编辑</a>';
            $html .= '<a class="layui-btn layui-btn-danger layui-btn-xs del-class" data-id="'.$id.'">删除</a>';
        }
        return $html;
    }


    /**
     * 获取分类
     * @param $parent_id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChildClass($parent_id = self::TOP_CLASS_PARENT_ID)
    {
        $where[] = array('parent_id', 'eq', $parent_id);

        $data = $this->field('id, name, sort, image_id')
            ->where($where)
            ->order('sort asc')
            ->select();

        foreach($data as &$v)
        {
            if($v['image_id'])
            {
                $v['image_url'] = _sImage($v['image_id']);
            }
        }
        return $data;
    }


    /**
     * 添加商品分类
     * @param $data
     * @return int|string
     */
    public function add($data)
    {
        return $this->insert($data);
    }


    /**
     * 获取一个分类信息
     * @param $id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCatInfo($id)
    {
        $where[] = ['id', 'eq', $id];
        $data = $this->field('id, name, parent_id, type_id, sort, image_id')
            ->where($where)
            ->find();
        if($data)
        {
            return $data;
        }
        else
        {
            return false;
        }
    }


    /**
     * 编辑商品分类
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($data)
    {
        $return = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];

        //判断是否要变成二级分类
        if($data['parent_id'] != self::TOP_CLASS_PARENT_ID)
        {
            //判断是否有子类
            $result = $this->where('parent_id', 'eq', $data['id'])
                ->select();
            if(count($result) > 0)
            {
                $return['msg'] = '该分类下有二级分类，无法转移分类';
                return $return;
            }
        }

        $res = $this->update($data);
        $return['data'] = $res;
        if($res)
        {
            $return['status'] = true;
            $return['msg'] = '修改成功';
        }
        else
        {
            $return['msg'] = '修改失败';
        }
        return $return;
    }


    /**
     * 判断这个分类是否可以删除
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getIsDel($id)
    {
        $info = $this->getCatInfo($id);
        if($info)
        {
            if($info['parent_id'] != self::TOP_CLASS_PARENT_ID)
            {
                //子类可以删除
                $return_data = array('is' => true, 'name'=> $info['name']);
            }
            else
            {
                //父类判断是否有子类
                $result = $this->where('parent_id', 'eq', $id)
                    ->select();
                if(count($result) > 0)
                {
                    $return_data = array('is' => false, 'name'=> $info['name']);
                }
                else
                {
                    $return_data = array('is' => true, 'name'=> $info['name']);
                }
            }
        }
        else
        {
            $return_data = array('is' => false, 'name'=> $info['name']);
        }
        return $return_data;
    }


    /**
     * 删除商品分类
     * @param $id
     * @return bool|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function del($id)
    {
        $is_del = $this->getIsDel($id);
        if($is_del['is'])
        {
            $where[] = ['id', 'eq', $id];
            $return_data = $this->where($where)
                ->delete();
        }
        else
        {
            $return_data = false;
        }
        return $return_data;
    }


    /**
     * 根据父级ID获取全部子类信息
     * @param int $parentId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-12 16:50
     */
    public function getCatByParentId($parentId = 0)
    {
        $filter['parent_id'] = $parentId;

        $data = $this->field('id, name, parent_id, type_id, sort, image_id')
            ->where($filter)
            ->order('sort asc')
            ->select();

        return $data;
    }

    /**
     * 根据名称获取分类信息
     * @param string $name
     * @param bool $isForce
     * @return bool|int|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfoByName($name = '', $isForce = false)
    {
        if (!$name ) {
            return false;
        }
        $cat_id = 0;
        $cat = $this->field('id')->where([['name', 'like', '%' . $name . '%']])->find();

        if (!$cat && $isForce) {
            $this->save([
                'name' => $name,
            ]);
            $cat_id = $this->getLastInsID();
        } elseif ($cat) {
            $cat_id = $cat['id'];
        }
        return $cat_id;
    }


    /**
     * 判断商品分类下面是否有某一个商品分类
     * @param $cat_parent_id
     * @param $cat_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isChild($cat_parent_id,$cat_id){
        if($cat_parent_id == $cat_id){
            return true;
        }
        $info = $this->where(['id'=>$cat_parent_id])->find();
        if(!$info){
            return false;
        }

        $children = $this->where(['parent_id'=>$info['id']])->select();
        foreach($children as $k => $v){
            if($this->isChild($v['id'],$cat_id)){
                return true;
            }
        }
        return false;
    }

    //根据最后一级id 获取分类信息
    public function getCatByLastId($id, $data = [])
    {
        $info   = $this->where(['id' => $id])->find();
        $data[] = $info;
        if ($info['parent_id']) {
            return $this->getCatByLastId($info['parent_id'], $data);
        } else {
            return $data;
        }

    }
}