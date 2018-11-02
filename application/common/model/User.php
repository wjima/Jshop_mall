<?php
namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class User extends Common
{
    use SoftDelete;
    protected $deleteTime = 'isdel';

    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';


    const STATUS_NORMAL = 1;        //用户状态 正常
    const STATUS_DISABLE = 2;       //用户状态 停用

    const SEX_BOY = 1;
    const SEX_GIRL = 2;
    const SEX_OTHER = 3;

    //protected $resultSetType = 'collection';

    protected $rule = [
        //'username' => 'length:6,20|alphaDash',
        'mobile' => ['regex' => '^1[3|4|5|7|8][0-9]\d{4,8}$'],
        'sex' => 'in:1,2,3',
        'nickname' => 'length:2,50',
        'password' => 'length:6,20',
    ];
    protected $msg = [
        //'username.length' => '用户名长度6~20位',
        //'username.alphaDash' => '用户名只能是字母、数字或下划线组成',
        'mobile' => '请输入一个合法的手机号码',
        'sex' => '请选择合法的性别',
        'nickname' => '昵称长度为2-50个字符',
        'password' => '密码长度6-20位',
    ];

    /**
     * 注册添加用户
     * @param array $data 新建用户的数据数组
     * @param int $loginType 登陆类型，1网页登陆，存session，2接口登陆，返回token
     *
     */
    public function toAdd($data, $loginType=1)
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
        if(!$smsModel->check($data['mobile'], $data['code'], 'reg')){
            $result['msg'] = '短信验证码错误';
            return $result;
        }
        $data['ctime'] = time();
        $data['password'] = $this->enPassword($data['password'], $data['ctime']);

        if(!isset($data['avatar'])){

            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $data['avatar'] =$http_type . $_SERVER['HTTP_HOST'].config('jshop.default_image');
        }
        if(!isset($data['nickname'])){
            $data['nickname'] = format_mobile($data['mobile']);
        }

        //保存推荐人
        if(isset($data['pid'])){
            $pinfo = $this->where(['id'=>$data['pid']])->find();
            if(!$pinfo){
                error_code(10014);
            }
        }


        //插入数据库
        $this->data($data)->allowField(true)->save();

        return $this->setSession($this ,$loginType);
    }


    /**
     *第三方添加用户，或者总管理员添加用户，不需要校验等一系列的东西，直接创建账户，所以在外面就要先校验好数据
     * @param $data
     */
    public function thirdAdd($data)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        if(!isset($data['avatar'])){
            $data['avatar'] = config('jshop.default_image');
        }
        if(isset($data['pid'])){
            $pinfo = $this->where(['id'=>$data['pid']])->find();
            if(!$pinfo){
                error_code(10014);
            }
        }

        $data['ctime'] = time();
        $result['data'] = $this->insertGetId($data);
        $result['status'] = true;
        return $result;
    }
    /**
     * 用户登陆
     * @param array $data 用户登陆信息
     * @param int   $loginType 1就是默认的，存session，2就是返回user_token
     * @param int   $platform 平台id，主要和session有关系 1就是默认的平台，，2就是微信小程序平台，当需要放回user_token的时候，会用到此字段
     *
     */
    public function toLogin($data, $loginType=1,$platform=1)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );

        if(!isset($data['mobile']) || !isset($data['password'])) {
            $result['msg'] = '请输入手机号码或者密码';
            return $result;
        }
        //校验验证码
        if(session('?login_fail_num')){
            if(session('login_fail_num') >= config('jshop.login_fail_num')){
                if(!isset($data['captcha']) || $data['captcha'] == ''){
                    return error_code(10013);
                }
                if(!captcha_check($data['captcha'])){
                    return error_code(10012);
                };
            }
        }

        $userInfo = $this->where(array('username'=>$data['mobile']))->whereOr(array('mobile'=>$data['mobile']))->find();
        if(!$userInfo){
            $result['msg'] = '没有找到此账号';
            return $result;
        }

        //判断账号状态
        if($userInfo->status != self::STATUS_NORMAL) {
            $result['msg'] = '此账号已停用';
            return $result;
        }
        //判断是否是用户名登陆
        $userInfo = $this->where(array('username|mobile'=>$data['mobile'],'password'=>$this->enPassword($data['password'], $userInfo->ctime)))->find();
        if($userInfo){
            $result = $this->setSession($userInfo,$loginType,$platform);            //根据登陆类型，去存session，或者是返回user_token
        }else{
            //写失败次数到session里
            if(session('?login_fail_num')){
               session('login_fail_num',session('login_fail_num')+1);
            }else{
                session('login_fail_num',1);
            }
            $result['msg'] = '密码错误，请重试';
        }

        return $result;


    }

    /**
     * 手机短信验证码登陆
     * @param $data
     * @param int $loginType
     * @return array
     */
    public function smsLogin($data, $loginType=1,$platform=1)
    {
        $result = array(
            'status' => false,
            'data' => '',
            'msg' => ''
        );
        if(!isset($data['mobile'])) {
            $result['msg'] = '请输入手机号码';
            return $result;
        }
        if(!isset($data['code'])) {
            $result['msg'] = '请输入验证码';
            return $result;
        }

        //判断是否是用户名登陆
        $smsModel = new Sms();
        if(!$smsModel->check($data['mobile'], $data['code'], 'login')){
            $result['msg'] = '短信验证码错误';
            return $result;
        }

        $userInfo = $this->where(array('mobile'=>$data['mobile']))->find();
        if(!$userInfo){
            //没有此用户，创建此用户
            $userData['mobile'] = $data['mobile'];
            if(isset($data['pid'])){
                $userData['pid'] = $data['pid'];
            }
            $re = $this->thirdAdd($userData);
            if(!$re['status']){
                return $re;
            }
            $user_id = $re['data'];
            $userInfo = $this->where(array('id'=>$user_id))->find();
        }else{
            //判断账号状态
            if($userInfo->status != self::STATUS_NORMAL) {
                $result['msg'] = '此账号已停用';
                return $result;
            }
        }

        $result = $this->setSession($userInfo ,$loginType,$platform);            //根据登陆类型，去存session，或者是返回user_token


        return $result;


    }

    /**
     * 登陆注册的时候，发送短信验证码
     */
    public function sms($mobile, $code)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => '成功'
        ];

        $userInfo = $this->where(array('mobile'=>$mobile))->find();
        if($code == 'reg') {
            //注册
            if ($userInfo) {
                $result['msg'] = '此账号已经注册过，请直接登陆';
                return $result;
            }

            //判断账号状态
//            if($userInfo->status != self::STATUS_NORMAL) {
//                $result['msg'] = '此账号已停用';
//                return $result;
//            }
        }elseif($code == 'login'){
            //登陆
        } elseif ($code === 'veri') {
            // 找回密码
        }else{
            //其他业务逻辑
            $result['msg'] = '无此业务类型';
            return $result;
        }

        //没问题了，就去发送短信验证码
        $smsModel = new Sms();
        return $smsModel->send($mobile, $code,[]);
    }

    /**
     *设置用户登录信息或者更新用户登录信息
     * User:tianyu
     * @param $userInfo
     * @param $data
     * @param $loginType            登陆类型1是存session，主要是商户端的登陆和网页版本的登陆,2就是token
     * @param int $platform         1就是普通的登陆，主要是vue登陆，2就是小程序登陆，写这个是为了保证h5端和小程序端可以同时保持登陆状态
     * @param int $type         1的话就是登录,2的话就是更新
     * @return array
     */
    public function setSession($userInfo ,$loginType,$platform=1,$type=1)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        switch ($loginType)
        {
            case 1:
                session('user',$userInfo->toArray());
                $result['status'] = true;
                break;
            case 2:
                $userTokenModel = new UserToken();
                $result = $userTokenModel->setToken($userInfo['id'],$platform);
                break;
        }

        if ($type == 1)
        {
            //$userLogModel = new UserLog();        //添加登录日志
            //$userLogModel->setLog($userInfo['id'],$userLogModel::USER_LOGIN);
        }

        return $result;

    }

    public function editInfo($id,$sex='',$birthday='',$nickname='',$avatar='')
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $data = [];

        if($sex != ''){
            $data['sex'] = $sex;
        }
        if($birthday != ''){
            $data['birthday'] = $birthday;
        }
        if($nickname != ''){
            $data['nickname'] = $nickname;
        }
        if ($avatar != '' ){
            $data['avatar'] = $avatar;
        }
        $re = $this->save($data,['id'=>$id]);
        if($re){
            //$userLogModel = new UserLog();
            //$userLogModel->setLog($id,$userLogModel::USER_EDIT);
            $result['status'] = true;
            $result['msg'] = '保存成功';
            return $result;
        }else{
            return error_code(10005);
        }

    }



    /**
     * 密码加密方法
     * @param string $pw 要加密的字符串
     * @return string
     */
    private function enPassword($password,$ctime){

        return md5(md5($password).$ctime);
    }

    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['sex']) && $post['sex'] != ""){
            $where[] = ['sex', 'eq', $post['sex']];
        }
        if(isset($post['id']) && $post['id'] != ""){
            $where[] = ['id', 'eq', $post['id']];
        }
        if(isset($post['username']) && $post['username'] != ""){
            $where[] = ['username', 'like', '%'.$post['username'].'%'];
        }
        if(isset($post['mobile']) && $post['mobile'] != ""){
            $where[] = ['mobile', 'eq', $post['mobile']];
        }
        if(isset($post['birthday']) && $post['birthday'] != ""){
            $where[] = ['birthday', 'eq', $post['birthday']];
        }
        if(isset($post['nickname']) && $post['nickname'] != ""){
            $where[] = ['nickname', 'like', '%'.$post['nickname'].'%'];
        }
        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = [];
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
            if($v['sex']) {
                $list[$k]['sex'] = config('params.user')['sex'][$v['sex']];
            }
            if($v['status']) {
                $list[$k]['status'] = config('params.user')['status'][$v['status']];
            }
        }
        return $list;
    }


    public function changeAvatar($id,$image_url)
    {
        $data['avatar'] = $image_url;
        $where['id'] = $id;
        if($this->save($data,$where)){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 获取用户的信息
     * @return array|null|\PDOStatement|string|\think\Model
     */
    public function getUserInfo($user_id)
    {
        $data = $this->where('id',$user_id)->find();
        if($data){
            $data['status'] = config('params.user')['status'][$data['status']];
            return $data;
        }else{
            return "";
        }

    }

    /**
     * 更新密码验证/找回密码验证
     * @param $data
     * @return array
     */
    public function checkCode($data)
    {
        $result = ['status'=>false,'msg'=>'','data'=>''];
        //修改密码验证原密码
        if ( isset($data['password']) && !empty($data['password']))
        {
            $user = $this->getUserInfo($data['user_id']);
            if ($user['password'] !== $this->enPassword($data['password'],$user['ctime']) )
            {
                $result['status'] = false;
                $result['msg'] = '原密码不正确!';
                return $result;
            }
        }

        if ( strval($data['newPwd']) !== strval($data['rePwd']) )
        {
            $result['msg'] = '两次密码不一致,请重新输入';
            return $result;
        }

        if ( strlen($data['newPwd']) < 6 )
        {
            $result['msg'] = '密码不能小于6位数';
            return $result;
        }

        //找回密码验证手机验证码
        if ( isset($data['code']) && !empty($data['code']))
        {
            $smsModel = new Sms();
            if ( !$smsModel->check($data['mobile'],$data['code'],'veri') )
            {
                $result['msg'] = '手机验证码错误!';
                return $result;
            }
        }

        return $this->editPwd($data['user_id'],$data['newPwd']);

    }


    /**
     *  修改密码
     * @param $user_id
     * @param $pwd
     * @return array
     */
    private function editPwd($user_id,$newPwd)
    {
        $result = [
            'status' => true,
            'msg' => '',
            'data' => ''
        ];

        $res_pwd = $this->save([
            'password'=>$this->enPassword($newPwd,$this->where('id',$user_id)->value('ctime'))
        ],['id'=>$user_id]);

        if ( !$res_pwd )
        {
            $result['status'] = false;
            $result['msg'] = '修改失败请重试!';
            return $result;
        }

        $result['msg'] = '密码修改成功!';
        return $result;
    }


    /**
     *
     *  获取用户的推荐列表
     * @param $user_id
     * @param $page
     * @param $limit
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function recommendList($user_id, $page=1, $limit=10)
    {
        $data = $this
            ->field('nickname, avatar, mobile, ctime')
            ->where('pid', $user_id)
            ->page($page, $limit)
            ->select();
        $count = $this
            ->field('nickname, avatar, mobile, ctime')
            ->where('pid', $user_id)
            ->count();
        if (!$data->isEmpty())
        {
            foreach ($data as $v) {
                $v['ctime'] = getTime($v['ctime']);
            }
            $result['data'] = $data;
        }
        return $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => $data,
            'total' => ceil($count/$limit)
        ];
    }


    /**
     * 获取用户的积分
     * @param $user_id
     * @param int $order_money
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserPoint($user_id, $order_money = 0)
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => 0,
            'available_point' => 0,
            'point_rmb' => 0,
            'switch' => 1
        ];


        $settingModel = new Setting();
        $switch = $settingModel->getValue('point_switch');
        if($switch == 2)
        {
            $return['status'] = true;
            $return['switch'] = 2;
            return $return;
        }

        $where[] = ['id', 'eq', $user_id];
        $data = $this->field('point')->where($where)->find();
        if($data !== false)
        {
            if($order_money != 0)
            {
                //计算可用积分
                $settingModel = new Setting();
                $orders_point_proportion = $settingModel->getValue('orders_point_proportion'); //订单积分使用比例
                $max_point_deducted_money = $order_money*($orders_point_proportion/100); //最大积分抵扣的钱
                $point_discounted_proportion = $settingModel->getValue('point_discounted_proportion'); //积分兑换比例
                $needs_point = $max_point_deducted_money*$point_discounted_proportion;
                $return['available_point'] = $needs_point>$data['point']?$data['point']:$needs_point;
                $return['point_rmb'] = $max_point_deducted_money;
            }

            $return['msg'] = '获取成功';
            $return['data'] = $data['point'];
            $return['status'] = true;
        }

        return $return;
    }
}