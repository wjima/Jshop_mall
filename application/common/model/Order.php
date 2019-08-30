<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://www.jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use think\model\concern\SoftDelete;
use think\Db;

/**
 * 订单主表
 * Class Order
 * @package app\common\model
 * @author keinx
 */
class Order extends Common
{
    protected $pk = 'order_id';

    use SoftDelete;
    protected $deleteTime = 'isdel';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const ORDER_STATUS_NORMAL = 1;          //订单状态正常
    const ORDER_STATUS_COMPLETE = 2;        //订单状态完成
    const ORDER_STATUS_CANCEL = 3;          //订单状态取消

    const PAY_STATUS_NO = 1;                //未付款
    const PAY_STATUS_YES = 2;               //已付款
    const PAY_STATUS_PARTIAL_YES = 3;       //部分付款
    const PAY_STATUS_PARTIAL_NO = 4;        //部分退款
    const PAY_STATUS_REFUNDED = 5;          //已退款

    const SHIP_STATUS_NO = 1;               //未发货
    const SHIP_STATUS_PARTIAL_YES = 2;      //部分发货
    const SHIP_STATUS_YES = 3;              //已发货
    const SHIP_STATUS_PARTIAL_NO = 4;       //部分退货
    const SHIP_STATUS_RETURNED = 5;         //已退货

    const RECEIPT_NOT_CONFIRMED = 1;        //未确认收货
    const CONFIRM_RECEIPT = 2;              //确认收货

    const NO_COMMENT = 1;                   //没有评价
    const ALREADY_COMMENT = 2;              //已经评价

    const ALL_PENDING_PAYMENT = 1;          //总订单类型 待付款
    const ALL_PENDING_DELIVERY = 2;         //待发货
    const ALL_PENDING_RECEIPT = 3;          //待收货
    const ALL_PENDING_EVALUATE = 4;         //待评价
    const ALL_COMPLETED_EVALUATE = 5;       //已评价
    const ALL_COMPLETED = 6;                //已完成
    const ALL_CANCEL = 7;                   //已取消

    const ORDER_TYPE_COMMON = 1;            //订单类型，1普通订单，严格按照cart模型里的type_common字段来设置，是一一对应的
    const ORDER_TYPE_PINTUAN = 2;           //订单类型，2拼团订单


    /**
     * 订单明细表关联
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('OrderItems');
    }

    /**
     * 订单的用户信息关联
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }

    /**
     * 发货信息关联
     * @return \think\model\relation\HasMany
     */
    public function delivery()
    {
        return $this->hasMany('BillDelivery');
    }

    /**
     * 售后关联
     * @return \think\model\relation\HasMany
     */
    public function aftersales()
    {
        return $this->hasMany('BillAftersales', 'order_id', 'order_id');
    }

    /**
     * 支付单关联
     */
    public function paymentRelItem()
    {
        return $this->hasMany('BillPaymentsRel', 'source_id', 'order_id');
    }

    /**
     * 退款单关联
     */
    public function refundItem()
    {
        return $this->hasMany('BillRefund', 'source_id', 'order_id');
    }

    /**
     * 提货单关联
     */
    public function ladingItem()
    {
        return $this->hasMany('BillLading', 'order_id', 'order_id');
    }

    /**
     * 退货单关联
     */
    public function returnItem()
    {
        return $this->hasMany('BillReship', 'order_id', 'order_id');
    }

    /**
     * 售后单关联
     * @return \think\model\relation\HasMany
     */
    public function aftersalesItem()
    {
        return $this->hasMany('BillAftersales', 'order_id', 'order_id');
    }

