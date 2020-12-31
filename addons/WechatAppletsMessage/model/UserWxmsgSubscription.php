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
     * @param $user_id
     * @param $type 模板消息类型，一个地方最多只能有3个消息订阅提醒
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tmpl($user_id, $type = 'order')
    {
        $return         = [
            'status' => false,
            'msg'    => error_code(10025, true),
            'data'   => []
        ];
        $addonModel     = new addonsModel();
        $return['data'] = $addonModel->getSetting('WechatAppletsMessage');
        $template       = $return['data']['template'];

        if ($return['data']) {
            foreach ($return['data'] as $key => &$v) {
                if (!isset($v['template_id']) || !$v['template_id'] || (isset($template[$key]) && $template[$key]['type'] != $type)) {
                    unset($return['data'][$key]);
                }
            }
            $return['status'] = true;
            $return['msg']    = '获取成功';
        }
        return $return;
    }


    /**
     * 设置订阅状态
     * @param $user_id
     * @param $template_id
     * @param $status
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setTip($user_id, $template_id, $status)
    {
        $return = [
            'status' => false,
            'msg' => '操作失败',
            'data' => ''
        ];

        $addonModel = new addonsModel();
        $con = $addonModel->getSetting('WechatAppletsMessage');
        $type = '';
        foreach ($con as $k => $v) {
            if ($v['template_id'] == $template_id) {
                $type = $k;
                break;
            }
        }

        $where[] = ['user_id', '=', $user_id];
        $where[] = ['type', '=', $type];
        $count = $this->where($where)
            ->count();

        if ($status == 'accept') {
            if ($count < 1) {
                $data['user_id'] = $user_id;
                $data['template_id'] = $template_id;
                $data['type'] = $type;
                $this->insert($data);
            } else {
                $data['template_id'] = $template_id;
                $this->where($where)
                    ->update($data);
            }
        } else {
            if ($count > 0) {
                $this->where($where)
                    ->delete();
            }
        }

        $return['status'] = true;
        $return['msg'] = '操作成功';
        return $return;
    }
}