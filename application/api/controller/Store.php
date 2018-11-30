<?php
namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\BillLading;
use app\common\model\Clerk;
use app\common\model\Setting;
use app\common\model\Store as Model;
use org\Wx;
use think\facade\Request;

/**
 * Class Store
 * @package app\api\controller
 */
class Store extends Api
{
    /**
     * 判断是否开启门店自提
     * @return array
     */
    public function getStoreSwitch()
    {
        $return = [
            'status' => true,
            'msg' => '获取成功',
            'data' => 2
        ];
        $settingModel = new Setting();
        $return['data'] = $settingModel->getValue('store_switch');
        return $return;
    }


    /**
     * 获取默认店铺
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDefaultStore()
    {
        $model = new Model();
        return $model->getDefaultStore();
    }


    /**
     * 获取店铺列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreList()
    {
        $model = new Model();
        $key = Request::param('key', '');
        return $model->getAllStoreList($key);
    }


    /**
     * 是否是店员
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isClerk()
    {
        $model = new Clerk();
        return $model->isClerk($this->userId);
    }


    /**
     * 店铺提货单列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeLadingList()
    {
        $model = new BillLading();
        return $model->getStoreLadingList($this->userId);
    }


    /**
     * 提货单详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ladingInfo()
    {
        $key = Request::param('key');
        $model = new BillLading();
        return $model->getInfo($key, $this->userId);
    }


    /**
     * 提单操作
     * @return array
     */
    public function lading()
    {
        $lading_id = Request::param('lading_id');
        $model = new BillLading();
        return $model->ladingOperating($lading_id, $this->userId);
    }


    /**
     * 提货单删除
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ladingDel()
    {
        $lading_id = Request::param('lading_id');
        $model = new BillLading();
        return $model->del($lading_id, $this->userId);
    }


    /**
     * 获取邀请小程序码
     */
    public function getInviteQRCode()
    {
        $invite = Request::param('invite', 0);
        $type = Request::param('type', 'index');
        $goods = Request::param('goods', 0);
        $page = 'pages/index/index';
        if($type == 'goods')
        {
            $page = 'pages/goods/detail/detail';
        }

        $wx = new Wx();
        $wx_appid = getSetting('wx_appid');
        $wx_app_secret = getSetting('wx_app_secret');
        $accessToken = $wx->getAccessToken($wx_appid, $wx_app_secret);
        if($accessToken)
        {
            $style['width'] = 300;
            return $wx->getParameterQRCode($accessToken, $page, $invite, $goods, $style, $wx_appid);
        }
        else
        {
            return $return = [
                'status' => false,
                'msg' => '后台小程序配置的APPID和APPSECRET错误，无法生成海报',
                'data' => ''
            ];
        }
    }
}