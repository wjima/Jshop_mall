<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/26 0026
 * Time: 11:54
 */
namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\AdvertPosition as positionModel;
use think\facade\Request;

class AdvertPosition extends Seller
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $position = new positionModel();
        if(Request::isAjax())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $position->tableData($data);
        }
        return $this->fetch();
    }


    /**
     *
     *  添加广告位
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $this->view->engine->layout(false);
        $position = new positionModel();
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $position->addData($data);
        }
        return $this->fetch('add',[ 'list' => config('adTpl.list') ]);
    }


    /**
     *  广告位编辑
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $positionModel = new positionModel();
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $positionModel->saveData($data);
        }
        $info = $positionModel->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->find();
        if (!$info) {
            return error_code(10002);
        }
        return $this->fetch('edit',['info'=>$info]);
    }


    /**
     *  更改状态启用禁用
     * @return array
     */
    public function changeState()
    {
        $positionModel = new positionModel();
        return $positionModel->changeState(input('param.id/d'),input('param.state'),$this->sellerId);
    }


    /**
     *  删除已添加的广告位
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function del()
    {
        $position = new positionModel();
        return $position->del(input('param.id/d'),$this->sellerId);
    }
}