<?php
namespace app\seller\controller;
use app\common\controller\Seller;
use app\common\model\SellerSetting;
use Request;


class Setting extends Seller
{
    public function index()
    {
        $sellerSettingModel = new SellerSetting();
        if(Request::isAjax()){
            foreach(input('param.') as $k => $v){
                $result = $sellerSettingModel->setValue($this->sellerId, $k, $v);
                //如果出错，就返回，如果是没有此参数，就默认跳过
                if(!$result['status'] && $result['data'] != 10008){
                    return $result;
                }
            }
            $result = array(
                'status' => true,
                'data' => [],
                'msg' => '保存成功'
            );
            return $result;
        }else{
            $data = $sellerSettingModel->getAll($this->sellerId);
            $this->assign('data', $data);
            return $this->fetch();
        }

    }
}