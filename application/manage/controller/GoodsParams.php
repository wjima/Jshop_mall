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
use app\common\model\GoodsParams as GPmodel;

/**
 * 商品参数
 * Class GoodsTypeSpec
 * @package app\Manage\controller
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:07
 */
class GoodsParams extends Manage
{
    /**
     * 商品类型列表
     * @return mixed
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $goodsParamsModel = new GPmodel();
            $filter = input('request.');
            return $goodsParamsModel->tableData($filter);
        }
        return $this->fetch('index');
    }


    /**
     * 添加参数
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-09 20:47
     * @return array
     */
    public function add()
    {
        $this->view->engine->layout(false);
        if (Request::isPost())
        {
            //存储添加内容
            $data = [
                'name' => input('post.name'),
                'type' => input('post.type'),
                'value' => input('post.value'),
            ];
            $goodsParamsModel = new GPmodel();
            $result = $goodsParamsModel->doAdd($data);
            if($result !== false)
            {
                $return_data = [
                    'status' => true,
                    'msg' => '保存成功',
                    'data' => $result,
                ];
            }
            return $return_data;
        }
        //获取添加页面
        $return_data['status'] = true;
        $return_data['msg'] = error_code(10037,true);
        $return_data['data'] = $this->fetch('add');
        return $return_data;
    }


    /**
     * 编辑
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $return_data = error_code(10021);
        $goodsParamsModel = new GPmodel();
        $id = input('id/d', '0');
        if (!$id) {
            // $return_data['msg'] = error_code(10003, true);
            return error_code(10013);
        }
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            //存储添加内容
            $data = [
                'name' => input('post.name'),
                'type' => input('post.type'),
                'value' => input('post.value'),
            ];
            $result = $goodsParamsModel->doAdd($data,$id);
            if($result !== false)
            {
                $return_data = [
                    'status' => true,
                    'msg' => '保存成功',
                    'data' => $result,
                ];
            }
            return $return_data;
        }
        $data = $goodsParamsModel->where(['id'=>$id])->find();
        if(!$data)
        {
            $return_data['msg'] = error_code(10002,true);
            return $return_data;
        }
        $this->assign($data->toArray());
        //获取添加页面
        $return_data['status'] = true;
        $return_data['msg'] = '成功';
        $return_data['data'] = $this->fetch('edit');
        return $return_data;
    }


    /**
     * 删除参数
     * @return array
     */
    public function del()
    {
        $result = [
            'status' => false,
            'msg' => error_code(10023,true),
            'data' => ''
        ];
        $id = input('post.id', 0);
        if($id)
        {
            $goodsParamsModel = new GPmodel();
            $filter['id'] = $id;
            $res = $goodsParamsModel->doDel($filter);
            if($res)
            {
                $result['msg'] = '删除成功';
                $result['status'] = true;
            }
        }
        return $result;
    }


    /**
     * 弹窗参数列表
     * @return array
     */
    public function getlist()
    {
        $return = [
            'status' => false,
            'msg' => error_code(10037,true),
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $return['status'] = true;
        $return['msg'] = '成功';
        $return['data'] = $this->fetch('getlist');
        return $return;
    }
}