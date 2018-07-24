<?php

namespace app\common\model;

use think\Db;

class ManageRole extends Common
{

    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';


    protected function tableWhere($post)
    {
        $where = [];


        if(isset($post['name']) && $post['name'] != ""){
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = 'utime desc';
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k => $v) {
            if($v['utime']) {
                $list[$k]['utime'] = getTime($v['utime']);
            }

        }
        return $list;
    }

    public function toDel($id)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $where['id'] = $id;

        $mrorModel = new ManageRoleOperationRel();

        Db::startTrans();
        try {
            $this->where($where)->delete();
            $mrorModel->where(['manage_role_id'=>$id])->delete();
            Db::commit();
            $result['status'] = true;
            $result['msg'] = '删除成功';
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * 取角色的操作权限数组
     * @param $seller_id
     * @param $id
     */
    public function getRoleOperation($id)
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];

        $where['id'] = $id;
        $sellerRoleInfo = $this->where($where)->find();
        if(!$sellerRoleInfo){
            return error_code(11071);
        }
        $mrorModel = new ManageRoleOperationRel();
        $permList = $mrorModel->where(['manage_role_id'=>$id])->select();

        if(!$permList->isEmpty()){
            $nodeList = array_column($permList->toArray(),'manage_role_id','operation_id');
        }else{
            $nodeList = [];
        }

        $operationModel = new Operation();
        $result['data'] = $operationModel->menuTree($operationModel::MENU_MANAGE,$nodeList);

        return $result;
    }



}