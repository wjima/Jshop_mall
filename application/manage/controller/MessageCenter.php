<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\Message;
use app\common\model\MessageCenter as MessageCenterModel;
use Request;


class MessageCenter extends Manage
{
    public function index()
    {

        $messageCenterModel = new MessageCenterModel();
        if(Request::isAjax()){

            $data = input('param.');
            return $messageCenterModel->tableData($data);
        }
        $this->assign('code',$messageCenterModel->seller_tpl);
        return $this->fetch('index');

    }

    //业务太简单，就不写到模型里了
    public function edit()
    {
        $re = [
            'status' => true,
            'data' => "",
            'msg' => ''
        ];
        if(!input('?param.id')){
            return error_code(10000);
        }else{
            $id = input('param.id');
        }

        $data = [];
        if(input('?param.sms')){
            $data['sms'] = input('param.sms');
        }
        if(input('?param.message')){
            $data['message'] = input('param.message');
        }
        if(input('?param.wx_tpl_message')){
            $data['wx_tpl_message'] = input('param.wx_tpl_message');
        }

        if($data){
            $messageCenterModel = new MessageCenterModel();
            $messageCenterModel->save($data,['id'=>$id]);
            return $re;
        }else{
            return error_code(10000);
        }

    }
}