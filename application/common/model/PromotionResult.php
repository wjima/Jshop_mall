<?php

namespace app\common\model;

class PromotionResult extends Common
{
    public $code = [

        'GOODS_REDUCE' => [
            'name' => '指定商品减固定金额',
            'type' => 'goods'
        ],
        'GOODS_DISCOUNT' => [
            'name' => '指定商品打X折',
            'type' => 'goods',
        ],
        'GOODS_ONE_PRICE' => [
            'name' => '指定商品一口价',
            'type' => 'goods',
        ],
        'ORDER_REDUCE' => [
            'name' => '订单减指定金额',
            'type' => 'order'
        ],
        'ORDER_DISCOUNT' => [
            'name' => '订单打X折',
            'type' => 'order',
        ],
        'GOODS_HALF_PRICE' => [
            'name' => '指定商品每第几件减指定金额',
            'type' => 'goods',
        ],
    ];


    /**
     * @param $code
     * @param array $params
     * @return string
     */
    public function getResultMsg($code, $params = [])
    {
        switch($code)
        {
            case 'GOODS_REDUCE':
                $msg = '减'.$params['money'].'元 ';
                break;
            case 'GOODS_DISCOUNT':
                $msg = '打'.$params['discount'].'折 ';
                break;
            case 'GOODS_ONE_PRICE':
                $msg = '一口价'.$params['money'].'元 ';
                break;
            case 'ORDER_REDUCE':
                $msg = '订单减'.$params['money'].'元 ';
                break;
            case 'ORDER_DISCOUNT':
                $msg = '订单打'.$params['discount'].'折 ';
                break;
            case 'GOODS_HALF_PRICE':
                $msg = '第'.$params['num'].'件'.$params['money'].'元';
                break;
        }
        return $msg;
    }

    //去计算结果
    public function toResult($resultInfo,&$cart, $promotionInfo)
    {
        if($this->code[$resultInfo['code']]){
            $method = 'result_'.$resultInfo['code'];
            $params = json_decode($resultInfo['params'],true);
            //如果是订单促销就直接去判断促销条件，如果是商品促销，就循环订单明细
            if($this->code[$resultInfo['code']]['type'] == 'goods'){
                $conditionModel = new PromotionCondition();
                foreach($cart['list'] as $k => $v){
                    $type = $conditionModel->goods_check($promotionInfo['id'],$v['products']['goods_id'],$v['nums']);
                    if($type == 2){
                        //到这里就说明此商品信息满足促销商品促销信息的条件，去计算结果
                        //注意，在明细上面，就不细分促销的种类了，都放到一个上面，在订单上面才细分
                        $promotionModel = $this->$method($params,$cart['list'][$k],$promotionInfo);
                        if($v['is_select']){
                            //根据具体的促销类型取做对应的操作
                            switch ($promotionInfo['type']){
                                case $promotionInfo::TYPE_PROMOTION:
                                    //设置总的商品促销金额
                                    $cart['goods_pmt'] = bcadd($cart['goods_pmt'], $promotionModel, 2);
                                    //设置总的价格
                                    $cart['amount'] = bcsub($cart['amount'], $promotionModel, 2);
                                    break;
                                case $promotionInfo::TYPE_COUPON:
                                    //优惠券促销金额
                                    $cart['coupon_pmt'] = bcadd($cart['coupon_pmt'], $promotionModel, 2);
                                    //设置总的价格
                                    $cart['amount'] = bcsub($cart['amount'], $promotionModel, 2);
                                    break;
                                case $promotionInfo::TYPE_GROUP:
                                    //团购
                                    $cart['goods_pmt'] = bcadd($cart['goods_pmt'], $promotionModel, 2);
                                    //设置总的价格
                                    $cart['amount'] = bcsub($cart['amount'], $promotionModel, 2);
                                    break;
                                case $promotionInfo::TYPE_SKILL:
                                    //秒杀
                                    $cart['goods_pmt'] = bcadd($cart['goods_pmt'], $promotionModel, 2);
                                    //设置总的价格
                                    $cart['amount'] = bcsub($cart['amount'], $promotionModel, 2);
                                    break;
                            }
                        }
                    }
                }
                //商品促销可能做的比较狠，导致订单价格为负数了，这里判断一下，如果订单价格小于0了，就是0了
                $cart['amount'] = $cart['amount']>0?$cart['amount']:0;
            }else{
                $this->$method($params,$cart,$promotionInfo);
            }
        }
        return true;
    }

