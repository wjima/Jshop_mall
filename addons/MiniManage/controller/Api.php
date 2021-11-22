<?php

namespace addons\MiniManage\controller;

use addons\MiniManage\model\AdminMessage;
use addons\MiniManage\model\AdminMessageCenter;
use addons\MiniManage\model\AdminToken;
use app\common\model\BillAftersales;
use app\common\model\BillAftersalesImages;
use app\common\model\BillAftersalesItems;
use app\common\model\BillDelivery;
use app\common\model\BillReship;
use app\common\model\Logistics;
use app\common\model\Order;
use app\common\model\Ship;
use app\common\model\Store;
use app\common\model\User;
use org\Curl;
use think\facade\Request;
use app\common\model\BillRefund;
use app\common\model\OrderItems;
use app\common\model\Payments;


class Api extends \app\common\controller\Api
{
    private $config = [];

    function __construct()
    {
        if (!input('?param.token')) {
            $this->config["result"] = error_code(14006);
        } else {
            $Model = new AdminToken();
            $result = $Model->checkToken(input('param.token'));
            if (!$result['status']) {
                $this->config["result"] = error_code(14007);
            } else {
                $this->config["result"]["status"] = true;
                $this->config["admin_id"] = $result['data']['admin_id'];
            }
        }
    }

    //首页
    public function index()
    {
        if (!$this->config["result"]["status"]) {
            return json($this->config["result"]);
        }
        $orderMode = new Order();
        //今日订单量
        $where = [];
        $where[] = ["ctime", ">", strtotime(date('Y-m-d', time()))];
        $list = [];
        $list = $orderMode->field("order_id,order_amount")->where($where)->select();
        $data["todayorder"] = count($list);
        $data["todayordermoney"] = 0;
        foreach ($list as $k => $v) {
            $data["todayordermoney"] += $v["order_amount"];
        }
        $data["todayordermoney"] = sprintf("%01.2f", $data["todayordermoney"]);
        //七日订单量
        $where = [];
        $where[] = ["ctime", ">", strtotime('-7 days')];
        $list = [];
        $list = $orderMode->field("order_id,order_amount")->where($where)->select();
        $data["sevendayorder"] = count($list);
        $data["sevendayordermoney"] = 0;
        foreach ($list as $k => $v) {
            $data["sevendayordermoney"] += $v["order_amount"];
        }
        $data["sevendayordermoney"] = sprintf("%01.2f", $data["sevendayordermoney"]);
        //总订单量
        $list = [];
        $list = $orderMode->field("order_id,order_amount")->select();
        $data["alldayorder"] = count($list);
        $data["allordermoney"] = 0;
        foreach ($list as $k => $v) {
            $data["allordermoney"] += $v["order_amount"];
        }
        $data["allordermoney"] = sprintf("%01.2f", $data["allordermoney"]);
        $data["daishiporder"] = $orderMode->field("order_id")->where("pay_status", "neq", 1)->where("ship_status", "in", [1,2])->where("status","eq",1)->count();
        $data["daipayorder"] = $orderMode->field("order_id")->where("pay_status", "eq", 1)->where("status","eq",1)->count();
        //会员数量
        $UserModel = new User();
        $data["todayusernum"] = $UserModel->field("id")->where("ctime", ">", strtotime(date('Y-m-d', time())))->count();
        $data["usernum"] = $UserModel->field("id")->count();
        //售后数量
        $afterModel = new BillAftersales();
        $data["todayafter"] = $afterModel->field("aftersales_id")->where("ctime", ">", strtotime(date('Y-m-d', time())))->count();
        $data["daiafter"] = $afterModel->field("aftersales_id")->where("status", "eq", 1)->count();
        $data["allafter"] = $afterModel->field("aftersales_id")->count();
        $data["images"] = $this->getimages();
        $return = [
            "status" => true,
            "msg" => "获取成功",
            "data" => $data
        ];
        return json($return);
    }

