<?php

namespace app\common\model;

class PromotionResult extends Common
{
    public $code = [

        'GOODS_REDUCE' => [
            'name' => '指定商品减固定金额',
            'type' => 'goods',
            'sort' => 100,
            'class' => 'GoodsReduce',
        ],
        'GOODS_DISCOUNT' => [
            'name' => '指定商品打X折',
            'type' => 'goods',
            'sort' => 100,
            'class' => 'GoodsDiscount'
        ],
        'GOODS_ONE_PRICE' => [
            'name' => '指定商品一口价',
            'type' => 'goods',
            'sort' => 100,
            'class' => 'GoodsOnePrice'
        ],
        'ORDER_REDUCE' => [
            'name' => '订单减指定金额',
            'type' => 'order',
            'sort' => 100,
            'class' => 'OrderReduce'
        ],
        'ORDER_DISCOUNT' => [
            'name' => '订单打X折',
            'type' => 'order',
            'sort' => 100,
            'class' => 'OrderDiscount'
        ],
        'GOODS_HALF_PRICE' => [
            'name' => '指定商品第X件减指定金额',
            'type' => 'goods',
            'sort' => 100,
            'class' => 'GoodsHalfPrice'
        ],
        'GIVEAWAY' => [
            'name' => '满足条件送赠品',
            'type' => 'order',
            'sort' => 100,
            'class' => 'Giveaway'
        ],
//        'GOODS_GIVEAWAY' => [
//            'name' => '商品满赠商品',
//            'type' => 'goods',
//            'class' => 'GoodsGiveaway'
//        ],
    ];


    /**
     * @param $code
     * @param array $params
     * @return string
     */
    public function getResultMsg($code, $params = [])
    {
        if(!$this->code[$code]){
            return "";
        }
        $code = '\\org\\promotion\\result\\'.$this->code[$code]['class'];
        $condition =  new $code();
        return $condition->getMsg($params);
    }

