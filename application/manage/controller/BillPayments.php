<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillPayments as BillPaymentsModel;
use app\common\model\Payments;
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
    public function pay()
    {
        $result = [
            'status' => false,
            'data' => array(),
            'msg' => ''
        ];
        if(!input('?param.order_id')){
            $result['msg'] = "请输入订单编号";
            return $result;
        }

        if(!input('?param.type')){
            $result['msg'] = "订单类型不能为空";
            return $result;
        }
        $BillPaymentsModel = new BillPaymentsModel();
        $result = $BillPaymentsModel->formatPaymentRel(input('param.order_id'), input('param.type'));
        if(!$result['status']){
            return $result;
        }
        //渲染界面，并输出
        $this->view->engine->layout(false);

        //取支付方式
        $paymentModel = new Payments();
        $result['data']['payments'] = $paymentModel->getList(0);

        //取之前传入的值
        $this->assign('order_id', input('order_id'));
        $this->assign('type', input('type'));

        $this->assign('data',$result['data']);

        $result['data']['tpl'] = $this->fetch();

        return $result;
    }
    public function toPay()
    {
        //卖家端直接支付的话，先生成支付单，然后去做付款操作
        $result = [
            'status' => true,
            'data' => array(),
            'msg' => ''
        ];
        if(!input('?param.order_id')){
            $result['msg'] = "请输入订单编号";
            return $result;
        }

        if(!input('?param.type')){
            $result['msg'] = "订单类型不能为空";
            return $result;
        }

        //::todo 校验支付方式是否存在

        //生成支付单
        $BillPaymentsModel = new BillPaymentsModel();
        $result = $BillPaymentsModel->toAdd(input('param.order_id'), input('param.payment_code'), '', input('param.type'));
        if(!$result['status']){
            return $result;
        }

        //支付单支付
        return $BillPaymentsModel->toUpdate($result['data']['payment_id'], $BillPaymentsModel::STATUS_PAYED, $result['data']['payment_code']);
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
