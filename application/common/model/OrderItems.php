<?php

namespace app\common\model;

use think\Db;

/**
 * 订单子表
 * Class OrderItems
 * @package app\common\model
 * @author keinx
 */
class OrderItems extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = false;
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

    /**
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        $where[] = ['order_id', '=', $post['order_id']];

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "id asc";
        return $result;
    }

    //后台订单编辑的时候，增加订单明细记录
    public function orderEditItemsAdd($order_id,$ids){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];


        $orderModel = new Order();
        $where[] = ['order_id', '=', $order_id];
        $where[] = ['status', '=', $orderModel::ORDER_STATUS_NORMAL];
        $where[] = ['ship_status', '=', $orderModel::SHIP_STATUS_NO];
        $orderInfo = $orderModel->where($where)->find();
        if(!$orderInfo){
            return error_code(13011);
        }

        $ids = explode(',', $ids);
        $productsModel = new Products();
        $goodsModel = new Goods();
        $itemsData = [];

        Db::startTrans();
        try {
            foreach($ids as $v){
                $productInfo = $productsModel->getProductInfo($v, false, $orderInfo['user_id']);
                if (!$productInfo['status']) {
                    return error_code(13012, false,$v);
                }
                $productInfo = $productInfo['data'];
                //下单，冻结库存
                $re = $goodsModel->changeStock($productInfo['id'], 'order', 1);
                if(!$re['status']){
                    return $re;
                }
    
                $one = [];
                $one['order_id'] = $order_id;
                $one['goods_id'] = $productInfo['goods_id'];
                $one['product_id'] = $productInfo['id'];
                $one['sn'] = $productInfo['sn'];
                $one['bn'] = $productInfo['bn'];
                $one['name'] = $productInfo['name'];
                $one['price'] = $productInfo['price'];
                $one['costprice'] = $productInfo['costprice'];
                $one['mktprice'] = $productInfo['mktprice'];
                $one['image_url'] = $productInfo['image_path'];
                $one['nums'] = 1;
                $one['amount'] = $productInfo['price'];
                $one['promotion_amount'] = $productInfo['promotion_amount'];
                $one['promotion_list'] = $productInfo['promotion_list'];
                $one['weight'] = 0;
                $one['sendnums'] = 0;
                $one['addon'] = $productInfo['spes_desc'];
                $itemsData[] = $one;
            }
            $this->saveAll($itemsData);
    
            //更新订单主体表
            $this->orderEditOrderUpdate($order_id);
            Db::commit();
            $result['status'] = true;
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
        return $result;

    }

    //后台订单编辑的时候，删除订单明细记录
    public function orderEditItemsDel($order_id,$id){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $orderModel = new Order();
        $where[] = ['order_id', '=', $order_id];
        $where[] = ['status', '=', $orderModel::ORDER_STATUS_NORMAL];
        $where[] = ['ship_status', '=', $orderModel::SHIP_STATUS_NO];
        $orderInfo = $orderModel->where($where)->find();
        if(!$orderInfo){
            return error_code(13011);
        }

        $where2[] = ['id', '=',$id];
        $where2[] = ['order_id', '=', $order_id];
        $info = $this->where($where2)->find();
        if(!$info){
            return error_code(13013);
        }
        Db::startTrans();
        try {
            //删除明细，解冻库存
            $goodsModel = new Goods();
            $re = $goodsModel->changeStock($info['product_id'], 'cancel', $info['nums']);
            if(!$re['status']){
                return $re;
            }
            $info->delete();
            //更新订单主体表
            $this->orderEditOrderUpdate($order_id);

            Db::commit();
            $result['status'] = true;
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
        return $result;
    }
    //后台订单编辑的时候，更新订单明细记录
    public function orderEditItemsEdit($order_id,$id,$data){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $orderModel = new Order();
        $where[] = ['order_id', '=', $order_id];
        $where[] = ['status', '=', $orderModel::ORDER_STATUS_NORMAL];
        $where[] = ['ship_status', '=', $orderModel::SHIP_STATUS_NO];
        $orderInfo = $orderModel->where($where)->find();
        if(!$orderInfo){
            return error_code(13011);
        }


        $where2[] = ['id', '=',$id];
        $where2[] = ['order_id', '=', $order_id];
        $info = $this->where($where2)->find();
        if(!$info){
            return error_code(10000);
        }

        Db::startTrans();
        try {
            if(isset($data['nums'])){
                $data['nums'] = abs((int)$data['nums']);
                if($data['nums'] === 0){
                    return error_code(13014);
                }
                if($info->nums != $data['nums']){
                    $goodsModel = new Goods();
                    //冻结或者解冻库存
                    if($info->nums > $data['nums']){
                        //数量减少要释放库存
                        $re = $goodsModel->changeStock($info['product_id'], 'cancel', $info->nums - $data['nums']);
                    }else{
                        //数量增加要冻结库存
                        $re = $goodsModel->changeStock($info['product_id'], 'order', $data['nums'] - $info->nums);
                    }
                    if(!$re['status']){
                        return $re;
                    }
    
    
                    $info->nums = $data['nums'];
                }
            }
            if(isset($data['price'])){
                $info->price = $data['price'];
            }
            if(isset($data['promotion_amount'])){
                $info->promotion_amount = $data['promotion_amount'];
            }
            $info->amount = $info->nums * $info->price;
            // //计算重量
            $goodsModel = new Goods();
            $goodsInfo = $goodsModel->where('bn',$info->bn)->find();
            if($goodsInfo){
                $info->weight = $info->nums * $goodsInfo['weight'];
            }
            $info->save();
    
            //更新订单主体表
            $this->orderEditOrderUpdate($order_id);

            Db::commit();
            $result['status'] = true;
        } catch (\Exception $e) {
            Db::rollback();
            $result['msg'] = $e->getMessage();
            return $result;
        }
        return $result;
    }

    //订单明细更新之后，订单主体表更新,会更新商品总重量goods_amount、商品总重量weight、商品优惠金额goods_pmt
    private function orderEditOrderUpdate($order_id){
        $result = [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];
        $data['goods_amount'] = 0;
        $data['weight'] = 0;
        $data['goods_pmt'] = 0;
        $list = $this->where('order_id',$order_id)->select();
        foreach($list as $v){
            $data['goods_amount'] += $v['amount'];
            $data['weight'] += $v['weight'];
            $data['goods_pmt'] += $v['promotion_amount'];
        }
        $orderModel = new Order();
        $where[] = ['order_id', '=', $order_id];
        $where[] = ['status', '=', $orderModel::ORDER_STATUS_NORMAL];
        $where[] = ['ship_status', '=', $orderModel::SHIP_STATUS_NO];
        $orderModel->save($data, $where);

        //订单记录
        $orderLog = new OrderLog();
        $orderLog->addLog($order_id,0, $orderLog::LOG_TYPE_EDIT, '后台订单明细编辑修改', $data);

        return $result;
    }


}