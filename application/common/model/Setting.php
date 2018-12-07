<?php

namespace app\common\model;


class Setting extends Common
{
    public $skeys = [
        'shop_name' => [
            'name' => '店铺名称',
            'value' => '我的店铺'
        ],
        'shop_address' => [
            'name' => '店铺地址',
            'value' => '我的店铺地址'
        ],
        'shop_logo' => [
            'name' => '店铺logo',
            'value' => '',
        ],
        'shop_mobile' => [
            'name' => '店铺联系方式',
            'value' => ''
        ],
        'store_switch' => [
            'name' => '开启门店自提',
            'value' => '2'
        ],
        'cate_style' => [
            'name' => '分类样式',
            'value' => '3'
        ],
        'tocash_money_low'=>[
            'name'=>'最低提现金额',
            'value'=>'0'
        ],
        'order_cancel_time' => [
            'name' => '订单取消时间',
            'value' => '1'
        ],
        'order_complete_time' => [
            'name' => '订单完成时间',
            'value' => '30'
        ],
        'order_autoSign_time' => [
            'name' => '订单确认收货时间',
            'value' => '20'
        ],
        'order_autoEval_time' => [
            'name' => '订单自动评价时间',
            'value' => '30'
        ],
        'goods_stocks_warn' =>  [
            'name' => '库存警报数量',
            'value' => '10'
        ],
        'is_author'           =>  [         //此字段不显示到前台，在控制器中直接进行操作的，对商户不可见
            'name' => '是否授权',
            'value' => ''
        ],
        'reship_name' => [
            'name' => '退货联系人',
            'value' => ''
        ],
        'reship_mobile' => [
            'name' => '退货联系方式',
            'value' => ''
        ],
        'reship_area_id' => [
            'name' => '退货区域',
            'value' => ''
        ],
        'reship_address' => [
            'name' => '退货详细地址',
            'value' => ''
        ],
        'sign_point_type' => [
            'name' => '签到奖励类型',
            'value' => 2
        ],
        'sign_random_min' => [
            'name' => '随机奖励积分最小值',
            'value' => 1,
        ],
        'sign_random_max' => [
            'name' => '随机奖励积分最大值',
            'value' => 10
        ],
        'first_sign_point' => [
            'name' => '首次奖励积分',
            'value' => 1
        ],
        'continuity_sign_additional' => [
            'name' => '连续签到追加',
            'value' => 1
        ],
        'sign_most_point' => [
            'name' => '单日最大奖励',
            'value' => 10
        ],
        'point_switch' => [
            'name' => '开启积分功能',
            'value' => 1
        ],
        'point_discounted_proportion' => [
            'name' => '订单积分折现比例',
            'value' => 100
        ],
        'orders_point_proportion' => [
            'name' => '订单积分使用比例',
            'value' => 10
        ],
        'orders_reward_proportion' => [
            'name' => '订单积分奖励比例',
            'value' => 1
        ],

        'sign_appoint_date_status' => [
            'name' => '指定特殊日期状态',
            'value' => false
        ],
        'sign_appoint_date' => [
            'name' => '指定特殊日期',
            'value' => ''
        ],
        'sign_appoint_data_type' => [
            'name' => '指定日期奖励类型',
            'value' => 1
        ],
        'sign_appoint_date_rate' => [
            'name' => '指定日期倍率',
            'value' => 2
        ],
        'sign_appoint_date_additional' => [
            'name' => '指定日期追加',
            'value' => 10
        ],
        'wx_nick_name'=>[
            'name'=>'小程序名称',
            'value' => 'JSHOP',
        ],
        //小程序设置
        'wx_appid'=>[           //小程序id
            'name'=>'AppId',
            'value' => '',
        ],
        'wx_app_secret'=>[
            'name'=>'AppSecret',
            'value' => '',
        ],
        'wx_user_name'=>[
            'name'=>'原始Id',
            'value' => '',
        ],
        'wx_principal_name'=>[
            'name'=>'主体信息',
            'value' => '河南吉海网络科技有限公司',
        ],
        'wx_signature'=>[
            'name'=>'简介',
            'value' => 'Jshop小程序是一款标准B2C商城小程序',
        ],
        //小程序logo
        'wx_head_img'=>[
            'name'=>'Logo',
            'value'=>''
        ],
        'sms_user_id' => [
            'name' => '短信通道用户ID',
            'value' => ''
        ],
        'sms_account' => [
            'name' => '短信通道用户名',
            'value' => ''
        ],
        'sms_password' => [
            'name' => '短信通道用户密码',
            'value' => ''
        ],
        'sms_prefix' => [
            'name' => '短信前缀',
            'value' => 'Jshop'
        ],
        //公众号设置
        'wx_official_name'=>[
            'name'=>'公众号名称',
            'value' => '',
        ],
        'wx_official_id'=>[
            'name'=>'微信号',
            'value' => '',
        ],
        'wx_official_appid'=>[
            'name'=>'AppId',
            'value' => '',
        ],
        'wx_official_app_secret'=>[
            'name'=>'AppSecret',
            'value' => '',
        ],
        'wx_official_source_id'=>[
            'name'=>'公众号原始ID',
            'value' => '',
        ],
        'wx_official_token'=>[
            'name'=>'微信验证TOKEN',
            'value' => '',
        ],
        'wx_official_encodeaeskey'=>[
            'name'=>'EncodingAESKey',
            'value'=>''
        ],
        'wx_official_type'=>[
            'name'=>'公众号类型',
            'value'=>'service'
        ],
        //其他设置
        'qq_map_key'=>[
            'name'=>'腾讯地图key',
            'value'=>''
        ],
        'kuaidi100_customer'=>[
            'name'=>'公司编号',
            'value'=>''
        ],
        'kuaidi100_key'=>[
            'name'=>'授权key',
            'value'=>''
        ],
        'image_storage_type'=>[
            'name'=>'图片存储引擎',
            'value'=>'Local'
        ],
        'image_storage_params'=>[
            'name'=>'图片存储配置参数',
            'value'=>''
        ],
    ];


