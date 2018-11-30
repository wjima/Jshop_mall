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


class Images extends Manage
{

    public function index()
    {
        $imageModel = new imageModel();

        if(Request::isAjax()) {
            $filter = input('request.');
            return $imageModel->tableData($filter);
        }
        return $this->fetch('index');
    }


    /*
     * uploadImage
     * 上传图片
     */
    function uploadImage()
    {
        $filetypes = [
            'image' => [
                'title'      => 'Image files',
                'extensions' => 'jpg,jpeg,png,gif,bmp4'
            ],
        ];

        $image_extensions = explode(',','jpg,jpeg,png,gif,bmp4');
        if(Request::isPost()) {
            $all_allowed_exts = array();
            foreach($filetypes as $mfiletype) {
                array_push($all_allowed_exts,$mfiletype['extensions']);
            }

            $all_allowed_exts = implode(',',$all_allowed_exts);
            $all_allowed_exts = explode(',',$all_allowed_exts);
            $all_allowed_exts = array_unique($all_allowed_exts);
            $upload_max_filesize = config('jshop.upload_filesize');
            $upload_max_filesize = empty($upload_max_filesize) ? 5242880 : $upload_max_filesize;//默认5M


            if(isset($_FILES['upfile'])){
                $file_extension   = get_file_extension($_FILES['upfile']['name']);
                $savepath = DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'uploads' . get_hash_dir($_FILES['upfile']['name']);
            }else{
                $file_extension   = get_file_extension($_FILES['file']['name']);
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

            $image_storage = config('jshop.image_storage');
            if (!$image_storage) {
                $image_storage = [
                    'type' => 'Local',
                ];
            }
            //增加后台设置，如果设置则用后台设置的
            if (getSetting('image_storage_params')) {
                $image_storage = array_merge(['type' => getSetting('image_storage_type')], getSetting('image_storage_params'));
            }
            $upload = new \org\Upload($config,$image_storage['type'],$image_storage);
            $info = $upload->upload();

            if($info) {
                $first         = array_shift($info);
                $url           = getRealUrl($savepath . $first['savename']);
                $preview_url   = $url;
                $iData['id']   = md5(get_hash($first['name']));
                $iData['type'] = $image_storage['type'];
                $iData['name'] = $first['name'];
                $iData['url']  = $url;
                $iData['ctime']  = time();
                $iData['path'] = ROOT_PATH .DIRECTORY_SEPARATOR.'public'.$savepath . $first['savename'];
                $image_model   = new imageModel();
                if($image_model->save($iData)) {

                    if(isset($_FILES['upfile'])){
                        $callback = input('callback','');
                        $editInfo = [
                            'originalName' => $iData['name'],
                            'name' => $first['savename'],
                            'url' => $url,
                            'size' => $first['size'],
                            'type' => $type,
                            'state' => 'SUCCESS',
                            'image_id' => $iData['id'],
                        ];

                        if($callback) {
                            echo '<script>'.$callback.'('.json_encode($editInfo).')</script>';exit;
                        } else {
                            echo json_encode($editInfo);exit;
                        }
                    }else{
                        $data = [
                            'url'        => $preview_url,
                            'image_id'   => $iData['id'],
                            'image_name' => $iData['name'],
                        ];
                        $response = [
                            'data'   => $data,
                            'status' => true,
                            'msg'    => $upload->getError()
                        ];
                        echo json_encode($response);exit;

                    }
                }else {
                    $response =  [
                        'data'   => '',
                        'status' => false,
                        'msg'    => "保存失败"
                    ];
                    echo json_encode($response);exit;
                }
            }else {

                $response = [
                    'data'   => '',
                    'status' => false,
                    'msg'    => $upload->getError()
                ];
                echo json_encode($response);exit;

            }
        }
    }

    public function listimage()
    {
        $imageModel = new imageModel();
        $filter = input('request.');
        $filter['limit'] = input('size', '20');
        $filter['start'] = input('start', '0');
        $filter['page'] = ($filter['start'] / $filter['limit']) + 1;
        $data = $imageModel->tableData($filter);
        $imageData = [];
        foreach ($data['data'] as $key => $val) {
            if($val['type']=='local'){
                $val['url'] = getRealUrl($val['url']);
            }
            $imageData[$key]['url'] = $val['url'];
            $imageData[$key]['image_id'] = $val['id'];
            $imageData[$key]['name'] = $val['name'];
            $imageData[$key]['ctime'] = $val['ctime'];
        }
        $iData['start'] = $filter['start'];
        $iData['state'] = 'SUCCESS';
        $iData['list'] = $imageData;
        $iData['total'] = $data['count'];
        echo json_encode($iData);
        exit();
    }

    /**
     * 图片管理
     */
    public function manage()
    {
        header("Content-Type: text/html; charset=utf-8");
        $config = ROOT_PATH . 'public' . DS . 'static' . DS . 'js' . DS . 'ue' . DS . 'config.json';
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($config)), true);
        $action = input('action');
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;
            /* 上传图片 */
            case 'uploadimage':
                $this->uploadImage();
            /* 列出图片 */
            case 'listimage':
                $this->listimage();
                break;
            /* 列出图片 */
            case 'search':
                $this->listimage();
                break;
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }
        $callback = input('callback');
        /* 输出结果 */
        if (isset($callback)) {
            if (preg_match("/^[\w_]+$/", $callback)) {
                echo htmlspecialchars($callback) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }

    /**
     * 图片裁剪
     */
    public function cropper()
    {
        $response = [
            'data' => '',
            'status' => 'fail',
            'msg' => "裁剪失败"
        ];

        if(!Request::isPost()) {
            return $response;
        }
        $imgUrl = $_POST['imgUrl'];
        $imgInitW = $_POST['imgInitW'];
        $imgInitH = $_POST['imgInitH'];
        $imgW = $_POST['imgW'];
        $imgH = $_POST['imgH'];
        $imgY1 = $_POST['imgY1'];
        $imgX1 = $_POST['imgX1'];
        $cropW = $_POST['cropW'];
        $cropH = $_POST['cropH'];
        $angle = $_POST['rotation'];

        $jpeg_quality = 100;
        //todo 判断文件是否是图片
        $output_file_path = DS . 'static' . DS . 'uploads' . get_hash_dir();
        $relpath = ROOT_PATH . DS . 'public' . $output_file_path;

        if (!is_dir($relpath)) {
            mkdirs($relpath);
        }

        $imgUrl = $this->getRealPath($imgUrl);

        if ((stripos($imgUrl, 'http') !== false) || (stripos($imgUrl, 'https') !== false)) {
            $tmp_img = $this->getImage($imgUrl, $relpath);
            if ($tmp_img['error'] > 0) {
                $response = Array(
                    "status" => 'error',
                    "message" => '裁剪失败'
                );
            }
            $imgUrl = $tmp_img['save_path'];
        }
        $tempFileName = "croppedImg_" . rand();
        $output_filename = $relpath . $tempFileName;
        $what = getimagesize($imgUrl);
        $file_name = $this->retrieve($imgUrl);
        switch (strtolower($what['mime'])) {
            case 'image/png':
                $img_r = imagecreatefrompng($imgUrl);
                $source_image = imagecreatefrompng($imgUrl);
                $type = '.png';
                break;
            case 'image/jpeg':
                $img_r = imagecreatefromjpeg($imgUrl);
                $source_image = imagecreatefromjpeg($imgUrl);
                error_log("jpg");
                $type = '.jpeg';
                break;
            case 'image/gif':
                $img_r = imagecreatefromgif($imgUrl);
                $source_image = imagecreatefromgif($imgUrl);
                $type = '.gif';
                break;
            default:
                die('image type not supported');
        }

        if (!is_writable(dirname($output_filename))) {
            $response = [
                'msg' => '裁剪失败',
            ];
            return $response;
        } else {
            $resizedImage = imagecreatetruecolor($imgW, $imgH);
            imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
            $rotated_image = imagerotate($resizedImage, -$angle, 0);
            $rotated_width = imagesx($rotated_image);
            $rotated_height = imagesy($rotated_image);
            $dx = $rotated_width - $imgW;
            $dy = $rotated_height - $imgH;
            $cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
            imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
            imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);
            $final_image = imagecreatetruecolor($cropW, $cropH);
            imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
            imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);

            imagejpeg($final_image, $output_filename . $type, $jpeg_quality);

            //保存到image里面，删除之前文件
            $url = $output_file_path . $tempFileName . $type;//todo 带上域名
            $iData['id'] = md5(get_hash($file_name));
            $iData['type'] = 'local';
            $iData['name'] = $file_name;
            $iData['url'] = $url;
            $iData['ctime'] = time();
            $iData['path'] = $output_filename . $type;
            $image_model = new imageModel();
            if ($image_model->save($iData)) {
                $response = [
                    "status" => 'success',
                    "url" => $url,
                    "src" => $url,
                    'image_id' => $iData['id'],
                ];
                $this->assign('data',$response);
                $this->view->engine->layout(false);
                $response['image_html'] = $this->fetch('gimage');
            }
            return $response;
        }

    }

    /**
     * 获取图片地址
     * @param $image_path 图片地址
     * @return mixed
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2017-11-28 16:11
     */
    private function getRealPath($image_path){
        $host =  $_SERVER['HTTP_HOST'];
        //增加图片裁剪功能
        if(!defined('APP_STATICS_HOST')&&strpos($image_path,'http://')!==false){
            $image_path = ROOT_PATH.'public'.str_replace('http://'.$host,'',$image_path);
        }else if(!defined('APP_STATICS_HOST')&&strpos($image_path,'https://')!==false){
            $image_path = ROOT_PATH.'public'.str_replace('https://'.$host,'',$image_path);
        }else if(strpos($image_path,'http://')===false&&strpos($image_path,'http://')===false){
            $tmp_url = explode('?',$image_path);
            $image_path = ROOT_PATH.'public'.$tmp_url[0];
        }
        return $image_path;
    }

    /**
     * php完美实现下载远程图片保存到本地
     * 参数：文件url,保存文件目录,保存文件名称，使用的下载方式
     * 当保存文件名称为空时则使用远程文件原来的名称
     * @param        $url
     * @param string $save_dir
     * @param string $filename
     * @param int    $type
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2017-11-28 16:00
     */
    private function getImage($url,$save_dir = '',$filename = '',$type = 0) {
        if(trim($url) == '') {
            return array(
                'file_name' => '',
                'save_path' => '',
                'error'     => 1
            );
        }
        if(trim($save_dir) == '') {
            $save_dir = './';
        }
        if(trim($filename) == '') {//保存文件名
            $ext = strrchr($url,'.');
            if($ext != '.gif' && $ext != '.jpg'&& $ext != '.png') {
                return array(
                    'file_name' => '',
                    'save_path' => '',
                    'error'     => 3
                );
            }
            $filename = time() . $ext;
        }
        if(0 !== strrpos($save_dir,'/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if(!file_exists($save_dir) && !mkdir($save_dir,0777,true)) {
            return array(
                'file_name' => '',
                'save_path' => '',
                'error'     => 5
            );
        }
        //获取远程文件所采用的方法
        if($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        }else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        $fp2 = @fopen($save_dir . $filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename,
            'error'     => 0
        );
    }

    /**
     * 获取文件名
     * @param $url
     * @return mixed
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2017-11-28 17:39
     */
    private function retrieve($url) {
        preg_match('/\/([^\/]+\.[a-z]+)[^\/]*$/',$url,$match);
        return $match[1];
    }
}
