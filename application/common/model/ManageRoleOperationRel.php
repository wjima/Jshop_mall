<?php
/**
 * 平台角色和权限关联关系表
 */

namespace app\common\model;

class ManageRoleOperationRel extends Common
{

    public function savePerm($role_id,$operations)
    {
        $operationModel = new Operation();
        $mrarModel = new ManageRoleAddonsRel();
        $addonsOperations = $operationModel->addonsOperations();
        $addonsOperations = array_column($addonsOperations,'name','id');


        //先删除此角色的所有权限
        $this->where(['manage_role_id' => $role_id])->delete();
        $mrarModel->where(['manage_role_id' => $role_id])->delete();

        $data = [];     //manage_role_operation_rel表
        $data1 = [];    //manage_role_addons_rel表


        foreach($operations as $k => $v){
            $row = [
                'manage_role_id' => $role_id
            ];
            //判断id是否在节点管理里，如果是，那么就加到manage_role_operation_rel表
            $info = $operationModel->where('id',$v['id'])->find();
            if($info){
                $row['operation_id'] = $v['id'];
                $data[] = $row;
            }else{
                if(isset($addonsOperations[$v['id']])){
                    $row['menu_id'] = $v['id'];
                    $data1[] = $row;
                }
            }
        }
        $this->saveAll($data);
        $mrarModel->saveAll($data1);

        return true;

    }

    /**
     * 取得角色的权限列表，如果role_id == 0 那么就是超级管理员，取所有的
     * @param int $role_id
     */
    public function getTree($role_id = 0)
    {
        $operationModel = new Operation();
        if($role_id == 0){
            $tree = $operationModel->menuTree($operationModel::MENU_SELLER);
        }else{
            $tree = $this->permTree($role_id,$operationModel::MENU_SELLER);
        }
        return $tree;
    }

    /**
     * 结构和operation里的此方法差不多，但是这里的数据是当前模型和操作表关联查出来的，是角色的仅有的操作递归查询出来
     * @param $pid
     * @param $defaultNode
     * @return array|\PDOStatement|string|\think\Collection
     */
    private function permTree($role_id,$pid,$defaultNode=""){
        $where['o.parent_menu_id'] = $pid;
        $where['o.perm_type'] = 1;              //主体权限
        $where['sror.seller_role_id'] = $role_id;

        $list = $this
            ->field('o.*')
            ->alias('sror')
            ->join(config('database.prefix').'operation o', 'o.id = sror.operation_id')
            ->where($where)
            ->select();
        foreach($list as $k => $v){
            $list[$k]['checkboxValue'] = $v['id'];
            //$list[$k]['checked'] = true;
            $list[$k]['children'] = $this->permTree($role_id,$v['id'],$defaultNode);
        }
        return $list;
    }

    /**
     * 判断管理员是否有当前的操作权限
     * @param $manage_id
     * @param $p_id     在operation表中的模块的id
     * @param $cont_name
     * @param $act_name
     */
    public function checkPerm($manage_id,$p_id,$cont_name,$act_name,$addon_name="")
    {
        $result = [
            'status'=> false,
            'data' => '',
            'msg' => ''
        ];

        $manageModel = new Manage();
        //如果是超级管理员，直接返回
        if($manage_id == $manageModel::TYPE_SUPER_ID){
            $result['status'] = true;
            return $result;
        }
        if($addon_name == ""){
            return $this->checkOperationPerm($manage_id,$p_id,$cont_name,$act_name);
        }else{
            return $this->checkAddonsPerm($manage_id,$cont_name,$act_name,$addon_name);
        }

    }

