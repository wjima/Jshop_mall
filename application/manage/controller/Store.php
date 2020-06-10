<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\Clerk;
use app\common\model\Store as storeModel;
use think\facade\Request;

/**
 * Class Store
 * @package app\Manage\controller
 */
class Store extends Manage
{
    /**
     * 列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $storeModel = new storeModel();
            return $storeModel->tableData(input('param.'));
        }
        return $this->fetch();
    }


    /**
     * 添加
     * @return array|mixed
     */
    public function add()
    {
        if(Request::isAjax())
        {
            $storeModel = new storeModel();
            $data =  input('param.');
            if($data['coordinate']){
                $coordinate = explode(',',$data['coordinate']);
                $data['latitude'] = $coordinate[0];
                $data['longitude'] = $coordinate[1];
            }
            return $storeModel->addData($data);
        }
        return $this->fetch();
    }


    /**
     * 门店修改
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $storeModel = new storeModel();
        if(Request::isAjax())
        {
            $data =  input('param.');
            if($data['coordinate']){
                $coordinate = explode(',',$data['coordinate']);
                $data['latitude'] = $coordinate[0];
                $data['longitude'] = $coordinate[1];
            }
            return $storeModel->editData($data);
        }
        $info = $storeModel->where('id',input('param.id/d'))->find();
        if(!$info)
        {
            return error_code(10002);
        }
        return $this->fetch('edit',[ 'info' => $info ]);
    }


    /**
     * 删除
     * @return array
     */
    public function del()
    {
        $storeModel = new storeModel();
        $result = ['status' => true, 'msg' => '删除成功', 'data' => ''];
        if (!$storeModel->where('id', input('param.id/d'))->delete()) {
            return error_code(10023);
        }
        return $result;
    }


    /**
     * 地图展示
     * @return mixed
     */
    public function showMap()
    {
        $this->view->engine->layout(false);
        $coordinate = input('param.coordinate');
        if($coordinate)
        {
            $this->assign('coordinate',$coordinate);
        }
        $qq_map_key = getSetting('qq_map_key');
        $this->assign('qq_map_key',$qq_map_key);
        return $this->fetch('map');
    }


    /**
     * 店员列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function clerkList()
    {
        $id = Request::param('id', false);
        if(Request::isAjax())
        {
            $clerkModel = new Clerk();
            $page = Request::param('page', 1);
            $limit = Request::param('limit', 20);
            return $clerkModel->getList($id, $page, $limit);
        }
        $this->assign('id', $id);
        $storeModel = new storeModel();
        $store = $storeModel->getAllList();
        $this->assign('store', $store);
        return $this->fetch('clerkList');
    }


    /**
     * 添加店员
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addClerk()
    {
        $store_id = Request::param('store_id', false);
        if(Request::isAjax())
        {
            $post_store_id = Request::param('store_id', false);
            $post_user_mobile = Request::param('mobile', false);
            $clerkModel = new Clerk();
            return $clerkModel->add($post_store_id, $post_user_mobile);
        }
        //全部店铺
        $storeModel = new StoreModel();
        $storeList = $storeModel->getAllList();
        $this->assign('storeList', $storeList);
        $this->assign('store_id', $store_id);
        return $this->fetch('addClerk');
    }


    /**
     * 删除店员
     * @return array
     */
    public function delClerk()
    {
        $id = Request::param('id');
        $clerkModel = new Clerk();
        return $clerkModel->del($id);
    }
}
