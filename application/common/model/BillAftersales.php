<?php
namespace app\common\model;

use think\Validate;
use think\Db;
use think\model\concern\SoftDelete;

class BillAftersales extends Common
{
    protected $pk = 'aftersales_id';

    const TYPE_REFUND = 1;        //售后类型 退款
    const TYPE_RESHIP = 2;       //售后类型 退货

    const STATUS_WAITAUDIT = 1;       //状态 等待审核
    const STATUS_SUCCESS = 2;       //状态 审核通过
    const STATUS_REFUSE = 3;        //状态 审核拒绝

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    //软删除位
    protected $deleteTime = 'isdel';


    /**
     * 订单售后状态，返回退款的金额和退货的明细
     * @param $order_id
     * @param bool $user_id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderAftersalesSatatus($order_id, $user_id = false)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];

        //查看订单是否在可售后的状态,只有已支付的，并且不是已完成和已取消的才可以售后
        $orderModel = new Order();
        $orderInfo  = $orderModel->getOrderInfoByOrderID($order_id, $user_id);
        if (!$orderInfo) {
            return error_code(13101);
        }

        //算已经退过款的金额，取已经完成的售后单的金额汇总
        $where[]               = ['order_id', 'eq', $order_id];
        $where[]               = ['status', 'eq', self::STATUS_SUCCESS];
        $orderInfo['refunded'] = $this->where($where)->sum('refund');   //已经退过款的金额

        //算已经退过的订单里的商品的数量
        $afterSalesItemsModel = new BillAftersalesItems();
        foreach ($orderInfo['items'] as $k => $v) {
            $orderInfo['items'][$k]['reship_nums'] = $afterSalesItemsModel
                ->alias('asi')
                ->join(config('database.prefix') . 'bill_aftersales a', 'asi.aftersales_id = a.aftersales_id')
                ->where([
                    ['asi.order_items_id', 'eq', $v['id']],
                    ['a.status', 'eq', self::STATUS_SUCCESS]
                ])
                ->sum('asi.nums');
        }
        $result['data'] = $orderInfo;
        $result['status'] = true;

        return $result;
    }


    /**
     * 创建售后单
     * @param $user_id
     * @param $order_id //发起售后的订单
     * @param $type //售后类型，1退款，2退款退货，如果是退款
     * @param array $items //如果是退款退货，退货的明细 以 [[order_item_id=>nums]]的二维数组形式传值
     * @param array $images
     * @param string $reason //售后理由
     * @param int $refund //退款金额，只在退款退货的时候用，如果是退款，直接就是订单金额
     * @param bool $formId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function toAdd($user_id, $order_id, $type, $items = [], $images = [], $reason = "", $refund = 0, $formId = false)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];

        $awhere[] = ['order_id', 'eq', $order_id];
        $awhere[] = ['user_id', 'eq', $user_id];
        $awhere[] = ['status', 'eq', self::STATUS_WAITAUDIT];
        $flag     = $this->where($awhere)->find();
        if ($flag) {
            return error_code(13102);
        }

        $orderInfo = $this->orderAftersalesSatatus($order_id, $user_id);
        if (!$orderInfo['status']) {
            return $orderInfo;
        }
        $orderInfo = $orderInfo['data'];
        $data['aftersales_id'] = get_sn(5);

        //如果是退款，退款金额用已支付的金额
        if ($type == self::TYPE_REFUND) {
            $refund = $orderInfo['payed'];
        }

        //校验订单是否可以进行此售后，并且校验订单价格是否合理
        $verify = $this->verify($type, $orderInfo, $refund);
        if (!$verify['status']) {
            return $verify;
        }

        //如果是退货，必须选择退货明细
        if ($type == self::TYPE_RESHIP && !$items) {
            return error_code(13205);
        }


        //判断图片是否大于系统限定
        if (count($images) > config('jshop.image_max')) {
            return error_code(10006);
        }

        //如果是退货，判断退货明细，数量是否超出可退的数量
        $aftersalesItems = $this->formatAftersalesItems($orderInfo, $items, $data['aftersales_id']);
        if (!$aftersalesItems['status']) {
            return $aftersalesItems;
        }

        Db::startTrans();
        try {
            $data['order_id'] = $order_id;
            $data['user_id']  = $user_id;
            $data['type']     = $type;
            $data['refund']   = $refund;
            $data['reason']   = htmlentities($reason);

            $this->save($data);
            //上面保存好售后单表，下面保存售后单明细表
            if ($type == self::TYPE_RESHIP) {
                $afterSalesItemsModel = new BillAftersalesItems();
                $afterSalesItemsModel->saveAll($aftersalesItems['data']);
            }
            //保存售后图片
            if ($images) {
                foreach ($images as $v) {
                    $rel_img['aftersales_id'] = $data['aftersales_id'];
                    $rel_img['image_id']      = $v;
                    $rel_arr[]                = $rel_img;
                }
                $afterSalesImagesModel = new BillAftersalesImages();
                $afterSalesImagesModel->saveAll($rel_arr);
            }

            //微信消息模板
            if ($formId) {
                $templateMessageModel = new TemplateMessage();
                $message              = [
                    'type'    => $templateMessageModel::TYPE_AFTER_SALE,
                    'code'    => $order_id,
                    'form_id' => $formId,
                    'status'  => $templateMessageModel::SEND_STATUS_NO
                ];
                $templateMessageModel->addSend($message);
            }
            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
        $result['status'] = true;
        $result['data']   = $data;
        return $result;
    }


    /**
     * 平台审核通过或者审核不通过
     * 如果审核通过了，是退款单的话，自动生成退款单，并做订单完成状态，如果是退货的话，自动生成退款单和退货单，如果
     * @param $aftersales_id
     * @param $status
     * @param int $refund
     * @param string $mark
     * @param array $items
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function audit($aftersales_id, $status, $refund = 0, $mark = "", $items = [])
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        $where  = [
            'aftersales_id' => $aftersales_id,
            'status'        => self::STATUS_WAITAUDIT
        ];
        $info   = $this->where($where)->find();
        if (!$info) {
            return error_code(13207);
        }

        $orderInfo = $this->orderAftersalesSatatus($info['order_id'], $info['user_id']);

        if (!$orderInfo['status']) {
            return $orderInfo;
        }
        $orderInfo = $orderInfo['data'];

        //校验订单是否可以进行此售后，并且校验订单价格是否合理
        $verify = $this->verify($info['type'], $orderInfo, $refund);
        if (!$verify['status']) {
            return $verify;
        }

        //如果是退货单，必须选择退货明细
        if ($info['type'] == self::TYPE_RESHIP && !$items) {
            return error_code(13205);
        }

        //如果是退货，判断退货明细，数量是否超出可退的数量
        if ($info['type'] == self::TYPE_RESHIP) {
            $aftersalesItems = $this->formatAftersalesItems($orderInfo, $items, $info['aftersales_id']);
            if (!$aftersalesItems['status']) {
                return $aftersalesItems;
            }
        }

        Db::startTrans();
        try {
            $data['status'] = $status;
            $data['mark']   = $mark;
            $data['refund'] = $refund;    //审核售后单的时候，可以修改退款金额，可以退款金额为0，只退货所以，这里要覆盖此字段
            $this->where($where)->data($data)->update();

            //审核通过的话，有退款的，生成退款单，根据最新的items生成退货单,并做订单的状态更改
            if ($status == self::STATUS_SUCCESS) {
                //如果有退款，生成退款单
                if ($refund > 0) {
                    $billRefundModel = new BillRefund();
                    $refund_re       = $billRefundModel->toAdd($info['user_id'], $info['order_id'], $billRefundModel::TYPE_ORDER, $refund, $info['aftersales_id']);
                    if (!$refund_re['status']) {
                        Db::rollback();
                        return $refund_re;
                    }
                }
                //如果有退货，生成退货单
                if ($info['type'] == self::TYPE_RESHIP && $items) {
                    $billReship = new BillReship();
                    $reship_re  = $billReship->toAdd($info['user_id'], $info['order_id'], $info['aftersales_id'], $aftersalesItems['data']);
                    if (!$reship_re['status']) {
                        Db::rollback();
                        return $reship_re;
                    }
                }
                //更新订单状态
                $orderModel = new Order();
                if ($info['type'] == self::TYPE_REFUND) {
                    //如果是退款，订单付款类型变成已退款状态，并且订单类型变成已完成
                    $order_data['pay_status'] = $orderModel::PAY_STATUS_REFUNDED;
                    $order_data['status']     = $orderModel::ORDER_STATUS_COMPLETE;
                } else {
                    //如果是退货状态，如果有退款，订单付款类型变成部分付款状态，如果款退完了，或者订单明细退完了，订单类型做已完成
                    //如果款退完了，订单就已完成
                    if (($refund + $orderInfo['refunded']) >= $orderInfo['payed']) {
                        $order_data['pay_status'] = $orderModel::PAY_STATUS_REFUNDED;
                        $order_data['status']     = $orderModel::ORDER_STATUS_COMPLETE;
                    } else {
                        $order_data['pay_status'] = $orderModel::PAY_STATUS_PARTIAL_NO;
                    }
                    //判断货物发完了没有，如果货已发完了，订单就已完成
                    $all_sened = true;
                    foreach ($orderInfo['items'] as $k => $v) {
                        if (isset($items[$v['id']])) {
                            $v['all_reship_nums'] = $v['reship_nums'] + $items[$v['id']];      //把本次退货数量加上去，然后再判断是否已经退完货了
                        } else {
                            $v['all_reship_nums'] = $v['reship_nums'];
                        }
                        if ($v['all_reship_nums'] < $v['nums']) {
                            //说明未退完商品
                            $all_sened = false;
                            break;
                        }
                    }
                    if ($all_sened) {
                        $order_data['status'] = $orderModel::ORDER_STATUS_COMPLETE;
                    }
                }
                $orderModel->where(['order_id' => $orderInfo['order_id'], 'status' => $orderModel::ORDER_STATUS_NORMAL])->data($order_data)->update();
                // 售后审核通过后操作
                hook('aftersalesSuccess', $info);
            }

            Db::commit();

            //审核通过的售后单锚点
            if($status == self::STATUS_SUCCESS)
            {
                hook('aftersalesreview', $aftersales_id);//添加商品后增加钩子
            }

            //发送售后审核消息
            $eventData                      = $orderInfo->toArray();
            $eventData['aftersales_status'] = ($status == self::STATUS_SUCCESS) ? '审核通过' : '审核拒绝';
            $eventData['aftersales_id']     = $aftersales_id;
            $eventData['mark']              = $mark;
            sendMessage($info['user_id'], 'aftersales_pass', $eventData);

            $result['status'] = true;
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }

        return $result;

    }


    //校验是否可以进行售后
    private function verify($type, $orderInfo, $refund)
    {
        //判断订单是否是可以售后
        $orderModel = new Order();
        if ($type == self::TYPE_REFUND) {
            //只有是已付款或部分付款并且是活动订单的才能退款
            if (
            !(($orderInfo['pay_status'] == $orderModel::PAY_STATUS_YES || $orderInfo['pay_status'] == $orderModel::PAY_STATUS_PARTIAL_YES) && $orderInfo['status'] == $orderModel::ORDER_STATUS_NORMAL)
            ) {
                return error_code(13203);
            }


        } elseif ($type == self::TYPE_RESHIP) {
            //不是未发货状态和已退货状态，并且订单是活动订单的才能退货
            if (
            !($orderInfo['ship_status'] != $orderModel::SHIP_STATUS_NO && $orderInfo['ship_status'] != $orderModel::SHIP_STATUS_RETURNED && $orderInfo['status'] == $orderModel::ORDER_STATUS_NORMAL)
            ) {
                return error_code(13204);
            }
            //判断此次退的金额加上已退的金额，不能超过已支付的金额


            if (($refund + $orderInfo['refunded']) > $orderInfo['payed']) {
                return error_code(13206);
            }


        } else {
            //售后类型不正确
            return error_code(10003);
        }
        return [
            'status' => true,
            'data'   => [],
            'msg'    => ''
        ];
    }


    /**
     * 根据退货的明细，生成售后单明细表的数据
     * @param $orderInfo            订单的详细数据
     * @param $items                前台选择的退货商品信息
     * @param $aftersales_id        将要保存的售后单的单号
     * @return array|\think\Config
     */
    private function formatAftersalesItems($orderInfo, $items, $aftersales_id)
    {
        $data = [];
        foreach ($items as $oi_id => $num) {
            if ($num <= 0) {
                continue;
            }
            foreach ($orderInfo['items'] as $v) {
                if ($v['id'] == $oi_id) {
                    //判断已经推过的加上本次退的，是否超过了购买的数量,具体取nums（购买数量）还是取sendnums(已发货数量),以后再说吧。要取购买数量，因为未发货的，也可以退的
                    if ($num + $v['reship_nums'] > $v['nums']) {
                        return error_code(13201);
                    }
                    $row['aftersales_id']  = $aftersales_id;
                    $row['order_items_id'] = $v['id'];
                    $row['goods_id']       = $v['goods_id'];
                    $row['product_id']     = $v['product_id'];
                    $row['sn']             = $v['sn'];
                    $row['bn']             = $v['bn'];
                    $row['name']           = $v['name'];
                    $row['image_url']      = $v['image_url'];
                    $row['nums']           = $num;
                    $row['addon']          = $v['addon'];
                }
            }
            $data[] = $row;
        }
        //判断生成的总记录条数，是否和前端传过来的记录条数对应上，如果没有对应上，就说明退货明细不正确
        if (count($data) != count($items)) {
            return error_code(13202);
        }
        return [
            'status' => true,
            'data'   => $data,
            'msg'    => ''
        ];
    }


