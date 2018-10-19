<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/3/19 0019
 * Time: 12:01
 */

namespace app\Manage\controller;

use app\common\controller\Manage;

use app\common\model\Store as storeModel;
use think\facade\Request;

class Store extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {

        if (Request::isAjax())
        {
            $storeModel = new storeModel();

            return $storeModel->tableData(input('param.'));
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
        if ( Request::isAjax() )
        {
            $storeModel = new storeModel();

            return $storeModel->addData(input('param.'));
        }

        return $this->fetch();
    }


    /**
     *
     *  门店修改
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $storeModel = new storeModel();

        if( Request::isAjax() )
        {
            return $storeModel->editData(input('param.'));
        }

        $info = $storeModel->where('id',input('param.id/d'))->find();

        if (!$info) {

            return error_code(10002);
        }

        return $this->fetch('edit',[ 'info' => $info ]);
    }


    /**
     *  删除
     * User:tianyu
     * @return array
     */
    public function del()
    {
        $storeModel = new storeModel();

        $result = ['status' => true,'msg' => '删除成功','data' => ''];

        if (!$storeModel->where('id',input('param.id/d'))->delete())
        {
            $result['status'] = false;

            $result['msg'] = '删除失败';
        }
        return $result;
    }



    public function showMap()
    {
        $this->view->engine->layout(false);

        $coordinate = input('param.coordinate');

        if($coordinate)
        {
            $this->assign('coordinate',$coordinate);
        }

        return $this->fetch('map');
    }


}