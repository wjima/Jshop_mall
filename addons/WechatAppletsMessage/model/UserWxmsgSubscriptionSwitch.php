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
 * Class UserWxmsgSubscriptionSwitch
 * @package addons\WechatAppletsMessage\model
 */
class UserWxmsgSubscriptionSwitch extends Model
{
    /**
     * 获取是否需要订阅通知
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isTip($user_id)
    {
        $return = [
            'status' => false,
            'msg' => error_code(10025,true),
            'data' => true,
            'switch' => false
        ];

        $addonModel = new addonsModel();
        $con = $addonModel->getSetting('WechatAppletsMessage');
        $flag = false;
        foreach ($con as $v) {
            if ($v['template_id'] != '') {
                $flag = true;
                $return['switch'] = true;
                break;
            }
        }

        if ($flag) {
            $res = $this->where('user_id', '=', $user_id)
                ->find();
            if ($res) {
                if ($res['switch'] == 1) {
                    $return['data'] = false;
                }
            } else {
                $suModel = new UserWxmsgSubscription();
                $count = $suModel->where('user_id', '=', $user_id)->count();
                if ($count > 0) {
                    $return['data'] = false;
                }
            }
        } else {
            $return['data'] = false;
        }
        $return['status'] = true;
        $return['msg'] = '获取成功';

        return $return;
    }


    /**
     * 关闭订阅通知
     * @param $user_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function closeTip($user_id)
    {
        $return = [
            'status' => false,
            'msg' => '操作失败',
            'data' => ''
        ];

        $res = $this->where('user_id', '=', $user_id)
            ->find();
        if ($res) {
            $data['switch'] = 1;
            $this->where('user_id', '=', $user_id)
                ->update($data);
        } else {
            $data['user_id'] = $user_id;
            $data['switch'] = 1;
            $this->insert($data);
        }
        $return['status'] = true;
        $return['msg'] = '关闭成功';

        return $return;
    }
}