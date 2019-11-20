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
     * 更改发货数量
     * @param $order_id
     * @param $item         发货明细
     * @return string
     * @throws \think\Exception
     */
    public function ship($order_id, $item)
    {
        $isOver = true;     //是否发完了，true发完了，false未发完
        $list = $this->where('order_id',$order_id)->select();
        foreach ($list as $k => $v) {
            if(isset($item[$v['product_id']])){
                $max_num = $v['nums'] - $v['sendnums'];     //还需要减掉收货数量
                if($item[$v['product_id']] > $max_num){     //如果发超了怎么办
                    exception($order_id."的".$v['product_id']."发超了",10000);
                }
                if($isOver && $item[$v['product_id']] < $max_num){          //判断是否订单发完了，有一个没发完，就是未发完
                    $isOver = false;
                }
                $v->setInc('sendnums',$item[$v['product_id']]);
                unset($item[$v['product_id']]);
            }
        }
        //如果没发完，也报错
        if($item){
            exception('发货明细里包含订单之外的商品',13008);
        }
        return $isOver;
    }
}