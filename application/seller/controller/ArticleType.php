<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/19 0019
 * Time: 13:30
 */

namespace app\seller\controller;

use  app\common\controller\Seller;
use app\common\model\ArticleType as articleTypeModel;
use think\facade\Request;

class  ArticleType extends Seller
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
            $tree = $articleTypeModel->getList($this->sellerId);
            $data = [
                'status' => true,
                'count' => count($tree),
                'data' => $tree
            ];
            return $data;
        }
        return $this->fetch();
    }


    /**
     *  添加分类
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $articleTypeModel = new ArticleTypeModel();
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            $res = $articleTypeModel->addData($data);
            return $res;
        }
        $tree = $articleTypeModel->getList($this->sellerId);
        $this->assign('tree',$tree);
        return $this->fetch('add');
    }


    /**
     *  添加子分类
     * @return mixed
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
            $typeInfo = $articleTypeModel->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->find();
            if (!$typeInfo) {
                return error_code(10002);
            }
            return $this->fetch('',['typeInfo'=>$typeInfo]);
        }
    }


    /**
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
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $articleTypeModel->editData($data);
        }
        $typeInfo = $articleTypeModel->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->find();
        return $this->fetch('edit',['typeInfo'=>$typeInfo]);
    }


    /**
     *  删除文章分类
     *  //判断是否有子分类
     *  //判断该分类下是否有文章
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
        if($articleTypeModel->where(['seller_id'=>$this->sellerId,'pid'=>input('param.id/d')])->find()) {
            $result['status'] = false;
            $result['msg'] = '该分类下有子分类无法删除';
        } else if($articleTypeModel->comments()->where(['seller_id'=>$this->sellerId,'type_id'=>input('param.id/d')])->find()) {
            $result['status'] = false;
            $result['msg'] = '该分类下有文章无法删除';
        } else {
            if (!$articleTypeModel->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->delete()) {
                $result['status'] = false;
                $result['msg'] = '删除失败';
            }
        }

        return $result;
    }

}