<?php

/**
 * 平台消息管理
 */

namespace app\manage\controller;

use app\common\controller\Manage as ManageController;

use app\common\model\MessageCenter;
use app\common\model\Sms as SmsModel;
use Request;


class Sms extends ManageController
{
    public function index()
    {
        $smsModel = new SmsModel();
        if(Request::isAjax()){

            return $smsModel->tableData(input('param.'));
        }else{
            $messageCenterModel = new MessageCenter();
            $this->assign('platformTpl',$messageCenterModel->tpl);
            $this->assign('smsTpl',$smsModel->sms_tpl);
            return $this->fetch('index');
        }
    }
    public function del()
    {
        if(!input('?param.id')){
            return error_code(10003);
        }
        $id = input('param.id');
        $smsModel = new SmsModel();
        if($smsModel->where(['id'=>$id])->delete()){
            return [
                'status' => true,
                'data' => '',
                'msg' => '删除成功'
            ];
        } else {
            return  error_code(10023);
        }
    }

}