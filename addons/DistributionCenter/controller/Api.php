<?php

namespace addons\DistributionCenter\controller;

use addons\DistributionCenter\model\Distribution;
use addons\DistributionCenter\model\DistributionOrder;
use app\common\model\Order;
use app\common\model\UserToken;

class Api
{

    private $userId = 0;//当前登录用户ID

    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
            header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
            exit;
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');

    }

    private function checkUser()
    {
        $data = input('param.');
        if (!$data['token']) {
            $result['msg'] = '请先登录';
            return $result;
        }
        $userTokenModel = new UserToken();
        $re             = $userTokenModel->checkToken(input('param.token'));

        if (!$re['status']) {
            return json(error_code(14007));
        } else {
            $this->userId = $re['data']['user_id'];
        }
    }

    /**
     * 申请成为分销商接口
     * @return array
     */
    public function applyDistribution()
    {
        $this->checkUser();
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [],
        ];
        $data   = input('param.');
        if ($data['agreement'] != 'on') {
            $result['status'] = false;
            $result['msg']    = '请勾选分销协议';
            return json($result);
        }
        $distribution = new Distribution();
        $res          = $distribution->addData($data, $this->userId);
        return json($res);
    }

    /**
     * 查询用户是否可以成为分销商
     * @return \think\response\Json
     */
    public function info()
    {
        $this->checkUser();
        $result         = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [],
        ];
        $checkCondition = input('check_condition', true);
        $distribution   = new Distribution();
        $res            = $distribution->getInfo($this->userId, $checkCondition);
        return json($res);
    }


    /**
     * 我推广的订单
     */
    public function myOrder()
    {
        $this->checkUser();
        $result                 = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [],
        ];
        $page                   = input('page', 1);
        $limit                  = input('limit', 10);
        $distributionOrderModel = new DistributionOrder();
        $res                    = $distributionOrderModel->getList($this->userId, $page, $limit);
        return json($res);
    }

    /**
     * 获取店铺信息
     * @return \think\response\Json
     */
    public function getStoreInfo()
    {
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [],
        ];

        $store = input('store', '');
        if (!$store) {
            $result['status'] = false;
            $result['msg']    = '店铺信息丢失';
            return json($result);
        }
        $store        = alphaID($store, true);
        $distribution = new Distribution();
        $res          = $distribution->getStore($store);
        return json($res);
    }


    /**
     * 店铺设置
     * @return \think\response\Json
     */
    public function setStore()
    {
        $this->checkUser();
        $result = [
            'status' => true,
            'msg'    => '设置成功',
            'data'   => [],
        ];
        $data   = input('param.');
        if (!$data['store_name']) {
            $result['status'] = false;
            $result['msg']    = '请填写店铺名称';
            return json($result);
        }
        if (!$data['store_logo']) {
            $result['status'] = false;
            $result['msg']    = '请上传店铺logo';
            return json($result);
        }
        if (!$data['store_banner']) {
            $result['status'] = false;
            $result['msg']    = '请上传店铺banner';
            return json($result);
        }
        $distribution = new Distribution();
        $res          = $distribution->updateInfoByUserId($data, $this->userId);
        return json($res);
    }

    /**
     * 我的店铺信息接口
     */
    public function myStore()
    {
        $this->checkUser();
        $distribution = new Distribution();
        $res = $distribution->getInfo($this->userId);
        return json($res);
    }
}