    /**
     * 获取订单列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getorderList()
    {
        if (!$this->config["result"]["status"]) {
            return json($this->config["result"]);
        }
        $input = [
            'status' => Request::param('status'),
            'page' => Request::param('page'),
            'limit' => Request::param('limit'),
            'keyword' => Request::param('keyword'),
        ];
        $model = new Order();
        $data = $model->getListFromWxApi($input);
        $return_data = array(
            'status' => true,
            'msg' => '获取成功',
            'data' => array(
                'list' => $data['data'],
                'count' => $data['count'],
                'page' => $input['page'],
                'limit' => $input['limit'],
                'status' => $input['status']
            )
        );
        return json($return_data);
    }

    /**
     * 获取订单详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details()
    {
        if (!$this->config["result"]["status"]) {
            return json($this->config["result"]);
        }
        $order_id = Request::param('order_id');
        $model = new Order();
        $result = $model->getOrderInfoByOrderID($order_id);
        if ($result) {
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $result
            ];
        } else {
            $return_data = [
                'status' => false,
                'msg' => error_code(10025, true),
                'data' => $result
            ];
        }
        return json($return_data);
    }

    /**
     * 订单发货信息
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ship()
    {
        if (!$this->config["result"]["status"]) {
            return json($this->config["result"]);
        }
        $return = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];
        //订单发货信息
        if (!input('?param.order_id')) {
            return json(error_code(13100));
        } else {
            $id = input('param.order_id');
        }
        $orderModel = new Order();
        $order_info = $orderModel->getOrderShipInfo($id);
        if (!$order_info['status']) {
            return json($order_info);
        }
        $order_info['data']["ship_area_name"] = get_area($order_info['data']["ship_area_id"]);
        $newdata["order"] = $order_info['data'];
        $orderdata = $orderModel->field("goods_amount,order_amount,order_pmt,goods_pmt")->where("order_id", "eq", $id)->find();
        $newdata["order"]["goods_amount"] = $orderdata["goods_amount"];
        $newdata["order"]["order_amount"] = $orderdata["order_amount"];
        $newdata["order"]["order_pmt"] = $orderdata["order_pmt"];
        $newdata["order"]["goods_pmt"] = $orderdata["goods_pmt"];

        //如果是门店自提的话，取门店列表信息
        if ($order_info['data']['store_id'] != 0) {
            $storeModel = new Store();
            $stores = $storeModel->select();
        } else {
            $stores = [];
        }
        $newdata["stores"] = $stores;

        //获取默认配送方式,为了on物流公司
        $shipModel = new Ship();
        $ship = $shipModel->where('id', $order_info['data']['logistics_id'])->find();
        if ($ship) {
            $ship_name = $ship['name'];
            $logi_code = $ship['logi_code'];
        } else {
            $ship_name = "";
            $logi_code = "";
        }
        $newdata["ship_name"] = $ship_name;
        $newdata["logi_code"] = $logi_code;

        //获取物流公司
        $logisticsModel = new Logistics();
        $logi_info = $logisticsModel->getAll();

        $newdata["logi"] = $logi_info;
        $return['status'] = true;
        $return['data'] = $newdata;
        return json($return);
    }

    /**
     * 订单去发货
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function toship()
    {
        if (!$this->config["result"]["status"]) {
            return json($this->config["result"]);
        }
        $order_id = input('param.order_id');
        $model = new \app\common\model\Order();
        $resultinfo = $model->getOrderInfoByOrderID($order_id);
        $items = [];
        foreach ($resultinfo["items"] as $k => $v) {
            $items[$v["product_id"]] = $v["nums"];
        }
        $billDeliveryModel = new \app\common\model\BillDelivery();
        $result = $billDeliveryModel->ship(
            $order_id,
            input('param.logi_code'),
            input('param.logi_no'),
            $items,
            $resultinfo["store_id"],
            input('param.ship_name', ""),
            input('param.ship_mobile', ""),
            input('param.ship_area_id', 0),
            input('param.ship_address', ""),
            input('param.memo', ""),
            input('seller_id')
        );
        return json($result);
    }

    /**
     * 信息列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function msmlist()
    {
        if (!$this->config["result"]["status"]) {
            return json($this->config["result"]);
        }
        $limit = input("limit");
        $page = input("page");
        $Model = new AdminMessage();
        $list = $Model->order('ctime desc')->paginate($limit)->toArray();
        foreach ($list["data"] as $k => $v) {
            if ($v["code"] == "order_payed" || $v["code"] == "create_order") {
                $params = json_decode($v["params"], true);
                $model = new Order();
                $info = $model->getOrderInfoByOrderID($params["order_id"]);
                $list["data"][$k]["items"] = $info["items"];
                $list["data"][$k]["order_id"] = $info["order_id"];
            } elseif ($v["code"] == "user_register") {
                $params = json_decode($v["params"], true);
                $list["data"][$k]["username"] = $params["nickname"];
            }
            elseif ($v["code"] == "after_order" || $v["code"] == "after_ship"){
                $params = json_decode($v["params"], true);
                $afterModel = new BillAftersalesItems();
                $list["data"][$k]["items"] = $afterModel->where("aftersales_id","eq",$params["aftersales_id"])->select();
                $list["data"][$k]["aftersales_id"] = $params["aftersales_id"];
            }
            $list["data"][$k]["ctime"] = date("Y-m-d H:i:s", $v["ctime"]);
        }
        if ($list["data"]) {
            $list["status"] = true;
            $list["msg"] = "";
        } else {
            $list["status"] = false;
            $list["msg"] = "暂无数据";
        }
        return json($list);
    }

    public function center()
    {
        if (!$this->config["result"]["status"]) {
            return json($this->config["result"]);
        }
        $Model = new AdminMessageCenter();
        $info = $Model->getlist();
        if ($info) {
            $return = [
                "status" => true,
                "msg" => "",
                "data" => $info
            ];
        } else {
            $return = [
                "status" => false,
                "msg" => "获取失败",
                "data" => []
            ];
        }
        return json($return);
    }

    public function tocenter()
    {
        if (!$this->config["result"]["status"]) {
            return json($this->config["result"]);
        }
        $return = [
            "status" => false,
            "msg" => "修改失败",
            "data" => []
        ];
        $ids = input("ids");
        switch ($ids) {
            case 1:
                $code = "all";
                break;
            case 2:
                $code = "user_register";
                break;
            case 3:
                $code = "create_order";
                break;
            case 4:
                $code = "order_payed";
                break;
            case 5:
                $code = "after_order";
                break;
            case 6:
                $code = "after_ship";
                break;
            default:
                return json($return);
        }
        $change = input("change");
        if (!$change) {
            $change = 2;
        }

        $Model = new AdminMessageCenter();
        $res = $Model->toedit($change, $code);
        if (!$res) {
            return json($return);
        } else {
            $return = [
                "status" => true,
                "msg" => "修改成功",
                "data" => []
            ];
            return json($return);
        }
    }

    public function afterlist()
    {
        $post = input();
        if ($post["code"] != 0 && $post["code"] != 1) {
            $post["status"] = 2;
        } elseif ($post["code"] == 1) {
            $post["status"] = 1;
        }
        $model = new BillAftersales();
        $itemsmodel = new BillAftersalesItems();
        $refoundmodel = new BillRefund();
        $retuen = $model->tableData($post);
        $reshipmodel = new BillReship();
        foreach ($retuen["data"] as $k => $v) {
            $retuen["data"][$k]["items"] = $itemsmodel->where("aftersales_id", "eq", $v["aftersales_id"])->select()->toArray();
            $refoundinfo = $refoundmodel->where("aftersales_id", "eq", $v["aftersales_id"])->find();
            if ($refoundinfo) {
                $retuen["data"][$k]["refound"] = $refoundinfo;
            } else {
                $retuen["data"][$k]["refound"] = "";
            }
            $reshipinfo = $reshipmodel->where("aftersales_id", "eq", $v["aftersales_id"])->find();
            if ($reshipinfo) {
                $retuen["data"][$k]["reship"] = $reshipinfo;
            } else {
                $retuen["data"][$k]["reship"] = "";
            }
        }
        foreach ($retuen["data"] as $k => $v) {
            if ($post["code"] == 2) {
                if (!$v["reship"]) {
                    unset($retuen["data"][$k]);
                }
            } elseif ($post["code"] == 3) {
                if (!$v["refound"]) {
                    unset($retuen["data"][$k]);
                }
            }
        }
        $retuen["data"] = array_values(json_decode($retuen["data"], true));
        $retuen["status"] = true;
        return json($retuen);
    }

    public function afterdetail()
    {
        if (!input('?param.aftersales_id')) {
            return json(error_code(13215));
        }
        $billAftersalesModel = new BillAftersales();
        $billAftersalesitemsModel = new BillAftersalesItems();
        $re = $this->preAudit(input('param.aftersales_id'));
        if (input('?param.edit')) {
            $items = [];
            if (input('?param.ids')) {
                $ids = explode(" ,", input("param.ids"));
                foreach ($ids as $k => $v) {
                    $info = $billAftersalesitemsModel->where("id", "eq", $v)->find();
                    $items[$info["order_items_id"]] = $info["nums"];
                }
            }
            if (!input('?param.status')) {
                return json(error_code(13228));
            }
            if (!input('?param.type')) {
                return json(error_code(10000));
            }
            if (!input('?param.refund')) {
                return json(error_code(13216));
            } else {
                $refund = input('param.refund/f');
            }
            $mark = input('param.mark', '');
            $res = $billAftersalesModel->audit(input('param.aftersales_id'), input('param.status'), input('param.type'), $refund, $mark, $items);
            return json($res);
        }
        return json($re);
    }

    /**
     * 未退款状态做退款
     * @return array|\think\Config
     */
    public function refund()
    {
        if (!input('?param.refund_id')) {
            return json(error_code(13215));
        }
        $billRefundModel = new BillRefund();
        $where['refund_id'] = input('param.refund_id');
        $where['status'] = $billRefundModel::STATUS_NOREFUND;
        $info = $billRefundModel->where($where)->find();
        if (!$info) {
            return json(error_code(13219));
        }
        if (input('?param.payment_code') && input('?param.status')) {
            if (!input('?param.status')) {
                return json(error_code(10000));
            }
            if (!input('?param.payment_code')) {
                return json(error_code(10000));
            }
            if (!input('?param.refund_id')) {
                return json(error_code(10000));
            }
            return $billRefundModel->toRefund(input('param.refund_id'), input('param.status'), input('param.payment_code'));
        }
        $ordertimes = new OrderItems();
        $items = $ordertimes->where("order_id", "eq", $info["source_id"])->select()->toArray();
        //取当前商户的所有支付方式
        $paymentsModel = new Payments();
        $payment_list = $paymentsModel->getList(0);
        $data["info"] = $info;
        $data["payment_list"] = $payment_list;
        $data["items"] = $items;
        $return = [
            'status' => true,
            'data' => $data,
            'msg' => ''
        ];
        return json($return);
    }

