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
use app\common\model\Addons as addonsModel;
use app\common\model\Hooks as hooksModel;
use think\facade\Cache;

/**
 * 钩子列表
 * Class Addons
 *
 * @package app\Manage\controller
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:07
 */
class Hooks extends Manage
{
    /**
     * 插件列表
     *
     * @return mixed
     */
    public function index()
    {
        if (Request::isAjax()) {
            $hooksModel = new hooksModel();
            return $hooksModel->tableData(input('param.'));
        }
        return $this->fetch('index');
    }


    /**
     * 钩子添加
     *
     * @return array|mixed
     */
    public function add()
    {
        $this->view->engine->layout(false);
        if (Request::isPost()) {
            $hooksModel = new hooksModel();
            return $hooksModel->addData(input('param.'));
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch()
        ];
    }


    /**
     * 钩子修改
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $hooksModel = new hooksModel();
        if (Request::isPost()) {
            return $hooksModel->saveData(input('param.'));
        }
        $data = $hooksModel->where('id', input('param.id/d'))->find();
        if (!$data) {
            return error_code(10002);
        }
        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('edit', ['data' => $data])
        ];
    }


    /**
     * 钩子软删除
     * User:mark
     *
     * @return array
     */
    public function del()
    {
        $result     = error_code(10023);
        $hooksModel = new hooksModel();
        if ($hooksModel::destroy(input('param.id/d'))) {
            $result['status'] = true;
            $result['msg']    = '删除成功';
        }
        return $result;
    }
}
