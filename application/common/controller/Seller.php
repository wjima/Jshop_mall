<?php

/**
 * 卖家后台的控制器基类，用于权限判断和一些统一的后台操作
 *
 * @author sin
 *
 */

namespace app\common\controller;

use app\common\model\SellerManage;
use app\common\model\SellerRoleOperationRel;
use think\Container;
use app\common\model\Operation;
use Request;


class Seller extends Base
{
    protected $sellerId = 0;
    protected function initialize()
    {
        parent::initialize();
        //没有登陆，请先登录
        if (!session('?user')) {
            cookie('redirect_url', Container::get('request')->url(), 3600);
            $this->redirect('b2c/common/login');
        }
        $this->userId = session('user')['id'];

        if (!session('?seller')) {
            //判断用户是否是卖家，并且仅有一个店铺，如果有多个店铺的话，让用户选择登陆哪个店铺
            $sellerModel = new \app\common\model\Seller();
            $re = $sellerModel->login($this->userId);
            if(!$re['status']){
                //登陆失败，做特殊处理

                //没有店铺，需要先注册或者绑定店铺
                if($re['data'] == 11086){
                    $this->redirect('seller/common/reg');
                }

                //如果多个店铺，就去选择登陆哪一个店铺
                if($re['data'] == 11087){
                    $this->redirect('seller/common/choose');
                }

                //店铺状态不对，没有审核通过或者是到期了
                if($re['data'] == 11501){
                    $this->error($re['msg'],url('seller/common/reg'));
                }

                $this->error($re['msg']);

            }
        }
        $this->sellerId = session('seller')['id'];

        $cont_name = request()->controller();
        $act_name = request()->action();

        $operationModel = new Operation();
        //判断当前是否有权限操作
        $srorModel = new SellerRoleOperationRel();
        $permRe = $srorModel->checkPerm($this->sellerId,$this->userId,$operationModel::MENU_SELLER,$cont_name,$act_name);
        if(!$permRe['status']){
            if(Request::isAjax()){
                $err = [
                    'status' => false,
                    'data' => '',
                    'msg' => $permRe['msg']
                ];
                echo json_encode($err);
                die();
                //return $permRe;
            }else{
                $this->error($permRe['msg']);
            }

        }

        //取菜单导航信息
        //$this->assign('nav', $operationModel->nav($mod_name,$cont_name ,$act_name ));
        $this->assign('nav', $operationModel->nav($operationModel::MENU_SELLER,$cont_name ,$act_name ));
        $this->assign('menu',$operationModel->sellerMenu($this->sellerId,$this->userId, $cont_name, $act_name));


        $jshopHost = Container::get('request')->domain();
        $this->assign('jshopHost',$jshopHost);
    }
}
