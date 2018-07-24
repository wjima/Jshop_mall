<?php

namespace app\common\model;

class SellerManage extends Common
{
    const TYPE_SUPER = 'super';            //超级管理员，是店铺的创建人,否则的话，就传角色id，多个角色id用英文逗号分割

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere['sm.seller_id'] = $post['seller_id'];

        $list = $this
            ->field('u.*,group_concat(sr.name) as role_name')
            ->alias('sm')
            ->join('user u', 'u.id = sm.user_id')
            ->leftJoin('seller_role sr','sr.id = sm.seller_role_id')
            ->where($tableWhere)
            ->group("sm.user_id")
            ->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        $re['sql'] = $this->getLastSql();

        return $re;
    }

    /**
     * 商户根据用户手机号码添加管理员
     * @param $seller_id        商户id
     * @param $mobile           用户手机号码，要判断此手机号码是否是已经管理员了
     * @param $role_ids         角色数组
     */
    public function toAdd($seller_id,$mobile,$role_ids)
    {
        $re = $this->checkMobile($seller_id,$mobile);
        if(!$re['status']){
            return $re;
        }
        //清空所有的旧角色
        $this->where(['user_id'=>$re['data'],'seller_id'=>$seller_id])->delete();
        $data = [];
        foreach($role_ids as $k => $v){
            $row['user_id'] = $re['data'];
            $row['seller_id'] = $seller_id;
            $row['seller_role_id'] = $k;
            $data[] = $row;
        }
        //如果没有选择任何角色，那么创建一条没有角色的记录
        if(!$data){
            $row['user_id'] = $re['data'];
            $row['seller_id'] = $seller_id;
            $row['seller_role_id'] = "";
            $data[] = $row;
        }
        $this->saveAll($data);

        return [
            'status' => true,
            'data' => '',
            'msg' => '添加成功'
        ];

    }

    public function toEdit($seller_id,$user_id,$role_ids)
    {
        $re = $this->checkMobile($seller_id,'',$user_id,2);
        if(!$re['status']){
            return $re;
        }
        $sellerRoleModel = new SellerRole();
        //清空所有的旧角色
        $this->where(['user_id'=>$re['data'],'seller_id'=>$seller_id])->delete();
        $data = [];
        foreach($role_ids as $k => $v){
            //判断当前商户是否有此角色，防止乱插入
            $srInfo = $sellerRoleModel->where(['id'=>$k,'seller_id'=>$seller_id])->find();
            if(!$srInfo){
                return error_code(10000);
            }

            $row['user_id'] = $re['data'];
            $row['seller_id'] = $seller_id;
            $row['seller_role_id'] = $k;
            $data[] = $row;
        }
        //如果没有选择任何角色，那么创建一条没有角色的记录
        if(!$data){
            $row['user_id'] = $re['data'];
            $row['seller_id'] = $seller_id;
            $row['seller_role_id'] = "";
            $data[] = $row;
        }
        $this->saveAll($data);

        return [
            'status' => true,
            'data' => '',
            'msg' => '保存成功'
        ];

    }


    /**
     * 检查用户是否可以成为此商户的管理员
     * @param $seller_id            平台id
     * @param string $mobile        用户手机号码，此字段和下面的用户id两者二选一就可以了
     * @param string $user_id       用户id
     * @param int $type             类型，如果是1就是新增，不仅其他店铺不能有，本店铺也不能是管理员，如果是2就是修改，其他店铺不能有，本店铺可以有
     * @return array|\think\Config
     */
    public function checkMobile($seller_id,$mobile = '',$user_id='',$type=1)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];

        //检查是否有这个用户，并且是否是启用状态
        $userModel = new User();
        if($mobile == '' && $user_id == ''){
            return error_code(11083);
        }
        $where = [];
        if($mobile != ""){
            $where['mobile'] = $mobile;
        }
        if($user_id != ""){
            $where['id'] = $user_id;
        }


        $userInfo = $userModel->where($where)->find();
        if(!$userInfo){
            return error_code(11004);
        }
        if($userInfo['status'] == $userModel::STATUS_DISABLE){
            return error_code(11006);
        }

        //检查当前用户在当前店铺是否是管理员
        $sellerList = $this->getSellerManage($userInfo['id'],$seller_id);

        if(isset($sellerList[$seller_id])){
            if($sellerList[$seller_id] == self::TYPE_SUPER){
                return error_code(11085);       //此账号是超级管理员，不需要设置
            }
            if($type == 1){
                return error_code(11084);   //已经是店铺管理员了，请勿重复设置
            }
        }




//        if($sellerList){
//            return error_code(11082);       //绑定过账号的话，就不让他再次绑定了，从源头上控制一个手机号码只能绑定一个店铺 这个后期可以拿掉，目前先这样
//        }
        $result['data'] = $userInfo['id'];
        $result['status'] = true;

        return $result;


    }

    /**
     * 根据用户id去用户的所有的有管理权限的店铺列表
     * @param $user_id      用户id
     * @param int $seller_id        如果传店铺了，就是限定某一个店铺，否则就是0，全部店铺
     * @return array
     */
    public function getSellerManage($user_id,$seller_id = 0)
    {
        $result = [];
        $where['user_id'] = $user_id;
        if($seller_id != 0){
            $where['id'] = $seller_id;
        }
        //如果开店了，就是创始人，后面的类型是1，就是店铺创始人
        $sellerModel = new Seller();
        $sellerInfo = $sellerModel->where($where)->find();
        if($sellerInfo){
            $result[$sellerInfo['id']] = self::TYPE_SUPER;
        }

        $where1['user_id'] = $user_id;
        if($seller_id != 0){
            $where1['seller_id'] = $seller_id;
        }
        //判断其他店铺是否是管理员，如果有，后面的类型是2,就是其他店铺的管理员
        $list = $this
            ->field('seller_id,group_concat(seller_role_id) as role_ids')
            ->where($where1)
            ->group('seller_id')
            ->select();
        foreach($list as $k => $v){
            $result[$v['seller_id']] = $v['role_ids'];
        }
        return $result;

    }

}