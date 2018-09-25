<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use Request;

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
            $page = input('page', 1);
            $limit = input('limit', 20);
            $input['delivery_id'] = input('delivery_id');
            $input['order_id'] = input('order_id');
            $input['logi_no'] = input('logi_no');
            $input['mobile'] = input('mobile');
            $billDeliveryModel = new \app\common\model\BillDelivery();
            $res = $billDeliveryModel->getList($page, $limit, $input);
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
        $res = model('common/BillDelivery')->getDeliveryInfo($delivery_id);
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
