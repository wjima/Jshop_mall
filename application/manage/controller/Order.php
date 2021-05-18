<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillAftersales;
use app\common\model\BillDelivery;
use app\common\model\BillPayments;
use app\common\model\Order as OrderModel;
use app\common\model\OrderItems;
use app\common\model\OrderLog;
use app\common\model\Ship;
use app\common\model\Logistics;
use app\common\model\Store;
use think\facade\Request;

/**
 * 订单模块
 * Class Order
 * @package app\Manage\controller
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
        if (!Request::isAjax()) {
            //数据统计
            $input = [
                'ids' => '0,1,2,3,4,5,6,7'
            ];

            $model = new OrderModel();
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
        } else {
            $input = array(
                'order_id' => Request::param('order_id'),
                'username' => Request::param('username'),
                'ship_mobile' => Request::param('ship_mobile'),
                'order_unified_status' => Request::param('order_unified_status',0),
                'date' => Request::param('date'),
                'source' => Request::param('source'),
                'page' => Request::param('page'),
                'limit' => Request::param('limit'),
                'order_type' => Request::param('type'),
            );
            $model = new OrderModel();
            $data = $model->getListFromAdmin($input);

            if (count($data['data']) > 0) {
                foreach ($data['data'] as &$v) {
                    $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
                }
                $return_data = array(
                    'status' => true,
                    'msg' => '查询成功',
                    'count' => $data['count'],
                    'data' => $data['data']
                );
            } else {
                $return_data = array(
                    'status' => false,
                    'msg' => error_code(10036,true),
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
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function view($id)
    {
        $return = error_code(10037);
        $this->view->engine->layout(false);
        $orderModel = new OrderModel();
        $order_info = $orderModel->getOrderInfoByOrderID($id);
        $this->assign('order', $order_info);

        $orderLog = new OrderLog();
        $order_log = $orderLog->getOrderLog($id);
        if ($order_log['status']) {
            $this->assign('order_log', $order_log['data']);
        } else {
            $this->assign('order_log', []);
        }

        $return['status'] = true;
        $return['msg'] = '成功';
        $return['data'] = $this->fetch('view');
        return $return;
    }


    /**
     * 编辑订单
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];
        $this->view->engine->layout(false);
        $orderModel = new OrderModel();
        if (!Request::isPost()) {
            //订单信息
            if (!input('?param.id')) {
                // $result['msg'] = error_code(13100);
                return error_code(13100);
            }
            $order_info = $orderModel->getOrderInfoByOrderID(input('param.id'));
            if(!$order_info){
                return error_code(10002);
            }
            $this->assign('order', $order_info);

            $storeModel = new Store();
            $store_list = $storeModel->getAllList();
            $this->assign('store_list', $store_list);
            $result['status'] = true;
            $result['data'] = $this->fetch('edit');
            return $result;
        } else {
            return $orderModel->edit(input('param.'));
        }
    }

    //订单编辑的时候，显示订单明细商品
    public function editItemsList(){
        if(!input('?param.order_id')){
            return error_code(10000);
        }
        $orderItemsModel = new OrderItems();
        return $orderItemsModel->tableData(input('param.'));
    }

    //订单编辑的时候，添加订单明细
    public function editItemsAdd(){
        if(!input('?param.order_id')){
            return error_code(10000);
        }
        //货品编号ids
        if(!input('?param.ids')){
            return error_code(10000);
        }

        $orderItemsModel = new OrderItems();
        return $orderItemsModel->orderEditItemsAdd(input('param.order_id'), input('param.ids'));
    }
    //订单编辑的时候，删除订单明细
    public function editItemsDel(){
        if(!input('?param.order_id')){
            return error_code(10000);
        }
        //货品编号id
        if(!input('?param.items_id')){
            return error_code(10000);
        }

        $orderItemsModel = new OrderItems();
        return $orderItemsModel->orderEditItemsDel(input('param.order_id'), input('param.items_id'));
    }
    //订单编辑的时候，更新单价或者数量
    public function editItemsEdit(){
        if(!input('?param.order_id')){
            return error_code(10000);
        }
        //货品编号id
        if(!input('?param.items_id')){
            return error_code(10000);
        }
        if(!input('?param.price') && !input('?param.nums') && !input('?param.promotion_amount')){
            return error_code(10000);
        }
        $data = [];
        if(input('?param.price')){
            $data['price'] = input('param.price');
        }
        if(input('?param.nums')){
            $data['nums'] = input('param.nums');
        }
        if(input('?param.promotion_amount')){
            $data['promotion_amount'] = input('param.promotion_amount');
        }

        $orderItemsModel = new OrderItems();
        return $orderItemsModel->orderEditItemsEdit(input('param.order_id'), input('param.items_id'),$data);
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
        $return = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];
        $this->view->engine->layout(false);
        if (Request::isPost()) {
            $billDeliveryModel = new BillDelivery();
            $result = $billDeliveryModel->ship(
                input('param.order_id'),
                input('param.logi_code'),
                input('param.logi_no'),
                input('param.items'),
                input('param.store_id',0),
                input('param.ship_name',""),
                input('param.ship_mobile',""),
                input('param.ship_area_id',0),
                input('param.ship_address',""),
                input('param.memo', "")
            );
            return $result;
        }
        //订单发货信息
        if(!input('?param.order_id')){
            return error_code(13100);
        }else{
            $id = input('param.order_id');
        }
        $orderModel = new OrderModel();
        $order_info = $orderModel->getOrderShipInfo($id);
        if (!$order_info['status']) {
            return $order_info;
        }
        $this->assign('order', $order_info['data']);

        //如果是门店自提的话，取门店列表信息
        if($order_info['data']['store_id'] != 0){
            $storeModel = new Store();
            $stores = $storeModel->select();
        }else{
            $stores = [];
        }
        $this->assign('stores',$stores);


        //获取默认配送方式,为了on物流公司
        $shipModel = new Ship();
        $ship = $shipModel->where('id',$order_info['data']['logistics_id'])->find();
        if($ship){
            $ship_name = $ship['name'];
            $logi_code = $ship['logi_code'];
        }else{
            $ship_name = "";
            $logi_code = "";
        }
        $this->assign('ship_name', $ship_name);
        $this->assign('logi_code', $logi_code);

        //获取物流公司
        $logisticsModel = new Logistics();
        $logi_info = $logisticsModel->getAll();
        $this->assign('logi', $logi_info);
        $return['status'] = true;
        $return['data'] = $this->fetch('ship');
        return $return;
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
        $id = Request::param('id');
        if (!$id) {
            return [
                'status' => false,
                'msg' => error_code(13100,true),
                'data' => ''
            ];
        }
        $model = new OrderModel();
        $result = $model->cancel($id);
        if ($result) {
            $return_data = [
                'status' => true,
                'msg' => '操作成功',
                'data' => $result
            ];
        } else {
            return error_code(10037);
        }
        return $return_data;
    }


    /**
     * 完成订单
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function complete()
    {
        $id = Request::param('id');
        $model = new OrderModel();
        $result = $model->complete($id);
        if ($result) {
            $return_data = array(
                'status' => true,
                'msg' => '操作成功',
                'data' => $result
            );
        } else {
            return  error_code(10037);
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
        $model = new OrderModel();
        $result = $model->destroy($id);
        if ($result) {
            $return_data = array(
                'status' => true,
                'msg' => '删除成功',
                'data' => $result
            );
        } else {
            return  error_code(10023);
        }
        return $return_data;
    }


//    /**
//     * 根据条件从数据库查询数据或者api请求获取快递信息
//     * User:tianyu
//     * @return array
//     * @throws \think\db\exception\DataNotFoundException
//     * @throws \think\db\exception\ModelNotFoundException
//     * @throws \think\exception\DbException
//     */
//    public function logistics()
//    {
//        $return = [
//            'status' => false,
//            'msg' => error_code(10037,true),
//            'data' => ''
//        ];
//        $this->view->engine->layout(false);
//        $billDeliveryModel = new BillDelivery();
//        $id = Request::param('order_id', '');
//        $data = $billDeliveryModel->getLogisticsInformation($id);
//        $return['status'] = true;
//        $return['msg'] = '成功';
//        $return['data'] = $this->fetch('logistics', ['data' => $data]);
//        return $return;
//    }


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
        $order_id = Request::param('order_id/s', '');
        $type = Request::param('type/d', self::SHOPPING);

        if (!$order_id) {
            $this->error(error_code(10051,true));
        }
        $orderModel = new OrderModel();
        $order_info = $orderModel->getOrderInfoByOrderID($order_id);
        $this->assign('order', $order_info);
        $this->view->engine->layout(false);
        $shop_name = getSetting('shop_name');
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
            $return = error_code(10717);
            $logi_code = Request::param('logi_code/s', '');
            $logi_no = Request::param('logi_no/s', '');
            $bt = Request::param('bt/s', '1'); //按钮类型
            if (!$logi_code) {
                // $return['msg'] = error_code(13229, true);
                return error_code(13229);
            }
            $order_info['logi_code'] = $logi_code;
            $order_info['logi_no'] = $logi_no;
            $order_info['bt'] = $bt;

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
            'msg' => error_code(10051,true),
            'data' => '',
            'status' => false
        ];
        if (!Request::isPost()) {
            $this->view->engine->layout(false);
            $order_id = Request::param('order_id');
            $this->assign('order_id', $order_id);

            //默认快递公司
            $orderModel = new OrderModel();
            $order_info = $orderModel->getOrderInfoByOrderID($order_id);
            $this->assign('order_info', $order_info);

            $ship['logi_code'] = $order_info['logistics']['logi_code'];
            $ship['logi_no'] = '';
            //获取是否获取电子面板
            if (!checkAddons('getPrintExpressInfo')) {
                $this->error(error_code(10717,true));
                return;
            }
            $print_express = hook('getPrintExpressInfo', ['order_id' => $order_id]);
            if ($print_express[0]['status']) {
                $ship['logi_code'] = $print_express[0]['data']['shipper_code'];
                $ship['logi_no'] = $print_express[0]['data']['logistic_code'];
            }
            $this->assign('ship', $ship);

            //获取物流公司
            $logisticsModel = new Logistics();
            $logi_info = $logisticsModel->getAll();
            $this->assign('logi', $logi_info);
            $return['status'] = true;
            $return['msg'] = '成功';
            $return['data'] = $this->fetch('print_form');
            return $return;
        }
    }


    /**
     * 存储卖家备注
     * @return array
     */
    public function saveMark()
    {
        $orderModel = new OrderModel();
        $order_id = Request::param('id');
        $mark = Request::param('mark', '');
        return $orderModel->saveMark($order_id, $mark);
    }

    public function aftersales(){
        $this->view->engine->layout(false);
        $order_id = input('order_id','');
        if(!$order_id) return error_code(10003);
        $aftersalesModel = new BillAftersales();
        if(request()->isPost()){
            $data = input('post.');
            $items = [];
            if(input('?post.order_items_id') && input('?post.aftersaleeItems')){
                $order_items_id = input('post.order_items_id/a');
                $aftersaleeItems = input('post.aftersaleeItems/a');
                foreach($order_items_id as $k => $v){
                    if($aftersaleeItems[$k]){
                        $items[$k] = $aftersaleeItems[$k];
                    }
                }
            }
            return $aftersalesModel->manageSave($order_id,$data,$items);
        }
        $info = $aftersalesModel->manageAdd($order_id);
        if(!$info['status']) return $info;
        $info = $info['data'];
        $this->assign("info",$info);
        $this->assign('order_items',$info['items']);
        return [
            'data'=>$this->fetch('bill_aftersales/add',[
                'info'=>$info,
                'order_items'=>$info['items']
            ]),
            'status'=>true
        ];
    }
}