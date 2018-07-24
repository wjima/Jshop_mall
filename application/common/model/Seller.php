<?php

namespace app\common\model;

use think\Db;
use think\Model;
use think\model\concern\SoftDelete;
use think\Validate;

class Seller extends Common
{
    use SoftDelete;
    protected $deleteTime   = 'isdel';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';


    const STATUS_NORMAL = 1;        //状态 正常
    const STATUS_WAIT_AUDIT = 2;       //状态 已提交，等待审核
    const STATUS_AUDIT_FAIL = 3;        //状态 审核失败
    const STATUS_DEFAULT = 4;           //默认状态

    const STORE_TYPE_SHARE = 1;     //共享店铺类型
    const STORE_TYPE_ALONE = 2;     //独立店铺

    protected $rule = [
        'area_id' => 'require',
        'seller_name' => 'require',
        'address' => 'require'
    ];
    protected $msg = [
        'seller_name.require' => '请输入店铺名称',
        'area_id.require' => '请选择省市区',
        'address.require' => '请输入收货地址'
    ];

    /**
     * 商户登陆
     * @param $user_id          前台登陆的用户的id
     * @param $data             登陆传的参数
     * @param $loginType        登陆方式
     * @return array
     */
    public function login($user_id,$seller_id = false)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        //看看用户都绑定了哪些店铺
        $sellerManageModel = new SellerManage();
        $list = $sellerManageModel->getSellerManage($user_id);

        if(empty($list)){
            return error_code(11086);           //不是管理员并且没有店铺，需要去注册店铺或者绑定管理员
        }
        //如果没有指定店铺的话，就要看是否有唯一店铺了，如果没有唯一店铺，就报错，因为不知道登陆谁
        if(!$seller_id){
            //如果有大于1个店铺，就得报错啊
            if(count($list) > 1){
                return error_code(11087);
            }else{
                $seller_id = key($list);            //只有一条记录，返回第一条记录的键名，也就是仅有的一个店铺的seller_id
            }
        }else{
            //虽然指定了店铺，但是我得校验用户是否有这个店铺的权限,防止随便输入一个店铺就要给你登陆，会造成致命bug
            if(!array_key_exists($seller_id,$list)){
                return error_code(10000);
            }
        }

        $sellerInfo = $this->where(['id'=>$seller_id])->find();

