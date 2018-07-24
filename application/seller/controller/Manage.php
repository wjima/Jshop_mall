<?php

/**
 * 商户的管理员
 */

namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\Operation;
use app\common\model\SellerManage;
use app\common\model\SellerRole;
use app\common\model\SellerRoleOperationRel;
use app\common\model\User;
use Request;


class Manage extends Seller
{
    public function index()
    {
        if(Request::isAjax()){
            $sellerManageModel = new SellerManage();
            $data['seller_id'] = $this->sellerId;
            return $sellerManageModel->tableData($data);
        }else{
            return $this->fetch('index');
        }
    }

    public function add()
    {
        $this->view->engine->layout(false);
        $sellerManageModel = new SellerManage();
        $sellerRoleModel = new SellerRole();
        $sellerRoleList = $sellerRoleModel->where(['seller_id'=>$this->sellerId])->select();

        if(Request::isPost()){
            if(!input('?param.mobile')){
                return error_code(11080);
            }
            return $sellerManageModel->toAdd($this->sellerId,input('param.mobile'),input('param.role_id/a',[]));
        }
        $this->assign('roleList',$sellerRoleList);
        return $this->fetch('edit');
    }
    public function edit()
    {
        $this->view->engine->layout(false);

        if(!input('?param.id')){
            return error_code(10000);
        }

        $userModel = new User();
        $userInfo = $userModel->getUserInfo(input('param.id'));
        if(!$userInfo){
            return error_code(11004);
        }

        $sellerManageModel = new SellerManage();

        if(Request::isPost()){
            return $sellerManageModel->toEdit($this->sellerId,input('param.id'),input('param.role_id/a',[]));
        }

        $sellerRoleModel = new SellerRole();
        $sellerRoleList = $sellerRoleModel->where(['seller_id'=>$this->sellerId])->select();
        $smList = $sellerManageModel->where(['user_id'=>input('param.id'),'seller_id'=>$this->sellerId])->select();
        foreach($sellerRoleList as $k => $v){
            $checked = false;
            foreach($smList as $i => $j){
                if($j['seller_role_id'] == $v['id']){
                    $checked = true;
                    break;
                }
            }
            $sellerRoleList[$k]['checked'] = $checked;
        }
        $this->assign('roleList',$sellerRoleList);
        $this->assign('userInfo',$userInfo);
        return $this->fetch('edit');
    }
    public function del()
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if(!input('?param.id')){
            return error_code(10000);
        }
        $where['seller_id'] = $this->sellerId;
        $where['user_id'] = input('param.id');

        $sellerManageModel = new SellerManage();
        $re = $sellerManageModel->where($where)->delete();

        if($re){
            $result['status'] = true;
            $result['msg'] = '删除成功';
        }else{
            $result['msg'] = '删除失败，请重试';
        }


        return $result;

    }
//    public function getOperation()
//    {
//        $this->view->engine->layout(false);
//        $operationModel = new Operation();
//        $data = $operationModel->menuTree($operationModel::MENU_SELLER);
//
//        $this->assign('data',json_encode($data));
//
//        return $this->fetch();
//
//    }
//    public function savePerm(){
//        $post = input('param.');
//
//        if(!isset($post['id'])){
//            return error_code(10000);
//        }
//        if(!isset($post['data'])){
//            return error_code(10000);
//        }
//        //保存角色信息
//        $sellerRoleModel = new SellerRole();
//        $sellerRoleInfo = $sellerRoleModel->where(['seller_id'=>$this->sellerId,'id'=>$post['id']])->find();
//        if(!$sellerRoleInfo){
//            return error_code(11071);
//        }
//        $srorModel = new SellerRoleOperationRel();
//
//        $srorModel->savePerm($post['id'],$post['data']);
//        return [
//            'status' => true,
//            'data' => '',
//            'msg' => '设置成功'
//        ];
//    }
//
//    public function test()
//    {
//        $srorModel = new SellerRoleOperationRel();
//        dump($srorModel->getTree(10002));
//    }


}