    //订单减固定金额
    private function result_ORDER_REDUCE($params,&$cart,$promotionInfo)
    {
        //判断极端情况，减的太多，超过购物车的总金额了，那么就最多减到0
        if($cart['amount'] < $params['money']){
            $params['money'] = $cart['amount'];
        }
        //总价格修改
        $cart['amount'] = bcsub($cart['amount'], $params['money'], 2);

        switch ($promotionInfo['type']){
            case $promotionInfo::TYPE_PROMOTION:
                //总促销修改
                $cart['order_pmt'] = bcadd($cart['order_pmt'], $params['money'], 2);

                //设置促销列表
                if(!isset($cart['promotion_list'][$promotionInfo['id']])){
                    $cart['promotion_list'][$promotionInfo['id']] = [
                        'name' => $promotionInfo['name'],
                        'type' => 2
                    ];
                }
                break;
            case $promotionInfo::TYPE_COUPON:
                //优惠券促销金额
                $cart['coupon_pmt'] = bcadd($cart['coupon_pmt'], $params['money'], 2);
                break;
        }
        return true;
    }
    //订单打X折
    private function result_ORDER_DISCOUNT($params,&$cart,$promotionInfo)
    {
        //判断参数是否设置的正确
        if($params['discount'] >=10 || $params['discount'] <=0){
            return true;
        }
        $order_amount = $cart['amount'];
        //总价格修改
        $cart['amount'] = bcdiv(bcmul(bcmul($cart['amount'],$params['discount'],3),10,2), 100, 2);
        switch ($promotionInfo['type']){
            case $promotionInfo::TYPE_PROMOTION:
                //总促销修改
                $cart['order_pmt'] = bcadd($cart['order_pmt'], bcsub($order_amount, $cart['amount'], 2), 2);

                //设置促销列表
                if(!isset($cart['promotion_list'][$promotionInfo['id']])){
                    $cart['promotion_list'][$promotionInfo['id']] = [
                        'name' => $promotionInfo['name'],
                        'type' => 2
                    ];
                }
                break;
            case $promotionInfo::TYPE_COUPON:
                //优惠券促销金额
                $cart['coupon_pmt'] = bcadd($cart['coupon_pmt'], bcsub($order_amount, $cart['amount'], 2), 2);
                break;
        }

        return true;
    }




    //指定商品减固定金额
    private function result_GOODS_REDUCE($params,&$v,$promotionInfo)
    {
        $promotionMoney = 0;

        //判断极端情况，减的太多，超过商品单价了，那么就最多减到0
        if($v['products']['price'] < $params['money']){
            $params['money'] = $v['products']['price'];
        }
        $v['products']['price'] = bcsub($v['products']['price'], $params['money'], 2);

        //此次商品促销一共优惠了多少钱
        $promotionMoney = bcmul($v['nums'], $params['money'], 2);
        //设置商品优惠总金额
        if(!isset($v['products']['promotion_amount'])){
            $v['products']['promotion_amount'] = 0;
        }
        $v['products']['promotion_amount'] = bcadd($v['products']['promotion_amount'], $promotionMoney, 2);
        //设置商品的实际销售金额（单品）
        $v['products']['amount'] = bcsub($v['products']['amount'], $promotionMoney, 2);

        return $promotionMoney;
    }
    //指定商品打X折
    private function result_GOODS_DISCOUNT($params,&$v,$promotionInfo)
    {
        $promotionMoney = 0;

        $goods_price = $v['products']['price'];
        $v['products']['price'] = bcdiv(bcmul(bcmul($v['products']['price'], $params['discount'], 3), 10, 2), 100, 2);
        $pmoney = bcsub($goods_price, $v['products']['price'], 2);        //单品优惠的金额

        $promotionMoney = bcmul($v['nums'], $pmoney, 2);
        //设置商品优惠总金额
        if(!isset($v['products']['promotion_amount'])){
            $v['products']['promotion_amount'] = 0;
        }
        $v['products']['promotion_amount'] = bcadd($v['products']['promotion_amount'], $promotionMoney, 2);
        //设置商品的实际销售总金额
        $v['products']['amount'] = bcsub($v['products']['amount'], $promotionMoney, 2);

        return $promotionMoney;
    }

    //商品一口价
    private function result_GOODS_ONE_PRICE($params,&$v,$promotionInfo)
    {
        $promotionMoney = 0;

        if($v['products']['price'] <= $params['money']){
            return $promotionMoney;
        }

        $goods_price = $v['products']['price'];
        $v['products']['price'] = bcdiv(bcmul($params['money'], 100, 2), 100, 2);
        $pmoney = bcsub($goods_price, $v['products']['price'], 2);        //单品优惠的金额

        $promotionMoney = bcmul($v['nums'], $pmoney, 2);

        //设置商品优惠总金额
        if(!isset($v['products']['promotion_amount'])){
            $v['products']['promotion_amount'] = 0;
        }
        $v['products']['promotion_amount'] = bcadd($v['products']['promotion_amount'], $promotionMoney, 2);
        //设置商品的实际销售总金额
        $v['products']['amount'] = bcsub($v['products']['amount'], $promotionMoney, 2);

        return $promotionMoney;
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
            ->select();

        $data = $this->tableFormat($list);

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = count($list);
        $re['data'] = $data;
        $re['sql'] = $this->getLastSql();

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
        if(!isset($this->code[$data['code']]['type'])){
            $result['msg'] = '没有此促销结果代码';
            return $result;
        }


        $data['params'] = json_encode($data['params']);
        if($data['id'] != ''){
            //更新
            $info = $this->getInfo($data['id']);
            if($info){
                if($this->allowField(true)->save($data,['id'=>$data['id']])){
                    $result['status'] = true;
                }else{
                    $result['msg'] = "保存失败";
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
                    $result['msg'] = "保存失败";
                }
                return $result;
            }else{
                $result['msg'] = '没有找到此促销记录';
                return $result;
            }
        }
    }

