<?php

namespace addons\Pc\controller;

use addons\Pc\model\PcFloorUrl;
use app\common\model\Article;
use app\common\model\Goods;
use myxland\addons\library\AddonController;
use think\Container;
use think\facade\Request;
use think\facade\Session;

class PcFloorAdv extends AddonController
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
        $model = new \addons\Pc\model\PcFloorAdv();
        if (Request::isAjax()) {

            return $model->tableData(input('param.'));
        }
        $floormodel = new \addons\Pc\model\PcFloor();
        $this->assign([
            'floor' => $floormodel->getAll(),
            'type'  => $model->data['type'],
            'cat'   => $model->data['cat'],
        ]);
        return $this->fetch();
    }

    public function add()
    {
        $model = new \addons\Pc\model\PcFloorAdv();
        if (Request::post()) {
            $data = [
                'floor_id' => input('floor_id/d', ''),
                'type'     => input('type/d', ''),
                'cat'      => input('cat/d', ''),
                'image'    => input('image', ''),
                'sort'     => input('sort', 100),
                'utime'    => time(),
            ];
            if ($data['cat'] == $model::CAT_ADV_URL) {
                $data['val'] = input('url', '');
            }
            if ($data['cat'] == $model::CAT_ADV_GOODS) $data['val'] = input('goods_id', '');
            if ($data['cat'] == $model::CAT_ADV_ARTICLE) {
                $data['val'] = input('article_id', '');
            }

            return $model->add($data);
        }
        $floorModel = new \addons\Pc\model\PcFloor();
        $this->assign([
            'floor'   => $floorModel->getAll(),
            'type'    => $model->data['type'],
            'cat'     => $model->data['cat'],
            'default' => config('jshop.default_image')
        ]);
        return $this->fetch();
    }

    public function edit()
    {
        $model = new \addons\Pc\model\PcFloorAdv();

        if (Request::post()) {
            $data = [
                'floor_id' => input('floor_id/d', ''),
                'type'     => input('type/d', ''),
                'cat'      => input('cat/d', ''),
                'image'    => input('image', ''),
                'sort'     => input('sort', 100),
                'utime'    => time(),
                'id'       => input('id', '')
            ];

            if ($data['cat'] == $model::CAT_ADV_URL) {
                $data['val'] = input('url', '');
            }
            if ($data['cat'] == $model::CAT_ADV_GOODS) $data['val'] = input('goods_id', '');
            if ($data['cat'] == $model::CAT_ADV_ARTICLE) {
                $data['val'] = input('article_id', '');
            }
            return $model->edit($data);
        }
        $id = input('id', '');
        if (!$id) {
            return error_code(10051);
        }
        $floorModel = new \addons\Pc\model\PcFloor();
        $info       = $model->getInfo($id);
        $this->assign([
            'info'    => $info,
            'floor'   => $floorModel->getAll(),
            'type'    => $model->data['type'],
            'cat'     => $model->data['cat'],
            'default' => config('jshop.default_image')
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
        $model = new \addons\Pc\model\PcFloorAdv();
        $rel   = $model->where('id', 'eq', $id)->delete();
        if ($rel) {
            $data = [
                'msg'    => '删除成功',
                'status' => true,
            ];
        }
        return $data;
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
        return $this->fetch('getGoods');
    }

    //单个文章选择
    public function articleInfo()
    {
        if (Request::get()) {
            $request           = input('param.');
            $model             = new Article();
            $request['is_pub'] = $model::IS_PUB_YES;     //必须是发布
            return $model->tableData($request);
        }
        $this->view->engine->layout(false);
        return $this->fetch('getArticle');
    }

    //批量删除
    public function batchDel()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数丢失',
        ];
        $ids    = input('ids/a', []);
        if (count($ids) <= 0) {
            return $result;
        }
        $model = new \addons\Pc\model\PcFloorAdv();
        foreach ($ids as $floor_id) {
            $delRes = $model->delFloor($floor_id);
            if (!$delRes['status']) {
                $result['msg'] = $delRes['msg'];
                return $result;
            }
        }
        $result['status'] = true;
        $result['msg']    = '删除成功';
        return $result;
    }
}
