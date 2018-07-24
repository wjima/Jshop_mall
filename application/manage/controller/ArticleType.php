<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/19 0019
 * Time: 13:30
 */

namespace app\Manage\controller;

use  app\common\controller\Manage;
use app\common\model\ArticleType as articleTypeModel;
use think\facade\Request;

class  ArticleType extends Manage
{

    /**
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $articleTypeModel = new ArticleTypeModel();
        if(Request::isAjax())
        {
            $data = ['status' => 0, 'msg'=>'该商户没有分类数据','data'=> []];
            if (input('param.seller_id/d'))
            {
                $tree = $articleTypeModel->getList(input('param.seller_id/d'));
                if ($tree)
                {
                    $data['status'] = 1;
                    $data['msg'] = '获取成功';
                    $data['data'] = $tree;
                }
            } else {
                $data[ 'msg' ] = '请先选择商户';
            }
            return $data;
        }
        return $this->fetch('',['sellerList'=>getSellerList()]);
    }


    /**
     *  获取对应商户的列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSellerTree ()
    {
        $articleTypeModel = new ArticleTypeModel();
        $res = $articleTypeModel->getList(input('param.seller_id'));
        if ($res)
        {
            $result['status'] = true;
            $result['data'] = $res;
        } else {
            $result['status'] = false;
            $result['data'] = [];
        }

        return $result;
    }


    /**
     *  分类添加
     * User:tianyu
     * @return array|mixed|\PDOStatement|string|\think\Collection
     */
    public function add()
    {
        $articleTypeModel = new ArticleTypeModel();
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            return $articleTypeModel->addData(input('param.'));
        }
        return $this->fetch('add',['sellerList'=>getSellerList()]);
    }


    /**
     *
     *  添加子分类
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addSon()
    {
        $articleTypeModel = new ArticleTypeModel();
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            $typeInfo = $articleTypeModel->with('sellerInfo')->where('id',input('param.id/d'))->find();
            if (!$typeInfo)
            {
                return error_code(10002);
            }
            return $this->fetch('',['typeInfo'=>$typeInfo]);
        }
    }


    /**
     *
     *  文章分类编辑
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {

        $this->view->engine->layout(false);
        $articleTypeModel = new ArticleTypeModel();
        if(Request::isPost())
        {
            return $articleTypeModel->editData(input('param.'));
        }
        $typeInfo = $articleTypeModel->where('id',input('param.id/d'))->find();
        if (!$typeInfo)
        {
            return error_code(10002);
        }
        return $this->fetch('edit',['typeInfo'=>$typeInfo]);
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function del()
    {
        $articleTypeModel = new ArticleTypeModel();
        $result = ['status'=>true, 'msg'=>'删除成功','data'=>''];
        //判断该分类下是否有子分类
        if($articleTypeModel->where('pid',input('param.id/d'))->find()) {
            $result['status'] = false;
            $result['msg'] = '该分类下有子分类无法删除';
        } else if($articleTypeModel->comments()->where('type_id',input('param.id/d'))->find()) {
            $result['status'] = false;
            $result['msg'] = '该分类下有文章无法删除';
        } else {
            if (!$articleTypeModel->where('id',input('param.id/d'))->delete()) {
                $result['status'] = false;
                $result['msg'] = '删除失败';
            }
        }

        return $result;
    }
}