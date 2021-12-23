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

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const STATUS_READY = 1;                 //准备发货
    const STATUS_ALREADY = 2;               //已发货
    const STATUS_CONFIRM = 3;               //已确认
    const STATUS_OTHER = 4;                 //其他

    /**
     * 发货详情关联
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('BillDeliveryItems', 'delivery_id', 'delivery_id');
    }

    public function orders()
    {
        return $this->hasMany('BillDeliveryOrderRel', 'delivery_id', 'delivery_id');
    }


    /**
     * 批量发货，可以支持多个订单合并发货，单个订单拆分发货等。
     * @param $order_id //英文逗号分隔的订单号
     * @param $logi_code //物流公司编码
     * @param $logi_no //物流单号
     * @param $items //发货明细
     * @param int $store_id //店铺收货地址
     * @param string $ship_name //收货人姓名
     * @param string $ship_mobile //收货人电话
     * @param int $ship_area_id //省市区id
     * @param string $ship_address //收货地址
     * @param string $memo 、、发货描述
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ship($order_id, $logi_code, $logi_no, $items, $store_id = 0, $ship_name = "", $ship_mobile = "", $ship_area_id = 0, $ship_address = "", $memo = "")
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];

        //获取订单详情
        $orderModel = new Order();
        $dinfo = $orderModel->getOrderShipInfo($order_id);

        if (!$dinfo['status']) {
            return $dinfo;
        }
        $dinfo = $dinfo['data'];
        $orders = $dinfo['orders'];

        //校验门店自提和普通订单收货地址是否填写
        if($store_id != 0){
            $storeModel = new Store();
            $info = $storeModel->where('id',$store_id)->find();
            if(!$info){
                return error_code(10000);
            }
            $ship_name = $info['store_name'];
            $ship_mobile = $info['mobile'];
            $ship_area_id = $info['area_id'];
            $ship_address = $info['address'];
        }
        if(!$ship_name || !$ship_mobile || !$ship_area_id || !$ship_address){
//            $result['msg'] = "收货地址信息不全";
            return error_code(13309);
        }

        $bill_delivery = [
            'delivery_id' => get_sn(8),
            'logi_code' => $logi_code,
            'logi_no' => $logi_no,
            'ship_area_id' => $ship_area_id,
            'ship_address' => $ship_address,
            'ship_name' => $ship_name,
            'ship_mobile' => $ship_mobile,
            'status' => self::STATUS_ALREADY,
            'memo' => $memo,
        ];

        //校验发货内容
        $tNum = 0;
        foreach($items as $product_id => $num){
            if(!isset($dinfo['items'][$product_id])){
                return error_code(13308);       //发货的商品不在发货明细里，肯定有问题
            }
            //判断总发货数量
            $tNum = $tNum + $num;
            //判断是否超发
            if(($dinfo['items'][$product_id]['nums'] - $dinfo['items'][$product_id]['sendnums'] - ($dinfo['items'][$product_id]['reship_nums'] - $dinfo['items'][$product_id]['reship_nums_ed'])) < $num){
//                $result['msg'] = error_code(13310,false,$dinfo['items'][$product_id]['name']);//$dinfo['items'][$product_id]['name']." 发超了";
                return error_code(13310,false,$dinfo['items'][$product_id]['name']);
            }
            //构建发货单明细
            $bdRel[] = [
                'delivery_id' => $bill_delivery['delivery_id'],
                'product_id' => $dinfo['items'][$product_id]['product_id'],
                'goods_id' => $dinfo['items'][$product_id]['goods_id'],
                'bn' => $dinfo['items'][$product_id]['bn'],
                'sn' => $dinfo['items'][$product_id]['sn'],
                'weight' => $dinfo['items'][$product_id]['weight'],
                'name' => $dinfo['items'][$product_id]['name'],
                'addon' => $dinfo['items'][$product_id]['addon'] ? $dinfo['items'][$product_id]['addon'] : '',
                'nums' => $num
            ];
        }

        if ($tNum < 1) {
//            $result['msg'] = '请至少发生一件商品！';
            return error_code(13311);
        }

        Db::startTrans();
        try {
            //插入发货单主体表
            $this->save($bill_delivery);
            //插入发货单明细表
            $billDeliveryItemsModel = new BillDeliveryItems();
            $billDeliveryItemsModel->saveAll($bdRel);

            //订单更新发货状态，发送各种消息
            foreach($orders as $v){
                $this->orderShip($v,$items,$bill_delivery,$store_id);
                $doRel[] = [
                    'order_id' => $v['order_id'],
                    'delivery_id' => $bill_delivery['delivery_id']
                ];
            }
            $billDeliveryOrderRelModel = new BillDeliveryOrderRel();
            $billDeliveryOrderRelModel->saveAll($doRel);

            Db::commit();
            $result['status'] = true;
        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();
            Db::rollback();
        }
        return $result;
    }

    /**
     * 确认签收,随着订单确认收货的来，包裹再一一签收也没啥意思。
     * @param $order_id
     * @return false|int
     */
    public function confirm($order_id)
    {
//        $where[] = ['order_id', 'eq', $order_id];
//        $data['confirm_time'] = time();
//        $res = $this->save($data, $where);
        return true;
    }

