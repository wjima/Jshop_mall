<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\BillDelivery;
use app\common\model\BillPayments;
use app\common\model\Order as Model;
use app\common\model\OrderItems;
use app\common\model\OrderLog;
use app\common\model\Ship;
use app\common\model\Logistics;
use app\common\model\Store;
use think\facade\Request;

/**
 * 订单模块
 * Class Order
 * @package app\seller\controller
 * @author keinx
 */
class Order extends Manage
{

    const SHOPPING = 1;//购物清单
    const DISTRIBUTION = 2;//配货单
    const UNION = 3;//联合打印
    const EXPRESS = 4;//联合打印

    /**
     * 订单列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
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

            $model = new Model();
            $count = $model->getOrderStatusNum($input);
            $counts = [
                'all' => $count[0],
                'payment' => $count[1],
                'delivered' => $count[2],
                'receive' => $count[3],
                'evaluated' => $count[4],
                'no_evaluat' => $count[5],
                'cancel' => $count[7],
                'complete' => $count[6]
            ];
            $this->assign('count', $counts);

            return $this->fetch('index');
        }
        else
        {
            $input = array(
                'order_id' => Request::param('order_id'),
                'username' => Request::param('username'),
                'ship_mobile' => Request::param('ship_mobile'),
                'order_unified_status' => Request::param('order_unified_status'),
                'date' => Request::param('date'),
                'source' => Request::param('source'),
                'page' => Request::param('page'),
                'limit' => Request::param('limit')
            );
            $model = new Model();
            $data = $model->getListFromAdmin($input);

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
        $orderModel = new Model();
        $order_info = $orderModel->getOrderInfoByOrderID($id, false, false);
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $this->view->engine->layout(false);
        $orderModel = new Model();
        if(!Request::isPost())
        {
            //订单信息
            $id = Request::param('id');
            $order_info = $orderModel->getOrderInfoByOrderID($id);
            $this->assign('order', $order_info);

            $order_type = Request::param('order_type');
            $this->assign('order_type', $order_type);

            $storeModel = new Store();
            $store_list = $storeModel->getAllList();
            $this->assign('store_list', $store_list);

            return $this->fetch('edit');
        }
        else
        {
            $data = Request::param();
            $result = $orderModel->edit($data);
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
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ship()
    {
        $this->view->engine->layout(false);
        if(!Request::isPost())
        {
            //订单发货信息
            $id = Request::param('order_id');
            $model = new Model();
            $order_info = $model->getOrderShipInfo($id);
            $this->assign('order', $order_info);

            //获取默认快递公司
            $shipModel = new Ship();
            $ship = $shipModel->get($order_info['logistics_id']);
            $this->assign('ship', $ship);

            //获取物流公司
            $logisticsModel = new Logistics();
            $logi_info = $logisticsModel->getAll();
            $this->assign('logi', $logi_info);

            return $this->fetch('ship');
        }
        else
        {
            $data = input('param.');
            $billDeliveryModel = new BillDelivery();
            $result = $billDeliveryModel->ship($data['order_id'], $data['logi_code'], $data['logi_no'], $data['memo'], $data['ship_data']);
            return $result;
        }
    }


    /**
     * 取消订单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cancel()
    {
        $id = input('post.id');
        if(!$id){
            return [
                'status' => false,
                'msg' => '操作失败',
                'data' => ''
            ];
        }
        $model = new Model();
        $result = $model->cancel($id);
        if($result)
        {
            $return_data = [
                'status' => true,
                'msg' => '操作成功',
                'data' => $result
            ];
        }
        else
        {
            $return_data = [
                'status' => false,
                'msg' => '操作失败',
                'data' => $result
            ];
        }
        return $return_data;
    }


    /**
     * 完成订单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function complete()
    {
        $id = Request::param('id');
        $model = new Model();
        $result = $model->complete($id);
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
        $id = Request::param('id');
        $model = new Model();
        $result = $model->destroy($id);
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
        $data = $billDeliveryModel->getLogisticsInformation(input('param.order_id',''));
        return $this->fetch('logistics',[ 'data' => $data ]);
    }


    /**
     * 数据统计
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function statistics()
    {
        $bpModel = new BillPayments();
        $bdModel = new BillDelivery();
        $payres = $bpModel->statistics();
        $deliveryres = $bdModel->statistics();

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


    /**
     * 订单打印
     * @return array|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function print_tpl()
    {
        $order_id = input('order_id/s', '');
        $type     = input('type/d', self::SHOPPING);

        if (!$order_id) {
            $this->error("关键参数丢失");
        }
        $orderModel = new Model();
        $order_info = $orderModel->getOrderInfoByOrderID($order_id, false, false);
        $this->assign('order', $order_info);
        $this->view->engine->layout(false);
        $shop_name   = getSetting('shop_name');
        $shop_mobile = getSetting('shop_mobile');
        $this->assign('shop_name', $shop_name);
        $this->assign('shop_mobile', $shop_mobile);

        if ($type == self::SHOPPING) {//购物清单
            return $this->fetch('shopping');
        } elseif ($type == self::DISTRIBUTION) {//配货单
            return $this->fetch('distribution');
        } elseif ($type == self::UNION) {
            return $this->fetch('union');
        } elseif ($type == self::EXPRESS) {
            $return = [
                'msg'    => '请先安装快递打印插件',
                'data'   => '',
                'status' => false
            ];
            $logi_code = input('logi_code/s', '');
            $logi_no   = input('logi_no/s', '');
            $bt        = input('bt/s', '1');//按钮类型
            if (!$logi_code) {
                $return['msg'] = '快递公司编码不能为空';
                return $return;
            }
            $order_info['logi_code'] = $logi_code;
            $order_info['logi_no']   = $logi_no;
            $order_info['bt']        = $bt;

            if (!checkAddons('printOrder')) {
                return $return;
            } else {
                $res = hook("printOrder", $order_info);
                return is_array($res) ? $res[0] : '';
            }
        }
    }


    /**
     * 打印快递单时，先选择快递公司
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function print_form()
    {
        $return = [
            'msg'    => '关键参数错误',
            'data'   => '',
            'status' => false
        ];
        if (!Request::isPost()) {
            $this->view->engine->layout(false);
            $order_id = input('order_id');
            $this->assign('order_id', $order_id);

            //默认快递公司
            $orderModel = new Model();
            $order_info = $orderModel->getOrderInfoByOrderID($order_id, false, false);
            $this->assign('order_info', $order_info);

            $ship['logi_code'] = $order_info['logistics']['logi_code'];
            $ship['logi_no']   = '';
            //获取是否获取电子面板
            if (!checkAddons('getPrintExpressInfo')) {
                $this->error("请先安装快递打印插件");return;
            }
            $print_express = hook('getPrintExpressInfo', ['order_id' => $order_id]);
            if ($print_express[0]['status']) {
                $ship['logi_code'] = $print_express[0]['data']['shipper_code'];
                $ship['logi_no']   = $print_express[0]['data']['logistic_code'];
            }
            $this->assign('ship', $ship);

            //获取物流公司
            $logi_info = model('common/Logistics')->getAll();
            $this->assign('logi', $logi_info);
            return $this->fetch('print_form');
        }
    }


    /**
     * 存储卖家备注
     * @return array
     */
    public function saveMark()
    {
        $orderModel = new Model();
        $order_id = Request::param('id');
        $mark = Request::param('mark', '');
        return $orderModel->saveMark($order_id, $mark);
    }
}