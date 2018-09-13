<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillPayments as BillPaymentsModel;
use app\common\model\Payments;
use app\common\model\PaymentsSellerRel;
use Request;

class BillPayments extends Manage
{
    public function index()
    {
        if(Request::isAjax()){
            $BillPaymentsModel = new BillPaymentsModel();
            $data = input('param.');
            return $BillPaymentsModel->tableData($data);
        }
        return $this->fetch('index');
    }
    //支付单查看
    public function view()
    {
        $this->view->engine->layout(false);
        if(!input('?param.payment_id')){
            return error_code(10056);
        }
        $BillPaymentsModel = new BillPaymentsModel();
        $where['payment_id'] = input('param.payment_id');
        $info = $BillPaymentsModel::with('rel')->where($where)->find();
        if(!$info){
            return error_code(10060);
        }
        if($info->rel){
            $info['rel_json'] = json_encode($info->rel);
        }

        $this->assign('info',$info);
        return [
            'status' => true,
            'data' => $this->fetch('view'),
            'msg' => ''
        ];
    }


}
