<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/8 0008
 * Time: 19:11
 */

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
        return $this->fetch('',['sellerList'=>getSellerList()]);
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
        return $this->fetch('add',['sellerList'=>getSellerList()]);

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
        $data = $brandModel->with('sellerInfo')->where('id',input('param.id/d'))->find();
        if (!$data) {
            return error_code(10002);
        }
        return $this->fetch('edit',['data' => $data]);
    }


    /**
     *  总后台品牌 软删除
     * User:tianyu
     * @return array
     */
    public function del()
    {
        $result = ['status'=>false,'msg'=>'删除失败','data'=>''];
        $brandModel = new BrandsModel();
        if ($brandModel::destroy(input('param.id/d'))) {
            $result['status'] = true;
            $result['msg'] = '删除成功';
        }
        return $result;
    }


}