<?php

namespace app\common\model;

/**
 * 订单子表
 * Class OrderItems
 * @package app\common\model
 * @author keinx
 */
class OrderItems extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'utime';
    protected $updateTime = 'utime';


    /**
     * 发货数量
     * @param $order_id
     * @param $item         发货明细
     * @return string
     * @throws \think\Exception
     */
    public function ship($order_id, $item)
    {
        $goodsModel = new Goods();
        $isOver = true;     //是否发完了，true发完了，false未发完
        $list = $this->where('order_id',$order_id)->select();
        foreach ($list as $k => $v) {
            if(isset($item[$v['product_id']])){
                $max_num = $v['nums'] - $v['sendnums'];     //还需要减掉已发数量

                //还需要减掉已退的数量
                $reship_nums = $this->getaftersalesNums($order_id,$v['sn']);
                $max_num = $max_num - $reship_nums;

                if($item[$v['product_id']] > $max_num){     //如果发超了怎么办
                    exception(error_code(13326,true,$order_id,$v['sn']),13326);  //$order_id."的".$v['sn']."发超了"
                }
                if($isOver && $item[$v['product_id']] < $max_num){          //判断是否订单发完了，有一个没发完，就是未发完
                    $isOver = false;
                }
                $v->setInc('sendnums',$item[$v['product_id']]);

                //发货后，减库存
                $goodsModel->changeStock($v['product_id'], 'send', $item[$v['product_id']]);


                unset($item[$v['product_id']]);
            }
        }
        //如果没发完，也报错
        if($item){
            exception(error_code(13008,true),13308);
        }
        return $isOver;
    }
    //算订单的商品退了多少个(未发货的退货数量，已发货的退货不算)
    public function getaftersalesNums($order_id,$sn){
        $where = [
            'a.order_id' => $order_id,
            'a.status' => 2,
            'asi.sn' => $sn,
            'a.type' => 1
        ];
        $afterSalesItemsModel = new BillAftersalesItems();
        $re = $afterSalesItemsModel
            ->alias('asi')
            ->join(config('database.prefix') . 'bill_aftersales a', 'asi.aftersales_id = a.aftersales_id')
            ->where($where)
            ->sum('asi.nums');


        return $re;
    }
}