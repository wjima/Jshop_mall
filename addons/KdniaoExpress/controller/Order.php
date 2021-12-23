<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11/0011
 * Time: 下午 15:48
 */
namespace addons\KdniaoExpress\controller;

use addons\KdniaoExpress\KdniaoExpress;
use addons\KdniaoExpress\model\FaceSheet;
use app\common\model\Logistics;
use myxland\addons\library\AddonController;
use think\facade\Session;
use app\common\model\ManageRoleOperationRel;
use app\common\model\Operation;
use think\Container;

class Order extends AddonController
{
    protected function initialize()
    {
        parent::initialize();

        //想实现判断后台的登陆判断很简单，写如下代码即可
        Session::init([
            'prefix'         => 'manage',
            'type'           => '',
            'auto_start'     => true,
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
    }

    public function batchPrint()
    {

        $ids        = input('ids/s', '');
        $page       = input('page/d', 1);
        $logi_code  = input('logi_code/s', '');
        $list       = explode(",", $ids);
        $total_page = count($list);
        $this->assign('page', $page);
        $this->assign('total_page', $total_page);
        $this->assign('data', $ids);
        $this->assign('logi_code', $logi_code);
        return $this->fetch('batchprint');
    }

    /**
     * 翻页处理
     * @return array
     */
    public function nextPrint()
    {

        $return = [
            'msg'    => '操作成功',
            'status' => true,
            'data'   => [],
        ];

        $page       = input('page/d', 1);
        $total_page = input('total_page/d', '1');
        $ids        = input('ids/s', '');
        $logi_code  = input('logi_code/s', '');
        $list       = explode(",", $ids);
        $order_id   = $list[$page - 1];
        if (!$order_id) {
            $return['status'] = false;
            $return['msg']    = '关键参数丢失';
            return $return;
        }
        $orderModel              = new \app\common\model\Order();
        $order_info              = $orderModel->getOrderInfoByOrderID($order_id);
        $order_info['logi_code'] = $logi_code;

        $kdniaoExpress = new KdniaoExpress();
        $res           = $kdniaoExpress->createKdApiEOrder($order_info);
        if (!$res['status']) {
            return $res;
        }
        $percent                      = sprintf("%.2f", ($page / $total_page) * 100);
        $page                         = $page + 1;
        $return['data']['data']       = $ids;
        $return['data']['percent']    = $percent;
        $return['data']['page']       = $page;
        $return['data']['total_page'] = $total_page;
        return $return;
    }

    /**
     * 打印订单
     */
    public function printExpress()
    {
        $ids = input('ids/s', '');
        if (!$ids) {
            echo '关键参数错误，请重试';
            exit();
        }
        $kdniaoExpress = new KdniaoExpress();
        $kdniaoExpress->printExpress($ids);
    }

    /**
     * 电子面单列表
     */
    public function faceSheet(){
        if($this->request->isAjax()){
            $faceSheet = new FaceSheet();
            return $faceSheet->tableData(input());
        }
        return $this->fetch();
    }

    /**
     * 添加电子面单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function faceSheetAdd(){
        $return_data = [
            'status' => true,
            'msg'    => '成功',
            'data'   => ''
        ];
        $this->view->engine->layout(false);
        if($this->request->isPost())
        {
            $faceSheet = new FaceSheet();
            return $faceSheet->faceSheetSave(input());
        }
        $logisticsModel = new Logistics();
        $logiList = $logisticsModel->order('sort asc')->select();

        $this->assign('logiList', $logiList);

        $return_data['data'] = $this->fetch('facesheet_add');
        return $return_data;
    }

    /**
     * 编辑电子面单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function faceSheetEdit(){
        $return_data = [
            'status' => true,
            'msg'    => '成功',
            'data'   => ''
        ];
        $this->view->engine->layout(false);

        $id = input('id');
        $faceSheet = new FaceSheet();

        if($this->request->isPost())
        {
            return $faceSheet->faceSheetSave(input());
        }

        $info = $faceSheet->where(['id'=>$id])->find();
        if($info){
            $info['logi_name'] = get_logi_info($info['logi_code'], 'logi_name');
        }

        $logisticsModel = new Logistics();
        $logiList = $logisticsModel->order('sort asc')->select();

        $this->assign('info', $info);
        $this->assign('logiList', $logiList);

        $return_data['data'] = $this->fetch('facesheet_add');
        return $return_data;
    }


    /**
     * 删除电子面单
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function faceSheetDel()
    {
        $return_data = error_code(10023);
        $faceSheet = new FaceSheet();
        $id = input('post.id/d',0);
        if(!$id)
        {
            return $return_data;
        }
        if($faceSheet->where(['id'=>$id])->delete())
        {
            $return_data['msg'] = '删除成功';
            $return_data['status'] = true;
        }
        return $return_data;
    }
}