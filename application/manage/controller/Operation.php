<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Operation as OperationModel;

use Request;


class Operation extends Manage
{
    public function index()
    {
        if(Request::isAjax()){
            $operationModel = new OperationModel();
            $data = input('param.');
            if(isset($data['parent_id']) && $data['parent_id'] != ""){
                //此处不需要做任何操作
            }else{
                $data['parent_id'] = $operationModel::MENU_MANAGE;
            }
            return $operationModel->tableData($data);
        }else{
            return $this->fetch('index');
        }
    }

    public function del()
    {
        if(!input('?param.id')){
            return error_code(10000);
        }
        $operationModel = new OperationModel();
        return $operationModel->toDel(input('param.id'));

    }
    //x新增和编辑
    public function add()
    {
        $this->view->engine->layout(false);
        $operationModel = new OperationModel();
        if(Request::isPost()){
            return $operationModel->toAdd(input('param.'));
        }else{
            //如果是编辑，取数据
            if(input("?param.id")){
                $info = $operationModel->where(['id'=>input('param.id')])->find();
                if(!$info){
                    return error_code(10000);
                }
                $this->assign('info',$info);
            }

            //取全树
            $list = $operationModel->where('type','neq','a')->order('sort asc')->select()->toArray();
            $tree = $operationModel->createTree($list,$operationModel::MENU_START,'parent_id');
            $this->assign('tree',$tree);

            //取菜单树
            $menuList = $operationModel->where('perm_type','lt',3)->order('sort asc')->select()->toArray();
            $menuTree = $operationModel->createTree($menuList,$operationModel::MENU_START,'parent_menu_id');
            $this->assign('menuTree',$menuTree);

            return $this->fetch('add');
        }
    }



}