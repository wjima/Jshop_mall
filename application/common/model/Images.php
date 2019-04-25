<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;
class Images extends Common
{

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $config['page'] = $post['page'];
        $config['list_rows'] = $post['limit'];
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit,false,$config);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        $re['sql'] = $this->getLastSql();

        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['id', 'eq', $post['id']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['ctime' => 'desc'];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        if(!$list->isEmpty()){
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

            foreach($list as $key=>$val){
                $list[$key]['ctime'] = date('Y-m-d H:i:s',$val['ctime']);
            }
        }
        return $list;
    }


    /**
     * 保存远程图片到本地
     * @param string $url
     * @return array
     */
    public function saveRemoteImage($url = '')
    {
        $return_data = [
            'status' => false,
            'msg'    => '保存失败',
            'data'   => ''
        ];
        $savepath    = '/static/uploads/images' . get_hash_dir();
        $filename    = md5(time()) . '.png';
        $data        = $this->getImage($url, $savepath, $filename);
        if($data['error']!=0){
            $return_data['msg'] = '图片保存失败';
            return $return_data;
        }
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
        if($image_storage['type'] == 'Local'){

        }else{
            $upload_max_filesize = config('jshop.upload_filesize');
            $upload_max_filesize = empty($upload_max_filesize) ? 5242880 : $upload_max_filesize;//默认5M
            //上传处理类
            $config = array(
                'rootPath' => ROOT_PATH . DIRECTORY_SEPARATOR . 'public',
                'savePath' => $savepath,
                'maxSize'  => $upload_max_filesize,
                'saveName' => array(
                    'uniqid',
                    ''
                ),
                'autoSub'  => false,
            );
            $upload = new \org\Upload($config,$image_storage['type'],$image_storage);
            $info = $upload->upload($data['save_path']);
            if(!$info){
                return [
                    'data'   => '',
                    'status' => false,
                    'msg'    => $upload->getError()
                ];
            }
            $first         = array_shift($info);
            $filename = $first['name'];
        }
        $url           = getRealUrl($savepath . $filename);
        $iData['id']   = md5(get_hash($filename));
        $iData['type'] = $image_storage['type'];
        $iData['name'] = $filename;
        $iData['url']  = $url;
        $iData['ctime']  = time();
        $iData['path'] = ROOT_PATH .'public'.$savepath . $filename;

        if(!$this->save($iData)) {
            return [
                'data'   => '',
                'status' => false,
                'msg'    => "保存失败"
            ];
        }
        $return_data['msg'] = '保存成功';
        $return_data['status'] = true;
        $return_data['data'] = [
            'url'        => $url,
            'image_id'   => $iData['id'],
            'image_name' => $iData['name'],
        ];
        return $return_data;
    }

    /**
     * php完美实现下载远程图片保存到本地
     * 参数：文件url,保存文件目录,保存文件名称，使用的下载方式
     * 当保存文件名称为空时则使用远程文件原来的名称
     * @param        $url
     * @param string $save_dir
     * @param string $filename
     * @param int $type
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2017-11-28 16:00
     */
    public function getImage($url, $save_dir = '', $filename = '', $type = 1)
    {
        if (trim($url) == '') {
            return array(
                'file_name' => '',
                'save_path' => '',
                'error'     => 1
            );
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//保存文件名
            $ext = strrchr($url, '.');
            if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png') {
                return array(
                    'file_name' => '',
                    'save_path' => '',
                    'error'     => 3
                );
            }
            $filename = time() . $ext;
        }
        if (substr($save_dir, -1) != '/') {
            $save_dir .= '/';
        }
        if(stripos($save_dir,ROOT_PATH) === false){
            $save_dir = ROOT_PATH.'public'.$save_dir;
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array(
                'file_name' => '',
                'save_path' => '',
                'error'     => 5
            );
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename,
            'error'     => 0
        );
    }

}
