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
use app\common\model\Notice as noticeModel;
use think\facade\Request;

class Notice extends Manage
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $noticeModel = new noticeModel();
        if (Request::isAjax()) {
            return $noticeModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /*
     * 添加公告
     * */
    public function add()
    {
        $this->view->engine->layout(false);
        $noticeModel = new noticeModel();
        if (Request::isPost()) {
            validateJshopToken();
            return $noticeModel->addData(input('param.'));
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch()
        ];

    }


    /**
     *
     *  公告编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $noticeModel = new noticeModel();
        if (Request::isPost()) {
            return $noticeModel->saveData(input('param.'));
        }
        $noticeInfo = $noticeModel->where('id', input('param.id/d'))->find();
        if (!$noticeInfo) {
            return error_code(10002);
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('edit', ['noticeInfo' => $noticeInfo])
        ];
    }

    /**
     *  公告软删除
     * User:tianyu
     *
     * @return array
     */
    public function del()
    {
        $result      = ['status' => true, 'msg' => '删除成功', 'data' => ''];
        $noticeModel = new noticeModel();
        if (!$noticeModel->destroy(input('post.id/d'))) {
            return error_code(10023);
        }

        return $result;
    }
}