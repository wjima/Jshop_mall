<?php
namespace app\seller\controller;
use app\common\controller\Seller;
use app\common\model\Message;
use app\common\model\MessageCenter as MessageCenterModel;
use Request;


class MessageCenter extends Seller
{
    public function index()
    {
        $messageCenterModel = new MessageCenterModel();
        if(Request::isAjax()){
            return $messageCenterModel->getSellerTpl($this->sellerId);
        }else{
            return $this->fetch();
        }

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
            'code'=>$code,
            'seller_id'=>$this->sellerId
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
                $data['sms'] = $messageCenterModel->seller_tpl[$code]['sms'];
            }
            if(input('?param.message')){
                $data['message'] = input('param.message');
            }else{
                $data['message'] = $messageCenterModel->seller_tpl[$code]['message'];
            }
            if(input('?param.wx_tpl_message')){
                $data['wx_tpl_message'] = input('param.wx_tpl_message');
            }else{
                $data['wx_tpl_message'] = $messageCenterModel->seller_tpl[$code]['wx_tpl_message'];
            }
            $messageCenterModel->save($data);
        }

        return $re;



    }

    //商户的消息列表,取当前用户的消息和平台发送的消息
    public function message()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $data['seller_id'] = 0;
            $data['user_id'] = $this->userId;
            $messageModel = new Message();
            return $messageModel->tableData($data);
        }
        return $this->fetch('message');
    }
    //商户的消息列表
    public function messageView()
    {
        if(!input('?param.id')){
            return error_code(10003);
        }
        $id = input('param.id');
        //$data['seller_id'] = $this->sellerId;
        $messageModel = new Message();
        $info = $messageModel->info($this->userId,$id);
        if($info){
            return [
                'status' => true,
                'data' => $info,
                'msg' => ''
            ];
        }else{
            return error_code(10002);
        }
    }
    //商户的消息 删除
    public function messageDel()
    {
        if(!input('?param.id')){
            return error_code(10003);
        }
        $id = input('param.id');
        //$data['seller_id'] = $this->sellerId;
        $messageModel = new Message();
        if($messageModel->where(['id'=>$id,'user_id'=>$this->userId])->delete()){
            return [
                'status' => true,
                'data' => '',
                'msg' => '删除成功'
            ];
        }else{
            return [
                'status' => false,
                'data' => '',
                'msg' => '删除失败'
            ];
        }
    }




}