    private function addCheck($data)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if(!isset($data['code']) || !isset($data['promotion_id']) || !isset($data['params'])){
            return error_code(10003);
        }
        if(!isset($this->code[$data['code']])){
            return error_code(15005);
        }
//        dump($data);
//        die();
        switch ($data['code'])
        {
            case 'GOODS_REDUCE':
                if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$data['params']['money'])){
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                if($data['params']['money'] == '' || $data['params']['money'] == '0'){
                    $result['msg'] = "请输入金额";
                    return $result;
                }
                break;
            case 'GOODS_DISCOUNT':
                if(!preg_match("/^[0-9]+(.[0-9]{1})?$/",$data['params']['discount'])){
                    $result['msg'] = "请正确输入折扣，最多1位小数";
                    return $result;
                }
                if($data['params']['discount'] == '' || $data['params']['discount'] == '0'){
                    $result['msg'] = "请输入0-10之间的数字";
                    return $result;
                }
                if(!($data['params']['discount']>0 && $data['params']['discount'] < 10)){
                    $result['msg'] = "请输入0-10之间的数字";
                    return $result;
                }

                break;
            case 'GOODS_ONE_PRICE':
                if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$data['params']['money'])){
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                if($data['params']['money'] == '' || $data['params']['money'] == '0'){
                    $result['msg'] = "请输入金额";
                    return $result;
                }
                break;
            case 'ORDER_REDUCE':
                if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$data['params']['money'])){
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                if($data['params']['money'] == '' || $data['params']['money'] == '0'){
                    $result['msg'] = "请输入金额";
                    return $result;
                }
                break;
            case 'ORDER_DISCOUNT':
                if(!preg_match("/^[0-9]+(.[0-9]{1})?$/",$data['params']['discount'])){
                    $result['msg'] = "请正确输入折扣，最多1位小数";
                    return $result;
                }
                if($data['params']['discount'] == '' || $data['params']['discount'] == '0'){
                    $result['msg'] = "请输入0-10之间的数字";
                    return $result;
                }
                if(!($data['params']['discount']>0 && $data['params']['discount'] < 10)){
                    $result['msg'] = "请输入0-10之间的数字";
                    return $result;
                }
                break;
            case 'GOODS_HALF_PRICE':
                if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$data['params']['money'])){
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                if($data['params']['money'] == '' || $data['params']['money'] == '0'){
                    $result['msg'] = "请输入金额";
                    return $result;
                }

                if($data['params']['num'] == '' || $data['params']['num'] == '0'){
                    $result['msg'] = "请输入大于0的数字";
                    return $result;
                }
                if(!($data['params']['num']>0)){
                    $result['msg'] = "请输入大于0的数字";
                    return $result;
                }
                break;
        }
        $result['status'] = true;


        return $result;
    }



    public function toDel($id)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        $info = $this->getInfo($id);
        if($info){
            $this->where(['id'=>$info['id'],'promotion_id'=>$info['promotion_id']])->delete();
            $result['status'] = true;
            return $result;
        }else{
            $result['msg'] = '没有找到此促销记录';
            return $result;
        }
    }

    //第几件减去指定金额
    private function result_GOODS_HALF_PRICE($params,&$v,$promotionInfo)
    {
        $promotionMoney = 0;
        //判断是否满足件数
        if($v['nums'] < $params['num']){
            return $promotionMoney;
        }
        //此次商品促销一共优惠了多少钱
        $times = floor($v['nums']/$params['num']);
        if($times>0){
            $promotionMoney =  $params['money']*$times;
        }
        //设置商品优惠总金额
        if(!isset($v['products']['promotion_amount'])){
            $v['products']['promotion_amount'] = 0;
        }
        $v['products']['promotion_amount'] = bcadd($v['products']['promotion_amount'], $promotionMoney, 2);
        //设置商品的实际销售金额（单品）
        $v['products']['amount'] = bcsub($v['products']['amount'], $promotionMoney, 2);
        $v['products']['amount'] = $v['products']['amount']>0?$v['products']['amount']:0;
        return $promotionMoney;
    }

}