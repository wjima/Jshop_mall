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
    const ALL_PARTIAL_DELIVERY = 8;         //部分发货
    const ALL_PENDING_RECEIPT = 3;          //待收货
    const ALL_PENDING_EVALUATE = 4;         //待评价
    const ALL_COMPLETED_EVALUATE = 5;       //已评价
    const ALL_COMPLETED = 6;                //已完成
    const ALL_CANCEL = 7;                   //已取消

    const ORDER_TYPE_COMMON = 1;            //订单类型，1普通订单，严格按照cart模型里的type_common字段来设置，是一一对应的
    const ORDER_TYPE_PINTUAN = 2;           //订单类型，2拼团订单

    const ORDER_TYPE_GROUP = 3;           //订单类型，3团购
    const ORDER_TYPE_SKILL = 4;           //订单类型，4秒杀
    const ORDER_TYPE_LOTTERY = 5;           //订单类型，5抽奖订单
    const ORDER_TYPE_BARGAIN = 6;           //订单类型，6砍价订单
    const ORDER_TYPE_COMBO = 8;           //订单类型，8免单活动订单

    const GIVEAWAY_STR = "[赠品]";         // 订单明细商品名称上的赠品的文字,在前端订单待评价列表和评价页面，会直接比对这个字段，如果是赠品的话，不显示。


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
     * 售后关联
     * @return \think\model\relation\HasMany
     */
    public function aftersales()
    {
        return $this->hasMany('BillAftersales', 'order_id', 'order_id');
    }


    /**
     * 支付单关联
     * @return \think\model\relation\HasMany
     */
    public function paymentRelItem()
    {
        return $this->hasMany('BillPaymentsRel', 'source_id', 'order_id');
    }


    /**
     * 退款单关联
     * @return \think\model\relation\HasMany
     */
    public function refundItem()
    {
        return $this->hasMany('BillRefund', 'source_id', 'order_id');
    }


    /**
     * 提货单关联
     * @return \think\model\relation\HasMany
     */
    public function ladingItem()
    {
        return $this->hasMany('BillLading', 'order_id', 'order_id');
    }


    /**
     * 退货单关联
     * @return \think\model\relation\HasMany
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
            $where[] = ['o.order_id', 'LIKE', '%' . trim($input['order_id']) . '%'];
        }
        //多个订单时
        if (isset($input['order_ids']) && $input['order_ids'] != "") {
            $where[] = ['o.order_id', 'in', $input['order_ids']];
        }
        if (!empty($input['username'])) {
            $where[] = ['u.username|u.mobile|u.nickname', 'eq', $input['username']];
        }
        if (!empty($input['ship_mobile'])) {
            $where[] = ['o.ship_mobile', 'eq', $input['ship_mobile']];
        }
        if (!empty($input['pay_status'])) {
            $where[] = ['o.pay_status', 'eq', $input['pay_status']];
        }
        if (!empty($input['ship_status'])) {
            $where[] = ['o.ship_status', 'eq', $input['ship_status']];
        }
        if (isset($input['order_type']) && !empty($input['order_type'])) {
            $where[] = ['o.order_type', 'eq', $input['order_type']];
        }

        if (!empty($input['date'])) {
            $date_string = $input['date'];
            $date_array = explode(' 到 ', urldecode($date_string));
            $sdate = strtotime($date_array[0] . ' 00:00:00');
            $edate = strtotime($date_array[1] . ' 23:59:59');
            $where[] = ['o.ctime', ['>=', $sdate], ['<=', $edate], 'and'];
        }
        if (!empty($input['start_date']) || !empty($input['end_date'])) {
            if (!empty($input['start_date']) && !empty($input['end_date'])) {
                $sdate = strtotime($input['start_date'] . ' 00:00:00');
                $edate = strtotime($input['end_date'] . ' 23:59:59');
                $where[] = ['o.ctime', ['>=', $sdate], ['<=', $edate], 'and'];
            } elseif (!empty($input['start_date'])) {
                $sdate = strtotime($input['start_date'] . ' 00:00:00');
                $where[] = ['o.ctime', '>=', $sdate];
            } elseif (!empty($input['end_date'])) {
                $edate = strtotime($input['end_date'] . ' 23:59:59');
                $where[] = ['o.ctime', '<=', $edate];
            }
        }
        if (!empty($input['source'])) {
            $where[] = ['o.source', 'eq', $input['source']];
        }
        if (!empty($input['user_id'])) {
            $where[] = ['o.user_id', 'eq', $input['user_id']];
        }
        if (!empty($input['order_unified_status'])) {
            $where = array_merge($where, $this->getReverseStatus($input['order_unified_status'], 'o.'));
        }
        if (!empty($input['type'])) {
            $where[] = ['o.order_type', 'eq', $input['type']];
        }
        $page = $input['page'] ? $input['page'] : 1;
        $limit = $input['limit'] ? $input['limit'] : 20;

        if ($isPage) {

            $data = $this::with('aftersales')->alias('o')
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
            $data = $this->alias('o')
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

        return ['data' => $data, 'count' => $count];
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
            //$as = new BillAftersales();
            foreach ($result['data'] as $k => &$v) {
                $v['status_text'] = config('params.order')['status'][$v['status']];
                $v['username'] = get_user_info($v['user_id'], 'nickname');
                $v['operating'] = $this->getOperating($v['order_id'], $v['status'], $v['pay_status'], $v['ship_status']);
                $v['area_name'] = get_area($v['ship_area_id']) . '-' . $v['ship_address'];
                $v['pay_status'] = config('params.order')['pay_status'][$v['pay_status']];
                $v['ship_status'] = config('params.order')['ship_status'][$v['ship_status']];
                $v['source'] = config('params.order')['source'][$v['source']];
                $v['type'] = config('params.order')['type'][$v['order_type']];
                //订单售后状态
                $v['after_sale_status'] = "";
                foreach ($v['aftersales'] as $j) {
                    $v['after_sale_status'] = $v['after_sale_status'] . config('params.bill_aftersales.status')[$j['status']] . " ";
                }

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


    // /**
    //  * 总后台获取数据
    //  * @param $input
    //  * @return array
    //  * @throws \think\db\exception\DataNotFoundException
    //  * @throws \think\db\exception\ModelNotFoundException
    //  * @throws \think\exception\DbException
    //  */
    // public function getListFromManage($input)
    // {
    //     $result = $this->getListByWhere($input);

    //     if (count($result['data']) > 0) {
    //         foreach ($result['data'] as $k => &$v) {
    //             $v['status_text'] = config('params.order')['status'][$v['status']];
    //             $v['username'] = get_user_info($v['user_id'], 'nickname');
    //             $v['operating'] = $this->getOperating($v['order_id'], $v['status'], $v['pay_status'], $v['ship_status'], 'manage');
    //             $v['area_name'] = get_area($v['ship_area_id']) . '-' . $v['ship_address'];
    //             $v['pay_status'] = config('params.order')['pay_status'][$v['pay_status']];
    //             $v['ship_status'] = config('params.order')['ship_status'][$v['ship_status']];
    //             $v['source'] = config('params.order')['source'][$v['source']];
    //         }
    //     }
    //     return $result;
    // }


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
            $where = $this->getReverseStatus($input['status'], 'o.');
        }
        if (!empty($input['user_id'])) {
            $where[] = ['o.user_id', 'eq', $input['user_id']];
        }

        if (!empty($input['keyword'])) {
            $where[] = ['o.order_id|oi.bn|oi.name|o.ship_mobile', 'LIKE', '%' . $input['keyword'] . '%'];
        }

        $page  = $input['page'] ? $input['page'] : 1;
        $limit = $input['limit'] ? $input['limit'] : 20;

        $data = $this::with('items')->where($where)
            ->field('o.*')
            ->alias('o')
            ->leftJoin('order_items oi', 'oi.order_id = o.order_id')
            ->order('o.ctime desc')
            ->page($page, $limit)
            ->group('order_id')
            ->select();
        /*$data = $this::with('items')->where($where)
            ->order('ctime desc')
            ->page($page, $limit)
            ->select();*/

        $count = $this::with('items')->where($where)
            ->field('o.*')
            ->alias('o')
            ->leftJoin('order_items oi', 'oi.order_id = o.order_id')
            ->group('order_id')
            ->count();
        return ['data' => $data, 'count' => $count];
    }


    /**
     * 获取订单不同状态的数量
     * @param $input
     * @return array
     */
    public function getOrderStatusNum($input)
    {
        $ids = explode(",", $input['ids']);
        if (isset($input['user_id']) && $input['user_id']) {
            $user_id = $input['user_id'];
        } else {
            $user_id = false;
        }

        $data = [];
        foreach ($ids as $k => $v) {
            $data[$v] = $this->orderCount($v, $user_id);
        }

        //售后状态查询
        if (isset($input['isAfterSale']) && $input['isAfterSale']) {
            $model = new BillAftersales();
            $number = $model->getUserAfterSalesNum($user_id, $model::STATUS_WAITAUDIT);
            $data['isAfterSale'] = $number;
        }
        return $data;
    }


    /**
     * 订单数量统计
     * @param int $id
     * @param bool $user_id
     * @return float|string
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
    protected function getOperating($id, $order_status, $pay_status, $ship_status)
    {
        $html = '<a class="layui-btn layui-btn-primary layui-btn-xs view-order" data-id="' . $id . '">查看</a>';
        if ($order_status == self::ORDER_STATUS_NORMAL) {
            //正常
            if ($pay_status == self::PAY_STATUS_NO) {
                $html .= '<a class="layui-btn layui-btn-xs pay-order" data-id="' . $id . '">支付</a>';
                $html .= '<a class="layui-btn layui-btn-xs edit-order2" data-id="' . $id . '" data-type="1">编辑</a>';
                $html .= '<a class="layui-btn layui-btn-xs cancel-order" data-id="' . $id . '">取消</a>';
            }else{
                if ($ship_status == self::SHIP_STATUS_NO || $ship_status == self::SHIP_STATUS_PARTIAL_YES) {
                    $html .= '<a class="layui-btn layui-btn-xs edit-order2" data-id="' . $id . '">编辑</a>';
                    $html .= '<a class="layui-btn layui-btn-xs ship-order" data-id="' . $id . '">发货</a>';
                }
                $html .= '<a class="layui-btn layui-btn-xs aftersales-order" data-id="' . $id . '">售后</a>';
                $html .= '<a class="layui-btn layui-btn-xs complete-order" data-id="' . $id . '">完成</a>';
//                if($ship_status == self::SHIP_STATUS_YES)
//                {
//                    $html .= '<a class="layui-btn layui-btn-primary layui-btn-xs order-logistics" data-id="'.$id.'">物流信息</a>';
//                }
            }
        }
        //取消
        if ($order_status == self::ORDER_STATUS_CANCEL) {
            $html .= '<a class="layui-btn layui-btn-danger layui-btn-xs del-order" data-id="' . $id . '">删除</a>';
        }
        return $html;
    }


    /**
     * 获取订单信息
     * @param $id
     * @param bool $user_id
     * @param bool $aftersale_level         //取售后单的时候，售后单的等级，0：待审核的和审核通过的售后单，1未审核的，2审核通过的
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderInfoByOrderID($id, $user_id = false, $aftersale_level = 0)
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
        //下单人
        $order_info['username'] = get_user_info($order_info['user_id'],'nickname');

        $order_info->items; //订单详情
        $order_info->user; //用户信息
        $order_info->paymentRelItem; //支付单
        $order_info->refundItem; //退款单
        $order_info->ladingItem; //提货单
        $order_info->returnItem; //退货单
        $order_info->aftersalesItem; //售后单

        //平摊商品价格
        $this->avePrice($order_info);

        //发货单
        $billDeliveryModel = new BillDelivery();
        $deliveryResult = $billDeliveryModel->getDeliveryList($id);
        $order_info['delivery'] = $deliveryResult['data'];

        //获取提货门店
        $order_info['store'] = false;
        if ($order_info['store_id'] != 0) {
            $storeModel = new Store();
            $storeInfo = $storeModel->get($order_info['store_id']);
            $storeInfo['all_address'] = get_area($storeInfo['area_id']) . $storeInfo['address'];
            $order_info['store'] = $storeInfo;
        }

        foreach ($order_info['delivery'] as &$v) {
            $v['logi_name'] = get_logi_info($v['logi_code'], 'logi_name');
        }

        $order_info->hidden(['user' => ['isdel', 'password']]);

        if ($order_info['logistics_id']) {
            $w[] = ['id', 'eq', $order_info['logistics_id']];
            $shipModel = new Ship();
            $order_info['logistics'] = $shipModel->where($w)->find();
        } else {
            $order_info['logistics'] = null;
        }
        $order_info['text_status'] = config('params.order')['status'][$order_info['status']];
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
        switch ($order_info['status']) {
            case self::ALL_PENDING_PAYMENT: //待付款
                $cancel = getSetting('order_cancel_time') * 86400;
                $ctime = $order_info['ctime'];
                $remaining = $ctime + $cancel - time();
                $order_info['remaining'] = $this->dateTimeTransformation($remaining);
                $order_info['remaining_time'] = $remaining;
                break;
            case self::ALL_PENDING_RECEIPT: //待收货
                $sign = getSetting('order_autoSign_time') * 86400;
                $utime = $order_info['utime'];
                $remaining = $utime + $sign - time();
                $order_info['remaining'] = $this->dateTimeTransformation($remaining);
                $order_info['remaining_time'] = $remaining;
                break;
            case self::ALL_PENDING_EVALUATE: //待评价
                $eval = getSetting('order_autoEval_time') * 86400;
                $confirm = $order_info['confirm_time'];
                $remaining = $confirm + $eval - time();
                $order_info['remaining'] = $this->dateTimeTransformation($remaining);
                $order_info['remaining_time'] = $remaining;
                break;
            default:
                $order_info['remaining'] = false;
                $order_info['remaining_time'] = false;
                break;
        }

//        //物流信息查询
//        $express_delivery = [];
//        if (isset($order_info['delivery'][0]) && $order_info['delivery'][0] && $logistics) {
//            foreach ($order_info['delivery'] as $v) {
//                $express = $billDeliveryModel->getLogistic($v['logi_code'], $v['logi_no']);
//                if ($express['status']) {
//                    $express_delivery[] = $express['data']['info']['data'][0];
//                } else {
//                    $express_delivery[] = [
//                        'context' => '已为你发货，请注意查收',
//                        'time' => date('Y-m-d H:i:s', $v['ctime'])
//                    ];
//                }
//            }
//        }
//        $order_info['express_delivery'] = $express_delivery;

        //支付单
        if (count($order_info['paymentRelItem']) > 0) {
            $billPaymentsModel = new BillPayments();
            foreach ($order_info['paymentRelItem'] as &$v) {
                $v['bill'] = $billPaymentsModel->get($v['payment_id']);
                $v['bill']['payment_code_name'] = config('params.payment_type')[$v['bill']['payment_code']];
                $v['bill']['status_name'] = config('params.bill_payments')['status'][$v['bill']['status']];
                $v['bill']['utime_name'] = getTime($v['bill']['utime']);
            }
        }

        //退款单
        if (count($order_info['refundItem']) > 0) {
            foreach ($order_info['refundItem'] as &$v) {
                $v['payment_code_name'] = config('params.payment_type')[$v['payment_code']];
                $v['status_name'] = config('params.bill_refund')['status'][$v['status']];
                $v['ctime_name'] = getTime($v['ctime']);
            }
        }

        //发货单
        if (count($order_info['delivery']) > 0) {
            $logiModel = new Logistics();
            $areaModel = new Area();
            foreach ($order_info['delivery'] as &$v) {
                $v['logi_code_name'] = $logiModel->getNameByCode($v['logi_code']);
                $v['ship_area_id_name'] = $areaModel->getAllName($v['ship_area_id']);
            }
        }

        //提货单
        if (count($order_info['ladingItem']) > 0) {
            $storeModel = new Store();
            $clerkModel = new Clerk();
            $billLadingModel = new BillLading();
            foreach ($order_info['ladingItem'] as &$v) {
                $v['store_id_name'] = $storeModel->getStoreName($v['store_id']);
                $v['status_name'] = config('params.bill_lading')['status'][$v['status']];
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
                $v['status_name'] = config('params.bill_reship')['status'][$v['status']];
                $v['utime_name'] = getTime($v['utime']);
            }
        }

        //售后单取当前活动的收货单
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
        //把退款金额和退货商品查出来
        $this->aftersalesVal($order_info, $aftersale_level);

        //促销信息
        if ($order_info['promotion_list']) {
            $order_info['promotion_list'] = json_decode($order_info['promotion_list'], true);
        }

        //发票信息
        $invoiceModel = new Invoice();
        $invoiceInfo = $invoiceModel->getOrderInvoiceInfo($id);
        if ($invoiceInfo['status']) {
            $order_info['invoice'] = $invoiceInfo['data'];
        } else {
            $order_info['invoice'] = [
                'type' => $order_info['tax_type'],
                'title' => $order_info['tax_title'],
                'tax_number' => $order_info['tax_code'],
            ];
        }

        return $order_info;
    }

    //平摊优惠，计算一下订单的实际价格
    private function avePrice(&$orderInfo)
    {
        $count     = count($orderInfo['items']);
        $total_pmt = 0;//累计优惠
        $total_order_pmt = $orderInfo['order_pmt']+$orderInfo['point_money'];//所有订单优惠，+$orderInfo['coupon_pmt']
        foreach ($orderInfo['items'] as $key => &$v) {
            if ($count - 1 == $key) {//最后一次
                $order_pmt = $total_order_pmt - $total_pmt;

                $ave_amount = round(($v['amount']-$order_pmt),2);
                $v['ave_price']  = round($ave_amount/ $v['nums'], 2);
                $v['ave_amount'] = $ave_amount;
            } else {
                if (!$orderInfo['goods_amount'] || $orderInfo['goods_amount'] == 0) {
                    $order_pmt = 0;
                } else {
                    $order_pmt = round($total_order_pmt * ($v['amount'] / $orderInfo['goods_amount']),2);
                }
                $ave_amount = round(($v['amount'] - $order_pmt), 2);
                $total_pmt += $order_pmt;
                $v['ave_price']  = round($ave_amount / $v['nums'], 2);
                $v['ave_amount'] = $ave_amount;
            }
        }
    }

    private function avePriceItem($orderInfo,&$items = [])
    {
        $count     = count($items);
        $total_pmt = 0;//累计优惠
        $total_order_pmt = $orderInfo['order_pmt']+$orderInfo['point_money'];//所有订单优惠，+$orderInfo['coupon_pmt']
        foreach ($items as $key => &$v) {
            if ($count - 1 == $key) {//最后一次
                $order_pmt = $total_order_pmt - $total_pmt;

                $ave_amount = round(($v['amount']-$order_pmt),2);
                $v['ave_price']  = round($ave_amount/ $v['nums'], 2);
                $v['ave_amount'] = $ave_amount;
            } else {
                if (!$orderInfo['goods_amount'] || $orderInfo['goods_amount'] == 0) {
                    $order_pmt = 0;
                } else {
                    $order_pmt = round($total_order_pmt * ($v['amount'] / $orderInfo['goods_amount']),2);
                }
                $ave_amount = round(($v['amount'] - $order_pmt), 2);
                $total_pmt += $order_pmt;
                $v['ave_price']  = round($ave_amount / $v['nums'], 2);
                $v['ave_amount'] = $ave_amount;
            }
        }
    }


    /**
     * 把退款的金额和退货的商品数量保存起来
     * @param $orderInfo
     * @param $aftersale_level      取售后单的时候，售后单的等级，0：待审核的和审核通过的售后单，1未审核的，2审核通过的
     * @return bool
     */
    public function aftersalesVal(&$orderInfo, $aftersale_level = 0)
    {
        $add_aftersales_status = false;     //是否可以提交售后,只要没退完就可以进行售后

        $billAftersalesModel = new BillAftersales();
        $re = $billAftersalesModel->orderToAftersales($orderInfo['order_id'], $aftersale_level);

        //已经退过款的金额
        $orderInfo['refunded'] = $re['data']['refund_money'];

        //算退货商品数量
        foreach ($orderInfo['items'] as $k => $v) {
            if (isset($re['data']['reship_goods'][$v['id']])) {
                $orderInfo['items'][$k]['reship_nums'] = $re['data']['reship_goods'][$v['id']]['reship_nums'];              //  退货的商品
                $orderInfo['items'][$k]['reship_nums_ed'] = $re['data']['reship_goods'][$v['id']]['reship_nums_ed'];        //  已发货的退货商品

                //商品总数量 - 已发货数量 - 未发货的退货数量（总退货数量减掉已发货的退货数量）
                if (!$add_aftersales_status && ($orderInfo['items'][$k]['nums']  - $orderInfo['items'][$k]['reship_nums']) > 0) {            //如果没退完，就可以再次发起售后
                    $add_aftersales_status = true;
                }
            } else {
                $orderInfo['items'][$k]['reship_nums'] = 0;                     //退货商品
                $orderInfo['items'][$k]['reship_nums_ed'] = 0;                  //已发货的退货商品

                if (!$add_aftersales_status) {            //没退货，就能发起售后
                    $add_aftersales_status = true;
                }
            }
        }
        //商品没退完或没退，可以发起售后，但是订单状态不对的话，也不能发起售后
        if ($orderInfo['pay_status'] == self::PAY_STATUS_NO || $orderInfo['status'] != self::ORDER_STATUS_NORMAL) {
            $add_aftersales_status = false;
        }
        $orderInfo['add_aftersales_status'] = $add_aftersales_status;


        return true;
    }


    /**
     * 时间转换
     * @param $time
     * @return string
     */
    protected function dateTimeTransformation($time)
    {
        $newtime = '';
        $d = floor($time / (3600 * 24));
        $h = floor(($time % (3600 * 24)) / 3600);
        $m = floor((($time % (3600 * 24)) % 3600) / 60);
        $s = floor((($time % (3600 * 24)) % 3600) % 60);
        $s = ($s < 10) ? '0' . $s : $s;
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
                    [$table_name . 'pay_status', 'neq', self::PAY_STATUS_NO],
                    [$table_name . 'ship_status', 'in', self::SHIP_STATUS_NO . ',' . self::SHIP_STATUS_PARTIAL_YES]
                ];
                break;
            case self::ALL_PENDING_RECEIPT: //待收货
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_NORMAL],
                    [$table_name . 'ship_status', 'in', self::SHIP_STATUS_YES . ',' . self::SHIP_STATUS_PARTIAL_YES],
                    [$table_name . 'confirm', 'eq', self::RECEIPT_NOT_CONFIRMED]
                ];
                break;
            case self::ALL_PENDING_EVALUATE: //待评价
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_NORMAL],
                    [$table_name . 'pay_status', 'neq', self::PAY_STATUS_NO],
                    [$table_name . 'ship_status', 'neq', self::SHIP_STATUS_NO],
                    [$table_name . 'confirm', 'eq', self::CONFIRM_RECEIPT],
                    [$table_name . 'is_comment', 'eq', self::NO_COMMENT]
                ];
                break;
            case self::ALL_COMPLETED_EVALUATE: //已评价
                $where = [
                    [$table_name . 'status', 'eq', self::ORDER_STATUS_NORMAL],
                    [$table_name . 'pay_status', 'neq', self::PAY_STATUS_NO],
                    [$table_name . 'ship_status', 'neq', self::SHIP_STATUS_NO],
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
        if ($billAftersalesCount > 0) {
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

        if ($info) {
            unset($result);
            $result = $info->save($data);
            //计算订单实际支付金额（要减去售后退款的金额）
            unset($money);
            unset($bawhere);
            unset($baList);
            $money = $info['payed'];
            $bawhere = [];
            $bawhere[] = ['order_id', 'eq', $id];
            $bawhere[] = ['status', 'eq', $baModel::STATUS_SUCCESS];
            $baList = $baModel->where($bawhere)->select();
            if ($baList && count($baList) > 0) {
                $refundMoney = 0;
                foreach ($baList as $k => $v) {
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
        } else {
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
        $where[] = ['order_id', 'in', $id];
        $where[] = ['pay_status', 'eq', self::PAY_STATUS_NO];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[] = ['ship_status', 'eq', self::SHIP_STATUS_NO];

        $msg = '后台订单取消操作';
        if ($user_id) {
            $where[] = ['user_id', 'eq', $user_id];
            $msg = '订单取消操作';
        }

        unset($order_info);
        $order_info = $this->where($where)
            ->select();

        unset($result);
        if ($order_info) {
            Db::startTrans();
            try {
                //更改状态和库存
                unset($order_ids);
                $order_ids = [];
                $orderLog = new OrderLog();
                foreach ($order_info as $k => $v) {
                    $order_ids[] = $v['order_id'];
                    //订单记录
                    $orderLog->addLog($v['order_id'], $v['user_id'], $orderLog::LOG_TYPE_CANCEL, $msg, $where);
                    //变更积分
                    if ($v['point'] > 0) {
                        $pointLogMode = new UserPointLog();
                        unset($res);
                        $res = $pointLogMode->setPoint($v['user_id'], $v['point'], $pointLogMode::POINT_TYPE_ADMIN_EDIT, '取消订单：' . $v['order_id'] . ' 返还积分');
                        if (!$res['status']) {
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
                foreach ($goods as $v) {
                    $goodsModel->changeStock($v['product_id'], 'cancel', $v['nums']);
                }

                $result = true;
                Db::commit();
                //再循环一遍，批量发送消息
                foreach($order_info as $key=>$val){
                    $val['reason'] = $msg;//取消原因
                    sendMessage($val['user_id'], 'order_cancle',$val);
                }
                hook('cancelorder', $order_info); // 订单取消的钩子
            } catch (\Exception $e) {
                $result = false;
                Db::rollback();
            }
        } else {
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
        $where[] = ['order_id', 'in', $order_ids];
        $where[] = ['user_id', 'eq', $user_id];

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
        $result = [
            'status' => false,
            'data' => "",
            'msg' => ""
        ];
        if(isset($data['order_amount'])){
            $udata['order_amount'] = $data['order_amount'];
        }
        $udata['source'] = $data['source'];
        $udata['cost_freight'] = $data['cost_freight'];

        if($data['delivery_type'] == 1){
            //快递
            $udata['ship_name'] = $data['ship_name'];
            $udata['ship_mobile'] = $data['ship_mobile'];
            $udata['ship_address'] = $data['ship_address'];
            $udata['ship_area_id'] = $data['ship_area_id'];
            $udata['store_id'] = 0;

        }else{
            //门店自提
            $storeModel = new Store();
            $info = $storeModel->where('id',$data['store_id'])->find();
            if(!$info){
                return error_code(10000);
            }
            $udata['ship_name'] = $data['tship_name'];
            $udata['ship_mobile'] = $data['tship_mobile'];
            $udata['ship_address'] = $info['address'];
            $udata['ship_area_id'] = $info['area_id'];
            $udata['store_id'] = $data['store_id'];
        }
        $udata['mark'] = $data['mark'];

        //发票信息
        if($data['tax_type'] == 2){
            $data['tax_code'] = "";
        }
        if($data['tax_type'] == 1){
            $data['tax_title'] = "";
        }
        $udata['tax_type'] = $data['tax_type'];
        $udata['tax_title'] = $data['tax_title'];
        $udata['tax_code'] = $data['tax_code'];

        $re = $this->save($udata,['order_id'=>$data['order_id']]);

        //订单记录
        $orderLog = new OrderLog();
        $orderLog->addLog($data['order_id'],0, $orderLog::LOG_TYPE_EDIT, '后台订单编辑修改', $udata);

        $result['status'] = true;
        $result['msg'] = "操作成功";

        return $result;
    }


    /**
     * 构建需要发货的数据，和发货单密切关联
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderShipInfo($ids)
    {
        $return = [
            'status' => true,
            'msg' => '',
            'data' => []
        ];
        $billAftersalesmodel = new BillAftersales();

        $where[] = ['order_id', 'in', $ids];
        $order = $this::with('items')
            ->field('order_id,user_id,pay_status,ship_status,status,logistics_id,logistics_name,cost_freight,ship_area_id,ship_address,ship_name,ship_mobile,weight,memo,store_id')
            ->where($where)
            ->select();
        if ($order->isEmpty()) {
            return error_code(13317);
        }
        $order = $order->toArray();
        $store_id = false;          //校验是普通快递收货，还是门店自提，这两种收货方式不能混着发
        foreach ($order as $k => $v) {
            if ($v['status'] != self::ORDER_STATUS_NORMAL) {
                $return['status'] = false;
                $return['msg'] .= error_code(13319,true,$v['order_id']);//'订单号：' . $v['order_id'] . ' 非正常状态不能发货。<br />';
            } elseif ($v['pay_status'] == self::PAY_STATUS_NO) {
                $return['status'] = false;
                $return['msg'] .= error_code(13320,true,$v['order_id']);//'订单号：' . $v['order_id'] . ' 未支付不能发货。<br />';
            } elseif (!in_array($v['ship_status'], [self::SHIP_STATUS_NO, self::SHIP_STATUS_PARTIAL_YES])) {
                $return['status'] = false;
                $return['msg'] .= error_code(13321,true,$v['order_id']);//'订单号：' . $v['order_id'] . ' 不是待发货和部分发货状态不能发货。<br />';
            }
            //校验，不能普通快递和门店自提，不能混发
            if ($store_id !== false) {
                if ($store_id != $v['store_id']) {
                    return error_code(13318);
                }
            } else {
                $store_id = $v['store_id'];
            }

            //判断是否有未审核的售后单，如果有，就不能发货，已做拦截
            $baInfo = $billAftersalesmodel->where(['order_id'=> $v['order_id'], 'status' => $billAftersalesmodel::STATUS_WAITAUDIT])->find();            //有一例都不让发货
            if($baInfo){
                $return['status'] = false;
                $return['msg'] = error_code(13322,true,$v['order_id']);//'订单号：'.$v['order_id'].'有未审核的售后单，请先处理掉才能发货。';
                return $return;
            }


            $this->aftersalesVal($order[$k]); //获取售后数量
        }
        if (!$return['status']) {
            return $return;
        }
        //------------------------------------------------

        $msg_arr = [
            'user_id' => true,
            'ship_info' => true
        ];

        $newOrder = [
            'order_id' => $ids,
            'weight' => 0,
            'memo' => [],
            'cost_freight' => 0,
            'store_id' => $order[0]['store_id'],
            'ship_area_id' => $order[0]['ship_area_id'],
            'ship_address' => $order[0]['ship_address'],
            'ship_name' => $order[0]['ship_name'],
            'ship_mobile' => $order[0]['ship_mobile'],
            'logistics_id' => $order[0]['logistics_id'],
            'items' => [],
            'orders' => $order                                  //把订单信息冗余上去
        ];
        foreach ($order as $v) {
            //组合总重量
            $newOrder['weight'] = $newOrder['weight'] + $v['weight'];
            $newOrder['cost_freight'] = $newOrder['cost_freight'] + $v['cost_freight'];
            //组合备注信息
            if ($v['memo'] && $v['memo'] != '') {
                $newOrder['memo'][$v['order_id']] = $v['memo'];
            }

            //组合明细
            //$this->aftersalesVal($v); //获取售后数量,在上面怼

            foreach ($v['items'] as $vv) {
                if (!isset($newOrder['items'][$vv['product_id']])) {
                    $newOrder['items'][$vv['product_id']] = $vv;
                } else {
                    $newOrder['items'][$vv['product_id']]['nums'] = $newOrder['items'][$vv['product_id']]['nums'] + $vv['nums']; //总数量
//                    $newOrder['items'][$vv['product_id']]['amount'] = $newOrder['items'][$vv['product_id']]['amount'] + $vv['amount']; //总价
//                    $newOrder['items'][$vv['product_id']]['promotion_amount'] = $newOrder['items'][$vv['product_id']]['promotion_amount'] + $vv['promotion_amount']; //总优惠金额
                    $newOrder['items'][$vv['product_id']]['weight'] = $newOrder['items'][$vv['product_id']]['weight'] + $vv['weight']; //总重量
                    $newOrder['items'][$vv['product_id']]['sendnums'] = $newOrder['items'][$vv['product_id']]['sendnums'] + $vv['sendnums']; //已发送数量
                    $newOrder['items'][$vv['product_id']]['reship_nums'] = $newOrder['items'][$vv['product_id']]['reship_nums'] + $vv['reship_nums']; //退货数量
                }
            }

            //判断是否有多个用户的订单
            if ($msg_arr['user_id']) {
                if ($msg_arr['user_id'] === true) {
                    $msg_arr['user_id'] = $v['user_id'];
                } else {
                    if ($msg_arr['user_id'] != $v['user_id']) {
                        $msg_arr['user_id'] = false;
                    }
                }
            }

            //判断是否是多个收货地址
            if ($msg_arr['ship_info']) {
                if ($msg_arr['ship_info'] === true) {
                    $msg_arr['ship_info'] = $v['ship_area_id'] . $v['ship_address'];
                } else {
                    if ($msg_arr['ship_info'] != $v['ship_area_id'] . $v['ship_address']) {
                        $msg_arr['ship_info'] = false;
                    }
                }
            }
        }

        //判断用户
        if (!$msg_arr['user_id']) {
            $return['msg'] .= error_code(13323,true);
        }
        //判断多个收货地址
        if (!$msg_arr['ship_info']) {
            $return['msg'] .= error_code(13324,true);
        }
        //是否有警告
        if ($return['msg'] != '') {
            $return['msg'] = rtrim($return['msg'], '，');
            $return['msg'] = error_code(13325,true,$return['msg']);//'请注意！合并发货订单中存在：' . $return['msg'] . '。确定发货吗？';
        }

        $return['data'] = $newOrder;
        return $return;
    }


    /**
     * 发货改状态
     * @param $order_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function ship($order_id, $item)
    {
        $where[] = ['order_id', 'eq', $order_id];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[] = ['ship_status', 'IN', [self::SHIP_STATUS_NO, self::SHIP_STATUS_PARTIAL_NO, self::SHIP_STATUS_PARTIAL_YES]];        //未发货，部分发货，部分退货状态(怕部分发货中的部分退货这种业务场景，所以加这个字段)
        $info = $this->where($where)->find();
        if (!$info) {
            return error_code(10000);
        }
        //更新订单明细发货数量，并校验是否发完
        $orderItemsModel = new OrderItems();
        $isOver = $orderItemsModel->ship($order_id, $item);
        if ($isOver) {
            $info->ship_status = self::SHIP_STATUS_YES;
        } else {
            $info->ship_status = self::SHIP_STATUS_PARTIAL_YES;
        }
        $info->save();
        return [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];
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
        $return_data = error_code(13007);

        $w[] = ['order_id', 'eq', $order_id];
        $w[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $order = $this->where($w)
            ->find();

        if (!$order) {
            return error_code(13101);
        }

        if ($order['pay_status'] == self::PAY_STATUS_YES || $order['pay_status'] == self::PAY_STATUS_PARTIAL_NO || $order['pay_status'] == self::PAY_STATUS_REFUNDED) {
            $return_data['msg'] = error_code(13008,true);
            $return_data['data'] = $order;
            $data = "订单" . $order_id . "支付失败，订单已经支付";
        } else {
            $data['payment_code'] = $payment_code;
            $data['payed'] = $order['order_amount'];
            $data['pay_status'] = self::PAY_STATUS_YES;
            $data['payment_time'] = time();
            $result = $this->where('order_id', 'eq', $order_id)
                ->update($data);

            $return_data['data'] = $result;
            if ($result !== false) {
                $return_data['status'] = true;
                $return_data['msg'] = '订单支付成功';

                //不同的订单类型会有不同的支付后的操作
            switch ($order['order_type']) {
                case self::ORDER_TYPE_COMMON:
                    //标准模式
                    break;
                case self::ORDER_TYPE_PINTUAN;
                    //拼团模式如果拼团满足拼团成功条件，做拼团成功状态的改变
                    $pintuanRecordModel = new PintuanRecord();
                    $pintuanRecordModel->pay($order_id);

                    break;
                case self::ORDER_TYPE_GROUP:
                    break;
                case self::ORDER_TYPE_SKILL:
                    break;
                case self::ORDER_TYPE_BARGAIN:
                    break;

            }
                

                //发票存储
                $invoiceModel = new Invoice();
                if ($order['tax_type'] != $invoiceModel::TAX_TYPE_NO) {
                    //组装发票信息
                    $taxInfo = [
                        'class' => $invoiceModel::TAX_CLASS_ORDER,
                        'source_id' => $order['order_id'],
                        'user_id' => $order['user_id'],
                        'type' => $order['tax_type'],
                        'title' => $order['tax_title'],
                        'tax_number' => $order['tax_code'],
                        'amount' => $order['order_amount'],
                        'status' => $invoiceModel::TAX_STATUS_NO
                    ];
                    $invoiceModel->add($taxInfo);
                }

                //发送支付成功信息,增加发送内容
                $order['pay_time'] = date('Y-m-d H:i:s', $data['payment_time']);
                $order['money'] = $order['order_amount'];
                $order['user_name'] = get_user_info($order['user_id']);
                sendMessage($order['user_id'], 'order_payed', $order);

                sendMessage($order['user_id'], 'seller_order_notice', $order);//给卖家发消息
                //订单支付完成后的钩子
                Hook('orderpayed', $order_id);
                Hook('orderpayedafter', $order);
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
     * @return array
     */
    public function confirm($order_id, $user_id = false)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $where[] = ['order_id', 'eq', $order_id];
        $where[] = ['pay_status', 'neq', self::PAY_STATUS_NO];
        $where[] = ['ship_status', 'neq', self::SHIP_STATUS_NO];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[] = ['confirm', 'neq', self::CONFIRM_RECEIPT];

        if ($user_id) {
            $where[] = ['user_id', 'eq', $user_id];
        }

        $data['confirm'] = self::CONFIRM_RECEIPT;
        $data['confirm_time'] = time();

        $info = $this->where($where)->find();
        if(!$info) return error_code(10000);
        Db::startTrans();
        try {
            //修改订单
            $re = $info->save($data, $where);
            if (!$re) {
//                $result['msg'] = "确认收货失败";
                Db::rollback();
                return error_log(13230);
            }else{
                //订单记录
                $orderLog = new OrderLog();
                $orderLog->addLog($order_id, $user_id, $orderLog::LOG_TYPE_SIGN, "确认收货成功", $where);
                $result['status'] = true;
            }

            //修改发货单,如果有为确认收货的发货单，那么给他们回传上去确认收货时间
//            $billDeliveryModel = new BillDelivery();
//            $billDeliveryModel->confirm($order_id);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
        }
        return $result;
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
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $order['order_id'] = get_sn(1);
        $order['user_id'] = $user_id;
        $order['order_type'] = $order_type;

        //生成收货信息
        $delivery_re = $this->formatOrderDelivery($order, $delivery);
        if (!$delivery_re['status']) {
            return $delivery_re;
        } else {
            $area_id = $delivery_re['data'];        //下单的省市区地址，算运费用。
        }
        //通过购物车生成订单信息和订单明细信息
        $order_re = $this->formatOrder($order, $user_id, $cart_ids, $area_id, $point, $coupon_code, false, $delivery['type'],$params);
        if (!$order_re['status']) {
            return $order_re;
        } else {
            $items = $order_re['data'];        //订单明细
        }

        //以下值不是通过购物车得来的，是直接赋值的，就写这里吧，不写formatOrder里了。
        $order['memo'] = $memo;
        $order['source'] = $source;
        $order['tax_type'] = $tax['tax_type'];
        $order['tax_title'] = $tax['tax_name'];
        $order['tax_code'] = $tax['tax_code'];

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
                $coupon = new Coupon();
                $coupon_res = $coupon->usedMultipleCoupon($coupon_code, $user_id);
                if (!$coupon_res['status']) {
                    Db::rollback();
                    return $coupon_res;
                }
            }

            //积分核销
            if ($order['point'] > 0) {
                $userPointLog = new UserPointLog();
                $pflag = $userPointLog->setPoint($user_id, 0 - $order['point'], $userPointLog::POINT_TYPE_DISCOUNT, $remarks = '订单：' . $order['order_id'] . ' 使用积分');
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
                case self::ORDER_TYPE_GROUP:
                    $promotionRecordModel = new PromotionRecord();
                    $pt_re = $promotionRecordModel->orderAdd($order, $items, $params,$promotionRecordModel::TYPE_GROUP);
                    if (!$pt_re['status']) {
                        Db::rollback();
                        return $pt_re;
                    }
                    break;
                case self::ORDER_TYPE_SKILL:
                    $promotionRecordModel = new PromotionRecord();
                    $pt_re = $promotionRecordModel->orderAdd($order, $items, $params,$promotionRecordModel::TYPE_SKILL);
                    if (!$pt_re['status']) {
                        Db::rollback();
                        return $pt_re;
                    }
                    break;
                case self::ORDER_TYPE_BARGAIN:
                    $bargainRecordModel = new BargainRecord();
                    $bo_re = $bargainRecordModel->updateRecord($params['record_id'], ['order_id'=>$order['order_id'],'status'=>$bargainRecordModel::STATUS_HAVE_ORDER]);
                    if (!$bo_re) {
                        Db::rollback();
                        return error_code(13231);
                    }
                    break;
                case self::ORDER_TYPE_COMBO:
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
            $order['tax_title'] = $tax['tax_name'];
            $order['tax_code'] = $tax['tax_code'];
            $order['tax_content'] = '商品明细';

            //发送消息
            $shipModel = new Ship();
            $ship = $shipModel->getInfo(['id' => $order['logistics_id']]);
            $order['ship_id'] = $ship['name'];
            $order['ship_addr'] = get_area($order['ship_area_id']) . $order['ship_address'];
            $order['form_id'] = $formId;
            sendMessage($user_id, 'create_order', $order);

            $result['status'] = true;
            $result['data'] = $order;
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
     * @param array $params 扩展信息字段
     * @return array|mixed //返回订单明细信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function formatOrder(&$order, $user_id, $cart_ids, $area_id, $point, $coupon_code, $free_freight = false, $delivery_type = '1',$params = [])
    {
        $cartModel = new Cart();
        $cartInfo = $cartModel->info($user_id, $cart_ids, $order['order_type'],false, $area_id, $point, $coupon_code, $free_freight, $delivery_type,$params);
        if (!$cartInfo['status']) {
            return $cartInfo;
        }
        $order['goods_amount'] = $cartInfo['data']['goods_amount'];
        $order['order_amount'] = $cartInfo['data']['amount'];
        if ($order['order_amount'] == 0) {
            $order['pay_status'] = self::PAY_STATUS_YES;
            $order['payment_time'] = time();
        }
        $order['cost_freight'] = $cartInfo['data']['cost_freight'];

        //优惠信息存储
        $promotion_list = [];
        foreach ($cartInfo['data']['promotion_list'] as $k => $v) {
            if (isset($v['type']) && $v['type'] == 2) {
                $promotion_list[] = $v;
            }
        }
        $order['promotion_list'] = json_encode($promotion_list);

        //积分使用情况
        $order['point'] = $cartInfo['data']['point'];
        $order['point_money'] = $cartInfo['data']['point_money'];

        $order['weight'] = $cartInfo['data']['weight'];
        $order['order_pmt'] = isset($cartInfo['data']['order_pmt']) ? $cartInfo['data']['order_pmt'] : 0;
        $order['goods_pmt'] = isset($cartInfo['data']['goods_pmt']) ? $cartInfo['data']['goods_pmt'] : 0;
        $order['coupon_pmt'] = $cartInfo['data']['coupon_pmt'];
        $order['coupon'] = json_encode($cartInfo['data']['coupon']);
        $order['ip'] = get_client_ip(0, true);


        //以上保存了订单主体表信息，以下生成订单明细表
        $items = $this->formatOrderItems($cartInfo['data']['list'], $order['order_id']);
        if (!$items) {
            return error_code(10000);       //判断订单明细为空的不能下单
        }
        //平摊商品价格,计算实际单价
        $this->avePriceItem($order,$items);
        return [
            'status' => true,
            'data' => $items,           //订单主体表通过引用直接返回值，订单明细通过这里返回值
            'msg' => ''
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

            $item['order_id'] = $order_id;
            $item['goods_id'] = $v['products']['goods_id'];
            $item['product_id'] = $v['products']['id'];
            $item['sn'] = $v['products']['sn'];
            $item['bn'] = $v['products']['bn'];
            if($v['type'] == Cart::TYPE_GIVEAWAY){
                $item['name'] = self::GIVEAWAY_STR.$v['products']['name'];
                $item['is_gift'] = 1;//是赠品，标记一下
            }else{
                $item['name'] = $v['products']['name'];
            }

            $item['price'] = $v['products']['price'];
            $item['costprice'] = $v['products']['costprice'];
            $item['mktprice'] = $v['products']['mktprice'];
            $item['image_url'] = get_goods_info($v['products']['goods_id'], 'image_id');
            $item['nums'] = $v['nums'];
            $item['amount'] = $v['products']['amount'];
            $item['promotion_amount'] = isset($v['products']['promotion_amount']) ? $v['products']['promotion_amount'] : 0;
            $item['weight'] = bcmul($v['weight'], $v['nums'], 2);
            $item['sendnums'] = 0;
            $item['addon'] = $v['products']['spes_desc'];
            // 是否免单商品
            $item['is_free'] = $v['products']['is_free'] ? $v['products']['is_free'] : 0;
            if (isset($v['products']['promotion_list'])) {
                $item['promotion_list'] = json_encode($v['products']['promotion_list']);
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
            $ushopInfo = $ushopModel->getShipById($delivery['uship_id'], $order['user_id']);
            if (!$ushopInfo) {
                return error_code(11050);
            }
            $area_id = $ushopInfo['area_id'];

            //快递邮寄
            $order['ship_area_id'] = $ushopInfo['area_id'];
            $order['ship_address'] = $ushopInfo['address'];
            $order['ship_name'] = $ushopInfo['name'];
            $order['ship_mobile'] = $ushopInfo['mobile'];
            $shipModel = new Ship();
            $shipInfo = $shipModel->getShip($ushopInfo['area_id']);
            $order['logistics_id'] = $shipInfo['id'];
            $order['logistics_name'] = $shipInfo['name'];
            $order['store_id'] = 0;
        } else {
            //门店自提
            $storeModel = new Store();
            $storeInfo = $storeModel->get($delivery['store_id']);
            if (!$storeInfo) {
                return error_code(11055);
            }
            $area_id = $storeInfo['area_id'];

            //门店自提
            $order['ship_area_id'] = $storeInfo['area_id'];
            $order['ship_address'] = $storeInfo['address'];
            $order['ship_name'] = $delivery['lading_name'];
            $order['ship_mobile'] = $delivery['lading_mobile'];
            $order['store_id'] = $delivery['store_id'];
            $order['logistics_id'] = 0;
        }

        return [
            'status' => true,
            'data' => $area_id,
            'msg' => ''
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
                    'msg' => '可以评价',
                    'data' => $res
                ];
            } else {
                return error_code(13405);
            }
        } else {
            return error_code(13009);
        }
        return $data;
    }


    /**
     * 自动取消订单
     * @param $setting
     * @return bool
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
//        $where[] = ['order_type', 'eq', self::ORDER_TYPE_COMMON];
        $where[] = ['order_type', 'in', self::ORDER_TYPE_COMMON.','.self::ORDER_TYPE_PINTUAN.','.self::ORDER_TYPE_GROUP.','.self::ORDER_TYPE_SKILL];

        $order_info = $this->field('order_id,pay_status,status,ctime,order_type')
            ->where($where)
            ->limit(50)
            ->select();

        $result = true;
        if (count($order_info) > 0) {
            $order_ids = [];
            foreach ($order_info as $v) {
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
        $where[] = ['pay_status', 'neq', self::PAY_STATUS_NO];
        $where[] = ['ship_status', 'neq', self::SHIP_STATUS_NO];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[] = ['utime', '<=', time() - $setting * 86400];
        $where[] = ['confirm', 'neq', self::CONFIRM_RECEIPT];

        unset($order_list);
        $order_list = $this->field('order_id,user_id,pay_status,ship_status,status,utime,confirm')
            ->where($where)
            ->select();
        if (count($order_list) > 0) {
            foreach ($order_list as $v) {
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
        $where[] = ['pay_status', 'eq', self::PAY_STATUS_YES];
        $where[] = ['ship_status', 'eq', self::SHIP_STATUS_YES];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $where[] = ['confirm', 'eq', self::CONFIRM_RECEIPT];
        $where[] = ['is_comment', 'eq', self::NO_COMMENT];
        $where[] = ['confirm_time', '<=', time() - $setting * 86400];
        $order_info = $this::with('items')->field('order_id, user_id')->where($where)
            ->select()->toArray();

        unset($order_ids);
        $order_ids = [];
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
        $wheres[] = ['order_id', 'in', $order_ids];
        $data['is_comment'] = self::ALREADY_COMMENT;
        $data['utime'] = time();
        $this->where($wheres)->update($data);

        //查询评价商品
        unset($goods_comment);
        $goods_comment = [];
        $gid = [];
        foreach ($order_items as $vo) {
            $goods_comment[] = [
                'score' => 5,
                'user_id' => $vo['user_id'],
                'goods_id' => $vo['goods_id'],
                'product_id' => $vo['product_id'],
                'order_id' => $vo['order_id'],
                'name' => $vo['name'],
                'addon' => $vo['addon'],
                'content' => '用户' . $setting . '天内未对商品做出评价，已由系统自动评价。',
                'ctime' => time(),
            ];
            if (isset($gid[$vo['goods_id']])) {
                $gid[$vo['goods_id']] += 1;
            } else {
                $gid[$vo['goods_id']] = 1;
            }
        }
        $goodsCommentModel = new GoodsComment();
        $goodsCommentModel->insertAll($goods_comment);

        // 统计到商品评论总条数
        //商品表更新评论数量
        foreach ($gid as $goods_id => $inc) {
            $goodsModel = new Goods();
            $goodsModel->where('id', $goods_id)->setInc('comments_count', $inc);
        }
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

        if (count($order_list) > 0) {
            foreach ($order_list as $v) {
                $this->complete($v['order_id']);
            }
        }
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
        $where[] = ['pay_status', 'eq', self::PAY_STATUS_NO];
        $where[] = ['status', 'eq', self::ORDER_STATUS_NORMAL];
        $remind_order_time = getSetting('remind_order_time');//催付款时间
        $second = $setting * 86400 - $remind_order_time * 3600;

        $second = time() - $second;

        $where[] = ['ctime', '<=', $second];
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
        $result = error_code(10083);
        $header = $this->csvHeader();
        $orderData = $this->getListFromAdmin($post, false);
        if ($orderData['count'] > 0) {
            $tempBody = $orderData['data'];
            $body = [];
            $i = 0;
            foreach ($tempBody as $key => $val) {
                $i++;
                $orderItems = $this->orderItems($val['order_id']);
                $itemData = [];
                /*foreach ($header as $hk => $hv) {
                    if (isset($hv['modify']) && isset($val[$hv['id']]) && $val[$hv['id']]) {
                        if (function_exists($hv['modify'])) {
                            $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                        }
                    } elseif (isset($val[$hv['id']]) && $val[$hv['id']]) {
                        $body[$i][$hk] = $val[$hv['id']];
                    } else {
                        $body[$i][$hk] = '';
                    }
                }*/

                foreach ($orderItems as $itemKey => $itemVal) {
                    $i++;
                    $sval = $val;
                    $sval['item_name'] = $itemVal['name'] . '-' . $itemVal['addon'];
                    $sval['item_price'] = $itemVal['price'];
                    $sval['item_nums'] = $itemVal['nums'];
                    $sval['item_amount'] = $itemVal['amount'];
                    $sval['item_sn'] = $itemVal['sn'];
                    $sval['item_bn'] = $itemVal['bn'];
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
            $result['msg'] = '获取成功';
            $result['data'] = $body;
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
                'id' => 'order_id',
                'desc' => '订单号',
                'modify' => 'convertString'
            ],
            [
                'id' => 'ctime',
                'desc' => '下单时间',
                'modify' => 'getTime',
            ],
            [
                'id' => 'status_text',
                'desc' => '订单状态',
            ],
            [
                'id' => 'username',
                'desc' => '用户名',
            ],
            [
                'id' => 'ship_name',
                'desc' => '收货人',
            ],
            [
                'id' => 'area_name',
                'desc' => '收货地址',
            ],
            [
                'id' => 'ship_mobile',
                'desc' => '收货人手机号',
                'modify' => 'convertString'
            ],
            [
                'id' => 'pay_status',
                'desc' => '支付状态',
            ],
            [
                'id' => 'ship_status',
                'desc' => '发货状态',
            ],
            [
                'id' => 'order_amount',
                'desc' => '订单总额',
            ],
            [
                'id' => 'source',
                'desc' => '订单来源',
            ],
            [
                'id' => 'item_name',
                'desc' => '商品名称',
            ],
            [
                'id' => 'item_price',
                'desc' => '商品单价',
            ],
            [
                'id' => 'item_nums',
                'desc' => '购买数量',
            ],
            [
                'id' => 'item_amount',
                'desc' => '商品总价',
            ],
            [
                'id' => 'item_sn',
                'desc' => '货品编码',
            ],
            [
                'id' => 'item_bn',
                'desc' => '商品编码',
            ],
            [
                'id' => 'item_weight',
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
        $items = $itemModel->field('*')->where(['order_id' => $order_id])->select();
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
            'msg' => error_code(13310,true),
            'data' => $mark
        ];

        $where[] = ['order_id', 'eq', $order_id];
        $data['mark'] = $mark;
        $result = $this->save($data, $where);

        if ($result !== false) {
            $return['status'] = true;
            $return['msg'] = '备注成功';
        }

        return $return;
    }


    /**
     * 查询团购秒杀下单数量
     * @param int $product_id
     * @param int $user_id
     * @param array $condition
     * @param int $order_type 订单类型
     * @return array
     */
    public function findLimitOrder($product_id = 0, $user_id = 0, $condition = [], $order_type = 0)
    {
        $return = [
            'status' => true,
            'msg'    => '查无订单',
            'data'   => [
                'total_orders'      => 0,
                'total_user_orders' => 0,
            ],
        ];
        if(!$product_id){
            return error_code(14011);
        }
        //计算订单总量
        $where   = [];
        $where[] = ['oi.product_id', '=', $product_id];
        $where[] = ['o.status', 'in', [self::ORDER_STATUS_NORMAL, self::ORDER_STATUS_COMPLETE]];//正常订单和已完成订单



        //已退款、已退货、部分退款的、部分退货的排除
        $where[] = ['o.pay_status', 'in', ['1', '2', '3']];
        $where[] = ['o.ship_status', 'in', ['1', '2', '3']];

        //已退款、已退货、部分退款的、部分退货的排除 todo 团购秒杀部分退换货问题
        //$where[] = ['o.pay_status', 'in',['1','2','3','4','5']];
        //$where[] = ['o.ship_status', 'in',['1','2','3','4','5']];


        //团购秒杀id
        if (isset($condition['id']) && $condition['id'] && ($order_type == self::ORDER_TYPE_GROUP || $order_type == self::ORDER_TYPE_SKILL)) {
            $where[] = ['pr.promotion_id', '=', $condition['id']];
        }
        //订单类型
        if ($order_type) {
            $where[] = ['o.order_type', '=', $order_type];
        }

        if ($order_type == self::ORDER_TYPE_PINTUAN) {

            //在活动时间范围内,拼团同一一个商品，同一个事件段内，只能有一个拼团，所以加上时间判断
            $where[] = ['o.ctime', '>=', $condition['stime']];
            $where[] = ['o.ctime', '<', $condition['etime']];

            $where[] = ['pr.rule_id', '=', $condition['rule_id']];

            $total_orders = $this->alias('o')
                ->join('order_items oi', 'oi.order_id = o.order_id')
                ->join('pintuan_record pr', 'pr.order_id = o.order_id')
                ->where($where)
                ->sum('oi.nums');

            //该会员已下多少订单
            $total_user_orders = 0;
            if ($user_id) {
                $where[]           = ['o.user_id', '=', $user_id];
                $total_user_orders = $this->alias('o')
                    ->join('order_items oi', 'oi.order_id = o.order_id')
                    ->join('pintuan_record pr', 'pr.order_id = o.order_id')
                    ->where($where)
                    ->sum('oi.nums');
            }

        } elseif ($order_type == self::ORDER_TYPE_SKILL || $order_type == self::ORDER_TYPE_GROUP) {
            $total_orders = $this->alias('o')
                ->join('order_items oi', 'oi.order_id = o.order_id')
                ->join('promotion_record pr', 'pr.order_id=o.order_id')
                ->where($where)
                ->sum('oi.nums');

            //该会员已下多少订单
            $total_user_orders = 0;
            if ($user_id) {
                $where[]           = ['o.user_id', '=', $user_id];
                $total_user_orders = $this->alias('o')
                    ->join('order_items oi', 'oi.order_id = o.order_id')
                    ->join('promotion_record pr', 'pr.order_id=o.order_id')
                    ->where($where)
                    ->sum('oi.nums');
            }
        } else {
            //在活动时间范围内
            /*$where[] = ['o.ctime', '>=', $condition['stime']];
            $where[] = ['o.ctime', '<', $condition['etime']];*/

            $total_orders = $this->alias('o')
                ->join('order_items oi', 'oi.order_id = o.order_id')
                ->where($where)
                ->sum('oi.nums');

            //该会员已下多少订单
            $total_user_orders = 0;
            if ($user_id) {
                $where[]           = ['o.user_id', '=', $user_id];
                $total_user_orders = $this->alias('o')
                    ->join('order_items oi', 'oi.order_id = o.order_id')
                    ->where($where)
                    ->sum('oi.nums');
            }
        }


        $return['msg']  = '查询成功';
        $return['data'] = [
            'total_orders'      => $total_orders,
            'total_user_orders' => $total_user_orders,
        ];
        return $return;
    }
    public function createAftersales($order_id){
        $order = $this->get($order_id);
        if($order['status'] != self::ORDER_STATUS_NORMAL || $order['pay_status'] == self::PAY_STATUS_NO) return error_code(10000);
        if($order['ship_status'] == self::SHIP_STATUS_NO){
            $type = 1;
        }else{
            $type = 2;
        }
        $aftersalesModel = new BillAftersales();
        return $aftersalesModel->toAdd($order['user_id'],$order_id,$type,[],[],'后台创建售后单',0);
    }


    // 检查订单支付状态
    public function checkOrderStatus($order_id)
    {
        $result = [
            'status' => true,
            'smg'    => '',
            'data'   => []
        ];
        $res    = $this->where('order_id', $order_id)->value('pay_status');
        if ($res == self::PAY_STATUS_YES) {
            // 支付失败，该订单已支付
            return error_code(13008);
        } else if ($res == self::PAY_STATUS_NO) {
            return $result;
        }
        return $result;
    }
}