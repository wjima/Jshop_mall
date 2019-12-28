<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace addons\WechatAppletsMessage\model;
use app\common\model\Addons as addonsModel;
use think\Model;


/**
 * Class UserWxmsgSubscription
 * @package addons\WechatAppletsMessage\model
 */
class UserWxmsgSubscription extends Model
{
    /**
     * 获取模板信息
     * @return array
     */
    public function tmpl()
    {
        $return = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];

        $addonModel = new addonsModel();
        $return['data'] = $addonModel->getSetting('WechatAppletsMessage');
        if($return['data']) {
            $return['status'] = true;
            $return['msg'] = '获取成功';
        }
        return $return;
    }
}