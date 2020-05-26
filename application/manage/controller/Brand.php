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
use app\common\model\Brand as BrandsModel;
use think\facade\Request;

class Brand extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $brandModel = new BrandsModel();
            return $brandModel->tableData(input('param.'));
        }
        return $this->fetch();
    }


    /**
     *  品牌添加
     * @return array|mixed
     */
    public function add()
    {
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            $brandModel = new BrandsModel();
            return $brandModel->addData(input('param.'));
        }

        return [
            'status' => true,
            'msg' => '获取成功',
            'data' => $this->fetch()
        ];
    }


    /**
     *  品牌修改
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $brandModel = new BrandsModel();
        if(Request::isPost())
        {
            return $brandModel->saveData(input('param.'));
        }
        $data = $brandModel->where('id',input('param.id/d'))->find();
        if (!$data) {
            return error_code(10002);
        }

        return [
            'status' => true,
            'msg' => '获取成功',
            'data' => $this->fetch('edit',['data' => $data])
        ];
    }


    /**
     *  总后台品牌 软删除
     * User:tianyu
     * @return array
     */
    public function del()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10023,true),
            'data' => []
        ];
        $brandModel = new BrandsModel();
        if ($brandModel::destroy(input('post.id/d'))) {
            $result['status'] = true;
            $result['msg'] = '删除成功';
        }
        return $result;
    }

    /**
     * 获取所有品牌
     */
    public function getAll()
    {
        $result     = [
            'status' => false,
            'msg'    => error_code(10025,true),
            'data'   => [],
        ];
        $brandModel = new BrandsModel();
        $brandList  = $brandModel->field('id,name,sort')->where([])->order('sort asc')->select();
        if (!$brandList->isEmpty()) {
            $result['data']   = $brandList->toArray();
            $result['status'] = true;
            $result['msg']    = '获取成功';
        }
        return $result;
    }

}