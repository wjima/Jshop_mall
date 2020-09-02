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
use app\common\model\Form as FormModel;
use app\common\model\Ietask;


class Form
{
    //执行导出任务
    public function exec(Job $job, $params)
    {
        $params['params'] = urldecode($params['params']);
        $filter = json_decode($params['params'], true);

        if (isset($filter['ids'])) {
            $filter['id'] = explode(',', $filter['ids']);
            unset($filter['ids']);
        }
        $form        = [];
        $ietaskModle = new Ietask();
        $formModel   = new FormModel();
        $header      = $formModel->csvHeader($filter);
        if (!$header['status']) {
            return $header;
        }

        foreach ($header['data'] as $key => $val) {
            $form['header'][$key] = $val['desc'];
        }
        $formData = $formModel->getCsvData($filter);

        if ($formData['status']) {
            $body         = $formData['data'];
            $form['body'] = $body;
            $csv          = new \org\Csv($form);

            $resCsv = $csv->export('form');
            if ($resCsv['status']) {
                $uData['file_name'] = $resCsv['data']['filename'];
                $uData['file_size'] = $resCsv['data']['filesize'];
                $uData['file_path'] = $resCsv['data']['file'] . $resCsv['data']['filename'];
                $uData['status']    = $ietaskModle::EXPORT_SUCCESS_STATUS;
                $uData['utime']     = time();
                $ietaskModle->update($uData, ['id' => $params['task_id']]);
            }
            $job->delete();
        } else {
            //失败，导出失败
            $uData['status']  = $ietaskModle::EXPORT_FAIL_STATUS;
            $uData['message'] = $formData['msg'];
            $uData['utime']   = time();
            $ietaskModle->update($uData, ['id' => $params['task_id']]);
        }
        if ($job->attempts() > 3) {
            $uData['status']  = $ietaskModle::EXPORT_FAIL_STATUS;
            $uData['message'] = '导出执行失败';
            $uData['utime']   = time();
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