    /**
     * 退款失败状态再次退款
     * @return array|\think\Config
     */
    public function reaudit()
    {
        if (!input('?param.refund_id')) {
            return json(error_code(13215));
        }
        $billRefundModel = new BillRefundModel();
        $where['refund_id'] = input('param.refund_id');
        $where['status'] = $billRefundModel::STATUS_FAIL;
        $info = $billRefundModel->where($where)->find();
        if (!$info) {
            return json(error_code(13224));
        }
        $res = $billRefundModel->paymentRefund(input('param.refund_id'));
        return json($res);
    }

    public function reship()
    {
        if (!input('?param.reship_id')) {
            return json(error_code(13220));
        }
        $billReshipModel = new BillReship();
        $where['reship_id'] = input('param.reship_id');
        $info = $billReshipModel->where($where)->find();
        if (!$info) {
            return json(error_code(13221));
        }
        if ($info->items) {
            $info['items_json'] = json_encode($info->items);
        }
        $return = [
            'status' => true,
            'data' => $info,
            'msg' => ''
        ];
        return json($return);
    }

    //退货 ，待确认
    public function confirmReship()
    {
        if (!input('?param.reship_id')) {
            return json(error_code(13220));
        }
        $billReshipModel = new BillReship();
        $res = $billReshipModel->confirmReship(input('param.reship_id'));
        return json($res);
    }

