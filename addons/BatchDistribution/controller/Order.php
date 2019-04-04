<?php

namespace addons\BatchDistribution\controller;

use app\common\model\Order as OrderModel;
use myxland\addons\library\AddonController;

class Order extends AddonController
{
    /**
     * 批量打印
     * @return mixed|void
     */
    public function batchPrint()
    {
        $ids = input('ids/s', '');
        if (!$ids) {
            echo '关键参数错误';
            return;
        }
        $list   = explode(',', $ids);
        $orders = [];
        foreach ((array)$list as $key => $value) {
            $orderModel   = new OrderModel();
            $orders[$key] = $orderModel->getOrderInfoByOrderID($value, false, false);
        }
        $this->assign('orders', $orders);
        $shop_name   = getSetting('shop_name');
        $shop_mobile = getSetting('shop_mobile');
        $this->assign('shop_name', $shop_name);
        $this->assign('shop_mobile', $shop_mobile);
        return $this->fetch('distribution');
    }

}