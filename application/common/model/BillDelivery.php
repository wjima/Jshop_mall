<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://www.jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Validate;
use org\Exp;
use think\Db;

/**
 * 发货单
 * Class BillDelivery
 * @package app\common\model
 * @author keinx
 */
class BillDelivery extends Common
{
    const STATUS_READY = 1;                 //准备发货
    const STATUS_ALREADY = 2;               //已发货
    const STATUS_CONFIRM = 3;               //已确认
    const STATUS_OTHER = 4;                 //其他
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

    /**
     * 发货详情关联
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('BillDeliveryItems', 'delivery_id', 'delivery_id');
    }


    /**
     * 发货
     * @param $order_id
     * @param $logi_code
     * @param $logi_no
     * @param $memo
     * @param $ship_data
     * @param int $number
     * @param string $ship_info
     * @return array|bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ship($order_id, $logi_code, $logi_no, $memo, $ship_data, $number = 1, $ship_info = '')
    {
        $return = [
            'status' => false,
            'msg' => '发货失败',
            'data' => ''
        ];

        //获取订单详情
        $orderModel = new Order();
        $isOrderShip = $orderModel->isOrderShipInfo($order_id);
        $order = $isOrderShip['data'];

        if ($isOrderShip['status']) {
            $resOrder = $orderModel->combinedOrderProcessing($order);

            //数量验证
            foreach ($ship_data as $v) {
                if (!isset($resOrder['data']['items'][$v[0]])) {
                    return error_code(13303);
                }
                $orderData = $resOrder['data']['items'][$v[0]];
                $max_ship_nums = $orderData['nums'] - $orderData['sendnums'] - $orderData['reship_nums'];
                if ($max_ship_nums < $v[1]) {
                    return error_code(13304);
                }
            }

            //组装发货总单需要的信息
            $delivery_id = get_sn(8);
            if ($number == 1) {
                $ship_area_id = $order[0]['ship_area_id'];
                $ship_address = $order[0]['ship_address'];
                $ship_name = $order[0]['ship_name'];
                $ship_mobile = $order[0]['ship_mobile'];
            } else {
                $ship_area_id = $resOrder['data']['ship_info'][$ship_info]['ship_area_id'];
                $ship_address = $resOrder['data']['ship_info'][$ship_info]['ship_address'];
                $ship_name = $resOrder['data']['ship_info'][$ship_info]['ship_name'];
                $ship_mobile = $resOrder['data']['ship_info'][$ship_info]['ship_mobile'];
            }
            $bull_delivery = [
                'delivery_id' => $delivery_id,
                'logi_code' => $logi_code,
                'logi_no' => $logi_no,
                'ship_area_id' => $ship_area_id,
                'ship_address' => $ship_address,
                'ship_name' => $ship_name,
                'ship_mobile' => $ship_mobile,
                'status' => self::STATUS_READY,
                'memo' => $memo,
                'ctime' => time(),
                'utime' => time()
            ];
            $order_ids = explode(',', $resOrder['data']['order_id']);

            //售后信息
            foreach ($order as &$v) {
                $orderModel->aftersalesVal($v);
            }

            Db::startTrans();
            try {
                //插入发货总单
                $this->insert($bull_delivery);

                //插入发货详单，修改库存
                $goodsModel = new Goods();
                $orderItemsModel = new OrderItems();
                $item_data = [];
                foreach ($ship_data as $s) {
                    if ($s[1] > 0) {
                        foreach ($order as $vv) {
                            $order_ship_items = [];
                            foreach ($vv['items'] as $vvv) {
                                if ($vvv['product_id'] == $s[0]) {
                                    //发货处理
                                    $nowGoodsShipNum = $vvv['nums'] - $vvv['sendnums'] - $vvv['reship_nums'];
                                    if ($nowGoodsShipNum > 0) {
                                        $nums = $nowGoodsShipNum >= $s[1] ? $s[1] : $nowGoodsShipNum;
                                        if ($nums > 0) {
                                            $item_data[] = [
                                                'delivery_id' => $delivery_id,
                                                'order_items_id' => $vvv['id'],
                                                'nums' => $nums
                                            ];
                                            $s[1] = $s[1] - $nums;

                                            $orderItems = $orderItemsModel->field('product_id')
                                                ->where('id', 'eq', $vvv['id'])
                                                ->find();
                                            if (!$orderItems) {
                                                return error_code(13306);
                                            }
                                            //库存更改失败直接跳过
                                            $goodsModel->changeStock($orderItems['product_id'], 'send', $nums);

                                            $order_ship_items[] = [
                                                $vvv['id'],
                                                $nums
                                            ];
                                        }
                                    }
                                }
                            }
                            //修改订单详细表
                            $orderItemsModel->ship($vv['order_id'], $order_ship_items);
                        }
                    }
                }
                $billDeliveryItemsModel = new BillDeliveryItems();
                $billDeliveryItemsModel->insertAll($item_data);

                //修改订单主表和发货单订单关联表
                $orderLog = new OrderLog();
                $billDeliveryOrderRelModel = new BillDeliveryOrderRel();
                $bill_rel = [];
                $msg_order = [];
                foreach ($order_ids as $id) {
                    $order_flag = $orderModel->ship($id);
                    if ($order_flag['status']) {
                        //订单记录
                        foreach ($order as $one) {
                            if ($one['order_id'] == $id) {
                                //添加记录
                                $orderLog->addLog($one['order_id'], $one['user_id'], $orderLog::LOG_TYPE_SHIP, '订单发货操作，发货单号：' . $delivery_id, [$order_id, $logi_code, $logi_no, $memo, $ship_data]);
                                //添加要发送消息的
                                $msg_order[] = $one;
                            }
                        }
                        //插入发货单订单关联表
                        $bill_rel[] = [
                            'order_id' => $id,
                            'delivery_id' => $delivery_id
                        ];
                    }
                }
                $billDeliveryOrderRelModel->insertAll($bill_rel);

                $return['status'] = true;
                $return['msg'] = '发货成功';
                Db::commit();

                //发送发货成功信息
                $shipModel = new Ship();
                $ship = $shipModel->getInfo(['id' => $order[0]['logistics_id']]);
                foreach ($msg_order as $msg_info) {
                    $msg_info['ship_id'] = $ship['name'];
                    $msg_info['ship_addr'] = get_area($bull_delivery['ship_area_id']) . $bull_delivery['ship_address'];
                    $msg_info['logistics_name'] = get_logi_info($logi_code);
                    $msg_info['ship_no'] = $logi_no;
                    sendMessage($msg_info['user_id'], 'delivery_notice', $msg_info);
                }
            } catch (\Exception $e) {
                $return['data'] = $e->getMessage();
                Db::rollback();
            }
        } else {
            $return['msg'] = $isOrderShip['msg'];
        }

        return $return;
    }

    /**
     * 确认签收
     * @param $order_id
     * @return false|int
     */
    public function confirm($order_id)
    {
        $where[] = ['order_id', 'eq', $order_id];
        $data['confirm_time'] = time();
        $res = $this->save($data, $where);
        return $res;
    }

