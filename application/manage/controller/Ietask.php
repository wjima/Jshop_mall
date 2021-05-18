<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;


use app\common\controller\Manage;
use Request;

use app\common\model\Ietask as ietaskModel;
use think\facade\Log;
use think\Queue;
use Apfelbox\FileDownload\FileDownload;


class Ietask extends Manage
{

    public function index()
    {
        $ietaskModel = new ietaskModel();

        if (Request::isAjax()) {
            $filter = input('request.');
            return $ietaskModel->tableData($filter);
        }
        return $this->fetch('index');
    }

    /**
     * taskname :导出任务名称
     * ids :导出时选中相关id
     * filter :导出时选中相关过滤
     * model : 导出任务job类
     * 添加导出任务
     *
     * @return array
     */
    public function export()
    {
        $result     = error_code(10039);
        $taskname   = input('taskname/s', '');
        $filter     = input('filter/s', '');
        $job        = input('model/s', '');
        if (empty($taskname)) {
            return error_code(10045);
        }
        if (!$taskname) {
            $taskname = md5(time());
        }
        if (!$job) {
            return $result;
        }
        $where = [];
        if ($filter) {
            $where = convertUrlQuery($filter);
        }
        //增加条件验证
        $model       = "app\\common\\model\\$job";
        if (method_exists($model, "exportValidate")) {
            $obj         = new $model();
            $validateRes = $obj->exportValidate($where); //验证过滤条件
            if (!$validateRes['status']) {
                return $validateRes;
            }
        }

        $ietaskModle    = new ietaskModel();
        $data['name']   = $taskname;
        $data['type']   = $ietaskModle::TYPE_EXPORT;
        $data['status'] = $ietaskModle::WAIT_STATUS;
        $data['params'] = json_encode($where);
        $res            = $ietaskModle->addExportTask($data, $job);
        if ($res !== false) {
            $result['status'] = true;
            $result['msg']    = '导出任务加入成功，请到控制面板->导入导出中下载文件';
        }

        return $result;
    }

    /**
     * 下载导入模板
     */
    public function importTemplete()
    {
        $tplName  = input('tplName', 'goods');
        $filePath = config('jshop.' . $tplName . '_import_templete');

        if (!file_exists($filePath)) { //检查文件是否存在
            echo '404';
        }
        $file_name = basename($filePath);
        $file_type = explode('.', $filePath);
        $file_type = $file_type[count($file_type) - 1];
        $file_name = trim($new_name == '') ? $file_name : urlencode($new_name);
        $file_type = fopen($filePath, 'r'); //打开文件
        //输入文件标签
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: " . filesize($filePath));
        header("Content-Disposition: attachment; filename=" . $file_name);
        //输出文件内容
        echo fread($file_type, filesize($filePath));
        fclose($file_type);
        exit();
    }


    public function import()
    {
        $result = error_code(10035);
        $file   = request()->file('importFile');
        if (!$file) {
            return $result;
        }
        $model = input('model', 'Goods');

        $savepath = ROOT_PATH . 'public' . DS . 'uploads' . get_hash_dir($file->getInfo('name'));
        $info     = $file->validate(['size' => config('jshop.upload_filesize'), 'ext' => 'csv'])->move($savepath);

        if ($info) {
            $params      = [
                'filename'  => $info->getFilename(),
                'file_size' => $file->getInfo('size'),
                'file_path' => $savepath . $info->getSaveName(),
            ];
            $ietaskModle = new ietaskModel();

            $data['name']      = $model . '-导入';
            $data['type']      = $ietaskModle::TYPE_INPORT;
            $data['params']    = json_encode($params);
            $data['status']    = $ietaskModle::WAIT_STATUS;
            $data['file_name'] = $file->getInfo('name');
            $data['file_size'] = getRealSize($file->getInfo('size'));
            $data['file_path'] = $savepath . $info->getSaveName();
            $res               = $ietaskModle->addImportTask($data, $model);
            if ($res !== false) {
                $result['status'] = true;
                $result['msg']    = '导入任务加入成功，请到任务列表中查看进度';
            }
            return $result;
        } else {
            // 上传失败获取错误信息
            $result['msg'] = $file->getError();
        }
        return $result;
    }

    public function down()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => error_code(10101,true)
        ];
        $id     = input('id/d', 0);
        if (!$id) {
            $result['msg'] = error_code(10051,true);
            return $result;
        }
        //todo 判断能否下载
        $result['status']      = true;
        $result['msg']         = '开始下载';
        $result['data']['url'] = url('ietask/dodown', ['id' => $id]);
        return $result;
    }

    public function doDown()
    {
        $id = input('id/d', 0);
        if (!$id) {
            $this->error(error_code(10051,true));
        }
        $ietaskModle = new ietaskModel();
        $task        = $ietaskModle->where(['id' => $id])->find();
        if ($task) {
            try {
                $fileDownload = FileDownload::createFromFilePath($task['file_path']);
                $fileDownload->sendDownload($task['file_name']);
            } catch (\Exception $e) {
                Log::record('文件下载失败，错误信息：' . json_encode($e->getMessage()));
                $this->error(error_code(10103,true));
            }
        } else {
            $this->error(error_code(10103,true));
        }
    }

    //删除
    public function del()
    {
        $result = error_code(10023);
        $id     = input('id/d', '');
        if (!$id) {
            // $result['msg'] = '关键参数丢失';
            return error_code(10051);
        }
        $model = new \app\common\model\Ietask();
        $rel   = $model->where('id', 'eq', $id)->delete();
        if ($rel) {
            $result['status'] = true;
            $result['msg']    = '删除成功';
        }
        return $result;
    }

}
