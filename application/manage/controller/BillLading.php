<?php
namespace app\Manage\controller;
use app\common\model\BillLading as Model;
use app\common\model\Store;
use think\facade\Request;
use app\common\controller\Manage;

/**
 * 提货单
 * Class BillLading
 * @package app\Manage\controller
 */
class BillLading extends Manage
{
    /**
     * 提货单列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $model = new Model();
            $where['id'] = Request::param('id', false);
            $where['order_id'] = Request::param('order_id', false);
            $where['store_id'] = Request::param('store_id', false);
            $where['name'] = Request::param('name', false);
            $where['mobile'] = Request::param('mobile', false);
            $where['status'] = Request::param('status', false);
            $where['date'] = Request::param('date', false);
            $page = Request::param('page', 1);
            $limit = Request::param('limit', 20);
            return $model->getList($where, $page, $limit);
        }

        $storeModel = new Store();
        $store = $storeModel->getAllList();
        $this->assign('store', $store);

        return $this->fetch();
    }


    /**
     * 获取详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info()
    {
        $this->view->engine->layout(false);
        $id = Request::param('id');

        if(Request::isPost())
        {
            $store_id = Request::param('store_id');
            $name = Request::param('name');
            $mobile = Request::param('mobile');
            $model = new Model();
            return $model->edit($id, $store_id, $name, $mobile);
        }

        $model = new Model();
        $result = $model->getInfo($id);
        $this->assign('info', $result['data'][0]);
        $storeModel = new Store();
        $store = $storeModel->getAllList();
        $this->assign('store', $store);
        return $this->fetch();
    }


    /**
     * 删除操作
     * @return array
     * @throws \think\exception\DbException
     */
    public function delLading()
    {
        $id = Request::param('id');
        $model = new Model();
        return $model->del($id);
    }


    /**
     * 提货单核销
     * @return array
     */
    public function write()
    {
        $id = Request::param('id');
        $model = new Model();
        return $model->ladingOperating($id, 0);
    }
}