    //设置参数
    public function setValue($skey, $value)
    {

        $result = $this->check($skey, $value);
        if(!$result['status']){
            return $result;
        }
        if(is_array($value)){
            $value = json_encode($value);
        }
        $info = $this->where(array('skey'=>$skey))->find();
        if($info){

            $info->value = $value;
            $info->save();
        }else{
            $model = new $this;
            $model->save([
                'skey' => $skey,
                'value' => $value
            ]);
        }
        $result['status'] = true;
        return $result;

    }


    //取得参数
    public function getValue($skey)
    {
        $info = $this->where(array('skey' => $skey))->find();
        if($info){
            if(isjson( $info['value'])){
                $info['value'] = json_decode($info['value'],true);
            }
            return $info['value'];
        }else{
            if(isset($this->skeys[$skey]['value'])){
                return $this->skeys[$skey]['value'];
            }else{
                return "";
            }
        }
    }


    //参数校验
    public function check($skey, $value)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        if (!isset($this->skeys[$skey])) {
            return error_code(10008);
        }


        if($skey == 'shop_name'){
            if($value == ''){
                $result['msg'] = "店铺名称不能为空";
                return $result;
            }
        }

        if($skey == 'shop_mobile'){
            if($value != ''){
                $isMob="/^1[34578]{1}\d{9}$/";

                $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";

                if(!preg_match($isMob,$value) && !preg_match($isTel,$value))
                {
                    $result['msg'] = '联系方式号码格式错误';
                    return $result;
                }
            }
        }

        $result['status'] = true;
        return $result;
    }


    //取得全部参数
    public function getAll()
    {
        $list = $this->select();
        foreach($this->skeys as $k => $v){
            foreach($list as $info){
                if($info['skey'] == $k){
                    if(isjson( $info['value'])){
                        $info['value'] = json_decode($info['value'],true);
                    }
                    $this->skeys[$k]['value'] = $info['value'];
                    break;
                }
            }
        }
        return $this->skeys;
    }

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
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        $re['sql'] = $this->getLastSql();

        return $re;
    }



    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['skey']) && $post['skey'] != ""){
            $where[] = ['skey', 'eq', $post['skey']];
        }
        if(isset($post['value']) && $post['value'] != ""){
            $where[] = ['value', 'like', '%'.$post['value'].'%'];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = [];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k => $v){
            if(isset($this->skeys[$v['skey']])){
                $list[$k]['key_name'] = $this->skeys[$v['skey']]['name'];
            }else{
                $list[$k]['key_name'] = "";
            }


        }
        return $list;
    }

}