    //去计算结果
    public function toResult($resultInfo,&$cart, $promotionInfo)
    {
        if(!$this->code[$resultInfo['code']]){
            return false;
        }
        $params = json_decode($resultInfo['params'],true);
        $code = '\\org\\promotion\\result\\'.$this->code[$resultInfo['code']]['class'];
        $result =  new $code();
        return $result->jshop($params,$cart,$promotionInfo);


        return true;
    }


    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {

        $tableWhere = $this->tableWhere($post);
        $list = $this
            ->field($tableWhere['field'])
            ->alias('pr')
            ->join(config('database.prefix').'promotion p','p.id = pr.promotion_id')
            ->where($tableWhere['where'])
            ->order(['sort'=>'ASC','id'=>'DESC'])
            ->select();

        $data = $this->tableFormat($list);

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = count($list);
        $re['data'] = $data;
//        $re['sql'] = $this->getLastSql();

        return $re;
    }
    protected function tableWhere($post)
    {
        $result['where'] = [];

        if(isset($post['promotion_id'])){
            $result['where']['pr.promotion_id'] = $post['promotion_id'];
        }

        if(isset($post['field'])){
            $result['field'] = $post['field'];
        }else{
            $result['field'] = "pr.*";
        }
        if(isset($post['order'])){
            $result['order'] = $post['order'];
        }else{
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
        foreach($list as $k => $v) {
            if($v['code']) {
                $list[$k]['name'] = $this->code[$v['code']]['name'];
            }
            if($v['params']) {
                $list[$k]['params'] = $v['params'];
            }
        }
        return $list;
    }
    //取信息
    public function getInfo($id){
        $where['pr.id'] = $id;
        $info = $this
            ->field('pr.*')
            ->alias('pr')
            ->join(config('database.prefix').'promotion p','p.id = pr.promotion_id')
            ->where($where)->find();
        if($info){
            $info['params'] = json_decode($info['params'],true);
        }
        return $info;
    }

    public function getResultList($id)
    {
        $where[] = ['promotion_id', 'eq', $id];
        $list = $this->where($where)->select();
        if($list !== false && count($list)>0)
        {
            foreach($list as $k => &$v)
            {
                $v['params'] = json_decode($v['params'], true);
            }
        }
        return $list;
    }

    /**
     *  添加促销的条件
     * User:wht
     * @param array $data
     * @return array
     */
    public function addData($data)
    {
        //校验结果
        $result = $this->addCheck($data);
        if(!$result['status']){
            return $result;
        }
        $result['status'] = false;          //重新置成false

        //判断如果是商品促销结果，在params里必须要有condition_id信息
        if (!isset($this->code[$data['code']])) {
            // $result['msg'] = '没有此促销结果代码';
            return error_code(15029);
        }

        $data['sort'] = $this->code[$data['code']]['sort'];
        $data['params'] = json_encode($data['params']);
        if($data['id'] != ''){
            //更新
            $info = $this->getInfo($data['id']);
            if($info){
                if($this->allowField(true)->save($data,['id'=>$data['id']])){
                    $result['status'] = true;
                }else{
                    return error_code(10004);
                }
                return $result;

            }
        }else{
            //添加
            //先判断是否有此促销
            $promotionModel = new Promotion();
            $where['id'] = $data['promotion_id'];
            $promotionInfo = $promotionModel->where($where)->find();
            if($promotionInfo){
                if($this->allowField(true)->save($data)){
                    $result['status'] = true;
                }else{
                    error_code(10004);
                }
                return $result;
            }else{
//                $result['msg'] = '没有找到此促销记录';
                return error_code(10519);
            }
        }
    }

    private function addCheck($data)
    {
        if(!isset($data['code']) || !isset($data['promotion_id']) || !isset($data['params'])){
            return error_code(10003);
        }
        if(!isset($this->code[$data['code']])){
            return error_code(15005);
        }

        $code = '\\org\\promotion\\result\\'.$this->code[$data['code']]['class'];
        $result =  new $code();
        return $result->manageCheck($data['params']);
    }



    public function toDel($id)
    {
        $info = $this->getInfo($id);
        if($info){
            $this->where(['id'=>$info['id'],'promotion_id'=>$info['promotion_id']])->delete();
            $result = [
                'status' => true,
                'data' => '',
                'msg' => ''
            ];
            return $result;
        }else{
            return error_code(15019);
        }
    }

//    //第几件减去指定金额
//    private function result_ORDER_HALF_PRICE($params,&$cart,$promotionInfo)
//    {
//
//        $conditionModel = new PromotionCondition();
//        $condition = $conditionModel->where([['promotion_id','=',$promotionInfo['id']]])->select();
//        $qualified_goods = [];
//        foreach($cart['list'] as $k => $v) {
//            $type = $conditionModel->goods_check($promotionInfo['id'], $v['products']['goods_id'], $v['nums']);
//            if($type == 2){
//                if(isset($qualified_goods[$v['products']['goods_id']]) && $qualified_goods[$v['products']['goods_id']]){
//                    $qualified_goods[$v['products']['goods_id']]['nums'] += $v['nums'];
//                }else{
//                    $qualified_goods[$v['products']['goods_id']] = [
//                        'id'=>$v['id'],
//                        'nums'=>$v['nums'],
//                    ];
//                }
//            }
//        }
//        $totalPromotionMoney = 0;
//        if($qualified_goods){
//            foreach($qualified_goods as $key=>$val){
//                $promotionMoney = 0;
//                //判断是否满足件数
//                if($val['nums'] < $params['num']){
//                    continue;
//                }
//                //此次商品促销一共优惠了多少钱
//                $times = floor($val['nums']/$params['num']);
//                if($times>0){
//                    $promotionMoney =  $params['money']*$times;
//                    $totalPromotionMoney = $totalPromotionMoney+$promotionMoney;
//                    $qualified_goods[$key]['promotion_money'] = $promotionMoney;
//                }
//
//            }
//        }
//
//        $order_amount = $cart['amount'];
//        //总价格修改
//        $cart['amount'] = bcsub($cart['amount'],$totalPromotionMoney,2);
//
//        switch ($promotionInfo['type']){
//            case $promotionInfo::TYPE_PROMOTION:
//                //总促销修改
//                $cart['order_pmt'] = bcadd($cart['order_pmt'], bcsub($order_amount, $cart['amount'], 2), 2);
//
//                //设置促销列表
//                if(!isset($cart['promotion_list'][$promotionInfo['id']])){
//                    $cart['promotion_list'][$promotionInfo['id']] = [
//                        'name' => $promotionInfo['name'],
//                        'type' => 2
//                    ];
//                }
//                break;
//            case $promotionInfo::TYPE_COUPON:
//                //优惠券促销金额
//                $cart['coupon_pmt'] = bcadd($cart['coupon_pmt'], bcsub($order_amount, $cart['amount'], 2), 2);
//                break;
//        }
//
//    }
//
//
//    /**
//     * 订单满赠
//     * @param $params
//     * @param $v
//     * @param $promotionInfo
//     * @throws \think\db\exception\DataNotFoundException
//     * @throws \think\db\exception\ModelNotFoundException
//     * @throws \think\exception\DbException
//     */
//    private function result_ORDER_GIVEAWAY($params, &$v, $promotionInfo)
//    {
//        $goodsModel = new Goods();
//        $productsModel = new Products();
//        $goods = $goodsModel->where('id', '=', $params['goods_id'])
//            ->find();
//        $goods['product'] = $productsModel->where('goods_id', '=', $params['goods_id'])
//            ->find();
//        //判断库存
//        $stock = $goods['product']['stock'] - $goods['product']['freeze_stock'];
//        if ( $stock != 0) {
//            if ($stock > $params['nums']) {
//                $num = $params['nums'];
//            } else {
//                $num = $stock;
//            }
//            $goods['nums'] = $num;
//            $v['giveaway'][] = $goods;
//        }
//    }
//
//
//    /**
//     * 商品满赠
//     * @param $params
//     * @param $v
//     * @param $promotionInfo
//     * @throws \think\db\exception\DataNotFoundException
//     * @throws \think\db\exception\ModelNotFoundException
//     * @throws \think\exception\DbException
//     */
//    private function result_GOODS_GIVEAWAY($params, &$v, $promotionInfo)
//    {
//        $goodsModel = new Goods();
//        $productsModel = new Products();
//        $goods = $goodsModel->where('id', '=', $params['goods_id'])
//            ->find();
//        $goods['product'] = $productsModel->where('goods_id', '=', $params['goods_id'])
//            ->find();
//        $promotionConditionModel = new PromotionCondition();
//        $condition = $promotionConditionModel
//            ->where('promotion_id', '=', $promotionInfo['id'])
//            ->find();
//        $condition = json_decode($condition['params'], true);
//        $servings = floor($v['nums'] / $condition['nums']);
//        $nums = $params['nums'] * $servings;
//        $stock = $goods['product']['stock'] - $goods['product']['freeze_stock'];
//        if ( $stock != 0) {
//            if ($stock > $nums) {
//                $num = $nums;
//            } else {
//                $num = $stock;
//            }
//            $goods['nums'] = $num;
//            $v['giveaway'][] = $goods;
//        }
//    }
}