    /**
     * 获取物流信息接口
     * 根据订单号查询
     * User:tianyu
     * @param string $order_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLogisticsInformation($order_id)
    {
        $result = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];
        $deliveryInfo = $this->where('order_id', $order_id)->find();
        if ($deliveryInfo) {
            // 获取发货单物流公司编码和单号
            if (!$deliveryInfo['logi_code'] && !$deliveryInfo['logi_no']) {
                return error_code(10051);
            }

            $logistics = $this->logistics_query($deliveryInfo['logi_code'], $deliveryInfo['logi_no']);
            if ($logistics['status'] === '200') {
                $result['status'] = true;
                $result['msg'] = '获取成功';
                $result['data'] = [
                    'list' => $logistics['data'],
                    'state' => config('params.order')['logistics_state'][$logistics['state']]
                ];
            } else {
                $result['msg'] = $logistics['message'];
            }
        }
        return $result;
    }


    /**
     * 物流信息查询根据快递编码和单号查询
     * @param $code
     * @param $no
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLogistic($code, $no)
    {
        $result = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];

        if (checkAddons('logisticsQuery')) {
            $Info = hook("logisticsQuery", ['code' => $code, 'no' => $no]);
            $logisticsInfo = $Info[0];//快递查询插件，只用第一个
        } else {
            $logisticsInfo = $this->logistics_query($code, $no);
        }
        if ($logisticsInfo['status'] === '200') {
            $result['data']['info'] = [
                'no' => $logisticsInfo['nu'],
                'data' => $logisticsInfo['data'],
                'state' => $logisticsInfo['state'],
                'state_name' => config('params.order')['logistics_state'][$logisticsInfo['state']]
            ];
        } else {
            $result['status'] = false;
            $result['msg'] = $logisticsInfo['message'] ? $logisticsInfo['message'] : "暂无消息";
        }

        return $result;
    }

    /**
     *  api获取快递信息
     * User:tianyu
     * @param $com      快递公司编码
     * @param $code     物流单号
     * @return mixed
     */
    private function logistics_query($com, $code)
    {
        $exp = new Exp();
        $res = $exp->postCurl($exp->assembleParam($com, $code));
        return $res;
    }


