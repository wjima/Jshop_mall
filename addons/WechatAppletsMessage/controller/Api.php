<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace addons\WechatAppletsMessage\controller;
use addons\WechatAppletsMessage\model\UserWxmsgSubscription;
use addons\WechatAppletsMessage\model\UserWxmsgSubscriptionSwitch;
use app\common\model\UserToken;
use myxland\addons\library\AddonController;
use think\facade\Request;


/**
 * 订阅接口
 * Class Api
 * @package addons\WechatAppletsMessage\controller
 */
class Api extends AddonController
{
    protected $userId = 0;

    /**
     * initialize
     * @return array|mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function initialize()
    {
        parent::initialize();
        //解决跨域问题
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
            exit;
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');

        $userTokenModel = new UserToken();
        $token = Request::param('token');
        $result = $userTokenModel->checkToken($token);
        if(!$result['status'])
        {
            return error_code(14007);
        }
        else
        {
            $this->userId = $result['data']['user_id'];
        }
    }


    /**
     * 判断是否需要显示订阅提醒
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function istip()
    {
        $switchModel = new UserWxmsgSubscriptionSwitch();
        return json($switchModel->isTip($this->userId));
    }


    /**
     * 关闭订阅提醒
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function closetip()
    {
        $switchModel = new UserWxmsgSubscriptionSwitch();
        return json($switchModel->closeTip($this->userId));
    }


    /**
     * 获取订阅模板
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tmpl()
    {
        $suModel = new UserWxmsgSubscription();
        return json($suModel->tmpl($this->userId));
    }


    /**
     * 设置订阅状态
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function settip()
    {
        $suModel = new UserWxmsgSubscription();
        $template_id = Request::param('template_id');
        $status = Request::param('status');
        return json($suModel->setTip($this->userId, $template_id, $status));
    }
}