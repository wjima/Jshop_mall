<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\job\export;

use app\common\model\Order;
use app\common\model\OrderItems;
use think\queue\Job;
use app\common\model\Goods as goodsModel;
use app\common\model\Ietask;


class Orders
{
    //执行导出任务
    public function exec(Job $job, $params)
    {

        $ietaskModle = new Ietask();
        $orderModel = new Order();
        $orderItemModel = new OrderItems();

        $goodsModel = new goodsModel();

        $header = $orderModel->csvHeader();
        foreach ($header as $key => $val) {
            $order['header'][$key] = $val['desc'];
        }
        $params['params'] = urldecode($params['params']);
        $filter = json_decode($params['params'], true);

        if (isset($filter['order_ids'])) {
            if ($filter['order_ids']) {
                $filter['order_ids'] = explode(',', $filter['order_ids']);
            }
            //unset($filter['order_ids']);
        }

        $orderData = $orderModel->getCsvData($filter);
        if ($orderData['status']) {
            $body = $orderData['data'];
            $order['body'] = $body;
            $csv = new \org\Csv($order);
            $resCsv = $csv->export('order');

            if ($resCsv['status']) {
                $uData['file_name'] = $resCsv['data']['filename'];
                $uData['file_size'] = $resCsv['data']['filesize'];
                $uData['file_path'] = $resCsv['data']['file'] . $resCsv['data']['filename'];
                $uData['status'] = $ietaskModle::EXPORT_SUCCESS_STATUS;
                $uData['utime'] = time();
                $ietaskModle->update($uData, ['id' => $params['task_id']]);
            }
            $job->delete();
        } else {
            //失败，导出失败
            $uData['status'] = $ietaskModle::EXPORT_FAIL_STATUS;
            $uData['message'] = $orderData['msg'];
            $uData['utime'] = time();
            $ietaskModle->update($uData, ['id' => $params['task_id']]);
        }
        if ($job->attempts() > 3) {
            $uData['status'] = $ietaskModle::EXPORT_FAIL_STATUS;
            $uData['message'] = '导出执行失败';
            $uData['utime'] = time();
            $ietaskModle->update($uData, ['id' => $params['task_id']]);
            $job->delete();
        }

        /*$job->release($delay); //$delay为延迟时间*/
    }

    public function failed($data)
    {

        // ...任务达到最大重试次数后，失败了
    }
}
