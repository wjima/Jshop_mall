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
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list       = $this
            ->alias("pg")
            ->field("pr.*,pg.goods_id,g.name as goods_name,g.image_id as goods_image_id")
            ->join("pintuan_rule pr", "pg.rule_id = pr.id")
            ->join("goods g", "g.id = pg.goods_id")
            ->where($tableWhere['where'])
            ->order($tableWhere['order'])
            ->paginate($limit);


        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
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
    protected function tableFormat($list)
    {
        foreach ($list as $key => $value) {
            if ($value['goods_image_id']) {
                $list[$key]['goods_image'] = _sImage($value['goods_image_id']);
            } else {
                $list[$key]['goods_image'] = _sImage();
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

        if (!$goodsInfo['status']) {
            return $goodsInfo;
        }

        //把拼团的一些属性等加上
        $where[] = ['pg.goods_id', 'eq', $gid];
        $info    = $this
            ->alias('pg')
            ->join('pintuan_rule pr', 'pg.rule_id = pr.id')
            ->where($where)
            ->find();
        if (!$info) {
            return error_code(10000);
        }
        $goodsInfo['data']['pintuan_rule'] = $info;
        //$goodsInfo['data']['pintuan_price'] = $goodsInfo['data']['price'] - $info['discount_amount'];
        //取拼团记录
        $recordModel = new PintuanRecord();
        //多少人在拼
        $rwhere[]                                 = ['rule_id', 'eq', $info['id']];
        $rwhere[]                                 = ['goods_id', 'eq', $gid];
        $goodsInfo['data']['pintuan_record_nums'] = $recordModel->where($rwhere)->count();


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
        $re                                  = $recordModel->getRecord($info['id'], $gid, 1);
        $goodsInfo['data']['pintuan_record'] = $re['data'];


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

