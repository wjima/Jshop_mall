<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use Request;
use app\common\model\BillReship as BillReshipModel;


class BillReship extends Manage
{

    public function index()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $billReshipModel = new BillReshipModel;
            return $billReshipModel->tableData($data);
        }
        return $this->fetch('index');
    }

    public function view()
    {
        $this->view->engine->layout(false);
        if(!input('?param.reship_id')){
            return error_code(13220);
        }
        $billReshipModel = new BillReshipModel();
        $where['reship_id'] = input('param.reship_id');
        $info = $billReshipModel->where($where)->find();
        if(!$info){
            return error_code(13221);
        }
        if($info->items){
            $info['items_json'] = json_encode($info->items);
        }

        $this->assign('info',$info);
        return [
            'status' => true,
            'data' => $this->fetch('view'),
            'msg' => ''
        ];
    }

    //退货 ，待确认
    public function confirmReship()
    {
        $this->view->engine->layout(false);
        if(!input('?param.reship_id')){
            return error_code(13220);
        }
        $billReshipModel = new BillReshipModel();
        return $billReshipModel->confirmReship(input('param.reship_id'));
    }
}
