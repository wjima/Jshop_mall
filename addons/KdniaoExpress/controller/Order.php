<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11/0011
 * Time: 下午 15:48
 */
namespace addons\KdniaoExpress\controller;

use addons\KdniaoExpress\KdniaoExpress;
use myxland\addons\library\AddonController;

class Order extends AddonController
{

    public function batchPrint()
    {

        $ids        = input('ids/s', '');
        $page       = input('page/d', 1);
        $logi_code  = input('logi_code/s', '');
        $list       = explode(",", $ids);
        $total_page = count($list);
        $this->assign('page', $page);
        $this->assign('total_page', $total_page);
        $this->assign('data', $ids);
        $this->assign('logi_code', $logi_code);
        return $this->fetch('batchprint');
    }

    /**
     * 翻页处理
     * @return array
     */
    public function nextPrint()
    {

        $return = [
            'msg'    => '操作成功',
            'status' => true,
            'data'   => [],
        ];

        $page       = input('page/d', 1);
        $total_page = input('total_page/d', '1');
        $ids        = input('ids/s', '');
        $logi_code  = input('logi_code/s', '');
        $list       = explode(",", $ids);
        $order_id   = $list[$page - 1];
        if (!$order_id) {
            $return['status'] = false;
            $return['msg']    = '关键参数丢失';
            return $return;
        }
        $orderModel              = new \app\common\model\Order();
        $order_info              = $orderModel->getOrderInfoByOrderID($order_id);
        $order_info['logi_code'] = $logi_code;

        $kdniaoExpress = new KdniaoExpress();
        $res           = $kdniaoExpress->createKdApiEOrder($order_info);
        if (!$res['status']) {
            return $res;
        }
        $percent                      = sprintf("%.2f", ($page / $total_page) * 100);
        $page                         = $page + 1;
        $return['data']['data']       = $ids;
        $return['data']['percent']    = $percent;
        $return['data']['page']       = $page;
        $return['data']['total_page'] = $total_page;
        return $return;
    }

    /**
     * 打印订单
     */
    public function printExpress()
    {
        $ids = input('ids/s', '');
        if (!$ids) {
            echo '关键参数错误，请重试';
            exit();
        }
        $kdniaoExpress = new KdniaoExpress();
        $kdniaoExpress->printExpress($ids);
    }
}