<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\CarouselSeat as positionModel;
use think\facade\Request;

class CarouselSeat extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $position = new positionModel();
        if (Request::isAjax()) {
            return $position->tableData(input('param.'));
        }
        return $this->fetch();
    }


    /**
     *  添加广告位
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $this->view->engine->layout(false);
        $position = new positionModel();
        if (Request::isPost()) {
            return $position->addData(input('param.'));
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('add', [
                    'list' => config('carousel_seat.list')
                ]
            )
        ];
    }


    /**
     *  广告位编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $positionModel = new positionModel();
        if (Request::isPost()) {
            return $positionModel->saveData(input('param.'));
        }
        $info = $positionModel->where('id', input('param.id/d'))->find();
        if (!$info) {
            return error_code(10002);
        }

        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('', ['info' => $info])
        ];
    }

    /**
     *  广告位删除
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function del()
    {
        $position = new positionModel();
        return $position->del(input('param.id/d'));
    }
}