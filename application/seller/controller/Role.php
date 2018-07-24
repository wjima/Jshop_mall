<?php
namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\Area;
use app\common\model\Operation;
use app\common\model\SellerRole;
use app\common\model\SellerRoleOperationRel;
use Request;


class Role extends Seller
{
    public function index()
    {
        if(Request::isAjax()){
            $sellerRoleModel = new SellerRole();
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;
            return $sellerRoleModel->tableData($data);
        }else{
            return $this->fetch('index');
        }
    }

    public function add()
    {
        $this->view->engine->layout(false);
        $sellerRoleModel = new SellerRole();
        if(Request::isPost()){
            if(!input('?param.name')){
                return error_code(11070);
            }

            $data['seller_id'] = $this->sellerId;
            $data['name'] = input('param.name');
            $sellerRoleModel->save($data);
            return [
                'status' => true,
                'data' => '',
                'msg' => '添加成功'
            ];


        }
        return $this->fetch('edit');
    }
    public function del()
    {
        if(!input('?param.id')){
            return error_code(10000);
        }

        $sellerRoleModel = new SellerRole();
        return $sellerRoleModel->toDel($this->sellerId,input('param.id'));

    }
    public function getOperation()
    {
        if(!input('?param.id')){
            return error_code(10000);
        }
        $this->view->engine->layout(false);

        $sellerRoleModel = new SellerRole();

        $re = $sellerRoleModel->getRoleOperation($this->sellerId,input('param.id/d'));
        if(!$re['status']){
            return $re;
        }

        $this->assign('data',json_encode($re['data']));
        $re['data'] = $this->fetch('getOperation');
        return $re;

    }
    public function savePerm(){
        $post = input('param.');

        if(!isset($post['id'])){
            return error_code(10000);
        }
        if(!isset($post['data'])){
            return error_code(10000);
        }
        //保存角色信息
        $sellerRoleModel = new SellerRole();
        $sellerRoleInfo = $sellerRoleModel->where(['seller_id'=>$this->sellerId,'id'=>$post['id']])->find();
        if(!$sellerRoleInfo){
            return error_code(11071);
        }
        $srorModel = new SellerRoleOperationRel();

        $srorModel->savePerm($post['id'],$post['data']);
        return [
            'status' => true,
            'data' => '',
            'msg' => '设置成功'
        ];
    }
}