<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Seller;
use app\common\model\SellerUser;
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
     * 积分页面
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function point()
    {
        if(Request::isAjax())
        {
            $sellerUser = new SellerUser();
            $input['seller_id'] = $this->sellerId;
            $input['page'] = input('page');
            $input['limit'] = input('limit');
            $input['mobile'] = input('mobile');
            $input['sex'] = input('sex');
            $input['birthday'] = input('birthday');
            $input['nickname'] = input('nickname');
            $input['status'] = input('status');
            return $sellerUser->adminPointList($input);
        }
        else
        {
            return $this->fetch('point');
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
        $seller_id = $this->sellerId;
        $flag = input('flag', 'false');

        if($flag == 'true')
        {
            $userPointLog = new UserPointLog();
            $res = $userPointLog->pointLogList($user_id, $seller_id, false, input('page', 1), input('limit', 20));
            return $res;
        }
        else
        {
            $this->assign('user_id', $user_id);
            $this->assign('seller_id', $seller_id);
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
        $seller_id = $this->sellerId;
        $flag = input('flag', 'false');

        if($flag == 'true')
        {
            $point = input('point');
            $memo = input('memo');
            $userPointLog = new UserPointLog();
            $res = $userPointLog->setPoint($user_id, $seller_id, $point, $userPointLog::POINT_TYPE_ADMIN_EDIT, $memo);
            return $res;
        }
        else
        {
            $this->assign('user_id', $user_id);
            $this->assign('seller_id', $seller_id);
            $sellerUser = new SellerUser();
            $point = $sellerUser->getInfo($user_id, $seller_id, 'point');
            $this->assign('point', $point['point']);
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

        $seller_id = $this->sellerId;
        $where['seller_id'] = $seller_id;
        $userLogModel = new UserLog();
        $list_login = $userLogModel->statistics(7,$this->sellerId,$userLogModel::USER_LOGIN);
        $list_reg = $userLogModel->statistics(7,$this->sellerId,$userLogModel::USER_REG);

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
            $seller_id = input('seller_id', false);
            $page = input('page', 1);
            $limit = input('limit', 20);
            $order_id = input('order_id', '');
            $evaluate = input('evaluate', 'all');
            $mobile = input('mobile', false);
            $res = model('common/GoodsComment')->getListSeller($seller_id, $page, $limit, $order_id, $evaluate, 'all', $mobile);
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
            //所属商户
            $seller = new Seller();
            $seller_list = $seller->getAllSellerList();
            $this->assign('seller_list', $seller_list);

            return $this->fetch('comment');
        }
    }




}