        if(!$sellerInfo){
            return error_code(11500);
        }
        //判断店铺到期等信息
        //::todo
        if($sellerInfo['status'] != self::STATUS_NORMAL){
            return error_code(11501);
        }
        return $this->setSession($sellerInfo);
    }
    /**
     * 前台 商户添加
     * @param array $data 新建用户的数据数组
     *
     */
    public function toReg($data,$userInfo)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );

        //校验数据
        $validate = new Validate($this->rule, $this->msg);
        if(!$validate->check($data)){
            $result['msg'] = $validate->getError();
            return $result;
        }

        //校验短信验证码
        $smsModel = new Sms();
        if(!$smsModel->check($userInfo['mobile'], $data['code'], 'seller_reg')){
            $result['msg'] = '短信验证码错误';
            return $result;
        }

        $data['status'] = self::STATUS_WAIT_AUDIT;
        $data['token'] = $this->algorithm($data['user_id']);
        $data['expire_date'] = strtotime("+1 year");

        //判断是更新还是新增
        $sellerInfo = $this->where(array('user_id'=>$userInfo['id']))->find();
        if($sellerInfo){
            if($sellerInfo['status'] == self::STATUS_AUDIT_FAIL || $sellerInfo['status'] == self::STATUS_DEFAULT){
                $this->allowField(true)->save($data,['user_id'=>$userInfo['id']]);
            }else{
                $result['msg'] = '已提交状态，或者正常状态，不需要重复提交';
                return $result;
            }
        }else{
            $data['user_id'] = $userInfo['id'];
            $this->data($data)->allowField(true)->save();
        }

        $result['status'] = true;
        return $result;
    }

    //商户端商户登陆
    private function setSession($sellerInfo)
    {
        $result = [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];
        session('seller',$sellerInfo->toArray());

        return $result;
    }

    /**
     * 获取卖家token
     * @param int $seller_id
     * @return mixed
     */
    public function getSellerToken($seller_id=0)
    {
        $seller = $this->field('token')->where(['id'=>$seller_id])->find();
        return $seller['token'];
    }


    /**
     *  获取商户信息
     * User:tianyu
     * @param $seller_id
     * @return array|null|\PDOStatement|string|Model
     */
    public function sellerInfo($seller_id)
    {
        $data = $this->where('id',$seller_id)->find();
        $data['status'] = config('params.seller')['status'][$data['status']];
        $data['level'] = config('params.seller')['level'][$data['level']];
        $data['address'] = get_area($data['area_id']).$data['address'];
        $data['expire_date'] = $data['expire_date'] ? date('Y-m-d H:i:s',$data['expire_date']) : " ";

        return $data;

    }

    private function algorithm($user_id){
        return strtoupper(substr(md5(md5($user_id).rand(1,10000)),0,6));
    }

    /**
     * 获取店铺信息列表
     * @param array $token
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreInfo($token = [])
    {
        $return_data = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        if($token != '')
        {
            $where[] = ['token', 'in', $token];
        }
        $where[] = ['store_type', 'eq', 1];
        $where[] = ['status', 'eq', 1];
        $store_list = $this->field('id, token, area_id, address')
            ->where($where)
            ->select();
        foreach($store_list as &$v)
        {
            $v['address'] = get_area($v['area_id']).'-'.$v['address'];
            $v['logo'] = _sImage(getShopSetting($v['id'], 'shop_logo'));
            $v['name'] = getShopSetting($v['id'], 'shop_name');
        }

        if(!$store_list)
        {
            $return_data['status'] = false;
            $return_data['msg'] = '获取失败';
        }
        $return_data['data'] = $store_list;

        return $return_data;
    }

    /**
     * 获取商家列表
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllSellerList()
    {
        $where[] = ['status', 'eq', 1];
        $res = $this->field('id, seller_name')
            ->where($where)
            ->select();

        return $res;
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
        $list = $this::with('userInfo')->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
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
        if(isset($post['token']) && $post['token'] != ""){
            $where[] = ['token', 'eq', $post['token']];
        }
        if(isset($post['seller_name']) && $post['seller_name'] != ""){
            $where[] = ['seller_name', 'like', '%'.$post['seller_name'].'%'];
        }
        if(isset($post['mobile']) && $post['mobile'] != ""){
            if($user_id = get_user_id($post['mobile'])){
                $where[] = ['user_id', 'eq', $user_id];
            }else{
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }
        if(isset($post['level']) && $post['level'] != ""){
            $where[] = ['level', 'eq', $post['level']];
        }
        if(isset($post['store_type']) && $post['store_type'] != ""){
            $where[] = ['level', 'eq', $post['level']];
        }
        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = 'status='.self::STATUS_WAIT_AUDIT.' desc,utime desc';
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
        foreach($list as $k => $v) {
            if($v['status']) {
                $list[$k]['status'] = config('params.seller')['status'][$v['status']];
            }
            if($v['store_type']) {
                $list[$k]['store_type'] = config('params.seller')['store_type'][$v['store_type']];
            }
            if($v['level']) {
                $list[$k]['level'] = config('params.seller')['level'][$v['level']];
            }
            if($v['address']) {
                $str = "";
                if($v['area_id']){
                    $str = get_area($v['area_id']);
                }
                $list[$k]['address'] = $str.' '.$v['address'];
            }
            if($v['expire_date']) {
                $list[$k]['expire_date'] = getTime($v['expire_date']);
            }

            if($v['utime']) {
                $list[$k]['utime'] = getTime($v['utime']);
            }
            if($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }

        }
        return $list;
    }

    public function userInfo()
    {
        return $this->hasOne('User','id', 'user_id')->bind([
            'mobile'
        ]);
    }

}