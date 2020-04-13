<?php

namespace addons\Pc\controller;


use app\common\model\Article;
use app\common\model\ArticleType;
use app\common\model\Goods;
use myxland\addons\library\AddonController;
use think\Container;
use think\Db;
use think\facade\Request;
use think\facade\Session;

class PcMenu extends AddonController
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

    /**
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {

        $model = new \addons\Pc\model\PcMenu();
        if (Request::isAjax()) {
            return $model->tableData(input('param.'));
        }
        return $this->fetch('', ['type' => $model->data['type'], 'code' => $model->data['code']]);
    }

    /**
     *
     *  添加
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $model = new \addons\Pc\model\PcMenu();
        if (Request::isPost()) {
            $data = input('param.');
            return $model->addData($data);
        }
        return $this->fetch('', ['type' => $model->data['type'], 'code' => $model->data['code']]);
    }


    /**
     *
     *  添加子分类
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addSon()
    {
        $model = new \addons\Pc\model\PcMenu();
        $this->view->engine->layout(false);
        $typeInfo = $model->where('id', input('param.id/d'))->find();
        if (!$typeInfo) return error_code(10002);
        return $this->fetch('addSon', ['typeInfo' => $typeInfo, 'type' => $model->data['type']]);
    }


    /**
     *
     *  编辑
     *
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $model = new \addons\Pc\model\PcMenu();
        if (Request::isPost()) {
            return $model->editData(input('param.'));
        }
        $id       = input('param.id/d');
        $typeInfo = $model->getPcMenu($id);
        if (!$id) {
            return error_code(10051);
        }
        if ($typeInfo['pid'] != 0) {
            $typeInfo['isSon'] = 'true';
        } else {
            $typeInfo['isSon'] = 'false';
        }
        if (!$typeInfo) return error_code(10002);
        return $this->fetch('', ['info' => $typeInfo, 'type' => $model->data['type'], 'code' => $model->data['code']]);
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function del()
    {
        $model  = new \addons\Pc\model\PcMenu();
        $result = ['status' => false, 'msg' => '删除失败', 'data' => ''];
        $id     = input('param.id/d');

        // 启动事务
        Db::startTrans();
        try {
            $where    = [];
            $where [] = ['id', 'eq', $id];
            $where [] = ['pid', 'eq', $id];
            $rel1     = $model->whereOr($where)->delete();
            $ids      = [];
            $ids[]    = $id;
            $rel2     = $model->where('pid', 'eq', $id)->select();
            foreach ($rel2 as $k => $v) {
                $ids[] = $v['id'];
            }
            $paramsModel = new \addons\Pc\model\PcMenuParams();
            $rel3        = $paramsModel->where('menu_id', 'in', $ids)->delete();
            if ($rel1 !== false && $rel3 !== false) {
                $result['status'] = true;
                $result['msg']    = '删除成功';
                Db::commit();
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $result['msg'] = $e;
        }
        return $result;
    }

    /**
     *  加载商品列表模板
     *
     * @return mixed
     */
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

    //文章分类数据
    public function getArticleType()
    {
        if (Request::isAjax()) {
            $request = input('param.');
            $model   = new ArticleType();
            return $model->tableData($request);
        }
        return false;
    }

    //文章分类渲染
    public function getArticle()
    {
        $this->view->engine->layout(false);
        return $this->fetch('tagSelectArticle');
    }

    //单个文章选择
    public function articleInfo()
    {

        if (Request::get()) {
            $request           = input('param.');
            $model             = new Article();
            $request['is_pub'] = $model::IS_PUB_YES;
            return $model->tableData($request);
        }
        $this->view->engine->layout(false);
        return $this->fetch('getArticle');
    }

    //单个商品选择
    public function goodsInfo()
    {
        $this->view->engine->layout(false);
        return $this->fetch('getGoods');
    }

}
