<?php

namespace app\common\model;

use think\Validate;
use think\Db;
use think\model\concern\SoftDelete;

class BillAftersales extends Common
{
    protected $pk = 'aftersales_id';

    const TYPE_REFUND = 1;        //售后类型 未收到货
    const TYPE_RESHIP = 2;       //售后类型 已收到货

    const STATUS_WAITAUDIT = 1;       //状态 等待审核
    const STATUS_SUCCESS = 2;       //状态 审核通过
    const STATUS_REFUSE = 3;        //状态 审核拒绝

    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    //软删除位
    protected $deleteTime = 'isdel';


    //    废弃的方法，建议用orderToAftersales方法吧。
    //    /**
    //     * 订单售后状态，返回退款的金额和退货的明细
    //     * @param $order_id
    //     * @param bool $user_id
    //     * @return array|mixed
    //     * @throws \think\db\exception\DataNotFoundException
    //     * @throws \think\db\exception\ModelNotFoundException
    //     * @throws \think\exception\DbException
    //     */
    //    public function orderAftersalesSatatus($order_id, $user_id = false)
    //    {
    //        $result = [
    //            'status' => false,
    //            'data'   => [],
    //            'msg'    => ''
    //        ];
    //
    //        //查看订单是否在可售后的状态,只有已支付的，并且不是已完成和已取消的才可以售后
    //        $orderModel = new Order();
    //        $orderInfo  = $orderModel->getOrderInfoByOrderID($order_id, $user_id);
    //        if (!$orderInfo) {
    //            return error_code(13101);
    //        }
    //
    //        //算已经退过款的金额，取已经完成的售后单的金额汇总
    //        $where[]               = ['order_id', 'eq', $order_id];
    //        $where[]               = ['status', 'eq', self::STATUS_SUCCESS];
    //        $orderInfo['refunded'] = $this->where($where)->sum('refund');   //已经退过款的金额
    //
    //        //算已经退过的订单里的商品的数量
    //        $afterSalesItemsModel = new BillAftersalesItems();
    //        foreach ($orderInfo['items'] as $k => $v) {
    //            $orderInfo['items'][$k]['reship_nums'] = $afterSalesItemsModel
    //                ->alias('asi')
    //                ->join(config('database.prefix') . 'bill_aftersales a', 'asi.aftersales_id = a.aftersales_id')
    //                ->where([
    //                    ['asi.order_items_id', 'eq', $v['id']],
    //                    ['a.status', 'eq', self::STATUS_SUCCESS]
    //                ])
    //                ->sum('asi.nums');
    //        }
    //        $result['data'] = $orderInfo;
    //        $result['status'] = true;
    //
    //        return $result;
    //    }

    /**
     * 根据订单号查询已经售后的内容
     * @param $order_id
     * @param bool $aftersale_level      取售后单的时候，售后单的等级，0：待审核的和审核通过的售后单，1未审核的，2审核通过的
     * @return array
     */
    public function orderToAftersales($order_id, $aftersale_level = 0)
    {
        $result = [
            'status' => false,
            'data' => [
                'refund_money' => 0,        //退款金额
                'reship_goods' => [],       //退货商品
                'bill_aftersales' => []     //关联的售后单
            ],
            'msg' => ''
        ];
        switch ($aftersale_level) {
            case 0:
                $status_arr = [self::STATUS_SUCCESS, self::STATUS_WAITAUDIT];
                break;
            case 1:
                $status_arr = [self::STATUS_WAITAUDIT];
                break;
            case 2:
                $status_arr = [self::STATUS_SUCCESS];
                break;
            default:
                return error_code(13600); //aftersale_level值类型不对

        }
        //算已经退过款的金额，取已经完成的售后单的金额汇总
        $where[] = ['order_id', 'eq', $order_id];
        $where[] = ['status', 'in', $status_arr];     //加上待审核状态，这样申请过售后的商品和金额不会再重复申请了

        $result['data']['refund_money'] = $this->where($where)->sum('refund');   //已经退过款的金额

        //算退货商品明细
        $afterSalesItemsModel = new BillAftersalesItems();
        $list = $afterSalesItemsModel
            ->alias('asi')
            ->field('asi.order_items_id,asi.nums as nums,a.status as status,a.type as type')
            ->join(config('database.prefix') . 'bill_aftersales a', 'asi.aftersales_id = a.aftersales_id')
            ->where($where)
            ->select();
        if (!$list->isEmpty()) {
            $list = $list->toArray();

            $reship_goods = [];
            foreach ($list as $v) {
                if (!isset($reship_goods[$v['order_items_id']])) {
                    $reship_goods[$v['order_items_id']] = [
                        'reship_nums' => 0,                         //售后商品数量
                        'reship_nums_ed' => 0,                      //已发货的商品进行退货的数量
                    ];
                }
                $reship_goods[$v['order_items_id']]['reship_nums'] += $v['nums'];
                if ($v['type'] == self::TYPE_RESHIP) {
                    $reship_goods[$v['order_items_id']]['reship_nums_ed'] += $v['nums'];
                }
            }
            $result['data']['reship_goods'] = $reship_goods;
        }

        //把订单有关的所有售后单也都查出来吧
        $result['data']['bill_aftersales'] = $this->field('aftersales_id,status')->where($where)->select();


        return $result;
    }


