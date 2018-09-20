<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\GoodsComment;
use app\common\model\UserLog;
use app\common\model\User as UserModel;
use app\common\model\UserPointLog;
use Request;

class User extends Manage
{
    public function index()
    {

        if(Request::isAjax()){
            $userModel = new UserModel();
            return $userModel->tableData(input('param.'));
        }else{
            return $this->fetch('index');
        }
    }


    /**
     * 获取积分记录
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pointLog()
    {
        $this->view->engine->layout(false);
        $user_id = input('user_id');
        $flag = input('flag', 'false');

        if($flag == 'true')
        {
            $userPointLog = new UserPointLog();
            $res = $userPointLog->pointLogList($user_id, false, input('page', 1), input('limit', 20));
            return $res;
        }
        else
        {
            $this->assign('user_id', $user_id);
            return $this->fetch('pointLog');
        }
    }


    /**
     * @return array|bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editPoint()
    {
        $this->view->engine->layout(false);
        $user_id = input('user_id');
        $flag = input('flag', 'false');

        if($flag == 'true')
        {
            $point = input('point');
            $memo = input('memo');
            $userPointLog = new UserPointLog();
            $res = $userPointLog->setPoint($user_id, $point, $userPointLog::POINT_TYPE_ADMIN_EDIT, $memo);
            return $res;
        }
        else
        {
            $this->assign('user_id', $user_id);
            $User = new User();
            $user_info = $User->where(['id'=>$user_id])->find();
            $this->assign('point', $user_info['point']);
            return $this->fetch('editPoint');
        }
    }


    //取当前店铺的所有用户的登陆退出消息,现在是绑定死一个用户，以后可能有多个用户
    public function userLogList()
    {
        $userLogModel = new UserLog();
        return $userLogModel->getList(session('user.id'));
    }
    //用户统计
    public function statistics()
    {
        $userLogModel = new UserLog();
        $list_login = $userLogModel->statistics(7,$userLogModel::USER_LOGIN);
        $list_reg = $userLogModel->statistics(7,$userLogModel::USER_REG);

        $data = [
            'legend' => [
                'data' => ['新增记录', '活跃记录']
            ],
            'xAxis' => [
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
                    'msg' => '获取成功',
                    'data' => $res['data']['list'],
                    'count' => $res['data']['count']
                ];
            }
            else
            {
                $return = [
                    'status' => false,
                    'msg' => '获取失败',
                    'data' => $res['data']['list'],
                    'count' => $res['data']['count']
                ];
            }
            return $return;
        }
        else
        {
            return $this->fetch('comment');
        }
    }




}
