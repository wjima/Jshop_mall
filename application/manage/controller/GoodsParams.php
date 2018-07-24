<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\GoodsParams as GPmodel;
use app\common\model\Seller;
use Request;

/**
 * 商品参数
 * Class GoodsTypeSpec
 * @package app\seller\controller
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
        if (!Request::isAjax()) {
            //所属商户
            $seller      = new Seller();
            $seller_list = $seller->getAllSellerList();
            $this->assign('seller_list', $seller_list);
        }else{
            $goodsParamsModel       = new GPmodel();
            $filter              = input('request.');
            return $goodsParamsModel->tableData($filter);
        }
        return $this->fetch('index');
    }

    /**
     * 添加参数
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-09 20:47
     */
    public function add()
    {
        $this->view->engine->layout(false);
        if (Request::isPost()) {
            $return_data = [
                'status' => false,
                'msg'    => '添加失败',
                'data'   => '',
            ];
            //存储添加内容
            $data             = [
                'name'      => input('post.name'),
                'type'      => input('post.type'),
                'value'     => input('post.value'),
                'seller_id' => $this->sellerId,
            ];
            $goodsParamsModel = new GPmodel();
            $result           = $goodsParamsModel->doAdd($data);
            if ($result !== false) {
                $return_data = [
                    'status' => true,
                    'msg'    => '保存成功',
                    'data'   => $result,
                ];
            }
            return $return_data;
        }
        //获取添加页面
        return $this->fetch('add');
    }

    public function edit()
    {
        $goodsParamsModel = new GPmodel();
        $id = input('id/d','0');
        if(!$id){
            return '参数错误';
        }
        $this->view->engine->layout(false);
        if (Request::isPost()) {
            $return_data = [
                'status' => false,
                'msg'    => '添加失败',
                'data'   => '',
            ];
            //存储添加内容
            $data             = [
                'name'      => input('post.name'),
                'type'      => input('post.type'),
                'value'     => input('post.value'),
                'seller_id' => $this->sellerId,
            ];
            $result           = $goodsParamsModel->doAdd($data,$id);
            if ($result !== false) {
                $return_data = [
                    'status' => true,
                    'msg'    => '保存成功',
                    'data'   => $result,
                ];
            }
            return $return_data;
        }

        $data = $goodsParamsModel->where(['id'=>$id])->find();
        if(!$data)
        {
            return '无数据';
        }
        $this->assign($data->toArray());
        //获取添加页面
        return $this->fetch('edit');
    }

    /**
     * 删除参数
     * @return array
     */
    public function del()
    {
        $result = [
            'status' => false,
            'msg'    => '删除失败',
            'data'   => '',
        ];
        $id     = input('post.id', 0);
        if ($id) {
            $goodsParamsModel    = new GPmodel();
            $filter['id']        = $id;
            $filter['seller_id'] = $this->sellerId;
            $res                 = $goodsParamsModel->doDel($filter);
            if ($res) {
                $result['msg']    = '删除成功';
                $result['status'] = true;
            }
        }
        return $result;
    }
}