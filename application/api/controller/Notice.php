<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use app\common\model\Notice as NoticeModel;
use app\common\controller\Api;
use think\facade\Cache;

/**
 * 公告
 * Class Notice
 * @package app\api\controller
 */
class Notice extends Api
{
    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeList()
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

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
        $type = input('param.type', 1);
        if (!Cache::has("jshop_notice_noticelist" . '_' . $order . '_' . $orderType . '_' . $pageSize . '_' . $page)) {
            $data = $noticeModel->getNoticeList($type, $order, $orderType, $page, $pageSize);
            if ($data) {
                $result['data'] = $data;
            }
            Cache::set("jshop_notice_noticelist" . '_' . $order . '_' . $orderType . '_' . $pageSize . '_' . $page, $result, 3600 * 5);
        } else {
            $result = Cache::get("jshop_notice_noticelist" . '_' . $order . '_' . $orderType . '_' . $pageSize . '_' . $page, $result);
        }
        return $result;
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
        $result = error_code(10025);
        $noticeModel = new NoticeModel;
        $data = $noticeModel->getNoticeInfo(input('param.id/d'));

        if ($data) {
            $result['status'] = true;
            $result['msg'] = '获取成功';
            $result['data'] = $data;
        }

        return $result;
    }
}