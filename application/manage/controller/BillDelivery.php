<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Seller;
use Request;

/**
 * 发货单
 * Class BillDelivery
 * @package app\seller\controller
 */
class BillDelivery extends Manage
{
    /**
     * 发货单列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax())
        {
            $seller_id = input('seller_id', false);
            $page = input('page', 1);
            $limit = input('limit', 20);
            $input['delivery_id'] = input('delivery_id');
            $input['order_id'] = input('order_id');
            $input['logi_no'] = input('logi_no');
            $input['mobile'] = input('mobile');
            $res = model('Common/BillDelivery')->getList($seller_id, $page, $limit, $input);
            if($res['status'])
            {
                foreach($res['data'] as $k => &$v)
                {
                    $v['username'] = get_user_info($v['user_id'], 'nickname');
                    $v['logi_name'] = get_logi_info($v['logi_code']);
                    $v['ship_address'] = get_area($v['ship_area_id']).'- '.$v['ship_address'];
                    $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
                }
            }
            return $res;
        }
        else
        {
            //所属商户
            $seller = new Seller();
            $seller_list = $seller->getAllSellerList();
            $this->assign('seller_list', $seller_list);
        }
        return $this->fetch('index');
    }


    /**
     * 查看发货单详情
     * @return array
     */
    public function view()
    {
        $this->view->engine->layout(false);
        $delivery_id = input('delivery_id');
        $seller_id = $this->sellerId;
        $res = model('common/BillDelivery')->getDeliveryInfo($delivery_id, $seller_id);
        if($res['status'])
        {
            $res['data']['username'] = get_user_info($res['data']['user_id'], 'nickname');
            $res['data']['logi_name'] = get_logi_info($res['data']['logi_code']);
            $res['data']['ship_address'] = get_area($res['data']['ship_area_id']).'- '.$res['data']['ship_address'];
            $res['data']['ctime'] = date('Y-m-d H:i:s', $res['data']['ctime']);
            $this->assign('data', $res['data']);
            $temp = $this->fetch('view');
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $temp
            ];
        }
        else
        {
            $return_data = [
                'status' => false,
                'msg' => '获取失败',
                'data' => ''
            ];
        }
        return $return_data;
    }



    public function items()
    {
        $delivery_id = input('delivery_id');

        $res = model('common/BillDeliveryItems')->getList($delivery_id);

        if($res['status'])
        {
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res['data']
            ];
        }
        else
        {
            $return_data = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $res['data']
            ];
        }
        return $return_data;
    }
}
