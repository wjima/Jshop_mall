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
use app\common\model\Ietask;

class Balance
{
    /**
     * 执行导出任务
     * @param Job $job
     * @param $params
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function exec(Job $job, $params)
    {
        $ietaskModle = new Ietask();
        $model = new \app\common\model\Balance();
        $header = $model->csvHeader();

        $balance = [];
        foreach ($header as $key => $val) {
            $balance['header'][$key] = $val['desc'];
        }
        $params['params'] = urldecode($params['params']);
        $filter = json_decode($params['params'], true);

        $data = $model->getCsvData($filter);
        if ($data['status']) {
            $body = $data['data'];
            $balance['body'] = $body;
            $csv = new \org\Csv($balance);
            $resCsv = $csv->export('balance');
            if ($resCsv['status']) {
                $bData['file_name'] = $resCsv['data']['filename'];
                $bData['file_size'] = $resCsv['data']['filesize'];
                $bData['file_path'] = $resCsv['data']['file'] . $resCsv['data']['filename'];
                $bData['status'] = $ietaskModle::EXPORT_SUCCESS_STATUS;
                $bData['utime'] = time();
                $ietaskModle->update($bData, ['id' => $params['task_id']]);
            }
            $job->delete();
        } else {
            //失败，导出失败
            $bData['status'] = $ietaskModle::EXPORT_FAIL_STATUS;
            $bData['message'] = $balance['msg'];
            $bData['utime'] = time();
            $ietaskModle->update($bData, ['id' => $params['task_id']]);
        }
        if ($job->attempts() > 3) {
            $bData['status'] = $ietaskModle::EXPORT_FAIL_STATUS;
            $bData['message'] = error_code(10039, true);
            $bData['utime'] = time();
            $ietaskModle->update($bData, ['id' => $params['task_id']]);
            $job->delete();
        }
    }


    public function failed($data)
    {
        // ...任务达到最大重试次数后，失败了
    }
}
