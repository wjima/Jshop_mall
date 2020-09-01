<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\job\export;

use think\queue\Job;
use app\common\model\Goods as goodsModel;
use app\common\model\Ietask;


class Goods
{
    //执行导出任务
    public function exec(Job $job, $params)
    {
        $ietaskModle = new Ietask();
        $goodsModel = new goodsModel();
        $header = $goodsModel->csvHeader();
        foreach ($header as $key => $val) {
            $goods['header'][$key] = $val['desc'];
        }
        $params['params'] = urldecode($params['params']);
        $filter = json_decode($params['params'], true);

        if (isset($filter['ids'])) {
            if ($filter['ids']) {
                $filter['id'] = explode(',', $filter['ids']);
            }
            unset($filter['ids']);
        }
        $goodsData = $goodsModel->getCsvData($filter);
        if ($goodsData['status']) {
            $body = $goodsData['data'];
            $goods['body'] = $body;
            $csv = new \org\Csv($goods);
            $resCsv = $csv->export('goods');
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
            $uData['message'] = $goodsData['msg'];
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