    /**
     * 获取订单原始数据
     * @param $input
     * @param bool $isPage
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getListByWhere($input, $isPage = true)
    {
        $where = [];
        //单个订单模糊搜索时
        if (!empty($input['order_id'])) {
            $where[] = array('o.order_id', 'LIKE', '%' . trim($input['order_id']) . '%');
        }
        //多个订单时
        if (isset($input['order_ids']) && $input['order_ids'] != "") {
            $where[] = ['o.order_id', 'in', $input['order_ids']];
        }
        if (!empty($input['username'])) {
            $where[] = array('u.username|u.mobile|u.nickname', 'eq', $input['username']);
        }
        if (!empty($input['ship_mobile'])) {
            $where[] = array('o.ship_mobile', 'eq', $input['ship_mobile']);
        }
        if (!empty($input['pay_status'])) {
            $where[] = array('o.pay_status', 'eq', $input['pay_status']);
        }
        if (!empty($input['ship_status'])) {
            $where[] = array('o.ship_status', 'eq', $input['ship_status']);
        }
        if (isset($input['order_type']) && !empty($input['order_type'])) {
            $where[] = array('o.order_type', 'eq', $input['order_type']);
        }

        if (!empty($input['date'])) {
            $date_string = $input['date'];
            $date_array  = explode(' 到 ', urldecode($date_string));
            $sdate       = strtotime($date_array[0] . ' 00:00:00');
            $edate       = strtotime($date_array[1] . ' 23:59:59');
            $where[]     = array('o.ctime', ['>=', $sdate], ['<=', $edate], 'and');
        }
        if (!empty($input['start_date']) || !empty($input['end_date'])) {
            if (!empty($input['start_date']) && !empty($input['end_date'])) {
                $sdate   = strtotime($input['start_date'] . ' 00:00:00');
                $edate   = strtotime($input['end_date'] . ' 23:59:59');
                $where[] = array('o.ctime', ['>=', $sdate], ['<=', $edate], 'and');
            } elseif (!empty($input['start_date'])) {
                $sdate   = strtotime($input['start_date'] . ' 00:00:00');
                $where[] = array('o.ctime', '>=', $sdate);
            } elseif (!empty($input['end_date'])) {
                $edate   = strtotime($input['end_date'] . ' 23:59:59');
                $where[] = array('o.ctime', '<=', $edate);
            }
        }
        if (!empty($input['source'])) {
            $where[] = array('o.source', 'eq', $input['source']);
        }
        if (!empty($input['user_id'])) {
            $where[] = array('o.user_id', 'eq', $input['user_id']);
        }
        if (!empty($input['order_unified_status'])) {
            $where = array_merge($where, $this->getReverseStatus($input['order_unified_status'], 'o.'));
        }
        if (!empty($input['type'])) {
            $where[] = array('o.order_type', 'eq', $input['type']);
        }
        $page  = $input['page'] ? $input['page'] : 1;
        $limit = $input['limit'] ? $input['limit'] : 20;

        if ($isPage) {

            $data = $this->alias('o')
                ->field('o.order_id, o.user_id, o.ctime, o.ship_mobile, o.ship_address, o.status, o.pay_status, o.ship_status, o.confirm, o.is_comment, o.order_amount, o.source, o.ship_area_id,o.ship_name, o.mark,o.order_type')
                ->join(config('database.prefix') . 'user u', 'o.user_id = u.id', 'left')
                ->where($where)
                ->order('ctime desc')
                ->page($page, $limit)
                ->select();


            $count = $this->alias('o')
                ->field('o.order_id, o.user_id, o.ctime, o.ship_mobile, o.ship_address, o.status, o.pay_status, o.ship_status, o.confirm, o.is_comment, o.order_amount, o.source, o.ship_area_id,o.ship_name, o.mark,o.order_type')
                ->join(config('database.prefix') . 'user u', 'o.user_id = u.id', 'left')
                ->where($where)
                ->count();
        } else {
            $data  = $this->alias('o')
                ->field('o.order_id, o.user_id, o.ctime, o.ship_mobile, o.ship_address, o.status, o.pay_status, o.ship_status, o.confirm, o.is_comment, o.order_amount, o.source, o.ship_area_id,o.ship_name, o.mark,o.order_type')
                ->join(config('database.prefix') . 'user u', 'o.user_id = u.id', 'left')
                ->where($where)
                ->order('ctime desc')
                ->select();
            $count = $this->alias('o')
                ->field('o.order_id, o.user_id, o.ctime, o.ship_mobile, o.ship_address, o.status, o.pay_status, o.ship_status, o.confirm, o.is_comment, o.order_amount, o.source, o.ship_area_id,o.ship_name, o.mark,o.order_type')
                ->join(config('database.prefix') . 'user u', 'o.user_id = u.id', 'left')
                ->where($where)
                ->count();
        }

        return array('data' => $data, 'count' => $count);
    }

    /**
     * 后台获取数据
     * @param $input
     * @param bool $isPage
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListFromAdmin($input, $isPage = true)
    {
        $result = $this->getListByWhere($input, $isPage);

        if (count($result['data']) > 0) {
            $as = new BillAftersales();

            foreach ($result['data'] as $k => &$v) {
                $v['status_text'] = config('params.order')['status_text'][$this->getStatus($v['status'], $v['pay_status'], $v['ship_status'], $v['confirm'], $v['is_comment'])];
                $v['username']    = get_user_info($v['user_id'], 'nickname');
                $v['operating']   = $this->getOperating($v['order_id'], $v['status'], $v['pay_status'], $v['ship_status']);
                $v['area_name']   = get_area($v['ship_area_id']) . '-' . $v['ship_address'];
                $v['pay_status']  = config('params.order')['pay_status'][$v['pay_status']];
                $v['ship_status'] = config('params.order')['ship_status'][$v['ship_status']];
                $v['source']      = config('params.order')['source'][$v['source']];
                $v['type']        =  config('params.order')['type'][$v['order_type']];
                //订单售后状态
                $v['after_sale_status'] = $as->getOrderAfterSaleStatus($v['order_id']);

                //获取订单打印状态
                $print_express = hook('getPrintExpressInfo', ['order_id' => $v['order_id']]);
                if ($print_express[0]['status']) {
                    $v['print'] = true;
                } else {
                    $v['print'] = false;
                }
                //备注醒目
                if (isset($v['mark']) && !empty($v['mark']) && $v['mark'] != '') {
                    $v['order_id_k'] = '<span style="color:#FF7159;" title="' . $v['mark'] . '">' . $v['order_id'] . '</span>';
                } else {
                    $v['order_id_k'] = $v['order_id'];
                }
            }
        }
        return $result;
    }

    /**
     * 总后台获取数据
     * @param $input
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListFromManage($input)
    {
        $result = $this->getListByWhere($input);

        if (count($result['data']) > 0) {
            foreach ($result['data'] as $k => &$v) {
                $v['status_text'] = config('params.order')['status_text'][$this->getStatus($v['status'], $v['pay_status'], $v['ship_status'], $v['confirm'], $v['is_comment'])];
                $v['username']    = get_user_info($v['user_id'], 'nickname');
                $v['operating']   = $this->getOperating($v['order_id'], $v['status'], $v['pay_status'], $v['ship_status'], 'manage');
                $v['area_name']   = get_area($v['ship_area_id']) . '-' . $v['ship_address'];
                $v['pay_status']  = config('params.order')['pay_status'][$v['pay_status']];
                $v['ship_status'] = config('params.order')['ship_status'][$v['ship_status']];
                $v['source']      = config('params.order')['source'][$v['source']];
            }
        }
        return $result;
    }

    /**
     * API获取数据
     * @param $input
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListFromApi($input)
    {
        $return_data = $this->getListByWhere($input);
        return $return_data;
    }

    /**
     * 获取待发货列表
     * @param $input
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//    public function getWaitListFromAdmin($input)
//    {
//        $input['pay_status'] = self::PAY_STATUS_YES;
//        $input['ship_status'] = self::SHIP_STATUS_NO;
//
//        $result = $this->getListByWhere($input);
//
//        if(count($result['data']) > 0)
//        {
//            foreach($result['data'] as $k => &$v)
//            {
//                $v['username'] = get_user_info($v['user_id'], 'nickname');
//                $v['operating'] = $this->getOperating($v['order_id'], $v['status'], $v['pay_status'], $v['ship_status']);
//                $v['area_name'] = get_area($v['ship_area_id']).'-'.$v['ship_address'];
//                $v['pay_status'] = config('params.order')['pay_status'][$v['pay_status']];
//                $v['ship_status'] = config('params.order')['ship_status'][$v['ship_status']];
//                $v['source'] = config('params.order')['source'][$v['source']];
//            }
//        }
//        return $result;
//    }

    /**
     * 获取订单列表微信小程序
     * @param $input
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListFromWxApi($input)
    {
        $where = [];
        if (!empty($input['status'])) {
            $where = $this->getReverseStatus($input['status']);
        }
        if (!empty($input['user_id'])) {
            $where[] = array('user_id', 'eq', $input['user_id']);
        }

        $page  = $input['page'] ? $input['page'] : 1;
        $limit = $input['limit'] ? $input['limit'] : 20;

        $data = $this::with('items')->where($where)
            ->order('ctime desc')
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)
            ->count();
        return array('data' => $data, 'count' => $count);
    }

    /**
     * 获取订单不同状态的数量
     * @param $input
     * @return array
     */
    public function getOrderStatusNum($input)
    {
        $ids = explode(",", $input['ids']);
        if ($input['user_id']) {
            $user_id = $input['user_id'];
        } else {
            $user_id = false;
        }

        $data = [];
        foreach ($ids as $k => $v) {
            $data[$v] = $this->orderCount($v, $user_id);
        }

        //售后状态查询
        $isAfterSale = $input['isAfterSale'];
        if ($isAfterSale) {
            $model               = new BillAftersales();
            $number              = $model->getUserAfterSalesNum($user_id, $model::STATUS_WAITAUDIT);
            $data['isAfterSale'] = $number;
        }
        return $data;
    }

    /**
     * 订单数量统计
     * @param $id
     * @param bool $user_id
     * @return int|string
     */
    protected function orderCount($id = 0, $user_id = false)
    {
        $where = [];
        //都需要验证的
        if ($user_id) {
            $where[] = ['user_id', 'eq', $user_id];
        }

        $where = array_merge($where, $this->getReverseStatus($id));

        return $this->where($where)
            ->count();
    }

    /**
     * 根据订单状态生成不同的操作按钮
     * @param $id
     * @param $order_status
     * @param $pay_status
     * @param $ship_status
     * @param string $from
     * @return string
     */
    protected function getOperating($id, $order_status, $pay_status, $ship_status, $from = 'seller')
    {
        $html = '<a class="layui-btn layui-btn-primary layui-btn-xs view-order" data-id="' . $id . '">查看</a>';

        if ($order_status == self::ORDER_STATUS_NORMAL) {
            //正常
            if ($pay_status == self::PAY_STATUS_NO && $from == 'seller') {
                $html .= '<a class="layui-btn layui-btn-xs pay-order" data-id="' . $id . '">支付</a>';
            }
            if ($pay_status != self::PAY_STATUS_NO) {
                if (($ship_status == self::SHIP_STATUS_NO || $ship_status == self::SHIP_STATUS_PARTIAL_YES) && $from == 'seller') {
                    $html .= '<a class="layui-btn layui-btn-xs edit-order" data-id="' . $id . '">编辑</a>';
                    $html .= '<a class="layui-btn layui-btn-xs ship-order" data-id="' . $id . '">发货</a>';
                }
                $html .= '<a class="layui-btn layui-btn-xs complete-order" data-id="' . $id . '">完成</a>';
//                if($ship_status == self::SHIP_STATUS_YES)
//                {
//                    $html .= '<a class="layui-btn layui-btn-primary layui-btn-xs order-logistics" data-id="'.$id.'">物流信息</a>';
//                }
            }
            if ($pay_status == self::PAY_STATUS_NO) {
                if ($from == 'seller') {
                    $html .= '<a class="layui-btn layui-btn-xs edit-order" data-id="' . $id . '" data-type="1">编辑</a>';
                }
                $html .= '<a class="layui-btn layui-btn-xs cancel-order" data-id="' . $id . '">取消</a>';
            }
        }
//        if ($order_status == self::ORDER_STATUS_COMPLETE)
//        {
//            $html .= '<a class="layui-btn layui-btn-primary layui-btn-xs order-logistics" data-id="'.$id.'">物流信息</a>';
//        }
        if ($order_status == self::ORDER_STATUS_CANCEL) {
            $html .= '<a class="layui-btn layui-btn-danger layui-btn-xs del-order" data-id="' . $id . '">删除</a>';
        }

        return $html;
    }

