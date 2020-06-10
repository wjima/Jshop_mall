<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use Request;
use app\common\controller\Manage;
use app\common\model\Images as imageModel;

/**
 * 文件上传
 * Class File
 *
 * @package app\Manage\controller
 */
class Files extends Manage
{
    public function uploadVideo()
    {
        $result = error_code(10035);
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $maxSize  = config('jshop.file_size');
        $savepath = '/static/uploads/video';

        $filePath = ROOT_PATH . 'public' . $savepath;
        if (!is_dir($filePath)) {
            @mkdir($filePath);
        }
        $info = $file->validate(['size' => $maxSize, 'ext' => 'mp4'])->move($filePath);
        if ($info) {
            $fileName           = $info->getFilename();
            $url                = getRealUrl($savepath . '/' . $info->getSaveName());
            $iData['id']        = md5(get_hash($fileName));
            $iData['type']      = 'local';
            $iData['file_type'] = 'video';
            $iData['name']      = $fileName;
            $iData['url']       = $url;
            $iData['ctime']     = time();
            $iData['path']      = $filePath . '/' . $info->getSaveName();
            $files              = new \app\common\model\Files();
            if ($files->save($iData)) {
                $result['data']   = $iData;
                $result['status'] = true;
                $result['msg']    = '上传成功';
                return $result;
            }
        } else {
            // 上传失败获取错误信息
            $result['msg'] = $file->getError();
            return $result;
        }
    }
}
