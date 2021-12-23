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
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere          = $this->tableWhere($post);
        $config['page']      = $post['page'];
        $config['list_rows'] = $post['limit'];
        $list                = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit, false, $config);
        $data                = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;
        // $re['sql']   = $this->getLastSql();

        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', '%' . $post['name'] . '%'];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['id', 'eq', $post['id']];
        }
        if (isset($post['group_id']) && $post['group_id'] != "") {
            $where[] = ['group_id', 'eq', $post['group_id']];
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
        if (!$list->isEmpty()) {
            $http_type  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $groupModel = new ImagesGroup();
            foreach ($list as $key => $val) {
                $list[$key]['ctime'] = date('Y-m-d H:i:s', $val['ctime']);
                if (!$val['group_id']) {
                    $list[$key]['group_name'] = '默认分组';
                } else {
                    $group                    = $groupModel->where('id', '=', $val['group_id'])->find();
                    $list[$key]['group_name'] = $group['name'];
                }
            }
        }
        return $list;
    }

    /**
     * 保存图片
     * @param string $url
     * @param false $remote
     * @param int $group_id 分组id
     * @return array|mixed|string
     */
    public function saveImage($url = '', $remote = false, $group_id = 0)
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10004, true),
            'data'   => ''
        ];
        if (!is_dir(ROOT_PATH . 'public/static/uploads/images/')) {
            @mkdirs(ROOT_PATH . 'public/static/uploads/images/');
        }
        //上传处理类
        $config        = [
            'rootPath' => ROOT_PATH . 'public',
            'savePath' => '/static/uploads/images',
            'subName'  => ['get_date_dir', ''],
            'maxSize'  => config('jshop.upload_filesize'),
            'exts'     => 'jpg,jpeg,png,gif,bmp4',
            'saveName' => ['uniqid', time()],
        ];
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
        if ($remote) {
            $savepath = '/static/uploads/images' . get_date_dir() . '/';
            $filename = md5(substr(msectime() . rand(0, 9), 1)) . '.png';
            $data     = $this->getImage($url, $savepath, $filename);
            if ($data['error'] != 0) {
//                $return_data['msg'] = '图片保存失败';
                return error_code(10042);
            }
            if (isset($data['save_dir']) && $data['save_dir']) {
                $savepath = $data['save_dir'];
            }
            if (isset($data['file_name']) && $data['file_name']) {
                $filename = $data['file_name'];
            }
            $raw = [
                'upfile' => [
                    'name'     => $data['file_name'],
                    'tmp_name' => $data['save_path'],
                ]
            ];
            if ($image_storage['type'] != 'Local') {
                $upload = new \org\Upload($config, $image_storage['type'], $image_storage);
                $info   = $upload->upload($raw);
            } else {
                $info['upfile'] = [
                    'name'     => $data['file_name'],
                    'savename' => $filename,
                    'savepath' => $savepath,
                    'key'      => 'upfile',
                ];
            }
        } else {
            $upload = new \org\Upload($config, $image_storage['type'], $image_storage);
            $info   = $upload->upload();
        }
        if ($info) {
            $first             = array_shift($info);
            $url               = getRealUrl($first['savepath'] . $first['savename']);
            $iData['id']       = md5(get_hash($first['name']));
            $iData['group_id'] = input('param.group_id', $group_id);
            $iData['type']     = $image_storage['type'];
            $iData['name']     = $first['name'];
            $iData['url']      = $url;
            $iData['ctime']    = time();
            $iData['path']     = ROOT_PATH . 'public' . $first['savepath'] . $first['savename'];
            if (!$this->save($iData)) {
                $return_data['msg'] = error_code(10004, true);
                return $return_data;
            }
            $return_data['status'] = true;
            $return_data['data']   = $iData;
            $return_data['msg']    = '保存成功';
        } else {
            $return_data['msg'] = $upload->getError();
            return $return_data;
        }
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
        if (stripos($url, request()->domain()) !== false) {
            $save_path = ROOT_PATH . str_replace(request()->domain() . '/', 'public/', $url);
            $filename  = basename($save_path);
            $save_dir  = str_replace(request()->domain(), '', $url);
            $save_dir  = str_replace($filename, '', $save_dir);

            return array(
                'file_name' => $filename,
                'save_path' => $save_path,
                'save_dir'  => $save_dir,
                'error'     => 0
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
        if (stripos($save_dir, ROOT_PATH) === false) {
            $save_dir = ROOT_PATH . 'public' . $save_dir;
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            }
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
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