    /**
     * 实际校验管理员是否有当前操作（系统级节点）权限
     * @param $manage_id
     * @param $p_id
     * @param $cont_name
     * @param $act_name
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkOperationPerm($manage_id,$p_id,$cont_name,$act_name){
        $result = [
            'status'=> false,
            'data' => '',
            'msg' => ''
        ];

        $operationModel = new Operation();

        //如果是指定的模块，就直接返回，这些模块不做权限操作
        if($operationModel->checkNeedPerm($p_id,$cont_name,$act_name)){
            $result['status'] = true;
            return $result;
        }
        //到这里就说明是普通的管理员，那么就取所有的角色所对应的节点
        $list = $operationModel->manageOperation($manage_id);
        if(!$list){
            return error_code(10010);       //可怜的，一个权限都没有
        }
        $newList = array_column($list,'name','id');

        //查控制器所对应的操作记录
        $contWhere['type'] = 'c';
        $contWhere['parent_id'] = $p_id;
        $contWhere['code'] = $cont_name;
        $contOperation = $operationModel->where($contWhere)->find();
        if(!$contOperation){
            return error_code(11088);
        }
        //查询方法
        $actWhere['type'] = 'a';
        $actWhere['parent_id'] = $contOperation['id'];
        $actWhere['code'] = $act_name;
        $actOperation = $operationModel->where($actWhere)->find();
        if(!$actOperation){
            return error_code(11089);
        }
        //查看是否是是关联权限，如果是关联权限去查找对应的关联操作的权限
        if($actOperation['perm_type'] == $operationModel::PERM_TYPE_REL){
            $actOperation = $operationModel->where(['id'=>$actOperation['parent_menu_id']])->find();
            if(!$actOperation){
                return error_code(11090);
            }
        }

        //万事具备，只欠东风了
        if(isset($newList[$actOperation['id']])){
            $result['status'] = true;
            return $result;
        }else{
            return error_code(10010);
        }
    }

    /**
     * 实际校验管理员是否有当前操作（插件级节点）权限
     * @param $manage_id
     * @param $cont_name
     * @param $act_name
     * @param $addon_name
     */
    private function checkAddonsPerm($manage_id,$cont_name,$act_name,$addon_name){
        $result = [
            'status'=> false,
            'data' => '',
            'msg' => ''
        ];
        $addon_name = strtolower($addon_name);
        $cont_name = strtolower($cont_name);
        $act_name = strtolower($act_name);

        $operationModel = new Operation();
        $manage_list = $operationModel->getManageAddonsMenu($manage_id);        //当前用户插件节点
        $list = $operationModel->addonsOperations();//所有插件节点
        //先查找控制器
        $cont_id = "";

        foreach($list as $v){
            if(isset($v['addons']) && $this->checkAddonsName($v['addons'], $addon_name)){
                if($v['type'] == 'c' && $this->checkAddonsName($v['code'], $cont_name)){
                    $cont_id = $v['id'];
                    break;
                }
            }
        }
        if ($cont_id == "") {
            // $result['msg'] = "没有找到此控制器";
            return error_code(11102);
        }
        $cont_id = strtolower($cont_id);

        //再查找方法
        $act_info = [];
        foreach($list as $v){
            if(isset($v['addons']) && $this->checkAddonsName($v['addons'], $addon_name)){
                if($v['type'] == 'a' && strtolower($v['code']) == $act_name && strtolower($v['parent_id']) == $cont_id ){
                    $act_info = $v;
                    break;
                }
            }
        }
        if (count($act_info) == 0) {
            // $result['msg'] = "没有找到此方法";
            return error_code(11103);
        }
        //看当前权限是否是关联权限
        if($act_info['perm_type'] == 3){
            foreach($list as $v){
                if(isset($v['addons']) && $this->checkAddonsName($v['addons'], $addon_name)){
                    if($v['id'] == $act_info['parent_menu_id'] ){
                        $act_info = $v;
                        break;
                    }
                }
            }
        }
        if ($act_info['perm_type'] == 3) {
            $result['msg'] = jshop_m_l(11104);        //可能没有找到所关联的节点，也可能关联节点也是关联关系，不做详细判断了
            return $result;
        }
        //去manage_list里看是否有这个节点
        foreach($manage_list as $v){
            if(isset($v['addons']) && $this->checkAddonsName($v['addons'], $addon_name)){
                if($v['id'] == $act_info['id'] ){
                    $result['status'] = true;
                    return $result;
                    break;
                }
            }
        }
        // $result['msg'] = "没有权限";
        return error_code(11105);
    }

    /**
     * 比对插件名称和控制器名臣个是否是一个，会对code的驼峰写法转化成下划线写法,然后在进行比对
     * @param $code
     * @param $name
     * @return bool
     */
    private function checkAddonsName($code,$name){
        //把code从驼峰法转变成匈牙利法
        $code2 = "";
        for($i = 0;$i<strlen($code);$i++) {
            if (
                $i != 0 &&
                ord($code[$i]) >= 65 &&
                ord($code[$i]) <= 90
            ) {
                $code2 .= "_" . chr(ord($code[$i]) + 32);
            } else {
                $code2 .= $code[$i];
            }
        }
        if(strtolower($code2) == strtolower($name)){
            return true;
        }{
            return false;
        }
    }
}