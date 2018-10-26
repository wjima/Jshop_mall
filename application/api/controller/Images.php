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
        $jshopHost = Container::get('request')->domain();
        $filetypes = [
            'image' => [
                'title'      => 'Image files',
                'extensions' => 'jpg,jpeg,png,gif,bmp4'
            ],
        ];
        $image_extensions = explode(',', 'jpg,jpeg,png,gif,bmp4');
        $all_allowed_exts = [];
        foreach($filetypes as $mfiletype)
        {
            array_push($all_allowed_exts, $mfiletype['extensions']);
        }
        $all_allowed_exts = implode(',', $all_allowed_exts);
        $all_allowed_exts = explode(',', $all_allowed_exts);
        $all_allowed_exts = array_unique($all_allowed_exts);
        $upload_max_filesize = config('jshop.upload_filesize');
        $upload_max_filesize = empty($upload_max_filesize) ? 5242880 : $upload_max_filesize;//默认5M
        if(isset($_FILES['upfile']))
        {
            $file_extension = get_file_extension($_FILES['upfile']['name']);
            $savepath = DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'uploads' . get_hash_dir($_FILES['upfile']['name']);
        }
        else
        {
            $file_extension = get_file_extension($_FILES['file']['name']);
            $savepath = DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'uploads' . get_hash_dir($_FILES['file']['name']);
        }
        //上传处理类
        $config = array(
            'rootPath' => ROOT_PATH . DIRECTORY_SEPARATOR . 'public',
            'savePath' => $savepath,
            'maxSize'  => $upload_max_filesize,
            'saveName' => array(
                'uniqid',
                ''
            ),
            'exts'     => $all_allowed_exts,
            'autoSub'  => false,
        );
        $upload = new \org\Upload($config,'Local');
        $info = $upload->upload();
        if($info)
        {
            $first = array_shift($info);
            $url = $savepath . $first['savename'];
            $preview_url = $url;
            $iData['id'] = md5(get_hash($first['name']));
            $iData['type'] = 'local';
            $iData['name'] = $first['name'];
            $iData['url'] = $url;
            $iData['ctime'] = time();
            $iData['path'] = ROOT_PATH .DIRECTORY_SEPARATOR.'public'.$savepath . $first['savename'];
            $image_model = new imageModel();
            if($image_model->save($iData))
            {
                $data = [
                    'status' => true,
                    'msg' => '上传成功',
                    'data' => [
                        'image_id' => $iData['id'],
                        'url' => $jshopHost.$url,
                        'type' => $first['type'],
                    ]
                ];
                return $data;
            }else {
                return [
                    'data' => '',
                    'status' => false,
                    'msg' => "保存失败"
                ];
            }
        }else {
            return [
                'data' => '',
                'status' => false,
                'msg' => $upload->getError()
            ];
        }
    }
}