    /**
     * 创建售后单
     * @param $user_id
     * @param $order_id //发起售后的订单
     * @param $type //是否收到退货，1未收到退货，不会创建退货单，2收到退货，会创建退货单,只有未发货的商品才能选择未收到货，只有已发货的才能选择已收到货
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

        //取和订单有关系的所有信息
        $orderModel = new Order();
        $orderInfo  = $orderModel->getOrderInfoByOrderID($order_id, $user_id);
        if (!$orderInfo) {
            return error_code(13101);
        }

        if (!$orderInfo['add_aftersales_status']) {
            return error_code(13200);
        }

        $data['aftersales_id'] = get_sn(5);

        //校验订单是否可以进行此售后，并且校验订单价格是否合理
        $verify = $this->verify($type, $orderInfo, $refund, $items);
        if (!$verify['status']) {
            return $verify;
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

        //做个简单校验，防止乱传值
        if ($type != self::TYPE_REFUND && $type != self::TYPE_RESHIP) {
            return error_code(10000);
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
            if ($aftersalesItems['data']) {
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
            Db::commit();
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
            hook("adminmessage",array('user_id'=>$user_id,"code"=>"after_order","params"=>$data));
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
     *
     * @param int $refund
     * @param string $mark
     * @param array $items
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function audit($aftersales_id, $status, $type, $refund = 0, $mark = "", $items = [])
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        $re = $this->preAudit($aftersales_id);
        if (!$re['status']) {
            return $re;
        }
        $info = $re['data']['info'];
        $orderInfo = $re['data']['orderInfo'];

        //校验订单是否可以进行此售后，并且校验订单价格是否合理
        $verify = $this->verify($type, $orderInfo, $refund, $items);
        if (!$verify['status'] && $status == self::STATUS_SUCCESS) {
            return $verify;
        }

        //根据退货的明细，生成售后单明细表的数据
        if ($items) {
            $aftersalesItems = $this->formatAftersalesItems($orderInfo, $items, $info['aftersales_id']);
            if (!$aftersalesItems['status']) {
                return $aftersalesItems;
            }
        } else {
            $aftersalesItems['data'] = [];
        }

        $orderModel = new Order();
        Db::startTrans();
        try {
            $where['aftersales_id'] = $aftersales_id;
            $where['status'] = self::STATUS_WAITAUDIT;

            $data['status'] = $status;
            $data['mark']   = $mark;
            $data['refund'] = $refund;    //审核售后单的时候，可以修改退款金额，可以退款金额为0，只退货所以，这里要覆盖此字段
            $data['type'] = $type;
            $this->where($where)->data($data)->update();

            //更新售后单明细表，先删除，然后全新插入
            $afterSalesItemsModel = new BillAftersalesItems();
            $afterSalesItemsModel->where('aftersales_id', $aftersales_id)->delete();
            if ($aftersalesItems['data']) {
                $afterSalesItemsModel = new BillAftersalesItems();
                $afterSalesItemsModel->saveAll($aftersalesItems['data']);
            }

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
                //如果已经发货了，要退货，生成退货单，让用户吧商品邮回来。
                if ($info['type'] == self::TYPE_RESHIP && $aftersalesItems['data']) {
                    $billReship = new BillReship();
                    $reship_re  = $billReship->toAdd($info['user_id'], $info['order_id'], $info['aftersales_id'], $aftersalesItems['data']);
                    if (!$reship_re['status']) {
                        Db::rollback();
                        return $reship_re;
                    }
                }
                //更新订单状态

                //如果是退款，退完了就变成已退款并且订单类型变成已完成，如果未退完，就是部分退款
                //判断退款金额不能超
                if ($refund > 0) {
                    if (($refund + $orderInfo['refunded']) == $orderInfo['payed']) {
                        $order_data['pay_status'] = $orderModel::PAY_STATUS_REFUNDED;
                        $order_data['status']     = $orderModel::ORDER_STATUS_COMPLETE;
                    } else {
                        $order_data['pay_status'] = $orderModel::PAY_STATUS_PARTIAL_NO;
                    }
                }


                //判断货物发完没，如果货已发完了，订单发货就变成已发货,为了判断在有退款的情况下，当
                $all_deliveryed = true;     //商品该发货状态，默认发货了,为了判断部分发货的情况下，的订单发货状态
                $no_deliveryed = true;      //是否都没发货,默认都没发货
                $all_sened = true;          //商品退货状态（所有退货，包含已发的退货和未发的退货），默认都退货了,为了判断都退了的话，订单状态变成已完成
                foreach ($orderInfo['items'] as $k => $v) {
                    if (isset($items[$v['id']])) {
                        $v['reship_nums'] += $items[$v['id']];      //把本次退货数量加上去
                        if ($type == self::TYPE_RESHIP) {
                            $v['reship_nums_ed'] += $items[$v['id']];
                        }
                    }
                    //有任何商品发货，都不是未发货状态
                    if ($no_deliveryed && ($v['sendnums'] > 0)) {
                        $no_deliveryed = false;
                    }
                    if ($all_deliveryed && ($v['nums'] - $v['sendnums'] - ($v['reship_nums'] - $v['reship_nums_ed']) > 0)) {
                        //说明该发货的商品没发完
                        $all_deliveryed = false;
                    }
                    if ($all_sened && ($v['reship_nums'] < $v['nums'])) {
                        //说明未退完商品
                        $all_sened = false;
                    }
                }
                if ($all_deliveryed && !$no_deliveryed) {
                    $order_data['ship_status'] = $orderModel::SHIP_STATUS_YES;
                }
                if ($all_sened) {
                    $order_data['status'] = $orderModel::ORDER_STATUS_COMPLETE;
                }

                //未发货的商品库存调整,如果订单未发货或者部分发货，并且用户未收到商品的情况下，需要解冻冻结库存
                if (
                    ($orderInfo['ship_status'] == $orderModel::SHIP_STATUS_NO || $orderInfo['ship_status'] == $orderModel::SHIP_STATUS_PARTIAL_YES) &&
                    $type == self::TYPE_REFUND &&
                    $aftersalesItems['data']
                ) {
                    //未发货商品解冻库存
                    $goodsModel = new Goods();
                    foreach ($aftersalesItems['data'] as $key => $val) {
                        $goodsModel->changeStock($val['product_id'], 'refund', $val['nums']);
                    }
                }

                //如果订单是已完成，但是订单的未发货商品还有的话，需要解冻库存
                if(isset($order_data['status']) && $order_data['status'] == $orderModel::ORDER_STATUS_COMPLETE){
                    $goodsModel = new Goods();
                    foreach ($orderInfo['items'] as $k => $v) {
                        $nums = $v['nums'] - $v['sendnums'] - ($v['reship_nums'] - $v['reship_nums_ed']);       //还未发货的数量
                        if ($nums > 0) {
                            $goodsModel->changeStock($v['product_id'], 'refund', $nums);
                        }
                    }
                }
                if(isset($order_data)){
                    $orderModel->where(['order_id' => $orderInfo['order_id'], 'status' => $orderModel::ORDER_STATUS_NORMAL])->data($order_data)->update();
                }
            }

            Db::commit();


            //售后单审核过后的锚点
            if ($status == self::STATUS_SUCCESS) {
                hook('aftersalesreview', $aftersales_id);           //此钩子名称不妥，会修改掉。
            }

            //发送售后审核消息
            // 售后审核消息传参数的话就用申请售后传过来的参数比较好
            $paramsData = [
                'aftersales_status'=>($status == self::STATUS_SUCCESS) ? '审核通过' : '审核拒绝',
                'aftersales_id'=>$aftersales_id,
                'mark'=>$mark,
                'status'=>$status,
                'type'=>$type,
                'refund'=>$refund,
                'items'=>$items,
                'order_id'=>$info['order_id'],
            ];
            sendMessage($info['user_id'], 'aftersales_pass', $paramsData);

            $result['status'] = true;
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }

        return $result;
    }

    //后端进行审核的时候，前置操作，1取出页面的数据，2在提交过来的表单的时候，进行校验
    public function preAudit($aftersales_id)
    {
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => ''
        ];

        $where['aftersales_id'] = $aftersales_id;
        $where['status'] = self::STATUS_WAITAUDIT;
        $info = $this::with('images,items')->where($where)->find();
        if (!$info) {
            return error_code(13207);
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

        $result['data']['info'] = $info;            //数据库中保存的售后单信息
        $result['data']['orderInfo'] = $orderInfo;  //订单信息，减掉了当次售后单中的退款金额和订单明细中的退货数量
        return $result;
    }


    /**
     * 校验是否可以进行售后
     * @param $type
     * @param $orderInfo
     * @param $refund
     * @param $items
     * @return array|mixed
     */
    private function verify($type, $orderInfo, $refund = 0, $items = [])
    {
        //判断订单是否是可以售后
        $orderModel = new Order();

        //只有活动订单才能售后
        if ($orderInfo['status'] != $orderModel::ORDER_STATUS_NORMAL) {
            return error_code(13200);
        }

        //未付款订单和已退款订单不能售后
        if ($orderInfo['pay_status'] == $orderModel::PAY_STATUS_NO || $orderInfo['pay_status'] == $orderModel::PAY_STATUS_REFUNDED) {
            return error_code(13203);
        }


        //如果订单未发货，那么用户不能选择已收到货
        if ($type == self::TYPE_RESHIP && $orderInfo['ship_status'] == $orderModel::SHIP_STATUS_NO) {
            return error_code(13227);
        }


        //判断退款金额不能超
        if (($refund + $orderInfo['refunded']) > $orderInfo['payed']) {
            return error_code(13206);
        }

        //根据是否已收到货和未收到货来判断实际可以退的数量，不能超过最大数量，已收到货的和未收到货的不能一起退，在这里做判断
        return $this->verifyNums($type, $orderInfo, $items);
    }