    /**
     * 获取订单信息
     * @param $id
     * @param bool $user_id
     * @param bool $logistics
     * @return Order|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderInfoByOrderID($id, $user_id = false, $logistics = true)
    {
        $order_info = $this->get($id); //订单信息

        if (!$order_info) {
            return false;
        }

        if ($user_id) {
            if ($user_id != $order_info['user_id']) {
                return false;
            }
        }

        $order_info->items; //订单详情
        $order_info->user; //用户信息
        $order_info->paymentRelItem; //支付单
        $order_info->refundItem; //退款单
        $order_info->delivery; //发货信息
        $order_info->ladingItem; //提货单
        $order_info->returnItem; //退货单
        $order_info->aftersalesItem; //售后单

        //获取提货门店
        $order_info['store'] = false;
        if ($order_info['store_id'] != 0) {
            $storeModel               = new Store();
            $storeInfo                = $storeModel->get($order_info['store_id']);
            $storeInfo['all_address'] = get_area($storeInfo['area_id']) . $storeInfo['address'];
            $order_info['store']      = $storeInfo;
        }

        foreach ($order_info['delivery'] as &$v) {
            $v['logi_name'] = get_logi_info($v['logi_code'], 'logi_name');
        }

        $order_info->hidden(['user' => ['isdel', 'password']]);

        if ($order_info['logistics_id']) {
            $w[]                     = ['id', 'eq', $order_info['logistics_id']];
            $order_info['logistics'] = model('common/Ship')->where($w)->find();
        } else {
            $order_info['logistics'] = null;
        }
        $order_info['text_status']    = $this->getStatus($order_info['status'], $order_info['pay_status'], $order_info['ship_status'], $order_info['confirm'], $order_info['is_comment']);
        $order_info['ship_area_name'] = get_area($order_info['ship_area_id']);

        if (isset(config('params.payment_type')[$order_info['payment_code']])) {
            $payment_name = config('params.payment_type')[$order_info['payment_code']];
        } else {
            $payment_name = false;
        }

        $order_info['payment_name'] = $payment_name ? $payment_name : '未知支付方式';

        //如果有优惠券，数据处理
        if ($order_info['coupon']) {
            $order_info['coupon'] = json_decode($order_info['coupon'], true);
        }

        //获取该状态截止时间
        switch ($order_info['text_status']) {
            case self::ALL_PENDING_PAYMENT: //待付款
                $cancel                       = getSetting('order_cancel_time') * 86400;
                $ctime                        = $order_info['ctime'];
                $remaining                    = $ctime + $cancel - time();
                $order_info['remaining']      = $this->dateTimeTransformation($remaining);
                $order_info['remaining_time'] = $remaining;
                break;
            case self::ALL_PENDING_RECEIPT: //待收货
                $sign                         = getSetting('order_autoSign_time') * 86400;
                $utime                        = $order_info['utime'];
                $remaining                    = $utime + $sign - time();
                $order_info['remaining']      = $this->dateTimeTransformation($remaining);
                $order_info['remaining_time'] = $remaining;
                break;
            case self::ALL_PENDING_EVALUATE: //待评价
                $eval                         = getSetting('order_autoEval_time') * 86400;
                $confirm                      = $order_info['confirm_time'];
                $remaining                    = $confirm + $eval - time();
                $order_info['remaining']      = $this->dateTimeTransformation($remaining);
                $order_info['remaining_time'] = $remaining;
                break;
            default:
                $order_info['remaining']      = false;
                $order_info['remaining_time'] = false;
                break;
        }

        //物流信息查询
        if (isset($order_info['delivery'][0]) && $order_info['delivery'][0] && $logistics) {
            $logi_code         = $order_info['delivery'][0]['logi_code'];
            $logi_no           = $order_info['delivery'][0]['logi_no'];
            $billDeliveryModel = new BillDelivery();
            $express_delivery  = $billDeliveryModel->getLogistic($logi_code, $logi_no);
            if ($express_delivery['status']) {
                $order_info['express_delivery'] = $express_delivery['data']['info']['data'][0];
            } else {
                $order_info['express_delivery'] = [
                    'context' => '已为你发货，请注意查收',
                    'time'    => date('Y-m-d H:i:s', $order_info['delivery'][0]['ctime'])
                ];
            }
        }

        //支付单
        if (count($order_info['paymentRelItem']) > 0) {
            $billPaymentsModel = new BillPayments();
            foreach ($order_info['paymentRelItem'] as &$v) {
                $v['bill']                      = $billPaymentsModel->get($v['payment_id']);
                $v['bill']['payment_code_name'] = config('params.payment_type')[$v['bill']['payment_code']];
                $v['bill']['status_name']       = config('params.bill_payments')['status'][$v['bill']['status']];
                $v['bill']['utime_name']        = getTime($v['bill']['utime']);
            }
        }

        //退款单
        if (count($order_info['refundItem']) > 0) {
            foreach ($order_info['refundItem'] as &$v) {
                $v['payment_code_name'] = config('params.payment_type')[$v['payment_code']];
                $v['status_name']       = config('params.bill_refund')['status'][$v['status']];
                $v['ctime_name']        = getTime($v['ctime']);
            }
        }

        //发货单
        if (count($order_info['delivery']) > 0) {
            $logiModel = new Logistics();
            $areaModel = new Area();
            foreach ($order_info['delivery'] as &$v) {
                $v['logi_code_name']    = $logiModel->getNameByCode($v['logi_code']);
                $v['ship_area_id_name'] = $areaModel->getAllName($v['ship_area_id']);
            }
        }

        //提货单
        if (count($order_info['ladingItem']) > 0) {
            $storeModel      = new Store();
            $clerkModel      = new Clerk();
            $billLadingModel = new BillLading();
            foreach ($order_info['ladingItem'] as &$v) {
                $v['store_id_name'] = $storeModel->getStoreName($v['store_id']);
                $v['status_name']   = config('params.bill_lading')['status'][$v['status']];
                if ($v['status'] == $billLadingModel::STATUS_YES) {
                    $v['utime_name'] = getTime($v['utime']);
                } else {
                    $v['utime_name'] = '';
                }

                if ($v['clerk_id']) {
                    $v['clerk_id_name'] = $clerkModel->getClerkName($v['clerk_id']);
                } else {
                    $v['clerk_id_name'] = '';
                }
            }
        }

        //退货单
        if (count($order_info['returnItem']) > 0) {
            $logiModel = new Logistics();
            foreach ($order_info['returnItem'] as &$v) {
                $v['logi_code_name'] = $logiModel->getNameByCode($v['logi_code']);
                $v['status_name']    = config('params.bill_reship')['status'][$v['status']];
                $v['utime_name']     = getTime($v['utime']);
            }
        }

        //售后单
        $order_info['bill_aftersales_id'] = false;
        if (count($order_info['aftersalesItem']) > 0) {
            $billAftersalesModel = new BillAftersales();
            foreach ($order_info['aftersalesItem'] as $v) {
                $order_info['bill_aftersales_id'] = $v['aftersales_id'];
                //如果售后单里面有待审核的活动售后单，那就直接拿这条
                if ($v['status'] == $billAftersalesModel::STATUS_WAITAUDIT) {
                    break;
                }
            }
        }

        //促销信息
        if ($order_info['promotion_list']) {
            $order_info['promotion_list'] = json_decode($order_info['promotion_list'], true);
        }

        return $order_info;
    }

    /**
     * 时间转换
     * @param $time
     * @return false|string
     */
    protected function dateTimeTransformation($time)
    {
        $newtime = '';
        $d       = floor($time / (3600 * 24));
        $h       = floor(($time % (3600 * 24)) / 3600);
        $m       = floor((($time % (3600 * 24)) % 3600) / 60);
        $s       = floor((($time % (3600 * 24)) % 3600) % 60);
        $s       = ($s < 10) ? '0' . $s : $s;
        if ($d > '0') {
            $newtime = $d . '天' . $h . '小时' . $m . '分' . $s . '秒';
        } else {
            if ($h != '0') {
                $newtime = $h . '小时' . $m . '分' . $s . '秒';
            } else {
                $newtime = $m . '分' . $s . '秒';
            }
        }
        return $newtime;
    }

