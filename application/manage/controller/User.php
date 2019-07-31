<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Balance;
use app\common\model\GoodsComment;
use app\common\model\UserGrade;
use app\common\model\UserLog;
use app\common\model\User as UserModel;
use app\common\model\UserPointLog;
use think\facade\Request;

class User extends Manage
{
    /**
     * 用户列表
     * @return mixed
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $userModel = new UserModel();
            return $userModel->tableData(input('param.'));
        }
        return $this->fetch('index');
    }


    /**
     * 获取积分记录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pointLog()
    {
        $result = [
            'status' => false,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $user_id = input('user_id');
        $flag = input('flag', 'false');

        if($flag == 'true')
        {
            $userPointLog = new UserPointLog();
            return $userPointLog->pointLogList($user_id, false, input('page', 1), input('limit', 20));
        }

        $this->assign('user_id', $user_id);
        $result['status'] = true;
        $result['msg'] = '获取成功';
        $result['data'] = $this->fetch('pointLog');
        return $result;
    }


    /**
     * 编辑积分
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editPoint()
    {
        $result = [
            'status' => false,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $user_id = input('user_id');
        $flag = input('flag', 'false');

        if($flag == 'true')
        {
            $point        = input('point');
            $memo         = input('memo');
            $userPointLog = new UserPointLog();
            return $userPointLog->setPoint($user_id, $point, $userPointLog::POINT_TYPE_ADMIN_EDIT, $memo);
        }

        $this->assign('user_id', $user_id);
        $User = new UserModel();
        $where[] = ['id', 'eq', $user_id];
        $user_info = $User->where($where)->find();
        $this->assign('point', $user_info['point']);
        $result['status'] = true;
        $result['msg'] = '获取成功';
        $result['data'] = $this->fetch('editPoint');
        return $result;
    }


    /**
     * 取当前店铺的所有用户的登陆退出消息,现在是绑定死一个用户，以后可能有多个用户
     * @return \think\Paginator
     */
    public function userLogList()
    {
        $userLogModel = new UserLog();
        return $userLogModel->getList(session('user.id'));
    }


    /**
     * 用户统计
     * @return array
     */
    public function statistics()
    {
        $userLogModel = new UserLog();
        $list_login   = $userLogModel->statistics(7, $userLogModel::USER_LOGIN);
        $list_reg     = $userLogModel->statistics(7, $userLogModel::USER_REG);

        $data = [
            'legend' => [
                'data' => ['新增记录', '活跃记录']
            ],
            'xAxis'  => [
                [
                    'type' => 'category',
                    'data' => $list_login['day']
                ]
            ],
            'series' => [
                [
                    'name' => '新增记录',
                    'type' => 'line',
                    'data' => $list_reg['data']
                ],
                [
                    'name' => '活跃记录',
                    'type' => 'line',
                    'data' => $list_login['data']
                ]
            ]
        ];
        return $data;
    }


