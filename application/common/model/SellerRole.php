<?php

namespace app\common\model;

use think\Db;

class SellerRole extends Common
{

    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';


    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['seller_id']) && $post['seller_id'] != ""){
            $where[] = ['seller_id', 'eq', $post['seller_id']];
        }

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

    public function toDel($seller_id,$id)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $where['seller_id'] = $seller_id;
        $where['id'] = $id;

        $srorModel = new SellerRoleOperationRel();

        Db::startTrans();
        try {
            $this->where($where)->delete();
            $srorModel->where(['seller_role_id'=>$id])->delete();
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
    public function getRoleOperation($seller_id,$id)
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];

        $where['seller_id'] = $seller_id;
        $where['id'] = $id;
        $sellerRoleInfo = $this->where($where)->find();
        if(!$sellerRoleInfo){
            return error_code(11071);
        }
        $srorModel = new SellerRoleOperationRel();
        $permList = $srorModel->where(['seller_role_id'=>$id])->select();

        if(!$permList->isEmpty()){
            $nodeList = array_column($permList->toArray(),'seller_role_id','operation_id');
        }else{
            $nodeList = [];
        }

        $operationModel = new Operation();
        $result['data'] = $operationModel->menuTree($operationModel::MENU_SELLER,$nodeList);

        return $result;
    }



}