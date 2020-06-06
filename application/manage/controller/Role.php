<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\ManageRole;
use app\common\model\ManageRoleOperationRel;
use Request;

/**
 * Class Role
 * @package app\Manage\controller
 */
class Role extends Manage
{
    /**
     * @return mixed
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $manageRoleModel = new ManageRole();
            $data = input('param.');
            return $manageRoleModel->tableData($data);
        }
        return $this->fetch('index');
    }


    /**
     * 添加
     * @return array|mixed
     */
    public function add()
    {
//        $result = [
//            'status' => false,
//            'msg' => error_code(10037,true),
//            'data' => ''
//        ];
        $this->view->engine->layout(false);
        $manageRoleModel = new ManageRole();
        if(Request::isPost())
        {
            if(!input('?param.name'))
            {
                return error_code(11070);
            }

            $data['name'] = input('param.name');
            $manageRoleModel->save($data);
            $result['status'] = true;
            $result['msg'] = '添加成功';
            return $result;
        }
        $result['status'] = true;
        $result['msg'] = '成功';
        $result['data'] = $this->fetch('edit');
        return $result;
    }


    /**
     * 删除
     * @return array|mixed
     */
    public function del()
    {
        if(!input('?param.id'))
        {
            return error_code(10000);
        }
        $ManageRoleModel = new ManageRole();
        return $ManageRoleModel->toDel(input('param.id'));
    }


    /**
     * @return array|mixed|\think\Config
     */
    public function getOperation()
    {
        $return_data = [
            'code' => 0,
            'msg'  => '获取成功',
            'data' => [],
        ];

        if (!input('?param.id'))
        {
            return error_code(10000);
        }
        $manageRoleModel = new ManageRole();
        $re = $manageRoleModel->getRoleOperation(input('param.id/d'),session('manage')['id']);
        if(!$re['status'])
        {
            return $re;
        }
        $return_data['data'] = $re['data'];
        return $return_data;
    }


    /**
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function savePerm()
    {
        $post = input('param.');
        if(!isset($post['id']))
        {
            return error_code(10000);
        }
        if(!isset($post['data']))
        {
            return error_code(11072);
        }
        //保存角色信息
        $manageRoleModel = new ManageRole();
        $manageRoleInfo = $manageRoleModel->where(['id'=>$post['id']])->find();
        if(!$manageRoleInfo)
        {
            return error_code(11071);
        }
        $mrorModel = new ManageRoleOperationRel();
        $mrorModel->savePerm($post['id'],$post['data']);
        return [
            'status' => true,
            'data' => '',
            'msg' => '设置成功'
        ];
    }
}