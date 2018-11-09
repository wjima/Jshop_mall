<?php
namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\BillLading;
use app\common\model\Clerk;
use app\common\model\Store as Model;
use think\facade\Request;

/**
 * Class Store
 * @package app\api\controller
 */
class Store extends Api
{
    /**
     * 获取默认店铺
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDefaultStore()
    {
        $model = new Model();
        return $model->getDefaultStore();
    }


    /**
     * 获取店铺列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreList()
    {
        $model = new Model();
        return $model->getAllStoreList();
    }


    /**
     * 是否是店员
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isClerk()
    {
        $model = new Clerk();
        return $model->isClerk($this->userId);
    }


    /**
     * 店铺提货单列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeLadingList()
    {
        $model = new BillLading();
        return $model->getStoreLadingList($this->userId);
    }


    /**
     * 提货单详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ladingInfo()
    {
        $key = Request::param('key');
        $model = new BillLading();
        return $model->getInfo($key);
    }


    /**
     * 提单操作
     * @return array
     */
    public function lading()
    {
        $lading_id = Request::param('lading_id');
        $model = new BillLading();
        return $model->ladingOperating($lading_id, $this->userId);
    }
}