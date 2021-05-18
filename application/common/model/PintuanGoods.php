<?php

namespace app\common\model;

use app\common\model\Common;
use app\common\model\Products;

class PintuanGoods extends Common
{


    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post,$api = false)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);

        if($api){
            $tableWhere['where'][] = ['pr.stime','<=',time()];
            $tableWhere['where'][] = ['pr.etime','>',time()];
            $list       = $this
                ->alias("pg")
                ->field("pr.*,pg.goods_id,g.name as goods_name,g.image_id as goods_image_id")
                ->join("pintuan_rule pr", "pg.rule_id = pr.id")
                ->join("goods g", "g.id = pg.goods_id")
                ->where($tableWhere['where'])
                ->order($tableWhere['order'])
                ->page($post['page'],$limit)
                ->select();
            $data = $this->tableFormat($list,$api);         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $count   = $this
                ->alias("pg")
                ->field("pr.*,pg.goods_id,g.name as goods_name,g.image_id as goods_image_id")
                ->join("pintuan_rule pr", "pg.rule_id = pr.id")
                ->join("goods g", "g.id = pg.goods_id")
                ->where($tableWhere['where'])
                ->count();

        }else{
            $list       = $this
                ->alias("pg")
                ->field("pr.*,pg.goods_id,g.name as goods_name,g.image_id as goods_image_id")
                ->join("pintuan_rule pr", "pg.rule_id = pr.id")
                ->join("goods g", "g.id = pg.goods_id")
                ->where($tableWhere['where'])
                ->order($tableWhere['order'])
                ->paginate($limit);
            $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $count = $list->total();
        }
        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $count;
        $re['data']  = $data;

        return $re;
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $result['where'] = [];
        $result['field'] = "*";
        $result['order'] = [];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list,$api = false)
    {
        foreach ($list as $key => $value) {
            if ($value['goods_image_id']) {
                $list[$key]['goods_image'] = _sImage($value['goods_image_id']);
            } else {
                $list[$key]['goods_image'] = _sImage();
            }
            if($api){
                $goods = $this->getGoodsInfo($value['goods_id']);
                $list[$key]['goods'] = $goods['data'];

            }

        }
        return $list;
    }


    public function saveGoods($rule_id, $goods)
    {
        $where[] = ['rule_id', 'eq', $rule_id];
        $this->where($where)->delete();
        $goods_arr = explode(',', $goods);
        $data      = [];
        foreach ($goods_arr as $v) {
            $data[] = [
                'rule_id'  => $rule_id,
                'goods_id' => $v
            ];
        }
        if ($data) {
            $this->saveAll($data);
        }
        return true;
    }

    /**
     * 取拼团的商品信息，增加拼团的一些属性，会显示优惠价
     * @param $gid
     * @return array
     */
    public function getGoodsInfo($gid)
    {
        $goodsModel = new Goods();
        $goodsInfo  = $goodsModel->getGoodsDetial($gid);

        if (!$goodsInfo['status'] || !$goodsInfo['data']) {
            return error_code(15603);
        }

        $pintuanRuleModel = new PintuanRule();
        $where[]          = ['pr.status', 'eq', $pintuanRuleModel::STATUS_ON];
        $where[]          = ['pr.etime', '>', time()];

        //把拼团的一些属性等加上
        $where[] = ['pg.goods_id', 'eq', $gid];
        $info    = $this
            ->alias('pg')
            ->join('pintuan_rule pr', 'pg.rule_id = pr.id')
            ->where($where)
            ->find();

        if (!$info) {
            return error_code(15603);
        }
        $goodsInfo['data']['pintuan_rule']  = $info;
        $goodsInfo['data']['pintuan_price'] = $goodsInfo['data']['price'] - $info['discount_amount'];
        if ($goodsInfo['data']['pintuan_price'] < 0) {
            $goodsInfo['data']['pintuan_price'] = 0;
        }
        $orderModel = new Order();
        //取拼团记录
        $recordModel = new PintuanRecord();
        //多少人在拼
        $rwhere[] = ['pr.rule_id', 'eq', $info['id']];
        $rwhere[] = ['pr.goods_id', 'eq', $gid];
        // 不管是否支付都统计进来
        // $rwhere[] = ['o.pay_status', 'eq', $orderModel::PAY_STATUS_YES];


        //拼团中，未结束的
        $rwhere[] = ['pr.status', 'eq', $recordModel::STATUS_COMM];
        $rwhere[] = ['pr.close_time', '>', time()];

        $goodsInfo['data']['pintuan_record_nums'] = $recordModel
            ->alias('pr')
            ->join('order o', 'pr.order_id = o.order_id')
            ->where($rwhere)->count();


        $goodsInfo['data']['pintuan_rule']['pintuan_start_status'] = 1;
        //判断拼团状态
        $nowtime = time();
        if ($goodsInfo['data']['pintuan_rule']['stime'] > $nowtime) {
            $goodsInfo['data']['pintuan_rule']['pintuan_start_status'] = 2;//未开始
            $goodsInfo['data']['pintuan_rule']['lasttime']             = secondConversionArray($goodsInfo['data']['pintuan_rule']['stime'] - time());
        } elseif ($goodsInfo['data']['pintuan_rule']['stime'] <= $nowtime && $goodsInfo['data']['pintuan_rule']['etime'] > $nowtime) {
            $goodsInfo['data']['pintuan_rule']['lasttime']             = secondConversionArray($goodsInfo['data']['pintuan_rule']['etime'] - time());
            $goodsInfo['data']['pintuan_rule']['pintuan_start_status'] = 1;//已开始
        } else {
            $goodsInfo['data']['pintuan_rule']['pintuan_start_status'] = 3;//已过期
        }
        //拼团记录
        $re                                  = $recordModel->getRecord($info['id'], $gid, $recordModel::STATUS_COMM);
        $goodsInfo['data']['pintuan_record'] = $re['data'];

        //调整前台显示数量
        $check_order = $orderModel->findLimitOrder($goodsInfo['data']['product']['id'], 0, $info, $orderModel::ORDER_TYPE_PINTUAN);
        if (isset($info['max_goods_nums']) && $info['max_goods_nums'] != 0) {
            $goodsInfo['data']['stock'] = $info['max_goods_nums'];
            //活动销售件数
            $pintuan_stock                          = $info['max_goods_nums'] - $check_order['data']['total_orders'];
            $goodsInfo['data']['product']['stock']  = $pintuan_stock > 0 ? $pintuan_stock : 0;
            $goodsInfo['data']['buy_pintuan_count'] = $check_order['data']['total_orders'];
        } else {
            $goodsInfo['data']['buy_pintuan_count'] = $check_order['data']['total_orders'];
        }
        //原价
        $goodsInfo['data']['product']['mktprice'] = $goodsInfo['data']['product']['price'];
        return $goodsInfo;
    }

    /**
     * 取拼团的货品信息
     * @param $pid
     */
    public function getProductInfo($pid)
    {

        $productsModel = new Products();
        $product       = $productsModel->getProductInfo($pid);

        if (!$product['status']) {
            return $product;
        }

        //把拼团的一些属性等加上
        $where[] = ['pg.goods_id', 'eq', $product['data']['goods_id']];
        $info    = $this
            ->alias('pg')
            ->join('pintuan_rule pr', 'pg.rule_id = pr.id')
            ->where($where)
            ->find();
        if (!$info) {
            return error_code(10000);
        }

        $product['data']['pintuan_rule'] = $info;
        //$product['data']['pintuan_price'] = $product['data']['price'] - $info['discount_amount'];
        return $product;
    }


}

