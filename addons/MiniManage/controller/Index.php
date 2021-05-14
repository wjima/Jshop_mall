<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace addons\MiniManage\controller;

use addons\MiniManage\model\AdminMessage;
use addons\MiniManage\model\AdminMessageCenter;
use app\common\model\BillAftersales;
use app\common\model\BillRefund;
use app\common\model\OrderItems;
use app\common\model\Payments;
use myxland\addons\library\AddonController;
use think\Container;
use think\facade\Cache;
use think\facade\Session;
use app\common\model\ManageRoleOperationRel;
use app\common\model\Operation;
use Request;


/**
 * Class Index
 * @package addons\MiniManage\controller
 */
class Index extends AddonController
{

    public function index()
    {
        $this->view->engine->layout('layout');
        $this->view->engine->layout('../../../application/manage/view/layout');     //此处引入后台的样式，其实插件不仅仅局限于后台，他是一个单独的模块，可以做成独立的功能
        return $this->fetch();
    }

    public function sign()
    {
        $data = \think\facade\Cache::get("sign");
        if (!$data || input("type") == 1) {
            $chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
            $charsLen = count($chars) - 1;
            shuffle($chars);
            $output = "";
            for ($i = 0; $i < 16; $i++) {
                $output .= $chars[mt_rand(0, $charsLen)];
            }
            \think\facade\Cache::set("sign", $output, 3600 * 24 * 15);
        } else {
            $output = $data;
        }
        $request = Request::instance();
        $domain = $request->domain();
        return [
            "status" => true,
            "data" => [

                "yuming" => $domain,
                "sign" => $output
            ],
            "msg" => "获取成功"
        ];
    }

    public function getlist()
    {
        if (Request::isAjax()) {
            $model = new AdminMessage();
            $input = Request::param();
            return $model->tableData($input);
        }
        $type = 1;
        $this->assign('type', $type);
        return $this->fetch();
    }

    public function messagecenter()
    {
        $messageCenterModel = new AdminMessageCenter();
        if (Request::isAjax()) {
            return $messageCenterModel->getTpl();
        }
        $url = $messageCenterModel->getqr();
        $this->assign('code', $messageCenterModel->tpl);
        $this->assign('url', $url);
        return $this->fetch();

    }

    //业务太简单，就不写到模型里了
    public function edit()
    {
        $re = [
            'status' => true,
            'data' => "",
            'msg' => ''
        ];
        if (!input('?param.code')) {
            return error_code(10000);
        } else {
            $code = input('param.code');
        }
        $messageCenterModel = new AdminMessageCenter();
        //判断是否有此条记录，如有有，就update，否则就insert
        $where = [
            'code' => $code
        ];
        $info = $messageCenterModel->where($where)->find();
        if ($info) {
            //修改
            $data = [];
            if (input('?param.message')) {
                $data['message'] = input('param.message');
            }
            if (input('?param.wx_tpl_message')) {
                $data['wx_tpl_message'] = input('param.wx_tpl_message');
            }
            if ($data) {
                $messageCenterModel->save($data, $where);
            } else {
                return error_code(10000);
            }

        } else {
            //新增
            $data = $where;
            if (input('?param.message')) {
                $data['message'] = input('param.message');
            } else {
                $data['message'] = $messageCenterModel->tpl[$code]['message'];
            }
            if (input('?param.wx_tpl_message')) {
                $data['wx_tpl_message'] = input('param.wx_tpl_message');
            } else {
                $data['wx_tpl_message'] = $messageCenterModel->tpl[$code]['wx_tpl_message'];
            }
            $messageCenterModel->save($data);

        }
        $info = $messageCenterModel->where("code", "eq", $code)->find();
        if ($info["message"] == 2 && $info["wx_tpl_message"] == 2 && $info["overall"] == 1) {
            $messageCenterModel->where("code", "eq", $code)->update(["overall" => 2]);
        }
        if ($info["message"] == 1 || $info["wx_tpl_message"] == 1 && $info["overall"] == 2) {
            $messageCenterModel->where("code", "eq", $code)->update(["overall" => 1]);
        }
        return $re;

    }

    public function delmessage()
    {
        if (!input('?param.id')) {
            return error_code(10003);
        }
        $id = input('param.id');
        $messageModel = new AdminMessage();
        if ($messageModel->where(['id' => $id])->delete()) {
            return [
                'status' => true,
                'data' => '',
                'msg' => '删除成功'
            ];
        } else {
            return error_code(10023);
        }

    }


}