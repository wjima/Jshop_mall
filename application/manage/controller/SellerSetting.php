<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\SellerSetting as SellerSettingModel;
use Request;


class SellerSetting extends Manage
{
    public function index()
    {
        $sellerSettingModel = new SellerSettingModel();
        if(Request::isAjax()){

            return $sellerSettingModel->tableData(input('param.'));
        }else{
            $this->assign('sellerList',getSellerList());
            $this->assign('skeys',$sellerSettingModel->skeys);
            return $this->fetch('index');
        }
    }
    public function edit()
    {
        if(!input('?param.seller_id') || !input('?param.skey') || !input('?param.value')){
            return error_code(10000);
        }
        $sellerSettingModel = new SellerSettingModel();
        return $sellerSettingModel->setValue(input('param.seller_id'), input('param.skey'), input('param.value'));
    }
}