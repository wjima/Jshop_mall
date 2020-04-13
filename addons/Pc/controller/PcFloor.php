<?php

namespace addons\Pc\controller;

use app\common\model\Goods;
use myxland\addons\library\AddonController;
use think\Container;
use think\facade\Request;
use think\facade\Session;

class PcFloor extends AddonController
{

    protected function initialize()
    {
        parent::initialize();

        //想实现判断后台的登陆判断很简单，写如下代码即可
        Session::init([
            'prefix'     => 'manage',
            'type'       => '',
            'auto_start' => true,
        ]);

        if (!session('?manage')) {
            cookie('redirect_url', Container::get('request')->url(), 3600);
            $this->redirect('manage/common/login');
        }
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能
    }

    public function index()
    {
        if (Request::isAjax()) {
            $model = new \addons\Pc\model\PcFloor();
            return $model->tableData(input('param.'));
        }
        return $this->fetch();
    }

    public function add()
    {
        $model = new \addons\Pc\model\PcFloor();
        if (Request::post()) {
            $data = [
                'name'    => input('name', ''),
                'keyword' => input('keyword', ''),
                'sort'    => input('sort', ''),
                'val'     => array_keys(input('goods/a', []))
            ];

            return $model->add($data);
        }
        return $this->fetch();
    }

    public function getGoods()
    {
        if (Request::get()) {
            $request               = input('param.');
            $goodModel             = new Goods();
            $request['marketable'] = $goodModel::MARKETABLE_UP;     //必须是上架的商品
            return $goodModel->tableData($request);
        }
        $this->view->engine->layout(false);
        return $this->fetch('tagSelectGoods');
    }

    public function edit()
    {
        $model = new \addons\Pc\model\PcFloor();
        if (Request::post()) {
            $data = [
                'name'    => input('name', ''),
                'keyword' => input('keyword', ''),
                'sort'    => input('sort', ''),
                'val'     => array_keys(input('goods/a', [])),
                'id'      => input('id', '')
            ];
            return $model->edit($data);
        }
        $id = input('id', '');
        if (!$id) {
            return error_code(10051);
        }
        $info = $model->getInfo($id);
        $this->assign([
            'info' => $info
        ]);
        return $this->fetch();
    }

    //删除
    public function del()
    {
        $id = input('id', '');
        if (!$id) {
            return error_code(10051);
        }
        $data  = [
            'msg'    => '删除失败',
            'status' => false,
        ];
        $model = new \addons\Pc\model\PcFloor();
        $rel   = $model->where('id', 'eq', $id)->delete();
        if ($rel) {
            $data = [
                'msg'    => '删除成功',
                'status' => true,
            ];
        }
        return $data;
    }

}
