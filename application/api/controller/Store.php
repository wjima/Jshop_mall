<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
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