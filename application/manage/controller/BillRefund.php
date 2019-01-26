<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillRefund as BillRefundModel;
use app\common\model\Payments;
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
    /**
     * 未退款状态做退款
     * @return array|\think\Config
     */
    public function refund()
    {
        $this->view->engine->layout(false);
        if(!input('?param.refund_id')){
            return error_code(13215);
        }
        $billRefundModel = new BillRefundModel();

        $where['refund_id'] = input('param.refund_id');
        $where['status'] = $billRefundModel::STATUS_NOREFUND;
        $info = $billRefundModel->where($where)->find();
        if(!$info){
            return error_code(13219);
        }

        if(Request::isPost()){
            if(!input('?param.status')){
                return error_code(10000);
            }

            if(!input('?param.payment_code')){
                return error_code(10000);
            }
            if(!input('?param.refund_id')){
                return error_code(10000);
            }


            return $billRefundModel->toRefund(input('param.refund_id'),input('param.status'),input('param.payment_code'));
        }



        $this->assign('info',$info);

        //取当前商户的所有支付方式
        $paymentsModel = new Payments();
        $this->assign('payment_list',$paymentsModel->getList(0));

        return [
            'status' => true,
            'data' => $this->fetch('refund'),
            'msg' => ''
        ];
    }
    /**
     * 退款失败状态再次退款
     * @return array|\think\Config
     */
    public function reaudit()
    {
        $this->view->engine->layout(false);
        if(!input('?param.refund_id')){
            return error_code(13215);
        }
        $billRefundModel = new BillRefundModel();

        $where['refund_id'] = input('param.refund_id');
        $where['status'] = $billRefundModel::STATUS_FAIL;
        $info = $billRefundModel->where($where)->find();
        if(!$info){
            return error_code(13224);
        }


        return $billRefundModel->paymentRefund(input('param.refund_id'));

    }

}
