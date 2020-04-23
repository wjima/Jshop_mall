<?php

namespace addons\WelfarePro\controller;

use addons\WelfarePro\model\WelfareproCoupon;
use addons\WelfarePro\model\WelfareproCouponLog;
use addons\WelfarePro\model\WelfareproHb;
use addons\WelfarePro\model\WelfareproHblog;
use addons\WelfarePro\model\WelfareproHbuser;
use app\common\model\Coupon;
use app\common\model\Promotion;
use myxland\addons\library\AddonController;
use Request;
use think\Container;
use think\facade\Session;
use app\common\model\Operation;
use app\common\model\ManageRoleOperationRel;



class Index extends AddonController
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

    public function index()
    {
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能

        $HbModel = new WelfareproHb();
        if (\think\facade\Request::isAjax())
        {
            return $HbModel->tableData(input('param.'));
        }
        return $this->fetch();
    }
    public function hbadd(){
        $return = [
            'status' => true,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $HbModel = new WelfareproHb();
        if(Request::isPost())
        {
            return $HbModel->toAdd(input('param.'));
        }
        else
        {
            //如果是编辑，取数据
            if(input("?param.id"))
            {
                $info = $HbModel->where(['id'=>input('param.id')])->find();
                if(!$info)
                {
                    return error_code(10000);
                }
                //取用户id
                $hbuserModel= new WelfareproHbuser();
                $hb_users = $hbuserModel->where('hb_id',$info['id'])->where('type',1)->select()->toArray();
                if($hb_users && $hb_users[0]['user_id'] == 0){
                    unset($hb_users[0]);
                }
                $info['user_id'] = implode(',',array_column($hb_users,'user_id'));
                $this->assign('info',$info);
            }
            $return['status'] = true;
            $return['msg'] = '成功';
            $return['data'] = $this->fetch();
            return $return;
        }
    }
    public function hbdel(){
        if(!input('?param.id'))
        {
            return error_code(10000);
        }
        $HbModel = new WelfareproHb();
        return $HbModel->toDel(input('param.id'));
    }

    public function couponIndex()
    {
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能

        $HbModel = new WelfareproCoupon();
        if ($this->request->isAjax())
        {
            return $HbModel->getList(input('param.'));
        }
        return $this->fetch();
    }
    public function couponAdd(){
        $return = [
            'status' => true,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $HbModel = new WelfareproCoupon();
        if($this->request->isPost())
        {
            return $HbModel->toAdd(input('param.'));
        }
        else
        {
            //如果是编辑，取数据
            if(input("?param.id"))
            {
                $info = $HbModel->with(['user','coupon'])->where(['id'=>input('param.id')])->find();
                if(!$info)
                {
                    return error_code(10000);
                }

                if(empty($info['user'])){
                    $info['user_id'] =  '';
                }else{
                    $user_ids = $info->user->column('user_id');
                    $user_ids = implode(',',$user_ids);
                    $info['user_id'] = $user_ids;
                }
                $coupon_ids = $info->coupon->column('coupon_id');
                $info['coupon_ids'] = $coupon_ids;

                foreach ($info->coupon as $val){
                    $couponNums[$val['coupon_id']] = [
//                        'sendnum'=>$val['sendnum'],
                        'num'=>$val['num']
                    ];
                }
                $info['coupon_nums'] = $couponNums;
                unset($info->user);
                unset($info->coupon);
//                dump($info);die;
//                //取用户id
//                $hbuserModel= new WelfareproHbuser();
//                $hb_users = $hbuserModel->where('hb_id',$info['id'])->select()->toArray();
//                if($hb_users && $hb_users[0]['user_id'] == 0){
//                    unset($hb_users[0]);
//                }
//                $info['user_id'] = implode(',',array_column($hb_users,'user_id'));
                $this->assign('info',$info);

            }
            //优惠券列表
            $couponList = Promotion::where('type',Promotion::TYPE_COUPON)->field(['id','name','etime'])->select()->toArray();
//            foreach ($couponList as &$val){
//                $val['is_expired'] = time() - $val['etime'] > 0 ? 1 : 2;    //1 过期 2 未过期
//                $val['etime'] = date('Y-m-d H:i:s',$val['etime']);
//            }
            $this->assign('couponlist',$couponList);
            $return['status'] = true;
            $return['msg'] = '成功';
            $return['data'] = $this->fetch();
            return $return;
        }
    }
    public function couponDel(){
        if(!input('?param.id'))
        {
            return error_code(10000);
        }
        $HbModel = new WelfareproCoupon();
        return $HbModel->toDel(input('param.id'));
    }

    public function hblog(){
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能

        $HbModel = new WelfareproHblog();
        if (\think\facade\Request::isAjax())
        {
            return $HbModel->tableData(input('param.'));
        }
        return $this->fetch();
    }

    public function couponView(){
        $return = [
            'status' => true,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $id = input('id/d',0);  //批次ID
        if(empty($id)) {
            $return['msg'] = '缺少必要参数';
            $return['status'] = false;
            return $return;
        }
        if($this->request->isPost())
        {
            $param = input('post.');
            $model = new WelfareproCouponLog();
            return $model->getList($param);
        }
        $model = new WelfareproCoupon();
        $info = $model->where(['id'=>$id])->find();
        $this->assign('info',$info);
        $this->assign('id',$id);
        $return['msg'] = '成功';
        $return['data'] = $this->fetch();
        return $return;
    }
}