<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\MessageCenter as MessageCenterModel;
use Request;


class MessageCenter extends Manage
{
    public function index()
    {

        $messageCenterModel = new MessageCenterModel();
        if(Request::isAjax()){
            return $messageCenterModel->getTpl();
        }
        $this->assign('code',$messageCenterModel->tpl);
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
        if(!input('?param.code')){
            return error_code(10000);
        }else{
            $code = input('param.code');
        }
        $messageCenterModel = new MessageCenterModel();
        //判断是否有此条记录，如有有，就update，否则就insert
        $where = [
            'code'=>$code
        ];
        $info = $messageCenterModel->where($where)->find();
        if($info){
            //修改
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
                $messageCenterModel->save($data,$where);
            }else{
                return error_code(10000);
            }

        }else{
            //新增
            $data = $where;
            if(input('?param.sms')){
                $data['sms'] = input('param.sms');
            }else{
                $data['sms'] = $messageCenterModel->tpl[$code]['sms'];
            }
            if(input('?param.message')){
                $data['message'] = input('param.message');
            }else{
                $data['message'] = $messageCenterModel->tpl[$code]['message'];
            }
            if(input('?param.wx_tpl_message')){
                $data['wx_tpl_message'] = input('param.wx_tpl_message');
            }else{
                $data['wx_tpl_message'] = $messageCenterModel->tpl[$code]['wx_tpl_message'];
            }
            $messageCenterModel->save($data);
        }

        return $re;

    }
}