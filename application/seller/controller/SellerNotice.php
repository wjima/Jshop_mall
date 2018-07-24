<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5 0005
 * Time: 14:42
 */
namespace app\seller\controller;

use app\common\model\Seller;
use app\common\model\BackstageNotice;

class SellerNotice extends Seller
{


    /**
     *
     *  商户端获取公告信息
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function show()
    {
        $backstageNoticeModel = new backstageNotice();
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
        $backstageNoticeModel = new backstageNotice();
        return $backstageNoticeModel->getInfo($id);
    }
}