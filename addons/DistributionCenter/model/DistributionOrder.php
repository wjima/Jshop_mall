<?php

/**
 * 分销商模型
 */

namespace addons\DistributionCenter\model;

use app\common\model\Balance;
use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\User;
use app\common\model\UserGrade;
use app\common\model\OrderItems;
use think\Db;
use think\Model;
use think\Validate;


class DistributionOrder extends Model
{


    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';
    protected $createTime = 'ctime';


    const SETTLEMENT_YES = 1; //已结算
    const SETTLEMENT_NO = 2;//未结算
    const SETTLEMENT_CANCEL = 3;//未结算

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {

        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this
            ->field($tableWhere['field'])
            ->alias('do')
            ->join(config('database.prefix') . 'order o', 'o.order_id = do.order_id')
            ->where($tableWhere['where'])
            ->paginate($limit);
        $list = $list->getCollection();
        $data = [];
        if (!$list->isEmpty()) {
            $data = $this->tableFormat($list->toArray());
        }
        $count = $this
            ->field($tableWhere['field'])
            ->alias('do')
            ->join(config('database.prefix') . 'order o', 'o.order_id = do.order_id')
            ->where($tableWhere['where'])->count();

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $count;
        $re['data'] = $data;
        return $re;
    }

    protected function tableWhere($post)
    {
        $result['where'] = [];

        if (isset($post['order_id']) && $post['order_id']) {
            $result['where']['do.order_id'] = $post['order_id'];
        }
        if (isset($post['is_settlement']) && $post['is_settlement']) {
            $result['where']['do.is_settlement'] = $post['is_settlement'];
        }
        if (isset($post['user_mobile']) && $post['user_mobile']) {
            $user_id = get_user_id($post['user_mobile']);
            $result['where']['do.user_id'] = $user_id;

        }
        if (!empty($post['cdate'])) {
            $date_string = $post['cdate'];
            $date_array = explode(' 到 ', $date_string);
            $sdate = strtotime($date_array[0] . ' 00:00:00');
            $edate = strtotime($date_array[1] . ' 23:59:59');
            $result['where'][] = array('do.ctime', ['>=', $sdate], ['<=', $edate], 'and');
        }

        if (!empty($post['udate'])) {
            $date_string = $post['udate'];
            $date_array = explode(' 到 ', $date_string);
            $sdate = strtotime($date_array[0] . ' 00:00:00');
            $edate = strtotime($date_array[1] . ' 23:59:59');
            $result['where'][] = array('do.utime', ['>=', $sdate], ['<=', $edate], 'and');
        }
        if (isset($post['field'])) {
            $result['field'] = $post['field'];
        } else {
            $result['field'] = "do.*,o.order_amount";
        }
        if (isset($post['order'])) {
            $result['order'] = $post['order'];
        } else {
            $result['order'] = [];
        }
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

        foreach ((array)$list as $key => $value) {
            if ($value['is_settlement'] == self::SETTLEMENT_YES) {
                $list[$key]['is_settlement'] = '已结算';
            } elseif ($value['is_settlement'] == self::SETTLEMENT_NO) {
                $list[$key]['is_settlement'] = '未结算';
            } elseif ($value['is_settlement'] == self::SETTLEMENT_CANCEL) {
                $list[$key]['is_settlement'] = '已失效';
            }
            $orderItem = new OrderItems();
            $snlist = $orderItem->field("sn")->where("order_id", "eq", $value["order_id"])->column("sn");
            $list[$key]['sn'] = implode($snlist, ",");
            if ($value["pay_status"] && $value["ship_status"] && $value["status"]) {
                if ($value["status"] == 1) {
                    if ($value["pay_status"] == 1) {
                        $list[$key]['type'] = "未付款";
                    } elseif ($value["pay_status"] == 2) {
                        if ($value["ship_status"] == 1) {
                            $list[$key]['type'] = "未发货";
                        } elseif ($value["ship_status"] == 2) {
                            $list[$key]['type'] = "部分发货";
                        } elseif ($value["ship_status"] == 3) {
                            $list[$key]['type'] = "已发货";
                        }
                    } elseif ($value["pay_status"] == 3) {
                        $list[$key]['type'] = "部分付款";
                    } elseif ($value["pay_status"] == 4) {
                        $list[$key]['type'] = "已退款";
                    }
                } elseif ($value["status"] == 2) {
                    $list[$key]['type'] = "订单完成";
                } elseif ($value["status"] == 3) {
                    $list[$key]['type'] = "订单取消";
                }
            }
            $distributionModel = new Distribution();
            $list[$key]['user'] = $distributionModel->getInfoByUserId($value['user_id'], 'name');
            $list[$key]['buy_user'] = get_user_info($value['buy_user_id']);
            $list[$key]['ctime'] = getTime($value['ctime']);
            $list[$key]['utime'] = getTime($value['utime']);
        }
        return $list;
    }

