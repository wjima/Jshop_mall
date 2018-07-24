<?php

namespace app\api\controller;

use app\common\model\Notice as NoticeModel;
use app\common\controller\Api;

class Notice extends Api
{
    //获取公告类型
    public function noticeType()
    {
        $noticeModel = new NoticeModel;
        $result      = array_values($noticeModel->getNoticeType());
        if($result) {
            $result_data = array(
                'status' => true,
                'msg'    => '获取成功',
                'data'   => $result
            );
        }else {
            $result_data = array(
                'status' => false,
                'msg'    => '获取失败',
                'data'   => $result
            );
        }
        return $result_data;
    }

    //获取公告列表
    public function noticeList()
    {
        $noticeModel = new NoticeModel;

        //获取排序方法
        $order = input('param.order','id');
        //获取排序方式
        $orderType = input('param.orderType','desc');
        //每页显示多少，默认5条
        $pageSize = input('param.pageSize',5);
        //获取当前页
        $page = input('param.page',1);
        //获取公告类型
        $type   = input('param.type',1);
        $result = $noticeModel->getNoticeList($type,$this->sellerId,$order,$orderType,$page,$pageSize);
        $count  = count($result);


        if($result) {
            $data = [
                'list'      => $result,
                'count'     => $count,
                'order'     => $order,
                'page'      => $page,
                'pageSize'  => $pageSize
            ];
            $result_data = array(
                'status'    => true,
                'data' => $data,
                'msg'       => '获取成功',

            );
        }else {
            $result_data = array(
                'status' => false,
                'msg'    => '获取失败',
                'data'   => $result
            );
        }
        return $result_data;

    }


    /**
     *
     *  获取公告详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeInfo()
    {
        $noticeModel = new NoticeModel;
        $seller_id   = $this->sellerId;
        $id = input('id/d',0);

        $result = $noticeModel->where(['seller_id'=>$seller_id,'id'=>$id])->find();

        if($result) {
            $result_data = array(
                'status' => true,
                'msg'    => '获取成功',
                'data'   => $result
            );
        }else {
            $result_data = array(
                'status' => false,
                'msg'    => '获取失败',
                'data'   => $result
            );
        }
        return $result_data;
    }
}