    //后端进行审核的时候，前置操作，1取出页面的数据，2在提交过来的表单的时候，进行校验
    public function preAudit($aftersales_id)
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        $where['aftersales_id'] = $aftersales_id;
        $billAftersalesModel = new BillAftersales();
        $info = $billAftersalesModel::with('images,items')->where($where)->find();
        if (!$info) {
            return error_code(13207);
        }
        if ($info["images"]) {
            foreach ($info["images"] as $k => $v) {
                $info["images"][$k]["url"] = _sImage($v["image_id"]);
            }
        }
        //取订单的信息
        $orderModel = new Order();
        $orderInfo = $orderModel->getOrderInfoByOrderID($info['order_id'], $info['user_id'], 2);
        if (!$orderInfo) {
            return error_code(10000);
        }

        //订单上的退款金额和数量只包含已经售后的，这里要把当次售后单的商品信息保存到订单明细表上
        foreach ($orderInfo['items'] as $k => $v) {
            unset($orderInfo['items'][$k]['promotion_list']);       //此字段会影响前端表格显示，所以删掉
            $orderInfo['items'][$k]['addon'] = $v['addon'] ? $v['addon'] : '';
            $orderInfo['items'][$k]['the_reship_nums'] = 0;
            foreach ($info['items'] as $i => $j) {
                if ($v['id'] == $j['order_items_id']) {
                    $orderInfo['items'][$k]['the_reship_nums'] = $j['nums'];
                }
            }
        }
        $info["user_name"] = get_user_info($info["user_id"], "nickname");
        $result['data']['info'] = $info;            //数据库中保存的售后单信息
        $result['data']['orderInfo'] = $orderInfo;  //订单信息，减掉了当次售后单中的退款金额和订单明细中的退货数量
        return $result;
    }
    public function getimages(){
        $url = "https://jihainet.com/b2c/Callback/getimages";
        $curl = new Curl();
        $res = $curl->get($url);
        $res = json_decode($res,true);
        if($res["status"]){
            return $res["data"];
        }else{
            return [];
        }
    }
}