//    /**
//     * 获取物流信息接口
//     * 根据订单号查询
//     * User:tianyu
//     * @param string $order_id
//     * @return mixed
//     * @throws \think\db\exception\DataNotFoundException
//     * @throws \think\db\exception\ModelNotFoundException
//     * @throws \think\exception\DbException
//     */
//    public function getLogisticsInformation($order_id)
//    {
//        $result = [
//            'status' => false,
//            'msg' => error_code(10025,true),
//            'data' => []
//        ];
//        $deliveryInfo = $this->where('order_id', $order_id)->find();
//        if ($deliveryInfo) {
//            // 获取发货单物流公司编码和单号
//            if (!$deliveryInfo['logi_code'] && !$deliveryInfo['logi_no']) {
//                return error_code(10051);
//            }
//
//            $logistics = $this->logistics_query($deliveryInfo['logi_code'], $deliveryInfo['logi_no']);
//            if ($logistics['status'] === '200') {
//                $result['status'] = true;
//                $result['msg'] = '获取成功';
//                $result['data'] = [
//                    'list' => $logistics['data'],
//                    'state' => config('params.order')['logistics_state'][$logistics['state']]
//                ];
//            } else {
//                $result['msg'] = $logistics['message'];
//            }
//        }
//        return $result;
//    }


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
            $result['msg'] = $logisticsInfo['message'] ? $logisticsInfo['message'] : error_code(10099,true);
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
            ->field('d.*,r.order_id')
            ->join('bill_delivery_order_rel r', 'd.delivery_id = r.delivery_id')
            ->where($where)
            ->group('r.delivery_id,r.order_id')
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
            // $return_data = [
            //     'status' => false,
            //     'msg' => error_code(10025,true),
            //     'data' => $res,
            //     'count' => $count
            // ];
            return error_code(10025);
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
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];

        $where[] = ['delivery_id', 'eq', $delivery_id];
        $info = $this::with(['items','orders'])->where($where)->find();
        if(!$info){
//            $result['msg'] = "无此记录";
            return error_code(10002);
        }
        $info = $info->toArray();

        $info['orders'] = implode(',',array_column($info['orders'],'order_id'));

        $result['status'] = true;
        $result['data'] = $info;

        return $result;
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
                'modify' => 'convertString'
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
            'msg' => ''
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
            return error_code(10083);
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
            'status' => true,
            'data' => [],
            'msg' => '验证成功',
        ];
        return $result;
    }

    //导出格式
    public function getExportList($input = [])
    {
        $return_data = error_code(10025);

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
            ->field('d.*,r.order_id')
            ->join('bill_delivery_order_rel r', 'd.delivery_id = r.delivery_id')
            ->where($where)
            ->group('r.delivery_id')
            ->order('ctime desc')
            ->select();

        if ($res) {

            $count = $this->alias('d')
                ->join('bill_delivery_order_rel r', 'd.delivery_id = r.delivery_id')
                ->group('r.delivery_id')
                ->where($where)
                ->count();

            foreach ($res as $k => &$v) {
                if(isset($v['user_id']) && $v['user_id']){
                    $v['username'] = get_user_info($v['user_id'], 'nickname');
                }else{
                    $v['username'] = '';
                }
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
    public function getDeliveryList($order_id)
    {
        $return_data = [
            'status' => true,
            'msg' => '',
            'data' => []
        ];
        $billDeliveryOrderRelModel = new BillDeliveryOrderRel();

        $where[] = ['dor.order_id', 'eq', $order_id];
        $return_data['data'] = $billDeliveryOrderRelModel
            ->alias('dor')
            ->field('d.*')
            ->join('bill_delivery d','d.delivery_id = dor.delivery_id')
            ->where($where)
            ->select();

        return $return_data;
    }

    /**
     * 给订单发货
     * @param $orderinfo        订单信息
     * @param $items            总的发货包裹内容
     * @param $deliveryInfo     发货单信息
     * @param $store_id         门店自提还是普通订单，0是普通订单，其他是门店自提
     */
    private function orderShip($orderInfo,&$items,$deliveryInfo,$store_id = 0){
        $item = [];
        foreach($orderInfo['items'] as $k => $v){
            if(isset($items[$v['product_id']])){
                $max_num = $v['nums'] - $v['reship_nums'] - $v['sendnums'];
                if($max_num > 0){       //如果此条订单明细需要发货的话
                    if($items[$v['product_id']] > $max_num){
                        //足够发此条记录的话
                        $item[$v['product_id']] = $max_num;
                        $items[$v['product_id']] -= $max_num;
                    }else{
                        //此条订单都发不满的情况下
                        $item[$v['product_id']] = $items[$v['product_id']];
                        unset($items[$v['product_id']]);
                    }
                }
            }
        }
        //如果有发货信息，就去给订单更新发货状态

        if(!$item){
            return false;
        }
        $orderModel = new Order();
        $re = $orderModel->ship($orderInfo['order_id'],$item);

        //如果是门店自提，生成提货单
        if ($store_id != 0) {
            $ladingModel = new BillLading();
            $ladingModel->addData($orderInfo['order_id'], $store_id, $orderInfo['ship_name'], $orderInfo['ship_mobile']);
        }

        if($re['status']){
            //订单日志
            $orderLog = new OrderLog();
            $orderLog->addLog($orderInfo['order_id'], $orderInfo['user_id'], $orderLog::LOG_TYPE_SHIP, '订单发货操作，发货单号：' . $deliveryInfo['delivery_id'], $deliveryInfo);

            //发送消息
            $deliveryInfo['logistics_name'] = get_logi_info($deliveryInfo['logi_code']);
            $deliveryInfo['order_id'] = $orderInfo['order_id'];
            sendMessage($orderInfo['user_id'], 'delivery_notice', $deliveryInfo);

        }
        return true;
    }

}