    /**
     * 返回layui的table所需要的格式
     * @param $post
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;
        $re['sql']   = $this->getLastSql();

        return $re;
    }


    /**
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['order_id']) && $post['order_id'] != "") {
            $where[] = ['order_id', 'like', '%' . $post['order_id'] . '%'];
        }
        if (isset($post['aftersales_id']) && $post['aftersales_id'] != "") {
            $where[] = ['aftersales_id', 'like', '%' . $post['aftersales_id'] . '%'];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['aftersales_id', 'in', $post['id']];
        }
        if (isset($post['date']) && $post['date'] != "") {
            $date    = explode(' 到 ', $post['date']);
            $where[] = ['ctime', 'between time', [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59']];
        }
        if (isset($post['mobile']) && $post['mobile'] != "") {
            if ($user_id = get_user_id($post['mobile'])) {
                $where[] = ['user_id', 'eq', $user_id];
            } else {
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }

        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }
        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', 'eq', $post['type']];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = 'aftersales_id desc';
        return $result;
    }


    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['status']) {
                $list[$k]['status_name'] = config('params.bill_aftersales')['status'][$v['status']];
            }
            if ($v['user_id']) {
                $list[$k]['user_id'] = get_user_info($v['user_id'], 'nickname');
            }

            if ($v['ctime']) {
                $list[$k]['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
            }
            if ($v['type']) {
                $list[$k]['type'] = config('params.bill_aftersales')['type'][$v['type']];
            }
        }
        return $list;
    }


    /**
     * 前端接口，分页
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListApi($data)
    {
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => ''
        ];

        $where['user_id']             = $data['user_id'];
        $result['data']['list']       = $this::with(['order' => ['items']])
            ->where($where)
            ->order('utime desc')
            ->page($data['page'], $data['limit'])
            ->select()->hidden(['order' => ['isdel']]);
        $total                        = $this
            ->where($where)
            ->order('utime desc')
            ->count();
        $result['data']['page']       = $data['page'];
        $result['data']['limit']      = $data['limit'];
        $result['data']['total_page'] = ceil($total / $data['limit']);
        return $result;
    }


    /**
     * 获取售后单信息
     * @param $aftersales_id
     * @param $user_id
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($aftersales_id, $user_id = false)
    {
        $result                 = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        $where['aftersales_id'] = $aftersales_id;
        if($user_id)
        {
            $where['user_id']       = $user_id;
        }
        $info                   = $this::with(['billReship' => ['items'], 'items', 'images', 'billRefund'])
            ->where($where)
            ->find();
        if (!$info) {
            return error_code(13223);
        }
        foreach ($info['images'] as $k => $v) {
            $info['images'][$k]['url'] = _sImage($v['image_id']);
        }

        $result['status'] = true;
        $result['data']   = $info;

        return $result;
    }


    /**
     * @return int|string
     */
    public function getCount()
    {
        $where[] = ['status', 'eq', self::STATUS_WAITAUDIT];
        $count   = $this->where($where)->count();
        return $count ? $count : 0;
    }


