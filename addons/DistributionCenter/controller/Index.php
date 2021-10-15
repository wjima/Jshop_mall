<?php

namespace addons\DistributionCenter\controller;

use addons\DistributionCenter\model\Distribution;
use addons\DistributionCenter\model\DistributionCondition;
use addons\DistributionCenter\model\DistributionGrade;
use addons\DistributionCenter\model\DistributionOrder;
use addons\DistributionCenter\model\DistributionResult;
use myxland\addons\library\AddonController;
use think\Container;
use think\facade\Cache;
use think\facade\Request;
use think\facade\Session;
use app\common\model\UserGrade;
use app\common\model\ManageRoleOperationRel;
use app\common\model\Operation;

class Index extends AddonController
{

    //说明：这里的构造函数是为了实现后端的权限判断，所以要这样写。
    protected function initialize()
    {
        parent::initialize();

        //想实现判断后台的登陆判断很简单，写如下代码即可
        Session::init([
            'prefix'     => 'manage',
            'type'       => '',
            'auto_start' => true,
        ]);

        if (!session('?manage')) {
            cookie('redirect_url', Container::get('request')->url(), 3600);
            $this->redirect('manage/common/login');
        }

        $operationModel = new Operation();

        //判断当前是否有权限操作
        $mrorModel = new ManageRoleOperationRel();
        $permRe    = $mrorModel->checkPerm(session('manage.id'), $operationModel::MENU_MANAGE, $this->controller, $this->action,$this->addon);
        if (!$permRe['status']) {
            if (Request::isAjax()) {
                $err = [
                    'status' => false,
                    'data'   => '',
                    'msg'    => $permRe['msg']
                ];
                echo json_encode($err);
                die();
                //return $permRe;
            } else {
                $this->error($permRe['msg']);
            }
        }
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout'); 
    }

    /**
     * 分销设置
     */
    public function setting()
    {
        $addonModel = new \app\common\model\Addons();
        $config     = $addonModel->getSetting('DistributionCenter');

        $setting = isset($config['setting']) ? $config['setting'] : [];
        if (Request::isPost()) {
            $params = input('param.');
            if (isset($params['__Jshop_Token__'])) {
                unset($params['__Jshop_Token__']);
            }
            $uData = $config;
            if ($config) {
                $uData['setting'] = array_merge($setting, $params);//todo 所有插件列表上面的配置，都要以setting为name
            }
            $addonModel->doSetting($uData, 'DistributionCenter');
            $result = [
                'status' => true,
                'data'   => [],
                'msg'    => '保存成功'
            ];
            Cache::clear();
            return $result;
        } else {
            $this->assign('data', $setting);
        }
        return $this->fetch();
    }

    /***
     * 等级列表
     * @return mixed
     */
    public function grade()
    {
        if (Request::isAjax()) {
            $userGradeModel = new UserGrade();
            $gradeList      = $userGradeModel->tableData(input('param.'));
            $disGradeModel  = new DistributionGrade();
            $res            = $disGradeModel->select();
            $isset          = true;
            if (count($res) <= 0) {
                $isset = false;
            }

            foreach ($gradeList['data'] as $key => $value) {
                if (!$isset) {
                    $gradeList['data'][$key]['is_def'] = '否';
                } else {
                    $has_default = false;
                    foreach ($res as $rkey => $rval) {
                        if ($rval['grade_id'] == $value['id'] && $rval['is_default'] == $disGradeModel::DEFAULT_YES) {
                            $gradeList['data'][$key]['is_def'] = '是';
                            $has_default                       = true;
                        }
                    }
                    if (!$has_default) {
                        $gradeList['data'][$key]['is_def'] = '否';
                    }
                }
            }
            return $gradeList;
        } else {
            return $this->fetch('grade');
        }
    }

    /**
     * 编辑等级
     */
    public function editGrade()
    {
        $userGradeModel = new UserGrade();
        $id             = input('id/d', 0);
        $gradeInfo      = $userGradeModel->get($id);
        $disGradeModel  = new DistributionGrade();
        if ($id) {
            $disGradeInfo = $disGradeModel->get($id);
            if ($disGradeInfo) {
                $gradeInfo = array_merge($gradeInfo->toArray(), $disGradeInfo->toArray());
            } else {
                $gradeInfo['is_default'] = $disGradeModel::DEFAULT_NO;
                $gradeInfo['upgrade']    = $disGradeModel::UPGRADE_NO;
            }
        }
        $this->assign('gradeInfo', $gradeInfo);
        if (Request::isPOST()) {
            $data                   = input('param.');
            $distributionGradeModel = new DistributionGrade();
            return $distributionGradeModel->addData($data);
        }
        return $this->fetch('edit_grade');
    }

    /***
     * 添加升级条件
     * @return array
     */
    public function conditionAdd()
    {
        $this->view->engine->layout(false);
        $conditionModel = new DistributionCondition();
        $this->assign('code', $conditionModel->code);
        return [
            'status' => true,
            'data'   => $this->fetch('condition_add'),
            'msg'    => ''
        ];
    }

