<?php
namespace app\api\controller;

use app\common\model\Images as imageModel;
use app\common\controller\Api;
use Request;
use think\Container;

class Images extends Api
{
    public function upload()
    {
        $imageModel = new \app\common\model\Images();
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
            return [
                'data'   => '',
                'status' => false,
                'msg'    => "上传失败"
            ];
        }
    }
}