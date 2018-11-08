<?php
namespace app\Manage\controller;
use app\common\model\BillLading as Model;
use app\common\model\Store;
use think\facade\Request;

/**
 * 提货单
 * Class BillLading
 * @package app\Manage\controller
 */
class BillLading extends Manage
{
    /**
     * 提货单列表
     * @return mixed
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
            $page = Request::param('page', 1);
            $limit = Request::param('limit', 20);
            return $model->getList($where, $page, $limit);
        }

        $storeModel = new Store();
        $store = $storeModel->getAllList();
        $this->assign('store', $store);

        return $this->fetch();
    }
}