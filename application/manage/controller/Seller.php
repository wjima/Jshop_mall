<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillPayments as BillPaymentsModel;
use app\common\model\Seller as SellerModel;
use Request;

class Seller extends Manage
{
    public function index()
    {
        if(Request::isAjax()){
            $sellerModel = new SellerModel();
            $data = input('param.');
            return $sellerModel->tableData($data);
        }
        return $this->fetch('index');
    }
    //商户编辑
    public function edit()
    {
        if(!input('?param.id')){
            return error_code(10000);
        }
        $sellerModel = new SellerModel();
        if(Request::isPost()){
            $data = input('param.');
            if(isset($data['expire_date'])){
                $data['expire_date'] = strtotime($data['expire_date']);
            }
            $sellerModel->allowField(true)->save($data,['id'=>input('param.id')]);
            return [
                'status' => true,
                'data' => '',
                'msg' => ''
            ];
        }
        $info = $sellerModel::with('userInfo')->where(['id'=>input('param.id')])->find();
        if($info){
            $this->assign('info',$info);
        }else{
            $this->error('没有找到此商户');
        }

        return $this->fetch('edit');

    }


}