    /**
     * 添加记录
     * @param array $order
     * @return array
     */
    public function addData($order = [])
    {
        $result = [
            'status' => false,
            'msg' => '保存失败',
            'data' => [],
        ];
        //查询获取几级返利
        $userModel = new User();
        $user = $userModel->field('pid,id,username,mobile')->where([['id', '=', $order['user_id']]])->find();
        $this->addOther($order, 1, $user['pid']);//todo 本级是否返利
        return $result;
    }

    /***
     * 循环插入上级
     * @param array $order 订单信息
     * @param int $level 第几级
     * @param int $user_id 用户id
     */
    public function addOther($order = [], $level = 0, $user_id = 0)
    {
        //查询上一级
        $userModel = new User();
        $user = $userModel->field('pid,id,username,mobile')->where([['id', '=', $user_id]])->find();
        $distributionModel = new Distribution();
        $commission = $distributionModel->getGradeAndCommission($user_id);//获取当前用户返佣设置
        if ($commission['status']) {//不是分销商的，不返利。 todo
            //直返本级
            $orderitemModel = new OrderItems();
            $goodsModel = new Goods();
            $itemlist = $orderitemModel->where("order_id", $order["order_id"])->select();
            if ($itemlist) {
                foreach ($itemlist as $k => $v) {
                    $distribution_type = $goodsModel->where("id", $v["goods_id"])->value("distribution_type");
                    if ($distribution_type == 2) {
                        $order['payed'] = $order['payed'] - $v["price"];
                        if($order['payed'] < 0){
                            $order['payed'] = 0;
                        }
                    }
                }
            }
            $amount = 0;

            if ($commission['status']) {
                if (isset($commission['data']['commission_' . $level]) && $commission['data']['commission_' . $level]['type'] == $distributionModel::COMMISSION_TYPE_FIXED) {
                    $amount = $commission['data']['commission_' . $level]['discount'];
                } elseif (isset($commission['data']['commission_' . $level])) {
                    $amount = ($commission['data']['commission_' . $level]['discount'] / 100) * ($order['payed'] - $order['cost_freight']);//todo 目前改为去掉配送费用计算佣金
                    $amount = sprintf("%.2f", $amount, 2);//保留2位小数，四舍五入
                } else {
                    $amount = 0;//没有设置时，不返利
                }
            }
            $iData['user_id'] = $user_id;
            $iData['buy_user_id'] = $order['user_id'];
            $iData['order_id'] = $order['order_id'];
            $iData['amount'] = $amount;
            $iData['level'] = $level;
            $iData['is_settlement'] = self::SETTLEMENT_NO;//默认未结算
            //判断是否返利过,有历史记录直接更新
            $commissOrder = $this->where([['order_id', '=', $order['order_id']], ['user_id', '=', $user_id]])->field('id,is_settlement')->find();
            if ($commissOrder) {
                $iData['utime'] = time();
                $this->allowField(true)->save($iData, ['id' => $commissOrder['id']]);
            } else {
                $iData['ctime'] = time();
                $iData['utime'] = time();
                $this->allowField(true)->insert($iData);
            }
        }
        if ($user['pid'] && ($level < $commission['data']['distribution_level'])) {
            //返第二级
            $level++;
            $this->addOther($order, $level, $user['pid']);
        }
    }