    //判断退货数量是否超标
    private function verifyNums($type, $orderInfo, $items)
    {
        $result = [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];
        foreach ($items as $k => $num) {
            foreach ($orderInfo['items'] as $v) {
                if ($v['id'] == $k) {
                    if ($type == self::TYPE_REFUND) {
                        $n = $v['nums'] - $v['sendnums'] - ($v['reship_nums'] - $v['reship_nums_ed']);
                        if ($n < $num) {
                            return error_code(13601, false, $v['name'], $v['addon'], $n); //"未发货商品-".$v['name'].$v['addon']."最多能退".$n."个";

                        }
                    } else {
                        $n = $v['sendnums'] - $v['reship_nums_ed'];
                        if ($n < $num) {
                            return error_code(13602, false, $v['name'], $v['addon'], $n); //"已发货商品-".$v['name'].$v['addon']."最多能退".$n."个";

                        }
                    }
                }
            }
        }
        return $result;
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
                    //判断已经退过的加上本次退的，是否超过了购买的数量,具体取nums（购买数量）还是取sendnums(已发货数量),以后再说吧。要取购买数量，因为未发货的，也可以退的
                    //                    if ($num + $v['reship_nums'] > $v['nums']) {
                    //                        return error_code(13201);
                    //                    }
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
                    $data[] = $row;
                    break;
                }
            }
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
        // $re['sql']   = $this->getLastSql();

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
        if (isset($data['order_id']) && $data['order_id'] != "") {
            $where['order_id'] = $data['order_id'];
        }


        $list = $this::with(['items', 'images'])
            ->where($where)
            ->order('utime desc')
            ->page($data['page'], $data['limit'])
            ->select();
        $total = $this
            ->where($where)
            ->count();
        $result['data']['list']       = $list;
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
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        $where['aftersales_id'] = $aftersales_id;
        if ($user_id) {
            $where['user_id']       = $user_id;
        }
        $info = $this::with(['billReship' => ['items'], 'items', 'images', 'billRefund'])
            ->where($where)
            ->find();
        if (!$info) {
            return error_code(13223);
        }
        if($info["bill_reship"]["logi_code"] != ""){
            $info["bill_reship"]["logi_code"] = get_logi_info($info["bill_reship"]["logi_code"] );
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
                'desc' => '退款总金额',
            ],
            [
                'id'   => 'reason',
                'desc' => '原因',
            ],
            [
                'id'   => 'ctime',
                'desc' => '申请时间',
            ],
            [
                'id' => 'item_name',
                'desc' => '商品名称',
                'modify' => 'convertString'
            ],
            [
                'id' => 'item_sn',
                'desc' => '货品编码',
                'modify' => 'convertString'
            ],
            [
                'id' => 'item_bn',
                'desc' => '商品编码',
                'modify' => 'convertString'
            ],
            [
                'id' => 'item_nums',
                'desc' => '退货数量',
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
            'msg'    => error_code(10083, true),

        ];
        $header   = $this->csvHeader();
        $userData = $this->getExportList($post);
        if ($userData['count'] > 0) {
            $tempBody = $userData['data'];
            $body     = [];
            $i        = 0;

            foreach ($tempBody as $key => $val) {
                $i++;
                if (empty($val['items'])) {
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
                } else {
                    foreach ($val['items'] as $k => $item) {
                        if ($k == 0) {
                            $val['item_name'] = $item['name'] . $item['addon'];
                            $val['item_sn'] = $item['sn'];
                            $val['item_bn'] = $item['bn'];
                            $val['item_nums'] = $item['nums'];
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
                        } else {
                            $tmp = [];
                            $tmp['item_name'] = $item['name'] . $item['addon'];
                            $tmp['item_sn'] = $item['sn'];
                            $tmp['item_bn'] = $item['bn'];
                            $tmp['item_nums'] = $item['nums'];
                            foreach ($header as $hk => $hv) {
                                if (isset($tmp[$hv['id']]) && $tmp[$hv['id']] && isset($hv['modify'])) {
                                    if (function_exists($hv['modify'])) {
                                        $body[$i][$hk] = $hv['modify']($tmp[$hv['id']]);
                                    }
                                } elseif (isset($tmp[$hv['id']]) && !empty($tmp[$hv['id']])) {
                                    $body[$i][$hk] = $tmp[$hv['id']];
                                } else {
                                    $body[$i][$hk] = '';
                                }
                            }
                            $tmp = [];
                        }
                        $i++;
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
            'status' => true,
            'data'   => [],
            'msg'    => '验证成功',
        ];
        return $result;
    }


    //导出格式
    public function getExportList($post = [])
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10025, true),
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

        $list = $this->with(['items' => function ($query) {
            return $query->field(['sn', 'bn', 'name', 'addon', 'nums', 'aftersales_id']);
        }])->where($where)
            ->order('aftersales_id desc')
            ->select()->toArray();

        if ($list) {
            $count = $this->where($where)->count();
            foreach ($list as $k => $v) {
                if ($v['status']) {
                    $list[$k]['status_name'] = config('params.bill_aftersales')['status'][$v['status']];
                }
                if ($v['user_id']) {
                    $list[$k]['user_id'] = get_user_info($v['user_id'], 'showname');
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

    public function manageAdd($order_id)
    {
        //取订单的信息
        $orderModel = new Order();
        $orderInfo = $orderModel->getOrderInfoByOrderID($order_id);
        if (!$orderInfo) {
            return error_code(10000);
        }
        //订单上的退款金额和数量只包含已经售后的，这里要把当次售后单的商品信息保存到订单明细表上
        foreach ($orderInfo['items'] as $k => $v) {
            unset($orderInfo['items'][$k]['promotion_list']);       //此字段会影响前端表格显示，所以删掉
            $orderInfo['items'][$k]['addon'] = $v['addon'] ? $v['addon'] : '';
            $orderInfo['items'][$k]['the_reship_nums'] = 0;
        }
        return [
            'status'=>true,
            'data'=>$orderInfo,
            'msg'=>""
        ];
    }

    public function manageSave($order_id,$data,$items)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => ''
        ];
        $res = $this->manageAdd($order_id);
        if(!$res['status']) return $res;
        $order = $res['data'];
        $orderModel = new Order();
        if($order['status'] != $orderModel::ORDER_STATUS_NORMAL || $order['pay_status'] == $orderModel::PAY_STATUS_NO) return error_code(10000);   // 当前订单状态不可申请售后
        // $type = $order['ship_status'] > $orderModel::SHIP_STATUS_NO ? 2:1;
        $type = $data['type'];
        if($type != 1 && $type != 2){
            $result['msg'] = '请选择商品状态是已发货还是未发货';
            return $result;
        }
        $refund = $data['refund'];

        //校验订单是否可以进行此售后，并且校验订单价格是否合理
        $verify = $this->verify($type, $order, $refund, $items);
        if (!$verify['status']) {
            return $verify;
        }

        $aftersales_id = get_sn(5);
        // 勾选退货了 根据退货的明细，生成售后单明细表的数据
        if($items){
            $new_items = [];
            $zero_num = 0;
            foreach ($items as $oi_id => $num) {
                if((int)$num <= 0){
                    $zero_num++;
                    continue;
                }
                foreach ($order['items'] as $v) {
                    if ($v['id'] == $oi_id) {
                        $row = [];
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
                        $new_items[] = $row;
                        // 已经找到商品了，可以跳出循环了
                        break;
                    }
                }
            }
            if(count($items) != count($new_items) + $zero_num) return error_code(13202);
            $items = $new_items;
        }

        $reason = $data['reason'];
        $mark = $data['mark'];  // 售后备注。
    
        $aftersalesData = [
            'aftersales_id' => $aftersales_id,
            'type' => $type,
            'order_id'=>$order_id,
            'user_id'=>$order['user_id'],
            'reason'=>$reason,
            'mark'=>'后台申请售后直接通过',
            'refund'=>$refund,
            'status'=>self::STATUS_SUCCESS,
        ];
        Db::startTrans();
        try {
            // 保存售后单
            $this->save($aftersalesData);

            //新增售后单明细表
            if ($items) {
                $afterSalesItemsModel = new BillAftersalesItems();
                $afterSalesItemsModel->saveAll($items);
            }

            //审核通过的话，有退款的，生成退款单，根据最新的items生成退货单,并做订单的状态更改

            //如果有退款，生成退款单
            if ($refund > 0) {
                $billRefundModel = new BillRefund();
                $refund_re       = $billRefundModel->toAdd($order['user_id'], $order_id, $billRefundModel::TYPE_ORDER, $refund, $aftersales_id);
                if (!$refund_re['status']) {
                    Db::rollback();
                    return $refund_re;
                }
            }
            //如果已经发货了，要退货，生成退货单，让用户把商品邮回来。
            if ($type == self::TYPE_RESHIP && $items) {
                $billReship = new BillReship();
                $reship_re  = $billReship->toAdd($order['user_id'], $order_id, $aftersales_id, $items);
                if (!$reship_re['status']) {
                    Db::rollback();
                    return $reship_re;
                }
            }
            //更新订单状态

            //如果是退款，退完了就变成已退款并且订单类型变成已完成，如果未退完，就是部分退款
            //判断退款金额不能超
            if ($refund > 0) {
                if (($refund + $order['refunded']) == $order['payed']) {
                    $order_data['pay_status'] = $orderModel::PAY_STATUS_REFUNDED;
                    $order_data['status']     = $orderModel::ORDER_STATUS_COMPLETE;
                } else {
                    $order_data['pay_status'] = $orderModel::PAY_STATUS_PARTIAL_NO;
                }
            }


            //判断货物发完没，如果货已发完了，订单发货就变成已发货,为了判断在有退款的情况下，当
            $all_deliveryed = true;     //商品该发货状态，默认发货了,为了判断部分发货的情况下，的订单发货状态
            $no_deliveryed = true;      //是否都没发货,默认都没发货
            $all_sened = true;          //商品退货状态（所有退货，包含已发的退货和未发的退货），默认都退货了,为了判断都退了的话，订单状态变成已完成
            foreach ($order['items'] as $k => $v) {
                if (isset($items[$v['id']])) {
                    $v['reship_nums'] += $items[$v['id']];      //把本次退货数量加上去
                    if ($type == self::TYPE_RESHIP) {
                        $v['reship_nums_ed'] += $items[$v['id']];
                    }
                }
                //有任何商品发货，都不是未发货状态
                if ($no_deliveryed && ($v['sendnums'] > 0)) {
                    $no_deliveryed = false;
                }
                if ($all_deliveryed && ($v['nums'] - $v['sendnums'] - ($v['reship_nums'] - $v['reship_nums_ed']) > 0)) {
                    //说明该发货的商品没发完
                    $all_deliveryed = false;
                }
                if ($all_sened && ($v['reship_nums'] < $v['nums'])
                ) {
                    //说明未退完商品
                    $all_sened = false;
                }
            }
            if ($all_deliveryed && !$no_deliveryed
            ) {
                $order_data['ship_status'] = $orderModel::SHIP_STATUS_YES;
            }
            if ($all_sened) {
                $order_data['status'] = $orderModel::ORDER_STATUS_COMPLETE;
            }

            //未发货的商品库存调整,如果订单未发货或者部分发货，并且用户未收到商品的情况下，需要解冻冻结库存
            if (
                ($order['ship_status'] == $orderModel::SHIP_STATUS_NO || $order['ship_status'] == $orderModel::SHIP_STATUS_PARTIAL_YES) &&
                $type == self::TYPE_REFUND &&
                $items
            ) {
                //未发货商品解冻库存
                $goodsModel = new Goods();
                foreach ($items as $key => $val) {
                    $goodsModel->changeStock($val['product_id'], 'refund', $val['nums']);
                }
            }

            //如果订单是已完成，但是订单的未发货商品还有的话，需要解冻库存
//            if (isset($order_data['status']) && $order_data['status'] == $orderModel::ORDER_STATUS_COMPLETE) {
//                $goodsModel = new Goods();
//                foreach ($order['items'] as $k => $v) {
//                    $nums = $v['nums'] - $v['sendnums'] - ($v['reship_nums'] - $v['reship_nums_ed']);       //还未发货的数量
//                    if ($nums > 0) {
//                        $goodsModel->changeStock($v['product_id'], 'refund', $nums);
//                    }
//                }
//            }
            if (isset($order_data)) {
                $orderModel->where(['order_id' => $order_id, 'status' => $orderModel::ORDER_STATUS_NORMAL])->data($order_data)->update();
            }
            

            Db::commit();


            //售后单审核过后的锚点
            hook('aftersalesreview', $aftersales_id);           //此钩子名称不妥，会修改掉。
            

            //发送售后审核消息
            // 售后审核消息传参数的话就用申请售后传过来的参数比较好
            $paramsData = [
                'aftersales_status'=>'审核通过',
                'aftersales_id'=>$aftersales_id,
                'mark'=>$mark,
                'status'=>self::STATUS_SUCCESS,
                'type'=>$type,
                'refund'=>$refund,
                'items'=>$items,
                'order_id'=>$order_id,
            ];
            sendMessage($order['user_id'], 'aftersales_pass', $paramsData);

            $result['status'] = true;
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
        return $result;
    }
}
