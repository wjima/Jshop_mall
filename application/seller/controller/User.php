<?php
namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\SellerUser;
use app\common\model\UserLog;
use app\common\model\User as UserModel;
use app\common\model\UserPointLog;
use Request;

class User extends Seller
{
    /**
     * 商户的用户首页
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
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
            return $this->fetch('index');
        }
    }

    //积分记录
    public function pointList()
    {
        if(Request::isAjax()){
            $userPointLog = new UserPointLog();
            $data = input('param.');
            $data['seller_id'] = $this->sellerId;

            return $userPointLog->tableData($data);
        }
        $this->assign('post',input('param.'));
        return $this->fetch('pointList');
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


    public function comment()
    {
        if(Request::isPost())
        {
            $seller_id = $this->sellerId;
            $page = input('page', 1);
            $limit = input('limit', 20);
            $order_id = input('order_id', '');
            $evaluate = input('evaluate', 'all');
            $res = model('common/GoodsComment')->getListSeller($seller_id, $page, $limit, $order_id, $evaluate);
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


    /**
     * 获取用户资料信息
     * @return mixed
     */
    public function information()
    {
        $sellerModel = new \app\common\model\Seller();
        $seller_info = $sellerModel->sellerInfo($this->sellerId);

        $userModel = new UserModel();
        $userInfo = $userModel->getUserInfo($this->userId);

        $this->assign('seller_info',$seller_info);
        $this->assign('user_info',$userInfo);
        return $this->fetch();
    }



    /**
     * 用户资料修改
     * @return array|\think\Config
     */
    public function editUserInfo()
    {
        $userModel = new UserModel();
        $res = $userModel->editInfo( $this->userId ,input('param.sex'),input('param.birthday'),input('param.nickname'),input('param.avatar'));

        if ( $res['status'] )
        {
            $userInfo = $userModel->getUserInfo($this->userId);
            $userModel->setSession($userInfo,[],1,1,2);     //更新用户session
            return $result = ['status' => true,'msg'=>'保存成功','data'=>''];
        }else{
            return $res;
        }
    }


    /**
     * 用户修改/找回密码
     * @return array
     */
    public function editPwd()
    {
        $userModel = new UserModel();
        $pass = input('param.');
        $pass['user_id'] = $this->userId;
        return $userModel->checkCode( $pass );
    }

}
