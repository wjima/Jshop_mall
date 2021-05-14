<?php

namespace app\common\model;

use org\QRcode;
use org\Wx;
use think\model\concern\SoftDelete;
use think\Validate;
use think\Db;

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
        'mobile'   => ['regex' => '^1[3|4|5|6|7|8|9][0-9]\d{4,8}$'],
        'sex'      => 'in:1,2,3',
        'nickname' => 'length:2,50',
        'password' => 'length:6,20',
        'p_mobile' => ['regex' => '^1[3|4|5|6|7|8|9][0-9]\d{4,8}$'],
    ];
    protected $msg = [
        //'username.length' => '用户名长度6~20位',
        //'username.alphaDash' => '用户名只能是字母、数字或下划线组成',
        'mobile'   => '请输入一个合法的手机号码',
        'sex'      => '请选择合法的性别',
        'nickname' => '昵称长度为2-50个字符',
        'password' => '密码长度6-20位',
        'p_mobile' => '邀请人栏请输入一个合法的手机号码',
    ];


    /**
     * 用户账户密码登陆
     * @param array $data 用户登陆信息
     * @param int $loginType 1就是默认的，存session，2就是返回user_token
     * @param int $platform 平台id，主要和session有关系 1就是默认的平台，，2就是微信小程序平台，当需要放回user_token的时候，会用到此字段
     *
     */
    public function toLogin($data, $loginType = 1, $platform = 1)
    {
        $result = array(
            'status' => false,
            'data'   => '',
            'msg'    => ''
        );

        if (!isset($data['mobile']) || !isset($data['password'])) {
            //            $result['msg'] = '请输入手机号码或者密码';
            return error_code(11031);
        }
        //校验验证码
        if (session('?login_fail_num')) {
            if (session('login_fail_num') >= config('jshop.login_fail_num')) {
                if (!isset($data['captcha']) || $data['captcha'] == '') {
                    return error_code(10013);
                }
                if (!captcha_check($data['captcha'])) {
                    return error_code(10012);
                };
            }
        }

        $userInfo = $this->where(array('username' => $data['mobile']))->whereOr(array('mobile' => $data['mobile']))->find();
        if (!$userInfo) {
            return error_code(11032);
        }


        //判断是否是用户名登陆
        $userInfo = $this->where(array('username|mobile' => $data['mobile'], 'password' => $this->enPassword($data['password'], $userInfo->ctime)))->find();
        if ($userInfo) {
            if ($userInfo['status'] == self::STATUS_NORMAL) {
                $result = $this->setSession($userInfo, $loginType, $platform);            //根据登陆类型，去存session，或者是返回user_token
            } else {
                return error_code(11022);
            }
        } else {
            //写失败次数到session里
            if (session('?login_fail_num')) {
                session('login_fail_num', session('login_fail_num') + 1);
            } else {
                session('login_fail_num', 1);
            }
            return error_code(11033);
        }

        return $result;
    }

    /**
     * 手机短信验证码登陆，同时兼有手机短信注册的功能，还有第三方账户绑定的功能
     * @param $data
     * @param int $loginType 登陆类型，1网页登陆，存session，2接口登陆，返回token
     * @param int $platform
     * @return array
     */
    public function smsLogin($data, $loginType = 1, $platform = 1)
    {
        if (!isset($data['mobile'])) {
            return error_code(11051);
        }
        if (!isset($data['code'])) {
            return error_code(10013);
        }

        //判断是否是用户名登陆
        $smsModel    = new Sms();
        $userWxModel = new UserWx();

        if (!$smsModel->check($data['mobile'], $data['code'], 'login')) {
            return error_code(11046);
        }

        $userInfo = $this->where(array('mobile' => $data['mobile']))->find();
        if (!$userInfo) {
            //没有此用户，创建此用户
            $userData['mobile'] = $data['mobile'];

            //判断是否是小程序里的微信登陆，如果是，就查出来记录，取他的头像和昵称
            if (isset($data['user_wx_id'])) {
                $user_wx_info = $userWxModel->where(['id' => $data['user_wx_id']])->find();
                if ($user_wx_info) {
                    if (!isset($data['avatar'])) {
                        $data['avatar'] = $user_wx_info['avatar'];
                    }
                    if (!isset($data['nickname'])) {
                        $data['nickname'] = $user_wx_info['nickname'];
                    }
                    //取性别
                    if (!isset($data['sex'])) {
                        $uData['sex'] = $user_wx_info['gender'] == 0 ? 3 : $user_wx_info['gender'];
                    }
                }
            }
            //如果没有头像和昵称，那么就取系统头像和昵称吧
            if (isset($data['avatar'])) {
                $userData['avatar'] = $data['avatar'];
            } else {
                $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
                $userData['avatar'] = _sImage('');
            }
            if (isset($data['nickname'])) {
                $userData['nickname'] = $data['nickname'];
            } else {
                $userData['nickname'] = format_mobile($data['mobile']);
            }
            if (isset($data['invitecode']) && $data['invitecode']) {
                $pid   = $this->getUserIdByShareCode($data['invitecode']);
                $pinfo = model('common/User')->where(['id' => $pid])->find();
                if ($pinfo) {
                    $userData['pid'] = $pid;
                } else {
                    //error_code(10014);
                    $userData['pid'] = 0;
                }
            }

            $userData['ctime'] = time();
            if (isset($data['password'])) {
                //判断密码是否符合要求
                if (!isset($data['password'][5]) || isset($data['password'][16])) {
                    return error_code(11009);
                }
                $userData['password'] = $this->enPassword($data['password'], $userData['ctime']);
            } else {
                $userData['password'] = "";
            }

            //取默认的用户等级
            $userGradeModel = new UserGrade();
            $userGradeInfo  = $userGradeModel->where('is_def', $userGradeModel::IS_DEF_YES)->find();
            if ($userGradeInfo) {
                $userData['grade'] = $userGradeInfo['id'];
            }


            $user_id = $this->insertGetId($userData);
            if (!$user_id) {
                return error_code(10019);
            }
            $userInfo = $this->where(array('id' => $user_id))->find();
            hook('newuserreg', $userInfo);
            hook("adminmessage",array('user_id'=>$user_id,"code"=>"user_register","params"=>$userInfo));
        } else {
            //如果有这个账号的话，判断一下是不是传密码了，如果传密码了，就是注册，这里就有问题，因为已经注册过
            if (isset($data['password'])) {
                return error_code(11019);
            }
        }
        //判断是否是小程序里的微信登陆，如果是，就给他绑定微信账号
        if (isset($data['user_wx_id'])) {
            $userWxModel->save(['user_id' => $userInfo['id']], ['id' => $data['user_wx_id']]);
        }

        if ($userInfo['status'] == self::STATUS_NORMAL) {
            $result = $this->setSession($userInfo, $loginType, $platform);            //根据登陆类型，去存session，或者是返回user_token
        } else {
            return error_code(11022);
        }

        return $result;
    }

    /**
     * 用户绑定新手机号码，当没有绑定手机号码，又必须绑定的时候，会报11027错误码，然后需要请求这个接口绑定手机号码，也可以做更改手机号码的接口
     * @param $user_id
     * @param $mobile
     * @param $code
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bindMobile($user_id, $mobile, $code)
    {
        $result = array(
            'status' => false,
            'data'   => '',
            'msg'    => ''
        );
        //判断是否是用户名登陆
        $smsModel    = new Sms();

        if (!$smsModel->check($mobile, $code, 'bind')) {
            return error_code(11046);
        }
        //校验手机号码是否已经绑定用户
        $info = $this->where('mobile', $mobile)->find();
        if ($info) {
            return error_code(11028);
        }

        $userInfo = $this->where('id', $user_id)->find();
        if ($userInfo) {
            $userInfo->mobile = $mobile;
            $userInfo->save();
            $result['status'] = true;
            return $result;
        } else {
            return error_code(11081);
        }
    }

    /**
     * 登陆注册的时候，发送短信验证码
     */
    public function sms($mobile, $code)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => '成功'
        ];

        $userInfo = $this->where(array('mobile' => $mobile))->find();
        if ($code == 'reg') {
            //注册，目前暂时没有走注册流程，注册和登陆是一样的接口，都是login，这里要尤其注意
            if ($userInfo) {
                return error_code(11047);
            }
            //判断账号状态
            //            if($userInfo->status != self::STATUS_NORMAL) {
            //                $result['msg'] = '此账号已停用';
            //                return $result;
            //            }
        } elseif ($code == 'login') {
            //登陆
        } elseif ($code === 'veri') {
            // 找回密码
        } elseif ($code === 'bind') {
            // 账户绑定手机号码
        } else {
            //其他业务逻辑
            return error_code(10080);
        }

        //没问题了，就去发送短信验证码
        $smsModel = new Sms();
        return $smsModel->send($mobile, $code, []);
    }

    /**
     *设置用户登录信息或者更新用户登录信息
     * User:tianyu
     * @param $userInfo
     * @param $data
     * @param $loginType            登陆类型1是存session，主要是商户端的登陆和网页版本的登陆,2就是token
     * @param int $platform 1就是普通的登陆，主要是vue登陆，2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc，写这个是为了保证h5端和小程序端可以同时保持登陆状态
     * @param int $type 1的话就是登录,2的话就是更新
     * @return array
     */
    public function setSession($userInfo, $loginType, $platform = 1, $type = 1)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        //判断账号状态
        if ($userInfo->status != self::STATUS_NORMAL) {
            return error_code(11022);
        }


        switch ($loginType) {
            case 1:
                session('user', $userInfo->toArray());
                $result['status'] = true;
                break;
            case 2:
                $userTokenModel = new UserToken();
                $result         = $userTokenModel->setToken($userInfo['id'], $platform);
                break;
        }

        if ($type == 1) {
            $userLogModel = new UserLog();        //添加登录日志
            $userLogModel->setLog($userInfo['id'], $userLogModel::USER_LOGIN);
        }
        return $result;
    }

    public function editInfo($id, $sex = '', $birthday = '', $nickname = '', $avatar = '')
    {
        $data   = [];

        if ($sex != '') {
            $data['sex'] = $sex;
        }
        if ($birthday != '') {
            $data['birthday'] = $birthday;
        }
        if ($nickname != '') {
            $data['nickname'] = htmlentities($nickname);
        }
        if ($avatar != '') {
            $data['avatar'] = $avatar;
        }
        $re = $this->save($data, ['id' => $id]);
        if ($re !== false) {
            //$userLogModel = new UserLog();
            //$userLogModel->setLog($id,$userLogModel::USER_EDIT);
            $result['status'] = true;
            $result['msg']    = '保存成功';
            $result['data'] = '';
            return $result;
        } else {
            return error_code(10005);
        }
    }


    /**
     * 密码加密方法
     * @param string $pw 要加密的字符串
     * @return string
     */
    private function enPassword($password, $ctime)
    {

        return md5(md5($password) . $ctime);
    }

    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['sex']) && $post['sex'] != "") {
            $where[] = ['sex', 'eq', $post['sex']];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['id', 'in', $post['id']];
        }
        if (isset($post['username']) && $post['username'] != "") {
            $where[] = ['username', 'like', '%' . $post['username'] . '%'];
        }
        if (isset($post['mobile']) && $post['mobile'] != "") {
            $where[] = ['mobile|username', 'eq', $post['mobile']];
        }
        if (isset($post['birthday']) && $post['birthday'] != "") {
            $where[] = ['birthday', 'eq', $post['birthday']];
        }
        if (isset($post['nickname']) && $post['nickname'] != "") {
            $where[] = ['nickname', 'like', '%' . $post['nickname'] . '%'];
        }
        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }
        if (isset($post['pmobile']) && $post['pmobile'] != "") {
            $pwhere[] = ['mobile|username', 'like', "%" . $post['pmobile'] . "%"];
            $user      = $this->field('id')->where($pwhere)->select();
            if (!$user->isEmpty()) {
                $user = array_column($user->toArray(), 'id');
                $where[] = ['pid', 'in', $user];
            } else {
                $where[] = ['pid', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }

            //            if ($puser_id = get_user_id($post['pmobile'])) {
            //                $where[] = ['pid', 'eq', $puser_id];
            //            } else {
            //                $where[] = ['pid', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            //            }
        }
        if (isset($post['grade']) && $post['grade'] != "") {
            $where[] = ['grade', 'in', $post['grade']];
        }
        if (isset($post['mobileOrUser']) && $post['mobileOrUser'] != "") {
            $where[] = ['username|mobile', 'like', '%' . $post['mobileOrUser'] . '%'];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "id desc";
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @param $list
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['sex']) {
                $list[$k]['sex'] = config('params.user')['sex'][$v['sex']];
            }
            if ($v['status']) {
                $list[$k]['status'] = config('params.user')['status'][$v['status']];
            }
            if ($v['pid']) {
                $list[$k]['pid_name'] = get_user_info($v['pid'], 'nickname');
            }
            if ($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
            if (isset($v['avatar']) && $v['avatar']) {
                $list[$k]['avatar'] = _sImage($v['avatar']);
            }
        }
        return $list;
    }

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post, $isPage = true)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);

        if ($isPage) {
            $list        = $this->with(['grade', 'userWx'])->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
            // $re['sql'] = $this->getLastSql();
            $data        = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $re['count'] = $list->total();
        } else {
            $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->select();
            if (!$list->isEmpty()) {
                $data = $this->tableFormat($list->toArray());
            }
            $re['count'] = count($list);
        }
        $re['code'] = 0;
        $re['msg']  = '';

        $re['data'] = $data;

        return $re;
    }


    public function changeAvatar($id, $image_url)
    {
        $data['avatar'] = $image_url;
        $where['id']    = $id;
        if ($this->save($data, $where)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 获取用户的信息
     * @return array|null|\PDOStatement|string|\think\Model
     */
    //    public function getUserInfo($user_id)
    //    {
    //        $data = $this->where('id', $user_id)->find();
    //        if ($data) {
    //            $data['state']    = $data['status'];
    //            $data['status']   = config('params.user')['status'][$data['status']];
    //            $data['p_mobile'] = $this->getUserMobile($data['pid']);
    //            return $data;
    //        } else {
    //            return "";
    //        }
    //    }

    public function getUserInfo($user_id)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $userInfo = $this::with("grade")
            ->field('id,username,mobile,sex,birthday,avatar,nickname,balance,point,grade,status,pid,password,ctime,remarks')
            ->where(array('id' => $user_id))
            ->find();
        if ($userInfo !== false) {
            $userInfo['avatar'] = _sImage($userInfo['avatar']);
            $userGradeModel = new UserGrade();
            $gradeinfo = $userGradeModel->where(['id' => $userInfo['grade']])->find();
            if ($gradeinfo) {
                $userInfo['grade_name'] = $gradeinfo['name'];
            } else {
                $userInfo['grade_name'] = "";
            }
            //判断是否有密码，如果有就是true，没有就是false
            if ($userInfo['password']) {
                $userInfo['password'] = true;
            } else {
                $userInfo['password'] = false;
            }

            $result['data'] = $userInfo;
            $result['status'] = true;
        } else {
            return  error_code(11004);
        }
        return $result;
    }

    //忘记密码，找回密码
    public function forgetPassword($mobile, $code, $newPwd)
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];

        if (empty($code)) {
            return error_code(10013);
        }
        $smsModel = new Sms();
        if (!$smsModel->check($mobile, $code, 'veri')) {
            return error_code(10012);
        }
        $userInfo = $this->where(['mobile' => $mobile])->find();
        if (!$userInfo) {
            $result['msg'] = '没有此手机号码';
            return error_code(11032);
        }
        return $this->editPwd($userInfo['id'], $newPwd, $userInfo['ctime']);
    }

    //修改用户密码，如果用户之前没有密码，那么就不校验原密码
    public function changePassword($user_id, $newPwd, $password = "")
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];
        //修改密码验证原密码
        $user = $this->get($user_id);
        if (!$user) {
            return error_code(10000);
        }

        if ($user['password']) {
            if (!$password) {
                return error_code(11012);
            }
            if ($user['password'] !== $this->enPassword($password, $user['ctime'])) {
                return error_code(11045);
            }
        }
        return $this->editPwd($user_id, $newPwd, $user['ctime']);
    }


    /**
     *  修改密码
     * @param $user_id
     * @param $pwd
     * @return array
     */
    private function editPwd($user_id, $newPwd, $ctime)
    {
        $result = [
            'status' => true,
            'msg'    => '',
            'data'   => ''
        ];

        if ($newPwd == '' || strlen($newPwd) < 6 || strlen($newPwd) > 16) {
            return error_code(11009);
        }

        $res_pwd = $this->save([
            'password' => $this->enPassword($newPwd, $ctime)
        ], ['id' => $user_id]);

        if (!$res_pwd) {
            return error_code(11044);
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
    public function recommendList($user_id, $page = 1, $limit = 10)
    {
        $data  = $this
            ->field('nickname, avatar, mobile, ctime')
            ->where('pid', $user_id)
            ->page($page, $limit)
            ->select();
        $count = $this
            ->field('nickname, avatar, mobile, ctime')
            ->where('pid', $user_id)
            ->count();
        if (!$data->isEmpty()) {
            foreach ($data as $v) {
                $v['ctime'] = getTime($v['ctime']);
            }
            $result['data'] = $data;
        }
        return $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $data,
            'total'  => ceil($count / $limit)
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
            'status'          => false,
            'msg'             => error_code(10025, true),
            'data'            => 0,
            'available_point' => 0,
            'point_rmb'       => 0,
            'switch'          => 1
        ];

        $settingModel = new Setting();
        $switch       = $settingModel->getValue('point_switch');
        if ($switch == 2) {
            $return['status'] = true;
            $return['switch'] = 2;
            return $return;
        }

        $where[] = ['id', 'eq', $user_id];
        $data    = $this->field('point')->where($where)->find();
        if ($data !== false) {
            if ($order_money != 0) {
                //计算可用积分
                $settingModel                = new Setting();
                $orders_point_proportion     = $settingModel->getValue('orders_point_proportion'); //订单积分使用比例
                $max_point_deducted_money    = $order_money * ($orders_point_proportion / 100); //最大积分抵扣的钱
                $point_discounted_proportion = $settingModel->getValue('point_discounted_proportion'); //积分兑换比例
                $needs_point                 = $max_point_deducted_money * $point_discounted_proportion;
                $return['available_point']   = floor($needs_point > $data['point'] ? $data['point'] : $needs_point);
                $return['point_rmb']         = $return['available_point'] / $point_discounted_proportion;
            }

            $return['msg']    = '获取成功';
            $return['data']   = $data['point'];
            $return['status'] = true;
        }

        return $return;
    }


    /**
     * 获取用户昵称 （废弃方法，不建议使用，建议使用get_user_info()函数）
     * @param $user_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserNickname($user_id)
    {
        $where[] = ['id', 'eq', $user_id];
        $result  = $this->field('nickname, mobile')
            ->where($where)
            ->find();
        if ($result) {
            $nickname = $result['nickname'] ? $result['nickname'] : format_mobile($result['mobile']);
        } else {
            $nickname = '';
        }

        return $nickname;
    }


    /**
     * 获取用户手机号
     * @param $user_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserMobile($user_id)
    {
        $where[] = ['id', 'eq', $user_id];
        $result  = $this->field('mobile')->where($where)->find();
        return $result['mobile'] ? $result['mobile'] : '';
    }


    /**
     * 通过手机号获取用户ID
     * @param $mobile
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserIdByMobile($mobile)
    {
        $where[] = ['mobile', 'eq', $mobile];
        $where[] = ['status', 'eq', self::STATUS_NORMAL];
        $result  = $this->field('id')->where($where)->find();
        return $result['id'] ? $result['id'] : false;
    }


    /**
     * 绑定上级
     * @param $user_id
     * @param $superior_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function setMyInvite($user_id, $superior_id)
    {
        if ($user_id == $superior_id) {
            return error_code(11049);
        }

        $userInfo = $this->get($user_id);
        if ($userInfo['pid'] && $userInfo['pid'] != 0) {
            return error_code(11053);
        }

        $superior = $this->get($superior_id);
        if (!$superior) {
            return error_code(11052);
        }

        $flag = $this->isInvited($user_id, $superior_id);
        if ($flag) {
            return error_code(11054);
        }

        $data['pid']    = $superior_id;
        $where[]        = ['id', 'eq', $user_id];
        $return = [];
        $return['data'] = $this->save($data, $where);
        if ($return['data'] !== false) {
            $return['status'] = true;
            $return['msg']    = '填写邀请码成功';
        } else {
            return error_code(11048);
        }

        return $return;
    }


    /**
     * 判断user_id是否是pid的父节点或者祖父节点,如果是，就返回true，如果不是就返回false
     * @param $user_id      下级节点
     * @param $pid          父节点
     * @return bool
     */
    public function isInvited($user_id, $pid)
    {
        $where[] = ['id', 'eq', $pid];
        $info    = $this->field('pid')->where($where)->find();
        if (!$info || $info['pid'] == 0) {
            return false;
        } else {
            if ($info['pid'] == $user_id) {
                return true;
            } else {
                return $this->isInvited($user_id, $info['pid']);
            }
        }
    }


    /**
     * 获取用户分享码
     * @param $user_id
     * @return float|int|string
     */
    public function getShareCodeByUserId($user_id)
    {
        $code = ((int) $user_id + 1234) * 3;
        return $code;
    }


    /**
     * 获取用户ID
     * @param $code
     * @return float|int
     */
    public function getUserIdByShareCode($code)
    {
        $user_id = ((int) $code / 3) - 1234;
        return $user_id;
    }


    /**
     * 修改邀请人
     * @param $id
     * @param $mobile
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function editInvite($id, $pid)
    {
        $return = [
            'status' => false,
            'msg'    => '',
            'data'   => ''
        ];

        if ($id == $pid) {
            return error_code(11049);
        }

        $isInvited = $this->isInvited($id, $pid);
        if ($isInvited) {
            return error_code(11054);
        }
        $return['status'] = true;
        $return['msg']    = '';

        return $return;
    }


    /**
     * 后台添加用户
     * @param $data
     * @return array
     */
    public function manageAdd($data)
    {

        if (isset($data['mobile'])) {
            if ($data['mobile'] == '') {
                return error_code(11051);
            }
            if (!isMobile($data['mobile'])) {
                return error_code(11057);
            }
            $flag = $this->checkUserByMobile($data['mobile']);
            if ($flag) {
                return error_code(11058);
            }
        }

        if (isset($data['username']) && $data['username'] != "") {
            if ($this->where('username', $data['username'])->find()) {
                return error_code(11059);
            }
        }

        if (isset($data['password'])) {
            if ($data['password'] == '' || strlen($data['password']) < 6 || strlen($data['password']) > 16) {
                return error_code(11009);
            }
            //密码效验
            if ($data['password'] !== $data['repassword']) {
                return error_code(11025);
            }
        }
        if (!isset($data['pid']) || $data['pid'] == '') {
            $data['pid'] = 0;
        }

        //默认用户等级
        if (!isset($data['grade'])) {
            $userGradeModel = new UserGrade();
            $gradeInfo = $userGradeModel->where('is_def', '1')->find();
            if ($gradeInfo) {
                $data['grade'] = $gradeInfo['id'];
            } else {
                $data['grade'] = 0;
            }
        }

        //用户备注只取前100个字符
        if (isset($data['remarks'])) {
            $data['remarks'] = trim($data['remarks']);
            if (mb_strlen($data['remarks']) > 100) {
                $data['remarks'] = substr($data['remarks'], 0, 99);
            }
        }
        $time                = time();
        $newData['username'] = $data['username'];
        $newData['mobile']   = isset($data['mobile']) ? $data['mobile'] : "";
        $newData['password'] = isset($data['password']) ? $this->enPassword($data['password'], $time) : "";
        $newData['sex']      = isset($data['sex']) ? $data['sex'] : 3;
        $newData['birthday'] = $data['birthday'] ? $data['birthday'] : null;
        $newData['avatar']   = isset($data['avatar']) ? $data['avatar'] : '';
        $newData['nickname'] = $data['nickname'];
        $newData['balance']  = 0;
        $newData['point']    = 0;
        $newData['ctime']    = $time;
        $newData['utime']    = $time;
        $newData['status']   = isset($data['status']) ? $data['status'] : self::STATUS_NORMAL;
        $newData['pid']      = $data['pid'];
        $newData['grade']    = $data['grade'];
        $newData['remarks'] = isset($data['remarks']) ? $data['remarks'] : '';
        $result         = $this->save($newData);
        $return['data'] = $this->id;
        if ($result) {
            $newData['id'] = $this->id;
            hook('addUserAfter', $newData); //添加用户后钩子
            if (session('manage.id')) {
                $userLogModel = new UserLog();
                $userLogModel->setLog(session('manage.id'), $userLogModel::USER_REG);
            }
            $return['status'] = true;
            $return['msg']    = '添加成功';
        } else {
            return error_code(10038);
        }

        return $return;
    }


    /**
     * 后台修改用户
     * @param $data
     * @return array
     */
    public function manageEdit($data)
    {
        $return = error_code(10024);

        //校验数据
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $return['msg'] = $validate->getError();
            return $return;
        }
        if (isset($data['pid']) && $data['pid'] != '') {
            $p = $this->editInvite($data['id'], $data['pid']);
            if ($p['status'] === false) {
                $return['msg'] = $p['msg'];
                return $return;
            }
        } else {
            $data['pid'] = 0;
        }
        //输入密码时修改密码
        if (isset($data['password']) && $data['password'] != '') {
            if (strlen($data['password']) < 6 || strlen($data['password']) > 20) {
                return error_code(11009);
            }
            //密码效验
            if ($data['password'] !== $data['repassword']) {
                return error_code(11025);
            }
            $userInfo            = $this->get($data['id']);
            $newData['password'] = $this->enPassword($data['password'], $userInfo['ctime']);
        }

        //用户备注只取前100个字符
        if (isset($data['remarks'])) {
            $data['remarks'] = trim($data['remarks']);
            if (mb_strlen($data['remarks']) > 100) {
                $data['remarks'] = substr($data['remarks'], 0, 99);
            }
            $newData['remarks'] = $data['remarks'];
        }
        $where[]             = ['id', 'eq', $data['id']];
        $newData['nickname'] = $data['nickname'];
        $newData['sex']      = $data['sex'] ? $data['sex'] : 3;
        $newData['birthday'] = $data['birthday'] ? $data['birthday'] : null;
        $newData['avatar']   = $data['avatar'];
        $newData['status']   = $data['status'];
        $newData['pid']      = $data['pid'];
        $newData['grade']    = $data['grade'];
        $result              = $this->save($newData, $where);
        $return['data']      = $result;

        if ($result) {
            $return['status'] = true;
            $return['msg']    = '修改成功';
        }

        return $return;
    }

    /**
     * 根据用户手机号获取用户id
     */
    public function checkUserByMobile($mobile)
    {
        $where[] = ['mobile', 'eq', $mobile];
        // $where[] = ['status', 'eq', self::STATUS_NORMAL];
        $res     = $this->field('id')->where($where)->find();
        return $res;
    }


    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id'   => 'mobile',
                'desc' => '手机号',
            ],
            [
                'id'   => 'sex',
                'desc' => '性别',
            ],
            [
                'id'   => 'birthday',
                'desc' => '生日',
            ],
            [
                'id'   => 'avatar',
                'desc' => '头像',
            ],
            [
                'id'   => 'nickname',
                'desc' => '昵称',
            ],
            [
                'id'   => 'balance',
                'desc' => '余额',
            ],
            [
                'id'   => 'point',
                'desc' => '积分',
                // 'modify'=>'getBool'
            ],
            [
                'id'   => 'status',
                'desc' => '状态',
                //'modify'=>'getMarketable',
            ],
            //            [
            //                'id' => 'pid_name',
            //                'desc' => '邀请人',
            //            ],
            //            [
            //                'id' => 'ctime',
            //                'desc' => '创建时间',
            //            ],
            [
                'id'   => 'username',
                'desc' => '用户名',
            ],
            //
        ];
    }


    /**
     * 获取csv数据
     * @param $post
     * @return array
     */
    public function getCsvData($post)
    {
        $result   = error_code(10083);
        $header   = $this->csvHeader();
        $userData = $this->tableData($post, false);


        if ($userData['count'] > 0) {
            $tempBody = $userData['data'];
            $body     = [];
            $i        = 0;

            foreach ($tempBody as $key => $val) {
                $i++;
                foreach ($header as $hk => $hv) {
                    if (isset($val[$hv['id']]) && $val[$hv['id']] && isset($hv['modify'])) {
                        if (function_exists($hv['modify'])) {
                            $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                        }
                    } elseif (isset($val[$hv['id']]) && !empty($val[$hv['id']])) {
                        $body[$i][$hk] = $val[$hv['id']];
                    } else {
                        $body[$i][$hk] = '';
                    }
                }
            }
            $result['status'] = true;
            $result['msg']    = '导出成功';
            $result['data']   = $body;
            return $result;
        } else {
            //失败，导出失败
            return $result;
        }
    }

    public function doAdd($data = [])
    {
        $result = $this->insert($data);
        if ($result) {
            return $this->getLastInsID();
        }
        return $result;
    }

    public function grade()
    {
        return $this->hasOne("UserGrade", 'id', 'grade')->bind(['grade_name' => 'name']);
    }
    public function userWx()
    {
        return $this->hasMany("UserWx", 'user_id', 'id');
    }


    /**
     * 按天统计新会员
     */
    public function statistics($day)
    {
        $where   = [];
        $where[] = ['ctime', '>', strtotime('-' . $day . ' days')];

        $field = 'DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d") as day, count(*) as nums';

        $res  = $this->field($field)->where($where)->where("TIMESTAMPDIFF(DAY,from_unixtime(ctime),now()) <7")->group('DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d")')->select();
        $data = get_lately_days($day, $res);
        return ['day' => $data['day'], 'data' => $data['data']];
    }

    /**
     * 按天统计当天下单活跃会员
     * @param $day
     * @return array
     */
    public function statisticsOrder($day)
    {
        $orderModel = new Order();
        $res        = [];
        for ($i = 0; $i < $day; $i++) {
            $where    = [];
            $curr_day = date('Y-m-d');
            if ($i == 0) {
                $where[]  = ['ctime', '<', time()];
                $curr_day = date('Y-m-d');
            } else {
                $where[]  = ['ctime', '<', strtotime(date("Y-m-d", strtotime("-" . $i . " day")) . ' 23:59:59')];
                $curr_day = date("Y-m-d", strtotime("-" . $i . " day"));
            }
            $where[] = ['ctime', '>=', strtotime(date("Y-m-d", strtotime("-" . $i . " day")) . ' 00:00:00')];
            $res[]   =
                [
                    'nums' => $orderModel->where($where)->group('user_id')->count(),
                    'day'  => $curr_day
                ];
        }

        $data = get_lately_days($day, $res);
        return ['day' => $data['day'], 'data' => $data['data']];
    }
}