    /**
     * 评价列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function comment()
    {
        if(Request::isPost())
        {
            $page = input('page', 1);
            $limit = input('limit', 20);
            $order_id = input('order_id', '');
            $evaluate = input('evaluate', 'all');
            $mobile = input('mobile', false);
            $goodsCommentModel = new GoodsComment();
            $res = $goodsCommentModel->getListComments($page, $limit, $order_id, $evaluate, 'all', $mobile);
            if($res['status'])
            {
                $return = [
                    'status' => true,
                    'msg'    => '获取成功',
                    'data'   => $res['data']['list'],
                    'count'  => $res['data']['count']
                ];
            }
            else
            {
                $return = [
                    'status' => false,
                    'msg'    => '获取失败',
                    'data'   => $res['data']['list'],
                    'count'  => $res['data']['count']
                ];
            }
            return $return;
        }
        return $this->fetch('comment');
    }


    /**
     * 修改邀请人
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editInvite()
    {
        $id = Request::param('id');
        $mobile = Request::param('mobile');
        $model = new UserModel();
        return $model->editInvite($id, $mobile);
    }


    /**
     * 添加用户
     * @return array
     */
    public function addUser()
    {
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            $input = Request::param();
            $userModel = new UserModel();
            $result = $userModel->manageAdd($input);
            return $result;
        }
        $gradeModel = new UserGrade();
        $userGrade = $gradeModel->getAll();
        $this->assign('grade', $userGrade);
        $result['status'] = true;
        $result['msg'] = '获取成功';
        $result['data'] = $this->fetch('addUser');
        return $result;
    }


    /**
     * 编辑用户
     * @return array
     */
    public function editUser()
    {
        $result = [
            'status' => false,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $userModel = new UserModel();

        if(Request::isPost())
        {
            $input = Request::param();
            $result = $userModel->manageEdit($input);
            return $result;
        }

        $user_id = Request::param('user_id');
        $info = $userModel->getUserInfo($user_id);
        $this->assign('info', $info);
        $gradeModel = new UserGrade();
        $userGrade = $gradeModel->getAll();
        $this->assign('grade', $userGrade);
        $result['status'] = true;
        $result['msg'] = '获取成功';
        $result['data'] = $this->fetch('editUser');
        return $result;
    }


    /**
     * 用户详情
     * @return mixed
     */
    public function details()
    {
        $result = [
            'status' => false,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $user_id = Request::param('user_id');
        $model = new UserModel();
        $info = $model->getUserInfo($user_id);
        $this->assign('info', $info);
        $result['status'] = true;
        $result['msg'] = '获取成功';
        $result['data'] = $this->fetch('details');
        return $result;
    }


    /**
     * 编辑余额
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editMoney()
    {
        $result = [
            'status' => false,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $user_id = input('user_id');
        $flag = input('flag', 'false');

        if($flag == 'true')
        {
            $money = input('money');
            $balanceMoney = new Balance();
            return $balanceMoney->change($user_id, $balanceMoney::TYPE_ADMIN, $money, 0);
        }

        $this->assign('user_id', $user_id);
        $User = new UserModel();
        $where[] = ['id', 'eq', $user_id];
        $user_info = $User->where($where)->find();
        $this->assign('money', $user_info['balance']);
        $result['status'] = true;
        $result['msg'] = '';
        $result['data'] = $this->fetch('editMoney');
        return $result;
    }


    /**
     * 用户等级列表
     * @return mixed
     */
    public function grade()
    {
        if(Request::isAjax())
        {
            $userGradeModel = new UserGrade();
            return $userGradeModel->tableData(input('param.'));
        }
        return $this->fetch('grade_index');
    }


    /**
     * 用户等级新增和编辑，都走这里
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function gradeAdd()
    {
        $result = [
            'status' => false,
            'msg' => '失败',
            'data' => ''
        ];
        $this->view->engine->layout(false);

        $userGradeModel = new UserGrade();
        if(Request::isPost())
        {
            $validate = new \app\common\validate\UserGrade();
            if (!$validate->check(input('param.')))
            {
                $result['msg'] = $validate->getError();
                return $result;
            }
            return $userGradeModel->toEdit(input('param.id'), input('param.name'), input('param.is_def', 2));
        }

        if(input('?param.id'))
        {
            $info = $userGradeModel->where('id', input('param.id'))->find();
            if(!$info)
            {
                $result['msg'] = "没有此条记录";
                return $result;
            }
            $this->assign('data', $info);
        }
        $result['status'] = true;
        $result['msg'] = '成功';
        $result['data'] = $this->fetch('grade_edit');
        return $result;
    }


    /**
     * 用户等级删除
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function gradeDel()
    {
        $result = [
            'status' => false,
            'msg' => '失败',
            'data' => ''
        ];

        $userGradeModel = new UserGrade();
        if(!input('?param.id'))
        {
            return error_code(10000);
        }

        $info = $userGradeModel->where('id', input('param.id'))->find();
        if(!$info)
        {
            $result['msg'] = "没有此用户等级";
            return $result;
        }
        $re = $userGradeModel->where('id', input('param.id'))->delete();
        if($re)
        {
            $result['status'] = true;
        }
        else
        {
            $result['msg'] = "删除失败";
        }
        return $result;
    }


    /**
     * 删除用户
     * @return array
     */
    public function delUser()
    {
        $result = [
            'status' => false,
            'msg' => '失败',
            'data' => ''
        ];

        $ids = input('ids/a', []);
        if(!$ids)
        {
            return $result;
        }
        $userModel = new UserModel();
        $res = $userModel::destroy($ids);
        if($res !== false)
        {
            $result['msg'] = '删除成功';
            $result['status'] = true;
        }
        return $result;
    }
}