    /**
     * 订单结算
     * @param string $order_id
     * @return array
     */
    public function finishOrder($order_id = '')
    {
        $result = [
            'status' => true,
            'msg' => '结算成功',
            'data' => [],
        ];
        $orderModel = new Order();
        $order = $orderModel->where([['order_id', '=', $order_id], ['status', '=', $orderModel::ORDER_STATUS_COMPLETE]])->field('status,payed,pay_status')->find();
        if ($order) {
            Db::startTrans();
            //更新
            $list = $this->where([['order_id', '=', $order_id], ['is_settlement', '=', self::SETTLEMENT_NO]])->select();
            if ($list) {
                $balanceModel = new Balance();
                foreach ($list->toArray() as $key => $value) {
                    //钱挪到会员余额里面
                    $res = $balanceModel->change($value['user_id'], $balanceModel::TYPE_DISTRIBUTION, $value['amount'], $value['order_id']);
                    if (!$res['status']) {
                        Db::rollback();
                    }
                }
                $this->where([['order_id', '=', $order_id], ['is_settlement', '=', self::SETTLEMENT_NO]])->update(['is_settlement' => self::SETTLEMENT_YES, 'utime' => time()]);//标记为结算，然后将钱挪到会员余额里面
                Db::commit();
            }
        }
        return $result;
    }

    /**
     * 获取我的推广订单
     * @param $user_id
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getList($user_id, $page = 1, $limit = 10)
    {
        $where[] = ['do.user_id', 'eq', $user_id];
        // 已失效的不显示在推广订单
        $where[] = ['do.is_settlement', '<>', 3];

        $list = $this
            ->field('do.*,o.order_amount,o.pay_status,o.ship_status,o.status')
            ->alias('do')
            ->join(config('database.prefix') . 'order o', 'o.order_id = do.order_id')
            ->where($where)
            ->order('id', 'desc')
            ->page($page, $limit)
            ->select();
        $data = [];
        if (!$list->isEmpty()) {
            $data = $this->tableFormat($list->toArray());
        }
        $count = $this
            ->field('do.*')
            ->alias('do')
            ->join(config('database.prefix') . 'order o', 'o.order_id = do.order_id')
            ->where($where)
            ->count();
        $re['status'] = true;
        $re['msg'] = '获取成功';
        $re['data']['count'] = $count;
        $re['data']['list'] = $data;
        return $re;
    }

    /**
     * 作废订单
     * @param string $id
     * @return array
     */
    public function cancleOrder($id = '0')
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '关键参数丢失'
        ];
        $res = $this->where([['id', '=', $id], ['is_settlement', '=', self::SETTLEMENT_NO]])->update(['is_settlement' => self::SETTLEMENT_CANCEL]);
        if ($res === false) {
            $result['msg'] = '该未结算的订单不存在';
            return $result;
        }
        $result['msg'] = '操作成功';
        $result['status'] = true;
        return $result;
    }


    /**
     * 作废订单
     * @param string $order_id
     * @return array
     */
    public function cancleOrderByOrderId($order_id = '')
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '关键参数丢失'
        ];
        $res = $this->where([['order_id', '=', $order_id], ['is_settlement', '=', self::SETTLEMENT_NO]])->update(['is_settlement' => self::SETTLEMENT_CANCEL]);
        if ($res === false) {
            $result['msg'] = '该未结算的订单不存在';
            return $result;
        }
        $result['msg'] = '操作成功';
        $result['status'] = true;
        return $result;
    }

    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id' => 'user',
                'desc' => '分销商姓名',
            ],
            [
                'id' => 'buy_user',
                'desc' => '购买用户',
            ],
            [
                'id' => 'order_id',
                'desc' => '订单号',
                'modify' => 'convertString'
            ],
            [
                'id' => 'order_amount',
                'desc' => '订单金额'
            ],
            [
                'id' => 'amount',
                'desc' => '结算金额',
            ],
            [
                'id' => 'is_settlement',
                'desc' => '是否结算',
            ],
            [
                'id' => 'ctime',
                'desc' => '创建时间',
            ],
            [
                'id' => 'utime',
                'desc' => '更新时间',
            ]
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
            'msg' => '无可导出订单'
        ];
        $header = $this->csvHeader();
        $userData = $this->tableData($post, false);


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

}