    public function conditionEdit()
    {
        $this->view->engine->layout(false);
        if (!(input('?param.condition_code') && input('?param.grade_id')) && !input('?param.id')) {
            return error_code(15003);
        }
        $conditionModel = new DistributionCondition();
        if (Request::isPOST()) {
            $data = input('param.');
            return $conditionModel->addData($data);
        }
        //如果是修改，就取数据，否则就是新增，直接渲染模板
        if (input('?param.id')) {
            $info = $conditionModel->getInfo(input('param.id'));
            $code = $info['code'];
            $this->assign($info->toArray());
        } else {
            $code = input('param.condition_code');
            $this->assign('grade_id', input('param.grade_id/d'));
            $this->assign('code', $code);
        }

        //初始化数据
        switch ($code) {
            case 'USER_GRADE':
                $userGradeModel = new UserGrade();
                $gradeList      = $userGradeModel->select();
                $this->assign('gradeList', $gradeList);
                break;
        }
        return [
            'status' => true,
            'data'   => $this->fetch('index/condition/' . $code),
            'msg'    => ''
        ];
    }

    /**
     * 升级条件设置
     */
    public function conditionList()
    {
        $conditionModel = new DistributionCondition();
        if (!input('?param.id')) {
            return error_code(10003);
        }
        $where['grade_id'] = input('param.id');
        return $conditionModel->tableData($where);
    }

    /**
     * 添加条件结果
     */
    public function resultAdd()
    {
        $this->view->engine->layout(false);
        $resultModel = new DistributionResult();
        $this->assign('code', $resultModel->code);
        return [
            'status' => true,
            'data'   => $this->fetch('result_add'),
            'msg'    => ''
        ];

    }

    public function resultEdit()
    {
        $this->view->engine->layout(false);

        if (!(input('?param.result_code') && input('?param.grade_id')) && !input('?param.id')) {
            return error_code(15003);
        }

        $resultModel = new DistributionResult();

        if (Request::isPOST()) {
            $data = input('param.');
            return $resultModel->addData($data);
        }

        //如果是修改，就取数据，否则就是新增，直接渲染模板
        if (input('?param.id')) {
            $info = $resultModel->getInfo(input('param.id'));
            if (!$info) {
                return error_code(15004);
            }
            $code = $info['code'];
            $this->assign($info->toArray());
        } else {
            $code = input('param.result_code');
            $this->assign('grade_id', input('param.grade_id/d'));
            $this->assign('code', $code);
        }


        return [
            'status' => true,
            'data'   => $this->fetch('index/result/' . $code),
            'msg'    => ''
        ];
    }

    //佣金设置列表
    public function resultList()
    {
        $resultModel = new DistributionResult();
        if (!input('?param.id')) {
            return error_code(10003);
        }

        //校验是否有此权限
        $userGradeModel = new UserGrade();
        $pwhere['id']   = input('param.id');
        $info           = $userGradeModel->where($pwhere)->find();
        if (!$info) {
            return error_code(10002);
        }

        $where['grade_id'] = input('param.id');
        return $resultModel->tableData($where);
    }

    //升级条件删除
    public function conditionDel()
    {
        //校验是否有此权限
        $userGradeModel = new UserGrade();
        $pwhere['id']   = input('param.grade_id');
        $info           = $userGradeModel->where($pwhere)->find();
        if (!$info) {
            return error_code(10002);
        }

        $conditionModel = new DistributionCondition();
        return $conditionModel->toDel(input('param.id'));
    }

    //佣金设置删除
    public function resultDel()
    {
        //校验是否有此权限
        $userGradeModel = new UserGrade();
        $pwhere['id']   = input('param.grade_id');
        $info           = $userGradeModel->where($pwhere)->find();
        if (!$info) {
            return error_code(10002);
        }

        $resultModel = new DistributionResult();
        return $resultModel->toDel(input('param.id'));
    }

    /**
     * 分销用户
     */
    public function distribution()
    {
        $distributionModel = new Distribution();
        if (Request::isAjax()) {
            return $distributionModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /***
     * 分销订单
     */
    public function order()
    {
        $distributionOrderModel = new DistributionOrder();
        if (Request::isAjax()) {
            return $distributionOrderModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    /**
     * 删除分销商
     * @return array
     */
    public function delDistribution()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '关键参数丢失'
        ];
        $id     = input('id/d', '0');
        if (!$id) {
            return $result;
        }
        $distributionModel = new Distribution();
        if ($distributionModel::destroy($id)) {
            $result['msg']    = '删除成功';
            $result['status'] = true;
        }
        return $result;
    }

    /**
     * 编辑分销商
     * @return array
     */
    public function editDistribution()
    {
        $this->view->engine->layout(false);
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '关键参数丢失'
        ];
        $id     = input('id/d', '0');
        if (!$id) {
            return $result;
        }
        $distributionModel = new Distribution();
        $data              = $distributionModel->get($id);
        $disGradeModel     = new DistributionGrade();
        $grade             = $disGradeModel->getGradeList();
        $this->assign('data', $data);
        $this->assign('grade', $grade);
        return [
            'status' => true,
            'data'   => $this->fetch('edit_distribution'),
            'msg'    => ''
        ];
    }

    /**
     * 保存分销商
     * @return array
     */
    public function doEditDistribution()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '关键参数丢失'
        ];
        $id     = input('id/d', '0');
        if (!$id) {
            return $result;
        }
        $data                = input('param.');
        $distributionModel   = new Distribution();
        $data['verify_time'] = time();
        return $distributionModel->updateInfo($data, $id);
    }

    /**
     * 取消订单
     * @return array
     */
    public function cancleOrder()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '关键参数丢失'
        ];
        $id     = input('id/d', '0');
        if (!$id) {
            return $result;
        }
        $distributionOrderModel = new DistributionOrder();
        return $distributionOrderModel->cancleOrder($id);
    }
}