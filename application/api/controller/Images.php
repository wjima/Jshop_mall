<?php
namespace app\api\controller;
use app\common\model\Images as imageModel;
use app\common\controller\Api;
use Request;

/**
 * 图片
 * Class Images
 * @package app\api\controller
 */
class Images extends Api
{
    public function upload()
    {
        $imageModel = new imageModel();
        $result     = $imageModel->saveImage();
        if ($result['status']) {
            $data = [
                'status' => true,
                'msg'    => '上传成功',
                'data'   => [
                    'image_id' => $result['data']['id'],
                    'url'      => $result['data']['url'],
                    'type'     => '',
                ]
            ];
            return $data;
        } else {
            return error_code(10035);
        }
    }
}