    /**
     * 获取状态
     * @param $status
     * @param $pay_status
     * @param $ship_status
     * @param $confirm
     * @param $is_comment
     * @return string
     */
    protected function getStatus($status, $pay_status, $ship_status, $confirm, $is_comment)
    {
        if ($status == self::ORDER_STATUS_NORMAL && $pay_status == self::PAY_STATUS_NO) {
            //待付款
            return self::ALL_PENDING_PAYMENT;
        } elseif ($status == self::ORDER_STATUS_NORMAL && $pay_status == self::PAY_STATUS_YES && $ship_status == self::SHIP_STATUS_NO) {
            //待发货
            return self::ALL_PENDING_DELIVERY;
        } elseif ($status == self::ORDER_STATUS_NORMAL && $ship_status == self::SHIP_STATUS_YES && $confirm == self::RECEIPT_NOT_CONFIRMED) {
            //待收货
            return self::ALL_PENDING_RECEIPT;
        } elseif ($status == self::ORDER_STATUS_NORMAL && $pay_status > self::PAY_STATUS_NO && $ship_status == self::SHIP_STATUS_YES && $confirm == self::CONFIRM_RECEIPT && $is_comment == self::NO_COMMENT) {
            //待评价
            return self::ALL_PENDING_EVALUATE;
        } elseif ($status == self::ORDER_STATUS_NORMAL && $pay_status > self::PAY_STATUS_NO && $ship_status == self::SHIP_STATUS_YES && $confirm == self::CONFIRM_RECEIPT && $is_comment == self::ALREADY_COMMENT) {
            //已评价
            return self::ALL_COMPLETED_EVALUATE;
        } elseif ($status == self::ORDER_STATUS_COMPLETE) {
            //已完成
            return self::ALL_COMPLETED;
        } elseif ($status == self::ORDER_STATUS_CANCEL) {
            //已取消
            return self::ALL_CANCEL;
        }
    }

