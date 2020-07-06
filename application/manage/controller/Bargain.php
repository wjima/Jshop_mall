<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Bargain as BargainModel;
use app\common\model\BargainLog;
use app\common\model\BargainRecord;
use think\facade\Request;


class Bargain extends Manage
{


    /**
     *
     * @return mixed
     */
    public function index()
    {
        if (Request::isAjax()) {
            $bargainModel = new BargainModel();
            $request      = input('param.');
            return $bargainModel->tableData($request);
        }
        return $this->fetch();
    }


    //添加&编辑砍价
    public function edit()
    {
        $result       = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        $bargainModel = new BargainModel();
        if (Request::isPost()) {
            $data     = input('param.');
            $validate = new \app\common\validate\Bargain();
            if (!$validate->check($data)) {
                $result['msg'] = $validate->getError();
                return $result;
            }
            return $bargainModel->toAdd($data);
        }
        //如果是编辑，取数据
        if (input('?param.id')) {
            $info = $bargainModel->where('id', input('param.id'))->find();
            if (!$info) {
                return error_code(10001);
            }
            $info['date'] = date('Y-m-d H:i:s', $info['stime']) . " 到 " . date('Y-m-d H:i:s', $info['etime']);
            $this->assign('info', $info);
        }
        return $this->fetch();
    }

    public function del()
    {

        if (!input('?param.id')) {
            return error_code(10000);
        }
        $bargainModel = new BargainModel();
        $bargainModel->where(['id' => input('param.id')])->delete();

        $bargainLogModel = new BargainLog();
        $bargainLogModel->where(['bargain_id' => input('param.id')])->delete();

        return [
            'status' => true,
            'data'   => '',
            'msg'    => ''
        ];
    }

    //排序更改
    public function updateSort()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '',
        ];
        $field  = input('post.field/s');
        $value  = input('post.value/d');
        $id     = input('post.id/d', '0');
        if (!$field || !$value || !$id) {
            return error_code(10003);
        }

        $bargainModel = new BargainModel();
        $rel          = $bargainModel->where('id', 'eq', $id)->update([$field => $value]);
        if ($rel) {
            $result['msg']    = '更新成功';
            $result['status'] = true;
        } else {
            return error_code(10021);
        }
        return $result;
    }

    /**
     *  更改设置状态
     *
     */
    public function changeState()
    {
        $result       = [
            'status' => false,
            'msg'    => '',
            'data'   => []
        ];
        $bargainModel = new BargainModel();
        $id           = input('param.id/d', 0);

        $state = input('param.state/s', 'true');

        if (!$id) return error_code(10003);
        if ($state == 'true') {
            $status = $bargainModel::STATUS_ON;
        } else {
            $status = $bargainModel::STATUS_OFF;
        }
        if ($bargainModel->save(['status' => $status], ['id' => $id])) {
            $result['status'] = true;
            $result['msg']    = '设置成功';
        } else {
            return error_code(10021);
        }
        return $result;
    }

    /**
     *
     * @return mixed
     */
    public function record()
    {
        if (Request::isAjax()) {
            $bargainRecordModel = new BargainRecord();
            $request      = input('param.');
            return $bargainRecordModel->tableData($request);
        }
        return $this->fetch();
    }

    /**
     * 砍价日志
     */
    public function recordLog()
    {
        $this->assign('record_id', input('record_id'));
        if (Request::isAjax()) {
            $bargainLogModel = new BargainLog();
            $request      = input('param.');
            return $bargainLogModel->tableData($request);
        }
        return $this->fetch();
    }
}
