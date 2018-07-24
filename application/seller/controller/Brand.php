<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/8 0008
 * Time: 19:11
 */

namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\Brand as BrandsModel;
use think\facade\Request;

class Brand extends Seller
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax()) {
            $request = input('param.');
            $request['seller_id'] = $this->sellerId;
            $brandModel = new BrandsModel();
            return $brandModel->tableData($request);
        }
        return $this->fetch();

    }


    /**
     *  添加
     * User:tianyu
     * @return array|mixed
     */
    public function add()
    {
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            $brandModel = new BrandsModel();
            return $brandModel->addData($data);
        }
        return $this->fetch('add');

    }


    /**
     *
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
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $brandModel->saveData($data);
        }
        $data = $brandModel->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->find();
        if (!$data) {
            return error_code(10002);
        }
        return $this->fetch('edit',['data' => $data]);
    }


    /**
     *  品牌删除
     * @return array
     */
    public function del()
    {
        $res = ['status' => false, 'msg' => '删除失败','data' => ''];
        $brandModel = new BrandsModel();
        if ($brandModel::destroy(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')]))
        {
            $res['status'] = true;
            $res['msg'] = '删除成功';
        }
        return $res;
    }


}