<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Bargain as BargainModel;
use app\common\model\BargainLog;
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
            'status' => true,
            'data'   => '',
            'msg'    => ''
        ];
        $bargainModel = new BargainModel();
        if (Request::isPost()) {
            $data = input('param.');
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
        return $this->fetch();;
    }

    public function del()
    {

        if (!input('?param.id')) {
            return error_code(10000);
        }
        $bargainModel = new BargainModel();
        $bargainModel->where(['id' => input('param.id')])->delete();

        $bargainLogModel = new BargainLog();
        $bargainLogModel->where(['bargain' => input('param.id')])->delete();

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
            'msg'    => '参数丢失',
        ];
        $field  = input('post.field/s');
        $value  = input('post.value/d');
        $id     = input('post.id/d', '0');
        if (!$field || !$value || !$id) {
            $result['msg']    = '参数丢失';
            $result['status'] = false;
        }

        $bargainModel = new BargainModel();
        $rel          = $bargainModel->where('id', 'eq', $id)->update([$field => $value]);
        if ($rel) {
            $result['msg']    = '更新成功';
            $result['status'] = true;
        } else {
            $result['msg']    = '更新失败';
            $result['status'] = false;
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
            'msg'    => '关键参数丢失',
            'data'   => []
        ];
        $bargainModel = new BargainModel();
        $id           = input('param.id/d', 0);

        $state = input('param.state/s', 'true');

        if (!$id) return $result;
        if ($state == 'true') {
            $status = $bargainModel::STATUS_ON;
        } else {
            $status = $bargainModel::STATUS_OFF;
        }
        if ($bargainModel->save(['status' => $status], ['id' => $id])) {
            $result['status'] = true;
            $result['msg']    = '设置成功';
        } else {
            $result['msg'] = '设置失败';
        }
        return $result;
    }
}
