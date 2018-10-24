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
use Request;
use app\common\model\GoodsCat;

/**
 * 商品分类
 * Class Categories
 * @package app\Manage\controller
 * @author keinx
 */
class Categories extends Manage
{
    /**
     * 商品分类列表
     * @return array|mixed
     */
    public function index()
    {
        if(!Request::isAjax())
        {
            //打开主页
            return $this->fetch('index');
        }
        else
        {
            $data = model('common/GoodsCat')->getList();
            if(count($data) > 0)
            {
                $return_data = array(
                    'status' => 1,
                    'msg' => "数据获取成功",
                    'count' => count($data),
                    'data' => $data
                );
            }
            else
            {
                $return_data = array(
                    'status' => 0,
                    'msg' => "没有分类快去添加一个吧",
                    'count' => count($data),
                    'data' => $data
                );
            }
            return $return_data;
        }
    }


    /**
     * 添加商品分类
     * @param int $parent_id
     * @return array|mixed
     */
    public function add($parent_id = GoodsCat::TOP_CLASS_PARENT_ID)
    {
        $this->view->engine->layout(false);
        if(!Request::isPost())
        {
            //获取添加页面
            $this->assign('parent_id', $parent_id); //父级ID
            $parent = model('common/GoodsCat')->getAllCat();
            $this->assign('parent', $parent); //顶级分类
            $type = model('common/GoodsType')->getList();
            $this->assign('type', $type['data']);
            return $this->fetch('add');
        }
        else
        {
            //存储添加内容
            $data = array(
                'parent_id' => input('parent_id'),
                'type_id' => input('type_id'),
                'name' => input('name'),
                'image_id' => input('image_id'),
                'sort' => input('sort')
            );
            $result = model('common/GoodsCat')->add($data);
            if($result !== false)
            {
                $return_data = array(
                    'status' => true,
                    'msg' => '添加成功',
                    'data' => $result
                );
            }
            else
            {
                $return_data = array(
                    'status' => false,
                    'msg' => '添加失败',
                    'data' => $result
                );
            }
            return $return_data;
        }
    }


    /**
     * 编辑商品分类
     * @param $id
     * @return array|mixed
     */
    public function edit($id)
    {
        $this->view->engine->layout(false);
        if(!Request::isPost())
        {
            //获取编辑页面
            $parent = model('common/GoodsCat')->getAllCat($id);
            $this->assign('parent', $parent); //父级分类
            $type = model('common/GoodsType')->getList();
            $this->assign('type', $type['data']);
            $data = model('common/GoodsCat')->getCatInfo($id);
            $this->assign('data', $data); //分类信息
            return $this->fetch('edit');
        }
        else
        {
            //存储编辑内容
            $data = array(
                'id' => input('id'),
                'parent_id' => input('parent_id'),
                'type_id' => input('type_id'),
                'name' => input('name'),
                'image_id' => input('image_id'),
                'sort' => input('sort')
            );
            $result = model('common/GoodsCat')->edit($data);
            return $result;
        }
    }


    /**
     * 删除商品分类
     * @param $id
     * @return array
     */
    public function del($id)
    {
        if(!Request::isPost())
        {
            //查询是否可以删除
            $result = model('common/GoodsCat')->getIsDel($id);
            if($result['is'])
            {
                $return_data = array(
                    'status' => true,
                    'msg' => '可以删除',
                    'data' => $result
                );
            }
            else
            {
                $return_data = array(
                    'status' => false,
                    'msg' => '该分类下存在子分类无法删除，请先删除子分类',
                    'data' => $result
                );
            }
            return $return_data;
        }
        else
        {
            //删除
            $result = model('common/GoodsCat')->del($id);
            if($result)
            {
                $return_data = array(
                    'status' => true,
                    'msg' => '删除成功',
                    'data' => $result
                );
            }
            else
            {
                $return_data = array(
                    'status' => false,
                    'msg' => '删除失败',
                    'data' => $result
                );
            }
            return $return_data;
        }
    }

    /**
     * 获取所有一级分类
     */
    public function getAll()
    {
        $result        = [
            'status' => false,
            'msg'    => '获取失败',
            'data'   => [],
        ];
        $goodsCatModel = new GoodsCat();
        $catList     = $goodsCatModel->field('id,name,sort')->where([['parent_id','=','0']])->order('sort asc')->select();
        if (!$catList->isEmpty()) {
            $result['data']   = $catList->toArray();
            $result['status'] = true;
            $result['msg']    = '获取成功';
        }
        return $result;
    }

}