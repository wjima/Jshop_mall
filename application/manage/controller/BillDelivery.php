<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillDeliveryItems;
use app\common\model\BillDeliveryOrderRel;
use think\facade\Request;

/**
 * 发货单
 * Class BillDelivery
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
            $page = Request::param('page', 1);
            $limit = Request::param('limit', 20);
            $input['delivery_id'] = Request::param('delivery_id');
            $input['order_id'] = Request::param('order_id');
            $input['logi_no'] = Request::param('logi_no');
            $input['mobile'] = Request::param('mobile');
            $input['date'] = Request::param('date');
            $billDeliveryModel = new \app\common\model\BillDelivery();
            $res = $billDeliveryModel->getList($page, $limit, $input);
            if($res['status'])
            {
                foreach($res['data'] as $k => &$v)
                {
                    $v['logi_name'] = get_logi_info($v['logi_code']);
                    $v['ship_address'] = get_area($v['ship_area_id']).'- '.$v['ship_address'];
                    $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
                }
            }
            return $res;
        }

        return $this->fetch('index');
    }


    /**
     * 查看发货单详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function view()
    {
        $this->view->engine->layout(false);
        $delivery_id = Request::param('delivery_id');
        $billDeliveryModel = new \app\common\model\BillDelivery();
        $res = $billDeliveryModel->getDeliveryInfo($delivery_id);
        if($res['status'])
        {
            //主信息
            $res['data']['logi_name'] = get_logi_info($res['data']['logi_code']);
            $res['data']['ship_address'] = get_area($res['data']['ship_area_id']).'- '.$res['data']['ship_address'];
            $res['data']['ctime'] = date('Y-m-d H:i:s', $res['data']['ctime']);
            $this->assign('data', $res['data']);

            //发货明细
            $delivery_id = Request::param('delivery_id');
            $billDeliveryItemsModel = new BillDeliveryItems();
            $items = $billDeliveryItemsModel->getList($delivery_id);
            $this->assign('items', $items['data']);

            //订单信息
            $delivery_id = Request::param('delivery_id');
            $billDeliveryOrderRelModel = new BillDeliveryOrderRel();
            $orders = $billDeliveryOrderRelModel->getOrderListByDeliveryId($delivery_id);
            $this->assign('orders', $orders);

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
}
