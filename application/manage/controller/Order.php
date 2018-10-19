<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\BillDelivery;
use app\common\model\OrderItems;
use app\common\model\OrderLog;
use Request;

/**
 * 订单模块
 * Class Order
 * @package app\seller\controller
 * @author keinx
 */
class Order extends Manage
{
    /**
     * 订单列表
     * @return array|mixed
     */
    public function index()
    {
        if(!Request::isAjax())
        {
            //订单来源
            $source = config('params.order')['source'];
            $this->assign('source', $source);

            //数据统计
            $input = [
                'ids' => '0,1,2,3,4,5,6,7'
            ];

            $count = model('common/Order')->getOrderStatusNum($input);
            $counts = [
                'all' => $count[0],
                'payment' => $count[1],
                'delivered' => $count[2],
                'receive' => $count[3],
                'evaluated' => $count[4],
                'no_evaluat' => $count[5],
                'cancel' => $count[6],
                'complete' => $count[7]
            ];
            $this->assign('count', $counts);

            return $this->fetch('index');
        }
        else
        {
            $input = array(
                'order_id' => input('order_id'),
                'username' => input('username'),
                'ship_mobile' => input('ship_mobile'),
                'order_unified_status' => input('order_unified_status'),
                'date' => input('date'),
                'source' => input('source'),
                'page' => input('page'),
                'limit' => input('limit')
            );
            $data = model('common/Order')->getListFromAdmin($input);

            if(count($data['data']) > 0)
            {
                foreach ($data['data'] as &$v)
                {
                    $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
                }
                $return_data = array(
                    'status' => true,
                    'msg' => '查询成功',
                    'count' => $data['count'],
                    'data' => $data['data']
                );
            }
            else
            {
                $return_data = array(
                    'status' => false,
                    'msg' => '没有符合的订单',
                    'count' => $data['count'],
                    'data' => $data['data']
                );
            }
            return $return_data;
        }
    }

    /**
     * 查看订单详情
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function view($id)
    {
        $this->view->engine->layout(false);
        $orderModel = new \app\common\model\Order();
        $order_info = $orderModel->getOrderInfoByOrderID($id);
        $this->assign('order', $order_info);

        $orderLog = new OrderLog();
        $order_log = $orderLog->getOrderLog($id);
        if($order_log['status'])
        {
            $this->assign('order_log', $order_log['data']);
        }
        else
        {
            $this->assign('order_log', []);
        }

        return $this->fetch('view');
    }


    /**
     * 编辑订单
     * @return array|mixed
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        if(!Request::isPost())
        {
            //订单信息
            $id = input('id');
            $order_info = model('common/Order')->getOrderInfoByOrderID($id);
            $this->assign('order', $order_info);

            $order_type = input('order_type');
            $this->assign('order_type', $order_type);

            return $this->fetch('edit');
        }
        else
        {
            $data = input('param.');
            $result = model('common/Order')->edit($data);
            if($result)
            {
                $return_data = array(
                    'status' => true,
                    'msg' => '编辑成功',
                    'data' => $result
                );
            }
            else
            {
                $return_data = array(
                    'status' => false,
                    'msg' => '编辑失败',
                    'data' => $result
                );
            }
            return $return_data;
        }
    }


    /**
     * 订单发货
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function ship()
    {
        $this->view->engine->layout(false);
        if(!Request::isPost())
        {
            //订单发货信息
            $id = input('order_id');
            $order_info = model('common/Order')->getOrderShipInfo($id);
            $this->assign('order', $order_info);

            //获取默认快递公司
            $ship = model('common/ship')->get($order_info['logistics_id']);
            $this->assign('ship', $ship);

            //获取物流公司
            $logi_info = model('common/Logistics')->getAll();
            $this->assign('logi', $logi_info);

            return $this->fetch('ship');
        }
        else
        {
            $data = input('param.');
            $result = model('common/BillDelivery')->ship($data['order_id'], $data['logi_code'], $data['logi_no'], $data['memo'], $data['ship_data']);
            return $result;
        }
    }


    /**
     * 取消订单
     * @return array
     */
    public function cancel()
    {
        $id = input('id');
        $seller_id = $this->sellerId;
        $result = model('common/Order')->cancel($id, $seller_id);
        if($result)
        {
            $return_data = array(
                'status' => true,
                'msg' => '操作成功',
                'data' => $result
            );
        }
        else
        {
            $return_data = array(
                'status' => false,
                'msg' => '操作失败',
                'data' => $result
            );
        }
        return $return_data;
    }


    /**
     * 完成订单
     * @return array
     */
    public function complete()
    {
        $id = input('id');
        $result = model('common/Order')->complete($id);
        if($result)
        {
            $return_data = array(
                'status' => true,
                'msg' => '操作成功',
                'data' => $result
            );
        }
        else
        {
            $return_data = array(
                'status' => false,
                'msg' => '操作失败',
                'data' => $result
            );
        }
        return $return_data;
    }


    /**
     * 删除订单数据（软删除）
     * @return array
     */
    public function del()
    {
        $id = input('id');
        $result = model('common/Order')->destroy($id);
        if($result)
        {
            $return_data = array(
                'status' => true,
                'msg' => '删除成功',
                'data' => $result
            );
        }
        else
        {
            $return_data = array(
                'status' => false,
                'msg' => '删除失败',
                'data' => $result
            );
        }
        return $return_data;
    }


    /**
     * 根据条件从数据库查询数据或者api请求获取快递信息
     * User:tianyu
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function logistics()
    {
        $this->view->engine->layout(false);
        $billDeliveryModel = new BillDelivery();
        $data = $billDeliveryModel->getLogisticsInformation(input('param.order_id/d'));
        return $this->fetch('logistics',[ 'data' => $data ]);
    }


    /**
     * 数据统计
     * @return array
     */
    public function statistics()
    {
        $payres = model('common/BillPayments')->statistics();
        $deliveryres = model('common/BillDelivery')->statistics();

        $data = [
            'legend' => [
                'data' => ['已支付', '已发货']
            ],
            'xAxis' => [
                [
                    'type' => 'category',
                    'data' => $payres['day']
                ]
            ],
            'series' => [
                [
                    'name' => '已支付',
                    'type' => 'line',
                    'data' => $payres['data']
                ],
                [
                    'name' => '已发货',
                    'type' => 'line',
                    'data' => $deliveryres
                ]
            ]
        ];
        return $data;
    }
}