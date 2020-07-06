<?php
namespace app\common\model;

use think\model\concern\SoftDelete;
use think\Validate;

class Manage extends Common
{

    const TYPE_SUPER_ID = 13;            //超级管理员 id

    const STATUS_NORMAL = 1;        //用户状态 正常
    const STATUS_DISABLE = 2;       //用户状态 停用

    protected $rule = [
        'username' => 'length:3,20|alphaDash',
        'mobile'   => 'require|mobile',
        'nickname' => 'length:2,50',
    ];
    protected $msg = [
        'username.length'    => '用户名长度6~20位',
        'username.alphaDash' => '用户名只能是字母、数字或下划线组成',
        'mobile.mobile'             => '请输入一个合法的手机号码',
        'mobile.require'        => '手机号码必填',
        'nickname'           => '昵称长度为2-50个字符',
    ];


    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }

        $list = $this
            ->field('m.*,group_concat(mr.name) as role_name')
            ->alias('m')
            ->leftJoin('manage_role_rel mrr', 'mrr.manage_id = m.id')
            ->leftJoin('manage_role mr', 'mr.id = mrr.role_id')
            ->where('m.id', 'neq', $this::TYPE_SUPER_ID)
            ->group("m.id")
            ->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;

        return $re;
    }


    /**
     * 注册添加用户
     * @param $data //新建用户的数据数组
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function toAdd($data)
    {
        $result = array(
            'status' => false,
            'data'   => '',
            'msg'    => ''
        );

        //校验数据
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['msg'] = $validate->getError();
            return $result;
        }


        //判断是新增还是修改
        if (isset($data['id'])) {
            $manageInfo = $this->where(['id' => $data['id']])->find();
            if (!$manageInfo) {
                return error_code(11010);
            }

            if (isset($data['password']) && !empty($data['password'])) {
                if (strlen($data['password']) <= 5 || strlen($data['password']) > 16) {
                    return error_code(11009);
                } else {
                    $data['password'] = $this->enPassword($data['password'], $manageInfo['ctime']);
                }
            } else {
                unset($data['password']);
            }

            unset($data['username']);       //不允许修改用户名
            //更新数据库
            $this->allowField(true)->save($data, ['id' => $data['id']]);
        } else {
            //判断用户名是否重复
            $manageInfo = $this->where(['username' => $data['username']])->find();
            if ($manageInfo) {
                return error_code(11011);
            }
            $data['ctime'] = time();

            if (!isset($data['password'][5]) || isset($data['password'][16])) {
                return error_code(11009);
            }
            $data['password'] = $this->enPassword($data['password'], $data['ctime']);
            //插入数据库
            $this->data($data)->allowField(true)->save();
            $data['id'] = $this->id;
        }
        //设置角色
        $manageRoleRelModel = new ManageRoleRel();
        //清空所有的旧角色
        $manageRoleRelModel->where(['manage_id' => $data['id']])->delete();

        if (isset($data['role_id'])) {
            $data1 = [];
            foreach ($data['role_id'] as $k => $v) {
                $row['manage_id'] = $data['id'];
                $row['role_id']   = $k;
                $data1[]          = $row;
            }
            $manageRoleRelModel->saveAll($data1);
        }


        $result['status'] = true;
        return $result;
    }

    /**
     * 管理员登陆
     * @param array $data 用户登陆信息
     *
     */
    public function toLogin($data)
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
        if (session('?manage_login_fail_num')) {
            if (session('manage_login_fail_num') >= config('jshop.manage_login_fail_num')) {
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


        //判断账号状态
        if ($userInfo->status != self::STATUS_NORMAL) {
            return error_code(11022);
        }

        //判断是否是用户名登陆
        $userInfo = $this->where(array('username|mobile' => $data['mobile'], 'password' => $this->enPassword($data['password'], $userInfo->ctime)))->find();
        if ($userInfo) {
            $result = $this->setSession($userInfo);
        } else {
            //写失败次数到session里
            if (session('?manage_login_fail_num')) {
                session('manage_login_fail_num', session('manage_login_fail_num') + 1);
            } else {
                session('manage_login_fail_num', 1);
            }
            return error_code(11033);
        }
        return $result;

    }

    /**
     * 管理员修改密码
     * @param $manage_id
     * @param $oldPassword
     * @param $newPassword
     * @return array|string
     */
    public function chengePwd($manage_id, $oldPassword, $newPassword)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        $info   = $this->where(['id' => $manage_id])->find();
        if (!$info) {

            return error_code(11032);
        }
        if ($oldPassword == $newPassword) {

            return error_code(11044);
        }

        if ($info['password'] != $this->enPassword($oldPassword, $info['ctime'])) {
            return error_code(11045);
        }

        $re = $this->save(['password' => $this->enPassword($newPassword, $info['ctime'])], ['id' => $info['id']]);
        if ($re) {
            $result['status'] = true;
            $result['msg']    = "修改成功";
        } else {
            return error_code(10024);
        }
        return $result;


    }

    private function setSession($userInfo)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        session('manage', $userInfo->toArray());

        $userLogModel = new UserLog();//添加登录日志
        $userLogModel->setLog($userInfo->id, $userLogModel::USER_LOGIN,[],$userLogModel::MANAGE_TYPE);

        $result['status'] = true;
        return $result;
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

}