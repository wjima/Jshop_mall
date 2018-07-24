<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/3/19 0019
 * Time: 12:01
 */

namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\Store as storeModel;
use think\facade\Request;

class Store extends Seller
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

            $post = input('param.');

            $post['seller_id'] = $this->sellerId;

            return $storeModel->tableData($post);
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

            $data = input('param.');

            $data['seller_id'] = $this->sellerId;

            return $storeModel->addData($data);
        }

        return $this->fetch();
    }


    /**
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
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $storeModel->editData($data);
        }

        $info = $storeModel->where('id',input('param.id/d'))->find();

        if (!$info) {

            return error_code(10002);
        }

        return $this->fetch('edit',[ 'info' => $info ]);
    }


    /**
     *  门店删除
     * @return array
     */
    public function del()
    {
        $storeModel = new storeModel();
        $result = ['status' => true,'msg' => '删除成功','data' => ''];
        if ( $storeModel->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->delete())
        {
            $result['status'] = false;

            $result['msg'] = '删除失败';
        }
        return $result;
    }


    /**
     *  加载地图
     * @return mixed
     */
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