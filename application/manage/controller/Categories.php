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
     * @return mixed
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $goodsCatModel = new GoodsCat();
            $data = input('param.');
            if(isset($data['parent_id']) && $data['parent_id'] != "")
            {
                //此处不需要做任何操作
            }
            else
            {
                $data['parent_id'] = $goodsCatModel::TOP_CLASS_PARENT_ID;
            }
            return $goodsCatModel->tableData($data);
        }
        return $this->fetch('index');
    }


    /**
     * 添加&编辑商品分类
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10037,true),
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $goodsCatModel = new GoodsCat();

        if(Request::isPost())
        {
            return $goodsCatModel->edit(input('param.'));
        }
        //获取编辑页面
        //取全树
        $list = $goodsCatModel->order('sort asc')->select()->toArray();
        $tree = $goodsCatModel->createTree($list,$goodsCatModel::TOP_CLASS_PARENT_ID);
        $this->assign('tree',$tree);

        $type = model('common/GoodsType')->getList();
        $this->assign('type', $type['data']);

        if(input("?param.id"))
        {
            $info = $goodsCatModel->where(['id'=>input('param.id')])->find();
            if(!$info)
            {
                return error_code(10000);
            }
            $this->assign('data',$info);
        }
        $result['status'] = true;
        $result['msg'] = '成功';
        $result['data'] = $this->fetch('edit');
        return $result;
    }


    /**
     * 删除商品分类
     * @param $id
     * @return bool|int
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function del($id)
    {
        $goodsCatModel = new GoodsCat();
        //删除
        return $goodsCatModel->del($id);
    }


    /**
     * 获取所有一级分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAll()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10025,true),
            'data' => [],
        ];
        $goodsCatModel = new GoodsCat();
        $catList = $goodsCatModel->field('id,name,sort')->where([['parent_id','=','0']])->order('sort asc')->select();
        if(!$catList->isEmpty())
        {
            $result['data'] = $catList->toArray();
            $result['status'] = true;
            $result['msg'] = '获取成功';
        }
        return $result;
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10025,true),
            'data' => [],
        ];
        $id = input('id/d','0');
        $goodsCatModel = new GoodsCat();
        $cat = $goodsCatModel->field('id,name,sort,type_id')->where([['id','=',$id]])->find();
        if($cat)
        {
            $result['data'] = $cat->toArray();
            $result['status'] = true;
            $result['msg'] = '获取成功';
        }
        return $result;
    }


    /**
     * 改变分类是否显示状态
     * @return array
     */
    public function changeState()
    {
        $result =  error_code(10003);
        $id = input('post.id/d', 0);
        $state = input('post.status/s', 'false');

        if(!$id)
        {
            return error_code(10003);
        }
        $iData = [];
        if($state == 'true')
        {
            $state = '1';
        }
        else
        {
            $state = '2';
        }
        $iData['status'] = $state;
        $model = new GoodsCat();
        if($model->save($iData, ['id' => $id]) !== false)
        {
            $result['msg'] = '设置成功';
            $result['status'] = true;
        } else {
            return  error_code(10004);
        }
        return $result;
    }


    /**
     * 获取全部分类树
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCatTree()
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        $goodsCatModel = new GoodsCat();
        $list = $goodsCatModel->order('sort asc')->select()->toArray();
        $result['data'] = $goodsCatModel->createTree($list, $goodsCatModel::TOP_CLASS_PARENT_ID);
        return $result;
    }
}