    /**
     * 获取发货单列表
     * @param int $page
     * @param int $limit
     * @param array $input
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($page = 1, $limit = 20, $input = [])
    {
        $where = [];
        if ($input['delivery_id']) {
            $where[] = ['d.delivery_id', 'like', '%' . $input['delivery_id'] . '%'];
        }
        if ($input['order_id']) {
            $where[] = ['r.order_id', 'like', '%' . $input['order_id'] . '%'];
        }
        if ($input['logi_no']) {
            $where[] = ['d.logi_no', 'like', '%' . $input['logi_no'] . '%'];
        }
        if ($input['mobile']) {
            $where[] = ['d.ship_mobile', 'like', '%' . $input['mobile'] . '%'];
        }
        if ($input['date']) {
            $date = explode(' 到 ', $input['date']);
            $where[] = ['d.ctime', 'between time', [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59']];
        }
        $res = $this->alias('d')
            ->field('d.*')
            ->join('bill_delivery_order_rel r', 'd.delivery_id = r.delivery_id')
            ->where($where)
            ->group('r.delivery_id')
            ->order('ctime desc')
            ->page($page, $limit)
            ->select();
        $count = $this->alias('d')
            ->join('bill_delivery_order_rel r', 'd.delivery_id = r.delivery_id')
            ->group('r.delivery_id')
            ->where($where)
            ->count();
        if ($res) {
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res,
                'count' => $count
            ];
        } else {
            $return_data = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $res,
                'count' => $count
            ];
        }
        return $return_data;
    }

    /**
     * 获取发货单详情
     * @param $delivery_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDeliveryInfo($delivery_id)
    {
        $where[] = ['delivery_id', 'eq', $delivery_id];

        $res = $this::with('items')->where($where)
            ->find();
        if ($res) {
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res
            ];
        } else {
            $return_data = [
                'status' => false,
                'msg' => '获取失败',
                'data' => $res
            ];
        }
        return $return_data;
    }

    /**
     * 发货单统计
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function statistics()
    {
        $num = 7;
        $day = date('Y-m-d', strtotime('-' . $num . ' day'));

        $res = $this->field(['count(1)' => 'nums', 'DATE_FORMAT(FROM_UNIXTIME(ctime),"%Y-%m-%d")' => 'day'])
            ->where('FROM_UNIXTIME(ctime) >= ' . $day)
            ->group('DATE_FORMAT(FROM_UNIXTIME(ctime),"%Y-%m-%d")')
            ->select();

        $data = get_lately_days($num, $res);
        return $data['data'];
    }

    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id' => 'delivery_id',
                'desc' => '发货单号',
                'modify' => 'convertString'
            ],
            [
                'id' => 'order_id',
                'desc' => '订单号',
                'modify' => 'convertString'
            ],
            [
                'id' => 'username',
                'desc' => '用户名',
            ],
            [
                'id' => 'logi_name',
                'desc' => '快递公司',
            ],
            [
                'id' => 'logi_no',
                'desc' => '快递单号',
            ],
            [
                'id' => 'ship_address',
                'desc' => '收货地址',
            ],
            [
                'id' => 'ship_name',
                'desc' => '收货人',

            ],
            [
                'id' => 'ship_mobile',
                'desc' => '收货电话',
                'modify' => 'convertString'
            ],
            [
                'id' => 'status',
                'desc' => '发货单状态',

            ],
            [
                'id' => 'ctime',
                'desc' => '创建时间',

            ],
        ];
    }

    /**
     * 获取csv数据
     * @param $post
     * @return array
     */
    public function getCsvData($post)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '无可导出数据'
        ];
        $header = $this->csvHeader();
        $userData = $this->getExportList($post);
        if ($userData['count'] > 0) {
            $tempBody = $userData['data'];
            $body = [];
            $i = 0;

            foreach ($tempBody as $key => $val) {
                $i++;
                foreach ($header as $hk => $hv) {
                    if (isset($val[$hv['id']]) && $val[$hv['id']] && isset($hv['modify'])) {
                        if (function_exists($hv['modify'])) {
                            $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                        }
                    } elseif (isset($val[$hv['id']]) && !empty($val[$hv['id']])) {
                        $body[$i][$hk] = $val[$hv['id']];
                    } else {
                        $body[$i][$hk] = '';
                    }
                }
            }
            $result['status'] = true;
            $result['msg'] = '导出成功';
            $result['data'] = $body;
            return $result;
        } else {
            //失败，导出失败
            return $result;
        }
    }

    /**
     * 导出验证
     * @param array $params
     * @return array
     */
    public function exportValidate(&$params = [])
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '参数丢失',
        ];
        return $result;
    }

    //导出格式
    public function getExportList($input = [])
    {
        $return_data = [
            'status' => false,
            'msg' => '获取失败',
            'data' => '',
            'count' => 0
        ];
        $where = [];

        if (isset($input['id']) && $input['id'] != "") {
            $where[] = ['delivery_id', 'in', $input['id']];
        }

        if ($input['delivery_id']) {
            $where[] = ['delivery_id', 'like', '%' . $input['delivery_id'] . '%'];
        }
        if ($input['date']) {
            $date = explode(' 到 ', $input['date']);
            $where[] = ['ctime', 'between time', [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59']];
        }
        if ($input['order_id']) {
            $where[] = ['order_id', 'like', '%' . $input['order_id'] . '%'];
        }
        if ($input['logi_no']) {
            $where[] = ['logi_no', 'like', '%' . $input['logi_no'] . '%'];
        }
        if ($input['mobile']) {
            $where[] = ['ship_mobile', 'like', '%' . $input['mobile'] . '%'];
        }

        $res = $this->where($where)
            ->order('ctime desc')
            ->select();

        if ($res) {
            $count = $this->where($where)->count();
            foreach ($res as $k => &$v) {
                $v['username'] = get_user_info($v['user_id'], 'nickname');
                $v['logi_name'] = get_logi_info($v['logi_code']);
                $v['ship_address'] = get_area($v['ship_area_id']) . '- ' . $v['ship_address'];
                $v['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
                $v['status'] = config('params.bill_delivery.status')[$v['status']];
            }
            $return_data = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $res,
                'count' => $count
            ];

        }
        return $return_data;
    }


    /**
     * 获取发货单列表
     * @param $delivery_ids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDeliveryList($delivery_ids)
    {
        $return_data = [
            'status' => false,
            'msg' => '获取失败',
            'data' => []
        ];

        $where[] = ['delivery_id', 'in', $delivery_ids];
        $return_data['data'] = $this::with('items')
            ->where($where)
            ->select();

        if ($return_data['data']) {
            $return_data['status'] = true;
            $return_data['msg'] = '获取成功';
        }

        return $return_data;
    }
}