    /**
     * 获取订单状态反查
     * @param $status
     * @param string $table_name
     * @return array
     */
    protected function getReverseStatus($status, $table_name = '')
    {
        $where = [];
        switch ($status) {
            case self::ALL_PENDING_PAYMENT: //待付款
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_NORMAL],
                    [$table_name . 'pay_status', 'eq', self::PAY_STATUS_NO]
                ];
                break;
            case self::ALL_PENDING_DELIVERY: //待发货
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_NORMAL],
                    [$table_name . 'pay_status', 'eq', self::PAY_STATUS_YES],
                    [$table_name . 'ship_status', 'eq', self::SHIP_STATUS_NO]
                ];
                break;
            case self::ALL_PENDING_RECEIPT: //待收货
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_NORMAL],
                    [$table_name . 'ship_status', 'eq', self::SHIP_STATUS_YES],
                    [$table_name . 'confirm', 'eq', self::RECEIPT_NOT_CONFIRMED]
                ];
                break;
            case self::ALL_PENDING_EVALUATE: //待评价
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_NORMAL],
                    [$table_name . 'pay_status', '>', self::PAY_STATUS_NO],
                    [$table_name . 'ship_status', 'eq', self::SHIP_STATUS_YES],
                    [$table_name . 'confirm', 'eq', self::CONFIRM_RECEIPT],
                    [$table_name . 'is_comment', 'eq', self::NO_COMMENT]
                ];
                break;
            case self::ALL_COMPLETED_EVALUATE: //已评价
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_NORMAL],
                    [$table_name . 'pay_status', '>', self::PAY_STATUS_NO],
                    [$table_name . 'ship_status', 'eq', self::SHIP_STATUS_YES],
                    [$table_name . 'confirm', 'eq', self::CONFIRM_RECEIPT],
                    [$table_name . 'is_comment', 'eq', self::ALREADY_COMMENT]
                ];
                break;
            case self::ALL_CANCEL: //已取消
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_CANCEL]
                ];
                break;
            case self::ALL_COMPLETED: //已完成
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_COMPLETE]
                ];
                break;
            default:
                break;
        }

        return $where;
    }

    /**
     * 完成订单操作
     * @param $id
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function complete($id)
    {
        //等待售后审核的订单，不自动操作完成。
        unset($bawhere);
        $baModel = new BillAftersales();
        $bawhere[] = ['order_id', 'eq', $id];
        $bawhere[] = ['status', 'eq', $baModel::STATUS_WAITAUDIT];
        unset($billAftersalesCount);
        $billAftersalesCount = $baModel->where($bawhere)->count();
        if($billAftersalesCount > 0)
        {
            return true;
        }
        unset($where);
        $where[] = ['order_id', 'eq', $id];
        $where[] = ['pay_status', 'neq', self::PAY_STATUS_NO];

        unset($data);
        $data['status'] = self::ORDER_STATUS_COMPLETE;
        $data['utime'] = time();

        unset($info);
        $info = $this->where($where)
            ->find();

        if($info)
        {
            unset($result);
            $result = $this->where($where)
                ->update($data);
            //计算订单实际支付金额（要减去售后退款的金额）
            unset($money);
            unset($bawhere);
            unset($baList);
            $money = $info['payed'];
            $bawhere = [];
            $bawhere[] = ['order_id', 'eq', $id];
            $bawhere[] = ['status', 'eq', $baModel::STATUS_SUCCESS];
            $baList = $baModel->where($bawhere)->select();
            if($baList && count($baList) > 0)
            {
                $refundMoney = 0;
                foreach($baList as $k => $v)
                {
                    $refundMoney = bcadd($refundMoney, $v['refund'], 2);
                }
                $money = bcsub($money, $refundMoney, 2);
            }

            //奖励积分
            $userPointLog = new UserPointLog();
            $userPointLog->orderComplete($info['user_id'], $money, $info['order_id']);

            Hook('orderFinish', $id);//订单完成钩子
            //订单记录
            $orderLog = new OrderLog();
            $orderLog->addLog($info['order_id'], $info['user_id'], $orderLog::LOG_TYPE_COMPLETE, '后台订单完成操作', $where);
        }
        else
        {
            $result = false;
        }
        return $result;
    }

    /**
     * 取消订单操作
     * @param $id
     * @param bool $user_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cancel($id, $user_id = false)
    {
        unset($where);
        $where[] = array('order_id', 'in', $id);
        $where[] = array('pay_status', 'eq', self::PAY_STATUS_NO);
        $where[] = array('status', 'eq', self::ORDER_STATUS_NORMAL);
        $where[] = array('ship_status', 'eq', self::SHIP_STATUS_NO);

        $msg = '后台订单取消操作';
        if($user_id)
        {
            $where[] = array('user_id', 'eq', $user_id);
            $msg = '订单取消操作';
        }

        unset($order_info);
        $order_info = $this->where($where)
            ->select();

        unset($result);
        if($order_info)
        {
            Db::startTrans();
            try {
                //更改状态和库存
                unset($order_ids);
                $order_ids = [];
                $orderLog = new OrderLog();
                foreach($order_info as $k => $v)
                {
                    $order_ids[] = $v['order_id'];
                    //订单记录
                    $orderLog->addLog($v['order_id'], $v['user_id'], $orderLog::LOG_TYPE_CANCEL, $msg, $where);
                    //变更积分
                    if($v['point'] > 0)
                    {
                        $pointLogMode = new UserPointLog();
                        unset($res);
                        $res = $pointLogMode->setPoint($v['user_id'], $v['point'], $pointLogMode::POINT_TYPE_ADMIN_EDIT, '取消订单：'.$v['order_id'].' 返还积分');
                        if(!$res['status'])
                        {
                            Db::rollback();
                            return false;
                        }
                    }
                }
                //状态修改
                unset($w);
                unset($d);
                $w[] = ['order_id', 'in', $order_ids];
                $d['status'] = self::ORDER_STATUS_CANCEL;
                $d['utime'] = time();
                $this->where($w)
                    ->update($d);
                $itemModel = new OrderItems();
                unset($goods);
                $goods = $itemModel->field('product_id, nums')->where($w)->select();
                $goodsModel = new Goods();
                foreach($goods as $v)
                {
                    $goodsModel->changeStock($v['product_id'], 'cancel', $v['nums']);
                }

                $result = true;
                Db::commit();
                hook('cancelorder', $order_info); // 订单取消的钩子
            } catch (\Exception $e) {
                $result = false;
                Db::rollback();
            }
        }
        else
        {
            $result = false;
        }
        return $result;
    }

    /**
     * 删除订单
     * @param $order_ids
     * @param $user_id
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del($order_ids, $user_id)
    {
        $where[] = array('order_id', 'in', $order_ids);
        $where[] = array('user_id', 'eq', $user_id);

        $result = $this->where($where)
            ->delete();
        return $result;
    }

    /**
     * 获取支付订单信息
     * @param $id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPayInfo($id)
    {
        return $this->where('order_id', 'eq', $id)->find();
    }

    /**
     * 编辑保存订单
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit($data)
    {
        if ($data['edit_type'] == 1) {
            $update = [
                'ship_area_id' => $data['ship_area_id'],
                'ship_address' => $data['ship_address'],
                'ship_name'    => $data['ship_name'],
                'ship_mobile'  => $data['ship_mobile']
            ];
            if ($data['order_amount']) {
                $update['order_amount'] = $data['order_amount'];
            }
        } elseif ($data['edit_type'] == 2) {
            $update['store_id']    = $data['store_id'];
            $update['ship_name']   = $data['ship_name'];
            $update['ship_mobile'] = $data['ship_mobile'];
        }


        $res = $this->where('order_id', 'eq', $data['order_id'])
            ->update($update);

        //订单记录
        $orderLog = new OrderLog();
        $w[]      = ['order_id', 'eq', $data['order_id']];
        $info     = $this->where($w)
            ->find();
        $orderLog->addLog($info['order_id'], $info['user_id'], $orderLog::LOG_TYPE_EDIT, '后台订单编辑修改', $update);

        return $res;
    }

    /**
     * 获取需要发货的信息
     * @param $id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderShipInfo($id)
    {
        $where[] = array('pay_status', 'neq', self::PAY_STATUS_NO);
        $where[] = array('order_id', 'eq', $id);
        $where[] = array('ship_status', 'in', self::SHIP_STATUS_NO . ',' . self::SHIP_STATUS_PARTIAL_YES);
        $where[] = array('status', 'eq', 1);

        $order = $this->field('order_id, logistics_id, logistics_name, cost_freight, ship_area_id, ship_address, ship_name, ship_mobile, weight, memo')
            ->where($where)
            ->find();

        $order['ship_area_id'] = get_area($order['ship_area_id']);

        return $order;
    }

    /**
     * 发货改状态
     * @param $order_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ship($order_id)
    {
        //查询发货数量和是否全部发货
        $ship_status = model('common/OrderItems')->isAllShip($order_id);
        //判断发货状态
        if ($ship_status == 'all') {
            $order_data['ship_status'] = self::SHIP_STATUS_YES;
        } else {
            $order_data['ship_status'] = self::SHIP_STATUS_PARTIAL_YES;
        }
        //发货
        $where[] = ['order_id', 'eq', $order_id];
        $result  = $this->save($order_data, $where);
        if ($result) {
            //判断生成门店自提单
            $order_info = $this->get($order_id);
            if ($order_info['store_id'] != 0) {
                $ladingModel = new BillLading();
                $ladingModel->addData($order_id, $order_info['store_id'], $order_info['ship_name'], $order_info['ship_mobile']);
            }
        }
        return $result;
    }

    /**
     * 支付
     * @param $order_id
     * @param $payment_code
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function pay($order_id, $payment_code)
    {
        $return_data = array(
            'status' => false,
            'msg'    => '订单支付失败',
            'data'   => array()
        );

        $w[]   = ['order_id', 'eq', $order_id];
        $w[]   = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $order = $this->where($w)
            ->find();

        if (!$order) {
            return $return_data;
        }

        if ($order['pay_status'] == self::PAY_STATUS_YES || $order['pay_status'] == self::PAY_STATUS_PARTIAL_NO || $order['pay_status'] == self::PAY_STATUS_REFUNDED) {
            $return_data['msg']  = '订单支付失败，该订单已支付';
            $return_data['data'] = $order;
            $data                = "订单" . $order_id . "支付失败，订单已经支付";
        } else {
            $data['payment_code'] = $payment_code;
            $data['payed']        = $order['order_amount'];
            $data['pay_status']   = self::PAY_STATUS_YES;
            $data['payment_time'] = time();
            $result               = $this->where('order_id', 'eq', $order_id)
                ->update($data);

            $return_data['data'] = $result;
            if ($result !== false) {
                $return_data['status'] = true;
                $return_data['msg']    = '订单支付成功';

                //发送支付成功信息,增加发送内容
                $order['pay_time']  = date('Y-m-d H:i:s', $data['payment_time']);
                $order['money']     = $order['order_amount'];
                $order['user_name'] = get_user_info($order['user_id']);
                sendMessage($order['user_id'], 'order_payed', $order);

                sendMessage($order['user_id'], 'seller_order_notice', $order);//给卖家发消息
                //订单支付完成后的钩子
                Hook('orderpayed', $order_id);
            }
        }

        //订单记录
        $orderLog = new OrderLog();
        $orderLog->addLog($order_id, $order['user_id'], $orderLog::LOG_TYPE_PAY, $return_data['msg'], [$return_data, $data]);

        return $return_data;
    }

    /**
     * 确认签收
     * @param $order_id
     * @param bool $user_id
     * @return bool
     */
    public function confirm($order_id, $user_id = false)
    {
        unset($where);
        $where[] = array('order_id', 'eq', $order_id);
        $where[] = array('pay_status', 'neq', self::PAY_STATUS_NO);
        $where[] = array('ship_status', 'neq', self::SHIP_STATUS_NO);
        $where[] = array('status', 'eq', self::ORDER_STATUS_NORMAL);
        $where[] = array('confirm', 'neq', self::CONFIRM_RECEIPT);
        $msg = '后台确认签收操作';
        if($user_id)
        {
            $where[] = array('user_id', 'eq', $user_id);
            $msg = '确认签收操作';
        }

        unset($data);
        $data['confirm']      = self::CONFIRM_RECEIPT;
        $data['confirm_time'] = time();

        unset($return);
        Db::startTrans();
        try {
            //修改订单
            $this->save($data, $where);

            //修改发货单
            model('common/BillDelivery')->confirm($order_id);

            //订单记录
            unset($w);
            unset($info);
            $orderLog = new OrderLog();
            $w[] = ['order_id', 'eq', $order_id];
            $info = $this->where($w)
                ->find();
            $orderLog->addLog($order_id, $info['user_id'], $orderLog::LOG_TYPE_SIGN, $msg, $where);

            $return = true;
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $return = false;
        }
        return $return;
    }

    /**
     * 生成订单
     * @param $user_id //用户id
     * @param $order_type //订单类型，1是普通订单，2是拼团订单
     * @param $cart_ids //购物车id
     * @param $delivery //物流信息
     *      当type为1的时候是普通下单，有以下几个值:
     *          uship_id 用户的收货地址id
     *      当type为2的时候，是门店自提，有以下几个值:
     *          store_id                提货门店id
     *          lading_name             提货人名称
     *          lading_mobile           提货人手机号码
     * @param $memo //订单备注
     * @param int $point //使用积分
     * @param bool $coupon_code //使用优惠券
     * @param bool $formId //微信小程序下单的时候表单id
     * @param int $source //来源id
     * @param array $tax //发票信息
     * @param array $params //订单参数，，主要跟type有关系，不同的type，可能保存不同的信息
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function toAdd($user_id, $order_type, $cart_ids, $delivery, $memo, $point = 0, $coupon_code = false, $formId = false, $source = 2, $tax = [], $params = [])
    {

        $result              = [
            'status' => false,
            'data'   => array(),
            'msg'    => ''
        ];
        $order['order_id']   = get_sn(1);
        $order['user_id']    = $user_id;
        $order['order_type'] = $order_type;

        //生成收货信息
        $delivery_re = $this->formatOrderDelivery($order, $delivery);
        if (!$delivery_re['status']) {
            return $delivery_re;
        } else {
            $area_id = $delivery_re['data'];        //下单的省市区地址，算运费用。
        }
        //通过购物车生成订单信息和订单明细信息
        $order_re = $this->formatOrder($order, $user_id, $cart_ids, $area_id, $point, $coupon_code,false,$delivery['type']);
        if (!$order_re['status']) {
            return $order_re;
        } else {
            $items = $order_re['data'];        //订单明细
        }


        //以下值不是通过购物车得来的，是直接赋值的，就写这里吧，不写formatOrder里了。
        $order['memo']        = $memo;
        $order['source']      = $source;
        $order['tax_type']    = $tax['tax_type'];
        $order['tax_title']   = $tax['tax_name'];
        $order['tax_code']    = $tax['tax_code'];
        $order['tax_content'] = '商品明细';

        Db::startTrans();
        try {
            $this->save($order);
            //上面保存好订单表，下面保存订单的其他信息
            //更改库存
            $goodsModel = new Goods();
            foreach ($items as $k => $v) {
                //更改库存
                $sflag = $goodsModel->changeStock($v['product_id'], 'order', $v['nums']);
                if (!$sflag['status']) {
                    Db::rollback();
                    return $sflag;
                }
            }
            $orderItemsModel = new OrderItems();
            $orderItemsModel->saveAll($items);

            //优惠券核销
            if ($coupon_code) {
                $coupon     = new Coupon();
                $coupon_res = $coupon->usedMultipleCoupon($coupon_code, $user_id);
                if (!$coupon_res['status']) {
                    Db::rollback();
                    return $coupon_res;
                }
            }

            //积分核销
            if ($order['point'] > 0) {
                $userPointLog = new UserPointLog();
                $pflag        = $userPointLog->setPoint($user_id, 0 - $order['point'], $userPointLog::POINT_TYPE_DISCOUNT, $remarks = '订单：'.$order['order_id'].' 使用积分');
                if (!$pflag['status']) {
                    Db::rollback();
                    return $pflag;
                }
            }

            //不同的订单类型会有不同的操作
            switch ($order_type) {
                case self::ORDER_TYPE_COMMON:
                    //标准模式不需要修改订单数据和商品数据
                    break;
                case self::ORDER_TYPE_PINTUAN;
                    //拼团模式去校验拼团是否存在，并添加拼团记录
                    $pintuanRecordModel = new PintuanRecord();
                    $pt_re = $pintuanRecordModel->orderAdd($order, $items, $params);
                    if (!$pt_re['status']) {
                        Db::rollback();
                        return $pt_re;
                    }
                    break;
                default:
                    Db::rollback();
                    return error_code(10000);
            }


            //提交数据库
            Db::commit();

            //清除购物车信息
            $cartModel = new Cart();
            $cartModel->del($user_id, $cart_ids, $order_type);

            //订单记录
            $orderLog = new OrderLog();
            $orderLog->addLog($order['order_id'], $user_id, $orderLog::LOG_TYPE_CREATE, '订单创建', $order);
            //0元订单记录支付成功
            if ($order['order_amount'] <= 0) {
                $orderLog->addLog($order['order_id'], $user_id, $orderLog::LOG_TYPE_PAY, '0元订单直接支付成功', $order);
            }

            //企业发票信息记录
            if ($tax['tax_type'] == 3) {
                $irModel = new InvoiceRecord();
                $irModel->add(['name' => $tax['tax_name'], 'code' => $tax['tax_code']]);
            }
            $order['tax_title']   = $tax['tax_name'];
            $order['tax_code']    = $tax['tax_code'];
            $order['tax_content'] = '商品明细';

            //发送消息
            $shipModel          = new Ship();
            $ship               = $shipModel->getInfo(['id' => $order['logistics_id']]);
            $order['ship_id']   = $ship['name'];
            $order['ship_addr'] = get_area($order['ship_area_id']) . $order['ship_address'];
            $order['form_id']   = $formId;
            sendMessage($user_id, 'create_order', $order);

            $result['status'] = true;
            $result['data']   = $order;
            return $result;
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * 生成订单的时候，根据购物车信息生成订单信息及明细信息
     * @param $order //订单数组，应用方式
     * @param $user_id //用户id
     * @param $cart_ids //购物车信息
     * @param $area_id //收货地区
     * @param $point //使用积分
     * @param $coupon_code //使用优惠券
     * @param bool $free_freight //是否包邮
     * @param string $delivery_type
     * @return array|mixed //返回订单明细信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function formatOrder(&$order, $user_id, $cart_ids, $area_id, $point, $coupon_code, $free_freight = false,$delivery_type='1')
    {
        $cartModel = new Cart();
        $cartInfo  = $cartModel->info($user_id, $cart_ids, $order['order_type'], $area_id, $point, $coupon_code, $free_freight,$delivery_type);
        if (!$cartInfo['status']) {
            return $cartInfo;
        }
        $order['goods_amount'] = $cartInfo['data']['goods_amount'];
        $order['order_amount'] = $cartInfo['data']['amount'];
        if ($order['order_amount'] == 0) {
            $order['pay_status']   = self::PAY_STATUS_YES;
            $order['payment_time'] = time();
        }
        $order['cost_freight'] = $cartInfo['data']['cost_freight'];

        //优惠信息存储
        $promotion_list = [];
        foreach ($cartInfo['data']['promotion_list'] as $k => $v) {
            if ($v['type'] == 2) {
                $promotion_list[] = $v;
            }
        }
        $order['promotion_list'] = json_encode($promotion_list);

        //积分使用情况
        $order['point']       = $cartInfo['data']['point'];
        $order['point_money'] = $cartInfo['data']['point_money'];

        $order['weight'] = $cartInfo['data']['weight'];;
        $order['order_pmt']  = isset($cartInfo['data']['order_pmt']) ? $cartInfo['data']['order_pmt'] : 0;
        $order['goods_pmt']  = isset($cartInfo['data']['goods_pmt']) ? $cartInfo['data']['goods_pmt'] : 0;
        $order['coupon_pmt'] = $cartInfo['data']['coupon_pmt'];
        $order['coupon']     = json_encode($cartInfo['data']['coupon']);
        $order['ip']         = get_client_ip();

        //以上保存了订单主体表信息，以下生成订单明细表
        $items = $this->formatOrderItems($cartInfo['data']['list'], $order['order_id']);
        if(!$items){
            return error_code(10000);       //判断订单明细为空的不能下单
        }
        return [
            'status' => true,
            'data'   => $items,           //订单主体表通过引用直接返回值，订单明细通过这里返回值
            'msg'    => ''
        ];
    }

    /**
     * 根据购物车的明细生成订单明细
     * @param $list //购物车明细
     * @param $order_id //订单id，为了生成订单明细上的订单号
     * @return array //订单明细数组
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function formatOrderItems($list, $order_id)
    {
        $items = [];
        foreach ($list as $v) {
            if (!$v['is_select']) {
                continue;
            }
            $item['order_id']         = $order_id;
            $item['goods_id']         = $v['products']['goods_id'];
            $item['product_id']       = $v['products']['id'];
            $item['sn']               = $v['products']['sn'];
            $item['bn']               = $v['products']['bn'];
            $item['name']             = $v['products']['name'];
            $item['price']            = $v['products']['price'];
            $item['costprice']        = $v['products']['costprice'];
            $item['mktprice']         = $v['products']['mktprice'];
            $item['image_url']        = get_goods_info($v['products']['goods_id'], 'image_id');
            $item['nums']             = $v['nums'];
            $item['amount']           = $v['products']['amount'];
            $item['promotion_amount'] = isset($v['products']['promotion_amount']) ? $v['products']['promotion_amount'] : 0;
            $item['weight']           = $v['weight'];
            $item['sendnums']         = 0;
            $item['addon']            = $v['products']['spes_desc'];
            if (isset($v['products']['promotion_list'])) {
                $promotion_list = [];
                foreach ($v['products']['promotion_list'] as $k => $v) {
                    $promotion_list[$k] = $v['name'];
                }
                $item['promotion_list'] = json_encode($promotion_list);
            }
            $items[] = $item;
        }
        return $items;
    }

    /**
     * 生成订单的收货信息
     * @param $order
     * @param $delivery
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function formatOrderDelivery(&$order, $delivery)
    {
        if ($delivery['type'] == 1) {
            //快递邮寄
            $ushopModel = new UserShip();
            $ushopInfo  = $ushopModel->getShipById($delivery['uship_id'], $order['user_id']);
            if (!$ushopInfo) {
                return error_code(11050);
            }
            $area_id = $ushopInfo['area_id'];

            //快递邮寄
            $order['ship_area_id']   = $ushopInfo['area_id'];
            $order['ship_address']   = $ushopInfo['address'];
            $order['ship_name']      = $ushopInfo['name'];
            $order['ship_mobile']    = $ushopInfo['mobile'];
            $shipInfo                = model('common/Ship')->getShip($ushopInfo['area_id']);
            $order['logistics_id']   = $shipInfo['id'];
            $order['logistics_name'] = $shipInfo['name'];
            $order['store_id']       = 0;

        } else {
            //门店自提
            $storeModel = new Store();
            $storeInfo  = $storeModel->get($delivery['store_id']);
            if (!$storeInfo) {
                return error_code(11055);
            }
            $area_id = $storeInfo['area_id'];

            //门店自提
            $order['ship_area_id'] = $storeInfo['area_id'];
            $order['ship_address'] = $storeInfo['address'];
            $order['ship_name']    = $delivery['lading_name'];
            $order['ship_mobile']  = $delivery['lading_mobile'];
            $order['store_id']     = $delivery['store_id'];
            $order['logistics_id'] = 0;
        }

        return [
            'status' => true,
            'data'   => $area_id,
            'msg'    => ''
        ];

    }

    /**
     * 判断订单是否可以评价
     * @param $order_id
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isOrderComment($order_id, $user_id)
    {
        $where[] = ['order_id', 'eq', $order_id];
        $where[] = ['user_id', 'eq', $user_id];

        $res = $this->where($where)
            ->find();
        if ($res) {
            if ($res['pay_status'] > self::PAY_STATUS_NO && $res['status'] == self::ORDER_STATUS_NORMAL && $res['ship_status'] > self::SHIP_STATUS_NO && $res['is_comment'] == self::NO_COMMENT) {
                $data = [
                    'status' => true,
                    'msg'    => '可以评价',
                    'data'   => $res
                ];
            } else {
                $data = [
                    'status' => false,
                    'msg'    => '订单状态存在问题，不能评价',
                    'data'   => $res
                ];
            }
        } else {
            $data = [
                'status' => false,
                'msg'    => '不存在这个订单',
                'data'   => $res
            ];
        }
        return $data;
    }

    /**
     * 自动取消订单
     * @param $setting
     * @return Order|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function autoCancel($setting)
    {
        unset($where);
        unset($order_info);
        unset($order_ids);
        unset($result);
        $where[] = ['pay_status', 'eq', self::PAY_STATUS_NO];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[] = ['ctime', '<=', time() - $setting * 86400];
        $where[] = ['order_type', 'eq', self::ORDER_TYPE_COMMON];

        $order_info = $this->field('order_id,pay_status,status,ctime,order_type')
            ->where($where)
            ->select();

        $result = true;
        if(count($order_info) > 0)
        {
            $order_ids = [];
            foreach($order_info as $v)
            {
                $order_ids[] = $v['order_id'];
            }
            $result = $this->cancel($order_ids);
        }
        return $result;
    }

    /**
     * 自动签收订单
     * @param $setting
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function autoSign($setting)
    {
        unset($where);
        $where[] = ['pay_status', 'eq', self::PAY_STATUS_YES];
        $where[] = ['ship_status', 'eq', self::SHIP_STATUS_YES];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[] = ['utime', '<=', time() - $setting * 86400];

        unset($order_list);
        $order_list = $this->field('order_id,user_id,pay_status,ship_status,status,utime')
            ->where($where)
            ->select();

        if(count($order_list) > 0)
        {
            foreach($order_list as $v)
            {
                $this->confirm($v['order_id']);
            }
        }
    }

    /**
     * 自动评价订单
     * @param $setting
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function autoEvaluate($setting)
    {
        $orderLog = new OrderLog();

        //查询订单
        $where[]    = ['pay_status', 'eq', self::PAY_STATUS_YES];
        $where[]    = ['ship_status', 'eq', self::SHIP_STATUS_YES];
        $where[]    = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[]    = ['confirm', 'eq', self::CONFIRM_RECEIPT];
        $where[]    = ['is_comment', 'eq', self::NO_COMMENT];
        $where[]    = ['confirm_time', '<=', time() - $setting * 86400];
        $order_info = $this::with('items')->field('order_id, user_id')->where($where)
            ->select()->toArray();

        unset($order_ids);
        $order_ids   = [];
        $order_items = [];
        foreach ($order_info as $vo) {
            $order_ids[] = $vo['order_id'];
            if (count($vo['items']) > 0) {
                foreach ($vo['items'] as &$vv) {
                    $vv['user_id'] = $vo['user_id'];
                }
                $order_items = array_merge($order_items, $vo['items']);
            }
            //订单记录
            $orderLog->addLog($vo['order_id'], $vo['user_id'], $orderLog::LOG_TYPE_AUTO_EVALUATION, '订单后台自动评价', $where);
        }

        //更新订单
        unset($wheres);
        $wheres[]           = ['order_id', 'in', $order_ids];
        $data['is_comment'] = self::ALREADY_COMMENT;
        $data['utime']      = time();
        $this->where($wheres)->update($data);

        //查询评价商品
        unset($goods_comment);
        $goods_comment = [];
        foreach ($order_items as $vo) {
            $goods_comment[] = [
                'score'    => 5,
                'user_id'  => $vo['user_id'],
                'goods_id' => $vo['goods_id'],
                'order_id' => $vo['order_id'],
                'addon'    => $vo['addon'],
                'content'  => '用户' . $setting . '天内未对商品做出评价，已由系统自动评价。',
                'ctime'    => time(),
            ];
        }
        model('common/GoodsComment')->insertAll($goods_comment);
    }

    /**
     * 自动完成订单
     * @param $setting
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function autoComplete($setting)
    {
        unset($where);
        $where[] = ['pay_status', 'eq', self::PAY_STATUS_YES];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[] = ['ship_status', 'eq', self::SHIP_STATUS_YES];//已发货
        $where[] = ['confirm', 'eq', self::CONFIRM_RECEIPT];//已确认收货
        $where[] = ['payment_time', '<=', time() - $setting * 86400];

        unset($order_list);
        $order_list = $this->field('order_id,user_id,pay_status,status,ship_status,confirm,payment_time')
            ->where($where)
            ->select();

        if(count($order_list) > 0)
        {
            foreach($order_list as $v)
            {
                $this->complete($v['order_id']);
            }
        }
    }

    /**
     * 获取当月的资金池
     * @return array
     */
    public function cashPooling()
    {
        $monthTimeStamp = $this->specifiedTimeStamp();
        $where[]        = ['utime', 'egt', $monthTimeStamp['start_time']];
        $where[]        = ['utime', 'elt', $monthTimeStamp['end_time']];
        $where[]        = ['pay_status', 'eq', self::PAY_STATUS_YES];
        $order_amount   = $this->where($where)->sum('order_amount');
        $result['data'] = $order_amount / 10;
        $result         = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $result
        ];
        return $result;
    }

    /**
     * 获取指定年月的第一天开始和最后一天结束的时间戳
     * @param string $year 年份
     * @param string $month 月份
     * @return array (本月开始时间，本月结束时间)
     */
    public function specifiedTimeStamp($year = "", $month = "")
    {
        if ($year == "") $year = date("Y");
        if ($month == "") $month = date("m");
        $month = sprintf("%02d", intval($month));
        //填充字符串长度
        $y = str_pad(intval($year), 4, "0", STR_PAD_RIGHT);
        $month > 12 || $month < 1 ? $m = 1 : $m = $month;
        $firstDay    = strtotime($y . $m . "01000000");
        $firstDayStr = date("Y-m-01", $firstDay);
        $lastDay     = strtotime(date('Y-m-d 23:59:59', strtotime("$firstDayStr +1 month -1 day")));

        return [
            "start_time" => $firstDay,
            "end_time"   => $lastDay
        ];
    }

    /**
     * 订单催付款
     * 默认提前1小时通知
     * @param int $setting
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function remind_order_pay($setting = 0)
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        $where[]           = ['pay_status', 'eq', self::PAY_STATUS_NO];
        $where[]           = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $remind_order_time = getSetting('remind_order_time');//催付款时间
        $second            = $setting * 86400 - $remind_order_time * 3600;

        $second = time() - $second;

        $where[]    = ['ctime', '<=', $second];
        $order_info = $this->where($where)
            ->select();
        if (count($order_info) > 0) {
            foreach ($order_info as $kk => $vv) {
                sendMessage($vv['user_id'], 'remind_order_pay', $vv);
            }
        }
    }

    /**
     * 获取csv数据
     * @param $post
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCsvData($post)
    {
        $result    = [
            'status' => false,
            'data'   => [],
            'msg'    => '无可导出订单'
        ];
        $header    = $this->csvHeader();
        $orderData = $this->getListFromAdmin($post, false);
        if ($orderData['count'] > 0) {
            $tempBody = $orderData['data'];
            $body     = [];
            $i        = 0;
            foreach ($tempBody as $key => $val) {
                $i++;
                $orderItems = $this->orderItems($val['order_id']);
                $itemData   = [];
                foreach ($header as $hk => $hv) {
                    if (isset($hv['modify']) && isset($val[$hv['id']]) && $val[$hv['id']]) {
                        if (function_exists($hv['modify'])) {
                            $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                        }
                    } elseif (isset($val[$hv['id']]) && $val[$hv['id']]) {
                        $body[$i][$hk] = $val[$hv['id']];
                    } else {
                        $body[$i][$hk] = '';
                    }
                }

                foreach ($orderItems as $itemKey => $itemVal) {
                    $i++;
                    $sval['item_name']   = $itemVal['name'] . '-' . $itemVal['addon'];
                    $sval['item_price']  = $itemVal['price'];
                    $sval['item_nums']   = $itemVal['nums'];
                    $sval['item_amount'] = $itemVal['amount'];
                    $sval['item_sn']     = $itemVal['sn'];
                    $sval['item_bn']     = $itemVal['bn'];
                    $sval['item_weight'] = $itemVal['weight'];
                    foreach ($header as $hk => $hv) {
                        if (isset($hv['modify']) && isset($sval[$hv['id']]) && $sval[$hv['id']]) {
                            if (function_exists($hv['modify'])) {
                                $body[$i][] = $hv['modify']($sval[$hv['id']]);
                            }
                        } elseif (isset($sval[$hv['id']]) && $sval[$hv['id']]) {
                            $body[$i][] = $sval[$hv['id']];
                        } else {
                            $body[$i][] = '';
                        }
                    }
                }
            }
            $result['status'] = true;
            $result['msg']    = '获取成功';
            $result['data']   = $body;
            return $result;
        } else {
            //失败，导出失败
            return $result;
        }
    }

    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id'     => 'order_id',
                'desc'   => '订单号',
                'modify' => 'convertString'
            ],
            [
                'id'     => 'ctime',
                'desc'   => '下单时间',
                'modify' => 'getTime',
            ],
            [
                'id'   => 'status_text',
                'desc' => '订单状态',
            ],
            [
                'id'   => 'username',
                'desc' => '用户名',
            ],
            [
                'id'   => 'ship_name',
                'desc' => '收货人',
            ],
            [
                'id'   => 'area_name',
                'desc' => '收货地址',
            ],
            [
                'id'     => 'ship_mobile',
                'desc'   => '收货人手机号',
                'modify' => 'convertString'
            ],
            [
                'id'   => 'pay_status',
                'desc' => '支付状态',
            ],
            [
                'id'   => 'ship_status',
                'desc' => '发货状态',
            ],
            [
                'id'   => 'order_amount',
                'desc' => '订单总额',
            ],
            [
                'id'   => 'source',
                'desc' => '订单来源',
            ],
            [
                'id'   => 'item_name',
                'desc' => '商品名称',
            ],
            [
                'id'   => 'item_price',
                'desc' => '商品单价',
            ],
            [
                'id'   => 'item_nums',
                'desc' => '购买数量',
            ],
            [
                'id'   => 'item_amount',
                'desc' => '商品总价',
            ],
            [
                'id'   => 'item_sn',
                'desc' => '货品编码',
            ],
            [
                'id'   => 'item_bn',
                'desc' => '商品编码',
            ],
            [
                'id'   => 'item_weight',
                'desc' => '商品总重量',
            ]
        ];
    }

    /**
     * 获取明细
     * @param string $order_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function orderItems($order_id = '')
    {
        $itemModel = new OrderItems();
        $items     = $itemModel->field('*')->where(['order_id' => $order_id])->select();
        if (!$items->isEmpty()) {
            return $items->toArray();
        }
        return [];
    }

    /**
     * 卖家备注
     * @param $order_id
     * @param string $mark
     * @return array
     */
    public function saveMark($order_id, $mark = '')
    {
        $return = [
            'status' => false,
            'msg'    => '备注失败',
            'data'   => $mark
        ];

        $where[]      = ['order_id', 'eq', $order_id];
        $data['mark'] = $mark;
        $result       = $this->save($data, $where);

        if ($result !== false) {
            $return['status'] = true;
            $return['msg']    = '备注成功';
        }

        return $return;
    }
}