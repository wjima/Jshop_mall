<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/20 0020
 * Time: 22:00
 */
namespace app\api\controller;

use app\common\controller\Api;

use app\common\model\Store as storeModel;

class Store extends Api
{
    //商户获取门店列表详情接口

    public function getStore()
    {
        $storeModel = new storeModel();
        return $storeModel->storeList($this->sellerId);
    }
}