    /**
     * @return \think\model\relation\HasMany
     */
    public function images()
    {
        return $this->hasMany('BillAftersalesImages', 'aftersales_id', 'aftersales_id');
    }


    /**
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('BillAftersalesItems', 'aftersales_id', 'aftersales_id');
    }


    /**
     * @return \think\db\Query|\think\model\relation\HasOne
     */
    public function order()
    {
        return $this->hasOne('Order', 'order_id', 'order_id');
    }


    /**
     * @return \think\model\relation\HasOne
     */
    public function billReship()
    {
        return $this->hasOne('BillReship', 'aftersales_id', 'aftersales_id');
    }


    /**
     * @return \think\model\relation\HasOne
     */
    public function billRefund()
    {
        return $this->hasOne('billRefund', 'aftersales_id', 'aftersales_id');
    }


    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id'     => 'aftersales_id',
                'desc'   => '售后单号',
                'modify' => 'convertString'
            ],
            [
                'id'     => 'order_id',
                'desc'   => '订单号',
                'modify' => 'convertString'
            ],
            [
                'id'   => 'status_name',
                'desc' => '状态',
            ],
            [
                'id'   => 'type',
                'desc' => '售后类型',
            ],
            [
                'id'   => 'user_id',
                'desc' => '用户',
            ],
            [
                'id'   => 'refund',
                'desc' => '退款金额',
            ],
            [
                'id'   => 'reason',
                'desc' => '原因',

            ],
            [
                'id'   => 'ctime',
                'desc' => '申请时间',

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
        $result   = [
            'status' => false,
            'data'   => [],
            'msg'    => '无可导出数据',

        ];
        $header   = $this->csvHeader();
        $userData = $this->getExportList($post);

        if ($userData['count'] > 0) {
            $tempBody = $userData['data'];
            $body     = [];
            $i        = 0;

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
            $result['msg']    = '导出成功';
            $result['data']   = $body;
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
            'data'   => [],
            'msg'    => '参数丢失',
        ];
        return $result;
    }


    //导出格式
    public function getExportList($post = [])
    {
        $return_data = [
            'status' => false,
            'msg'    => '获取失败',
            'data'   => '',
            'count'  => 0
        ];
        $where       = [];
        if (isset($post['order_id']) && $post['order_id'] != "") {
            $where[] = ['order_id', 'like', '%' . $post['order_id'] . '%'];
        }
        if (isset($post['aftersales_id']) && $post['aftersales_id'] != "") {
            $where[] = ['aftersales_id', 'like', '%' . $post['aftersales_id'] . '%'];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['aftersales_id', 'in', $post['id']];
        }
        if (isset($post['date']) && $post['date'] != "") {
            $date    = explode(' 到 ', $post['date']);
            $where[] = ['ctime', 'between time', [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59']];
        }
        if (isset($post['mobile']) && $post['mobile'] != "") {
            if ($user_id = get_user_id($post['mobile'])) {
                $where[] = ['user_id', 'eq', $user_id];
            } else {
                $where[] = ['user_id', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }
        }

        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }
        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', 'eq', $post['type']];
        }

        $list = $this->where($where)
            ->order('aftersales_id desc')
            ->select();

        if ($list) {
            $count = $this->where($where)->count();
            foreach ($list as $k => $v) {
                if ($v['status']) {
                    $list[$k]['status_name'] = config('params.bill_aftersales')['status'][$v['status']];
                }
                if ($v['user_id']) {
                    $list[$k]['user_id'] = format_mobile(get_user_info($v['user_id']));
                }

                if ($v['ctime']) {
                    $list[$k]['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
                }
                if ($v['type']) {
                    $list[$k]['type'] = config('params.bill_aftersales')['type'][$v['type']];
                }
            }
            $return_data = [
                'status' => true,
                'msg'    => '获取成功',
                'data'   => $list,
                'count'  => $count
            ];
        }
        return $return_data;
    }


    /**
     * 统计用户的售后数量
     * @param $user_id
     * @param $status
     * @return float|string
     */
    public function getUserAfterSalesNum($user_id, $status)
    {
        $where[] = ['user_id', 'eq', $user_id];
        $where[] = ['status', 'in', $status];
        return $this->where($where)->count();
    }


    /**
     * @param $order_id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderAfterSaleStatus($order_id)
    {
        $where[] = ['order_id', 'eq', $order_id];
        $info    = $this->where($where)->find();
        if ($info) {
            if ($info['status'] == self::STATUS_WAITAUDIT) {
                $text = '<span style="color:#ff7159;">待审核</span>';
            } else if ($info['status'] == self::STATUS_SUCCESS) {
                if ($info['type'] == self::TYPE_REFUND) {
                    $refundModel = new BillRefund();
                    $text        = '<span style="color:#ff7159;">' . $refundModel->getAftersalesStatus($info['aftersales_id']) . '</span>';
                } else if ($info['type'] == self::TYPE_RESHIP) {
                    $refundModel = new BillRefund();
                    $refund      = $refundModel->getAftersalesStatus($info['aftersales_id']);
                    $reshipModel = new BillReship();
                    $reship      = $reshipModel->getAftersalesStatus($info['aftersales_id']);
                    $text        = '<span style="color:#ff7159;">' . $reship . ',' . $refund . '</span>';
                } else {
                    $text = '<span style="color:#ff7159;">状态异常</span>';
                }
            } else if ($info['status'] == self::STATUS_REFUSE) {
                $text = '<span style="color:#ff7159;">审核拒绝</span>';
            } else {
                $text = '<span style="color:#ff7159;">状态异常</span>';
            }
        } else {
            $text = '';
        }

        return $text;
    }
}