<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/12 0012
 * Time: 14:18
 */
namespace app\manage\controller;

use app\common\controller\Manage;
use app\common\model\BackstageNotice as backstageNoticeModel;
use think\facade\Request;

class BackstageNotice extends Manage
{
    /**
     *
     *  商户端获取公告信息
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function show()
    {
        $backstageNoticeModel = new backstageNoticeModel();
        return $backstageNoticeModel->showData(input('param.'));
    }


    /**
     *
     *  获取公告详情
     * @param int $id
     *
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($id = 0)
    {
        $backstageNoticeModel = new backstageNoticeModel();
        return $backstageNoticeModel->getInfo($id);
    }



    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $backstageNoticeModel = new backstageNoticeModel();
        if(Request::isAjax())
        {
            return $backstageNoticeModel->tableData(input('param.'));
        }
        return $this->fetch();
    }


    /**
     *
     *  总后台公告添加
     * @return array|mixed
     */
    public function add()
    {
        if(Request::isAjax())
        {
            $backstageNoticeModel = new backstageNoticeModel();
            return $backstageNoticeModel->addData(input('param.'));
        }
        return $this->fetch();
    }


    /**
     *
     *  总后台公告编辑
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $backstageNoticeModel = new backstageNoticeModel();
        if(Request::isAjax())
        {
            return $backstageNoticeModel->saveData(input('param.'));
        }
        $backstageNoticeInfo = $backstageNoticeModel->getInfo(input('param.id/d'));
        return $this->fetch('edit',['info'=>$backstageNoticeInfo]);
    }


    /**
     *
     *  删除公告
     * @return array
     */
    public function del()
    {
        $backstageNoticeModel = new backstageNoticeModel();
        return $backstageNoticeModel->del(input('param.id/d'));
    }

}
