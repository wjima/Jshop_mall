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
use app\common\model\Form as FormModel;
use app\common\model\FormSubmit;
use app\common\model\FormSubmitDetail;
use org\Wx;
use think\facade\Request;
use app\common\validate\Form as FormValidate;

class Form extends Manage
{


    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $form = new FormModel();
        if (Request::isAjax()) {
            return $form->tableData(input('param.'));
        }
        return $this->fetch();
    }


    /**
     *  添加表单
     * User:mark
     * @return array|mixed
     */
    public function add()
    {
        if (Request::isPost()) {
            $result               = [
                'status' => false,
                'msg'    => '',
                'data'   => '',
            ];
            $form                 = new FormModel();
            $data['name']         = input('name/s', '');
            $data['img']          = input('img/a', []);
            $data['type']         = input('type/d', '1');
            $data['head_type']    = input('head_type/d', '1');
            $data['video']        = input('video/s', '');
            $data['video_cover']  = input('video_cover/s', '');
            $data['desc']         = input('desc/s', '');
            $data['field']        = input('field/a', '');
            $data['button_name']  = input('button_name/s', '');
            $data['button_color'] = input('button_color/s', '');
            $data['is_login']     = input('is_login', '2');
            $data['sort']         = input('sort/d', '100');
            $data['return_msg']   = input('return_msg/s', '保存成功');
            $data['end_date']     = input('end_date', '0');
            $data['times']        = input('times', '0');
            if ($data['end_date'] != '0') {
                $data['end_date'] = strtotime($data['end_date']);
            }
            if ($data['head_type'] == $form::HEAD_TYPE_SLIDE) {
                $data['head_type_value'] = implode(',', $data['img']);
            } elseif ($data['head_type'] == $form::HEAD_TYPE_IMAGE) {
                $data['head_type_value'] = $data['img'][0];
            } elseif ($data['head_type'] == $form::HEAD_TYPE_VIDEO) {
                $data['head_type_value'] = $data['video_cover'];
                $data['head_type_video'] = $data['video'];
            }
            $validate = new FormValidate();
            if (!$validate->check($data)) {
                $result['msg'] = $validate->getError();
                return $result;
            }
            if (!$form->add($data)) {
                return error_code(10004);
            }
            $result['msg']    = '保存成功';
            $result['status'] = true;
            return $result;
        }
        $formType = config('params.form');
        $this->assign('type', $formType['type']);
        $this->assign('field_type', $formType['field_type']);
        $this->assign('validation_type', $formType['validation_type']);
        $this->assign('head_type', $formType['head_type']);

        return $this->fetch('add');
    }


    /**
     *
     *  表单编辑
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $id   = input('id/d', 0);
        $form = new FormModel();
        if (Request::isPost()) {
            $result               = [
                'status' => false,
                'msg'    => '',
                'data'   => '',
            ];
            $data['id']           = input('id/d', '0');
            $data['name']         = input('name/s', '');
            $data['img']          = input('img/a', []);
            $data['type']         = input('type/d', '1');
            $data['head_type']    = input('head_type/d', '1');
            $data['video']        = input('video/s', '');
            $data['video_cover']  = input('video_cover/s', '');
            $data['desc']         = input('desc/s', '');
            $data['field']        = input('field/a', '');
            $data['button_name']  = input('button_name/s', '');
            $data['button_color'] = input('button_color/s', '');
            $data['is_login']     = input('is_login', '2');
            $data['sort']         = input('sort/d', '100');
            $data['return_msg']   = input('return_msg/s', '保存成功');
            $data['end_date']     = input('end_date', '0');
            $data['times']        = input('times', '0');
            if ($data['end_date'] != '0') {
                $data['end_date'] = strtotime($data['end_date']);
            }
            if ($data['head_type'] == $form::HEAD_TYPE_SLIDE) {
                $data['head_type_value'] = implode(',', $data['img']);
            } elseif ($data['head_type'] == $form::HEAD_TYPE_IMAGE) {
                $data['head_type_value'] = $data['img'][0];
            } elseif ($data['head_type'] == $form::HEAD_TYPE_VIDEO) {
                $data['head_type_video'] = $data['video'];
                $data['head_type_value'] = $data['video_cover'];
            }
            $validate = new FormValidate();
            if (!$validate->check($data)) {
                $result['msg'] = $validate->getError();
                return $result;
            }
            $where[] = ['id', '=', $data['id']];
            if (!$form->edit($where, $data)) {
                return error_code(10004);
            }
            $result['msg']    = '保存成功';
            $result['status'] = true;
            return $result;
        } else {
            $formInfo        = $form->getFormInfo($id);
            $video           = '';
            $head_type_value = [];
            $head_type_value = explode(',', $formInfo['data']['head_type_value']);
            if ($formInfo['data']['head_type'] == $form::HEAD_TYPE_VIDEO) {
                $video       = _sFile($formInfo['data']['head_type_video']);
                $video_cover = $head_type_value[0];
            }
            $this->assign('video', $video);
            $this->assign('head_type_value', $head_type_value);
            $this->assign('form', $formInfo['data']);
            $formType = config('params.form');
            $this->assign('type', $formType['type']);
            $this->assign('field_type', $formType['field_type']);
            $this->assign('validation_type', $formType['validation_type']);
            $this->assign('head_type', $formType['head_type']);
            return $this->fetch('edit');
        }
    }


    /***
     * 删除form
     * @return array
     */
    public function del()
    {
        $form         = new FormModel();
        $result       = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        $id           = input('param.id/d', 0);
        $deleteResult = $form->deleteForm($id);
        if (!$deleteResult['status']) {
            $result['status'] = false;
            $result['msg']    = $deleteResult['msg'];
        }
        return $result;
    }

    /***
     * 生成小程序码
     */
    public function generate()
    {
        $id     = input('id/d', 0);
        $result = [
            'status' => false,
            'msg'    => error_code(10003,true),
            'data'   => ''
        ];
        if (!$id) {
            echo $result['msg'];
            return;
        }
        $this->assign('id', $id);
        $this->view->engine->layout(false);
        return $this->fetch('qrcode');
    }

    public function qrcode()
    {
        $id = input('id/d', 0);
        $wx = new Wx();
        $wx->getFormWxcode($id);
    }

    /**
     * 表单提交列表
     * @return mixed
     */
    public function formSubmit()
    {
        $formSubmit = new FormSubmit();
        if (Request::isAjax()) {
            return $formSubmit->tableData(input('param.'));
        }
        $formModel = new FormModel();
        $formList  = $formModel->getAll('id,name');
        $this->assign('formList', $formList);
        return $this->fetch('submit_list');
    }

    /**
     * 表单报表
     */
    public function report()
    {
        $id     = input('id/d', 0);
        $result = [
            'status' => true,
            'msg'    => '',
            'data'   => ''
        ];
        $this->view->engine->layout(false);

        $formSubmit = new FormSubmit();

        $total_submit = $formSubmit->where(['form_id' => $id])->count();
        $total_sum    = $formSubmit->where(['form_id' => $id])->sum('money');
        $this->assign('total_submit', $total_submit);
        $this->assign('total_sum', getMoney($total_sum));
        $this->assign('id', $id);
        $result['data']   = $this->fetch('report');
        $result['status'] = true;
        $result['msg']    = '获取成功';
        return $result;

    }

    /**
     * 表单提交统计
     * @return array
     */
    public function statistics()
    {
        $id = input('id/d', 0);
        if (!$id) {
            return false;
        }
        $formSubmitModel = new FormSubmit();
        $payres          = $formSubmitModel->statisticsByFormid($id);

        $data = [
            'legend' => [
                'data' => ['提交量']
            ],
            'xAxis'  => [
                [
                    'type' => 'category',
                    'data' => $payres['day']
                ]
            ],
            'series' => [
                [
                    'name' => '提交量',
                    'type' => 'line',
                    'data' => $payres['data']
                ]
            ]
        ];
        return $data;
    }

    /**
     * 删除提交
     * @return array
     */
    public function delSubmit()
    {
        $formSubmitModel = new FormSubmit();
        $result          = [
            'status' => true,
            'msg'    => '删除成功',
            'data'   => ''
        ];
        $id              = input('param.id/d', 0);
        $deleteResult    = $formSubmitModel->deleteFormSubmit($id);
        if (!$deleteResult['status']) {
            $result['status'] = false;
            $result['msg']    = $deleteResult['msg'];
        }
        return $result;
    }


    /**
     * 表单明细
     * @return mixed
     */
    public function formSubmitDetail()
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => ''
        ];
        $id     = input('id/d', '0');
        if (!$id) {
            return error_code(20096);
        }
        $formSubmit     = new FormSubmit();
        $formSubmitInfo = $formSubmit->getDetail($id);
        if (!$formSubmitInfo['status']) {
            $result['msg'] = $formSubmitInfo['msg'];
            return $result;
        }
        $items = json_encode($formSubmitInfo['data']['items']);
        $this->assign('info', $formSubmitInfo['data']);
        $this->assign('items', $items);
        $this->view->engine->layout(false);
        $result['status'] = true;
        $result['msg']    = '获取成功';
        $result['data']   = $this->fetch('submit_detail');
        return $result;
    }

    /**
     * 编辑表单提交
     */
    public function editformSubmit()
    {
        $result = [
            'status' => false,
            'msg'    => '',
            'data'   => ''
        ];
        $id     = input('id/d', '0');
        if (!$id) {
            return error_code(20096);
        }
        $this->assign('id', $id);
        if (Request::isPost()) {
            $feedback   = input('feedback/s', '');
            $formSubmit = new FormSubmit();
            $res        = $formSubmit->save(['feedback' => $feedback, 'status' => $formSubmit::FORM_PAY_STATUS_YES], ['id' => $id]);
            if ($res) {
                $result['status'] = true;
                $result['msg']    = '操作成功';
                return $result;
            } else {
                // $result['msg'] = '操作失败';
                return error_code(10018);
            }
        } else {
            $this->view->engine->layout(false);
            $result['status'] = true;
            $result['msg']    = '获取成功';
            $result['data']   = $this->fetch('edit_form_submit');
            return $result;
        }
    }
}