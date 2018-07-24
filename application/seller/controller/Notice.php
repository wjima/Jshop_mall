<?php
/**
 * Created by PhpStorm.
 * User: tian yu
 * Date: 2018/1/18 0018
 * Time: 10:49
 */

namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\Notice as noticeModel;
use think\facade\Request;

class Notice extends Seller
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $noticeModel = new noticeModel();
        if(Request::isAjax())
        {
            $request = input('param.');
            $request['seller_id'] = $this->sellerId;
            return $noticeModel->tableData($request);
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
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $noticeModel->addData($data);
        }
        return $this->fetch('add');

    }


    /**
     *  公告修改
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $noticeModel = new noticeModel();
        if(Request::isPost())
        {
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $noticeModel->saveData($data);
        }
        $noticeInfo = $noticeModel->where(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')])->find();

        if (!$noticeInfo)
        {
            return error_code(10002);
        }

        return $this->fetch('edit',['noticeInfo'=>$noticeInfo]);
    }


    /**
     *  公告删除
     * @return array
     */
    public function del()
    {
        $result = ['status' => true, 'msg' => '删除成功', 'data'  => ''];
        $noticeModel = new noticeModel();
        if (!$noticeModel::destroy(['seller_id'=>$this->sellerId,'id'=>input('param.id/d')]))
        {
            $result['status'] = false;
            $result['msg'] = '删除失败';
        }

        return $result;
    }
}