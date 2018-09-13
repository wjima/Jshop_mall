<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillRefund as BillRefundModel;
use app\common\model\PaymentsSellerRel;
use Request;

class BillRefund extends Manage
{
    public function index()
    {
        if(Request::isAjax()){
            $data = input('param.');
            $billRefundModel = new BillRefundModel();
            return $billRefundModel->tableData($data);
        }
        return $this->fetch('index');
    }

    public function view()
    {
        $this->view->engine->layout(false);
        if(!input('?param.refund_id')){
            return error_code(13215);
        }
        $billRefundModel = new BillRefundModel();
        $where['refund_id'] = input('param.refund_id');
        $info = $billRefundModel->where($where)->find();
        if(!$info){
            return error_code(13219);
        }

        $this->assign('info',$info);
        return [
            'status' => true,
            'data' => $this->fetch('view'),
            'msg' => ''
        ];
    }
}
