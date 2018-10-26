<?php

namespace app\common\model;

class PromotionCondition extends Common
{
    public $code = [
        'GOODS_ALL' => [
            'name' => '所有商品满足条件',
            'type' => 'goods',
        ],
        'GOODS_IDS' => [
            'name' => '指定某些商品满足条件',
            'type' => 'goods',
        ],
        'GOODS_CATS' => [
            'name' => '指定商品分类满足条件',
            'type' => 'goods',
        ],
        'GOODS_BRANDS' => [
            'name' => '指定商品品牌满足条件',
            'type' => 'goods',
        ],
        'ORDER_FULL' => [
            'name' => '订单满XX金额满足条件',
            'type' => 'order',
        ],
    ];



    //检查是否满足条件
    public function check($conditionInfo,&$cart,$promotionInfo)
    {
        if($this->code[$conditionInfo['code']]){
            $method = 'condition_'.$conditionInfo['code'];
            $params = json_decode($conditionInfo['params'],true);
            //如果是订单促销就直接去判断促销条件，如果是商品促销，就循环订单明细
            if($this->code[$conditionInfo['code']]['type'] == 'goods'){
                $key = false;
                foreach($cart['list'] as $k => $v){
                    $type = $this->$method($params,$v['products']['goods_id'],$v['nums']);
                    if($type > 0){
                        switch ($promotionInfo['type']){
                            case $promotionInfo::TYPE_PROMOTION:
                                //设置商品促销列表
                                if(!isset($cart['list'][$k]['products']['promotion_list'][$promotionInfo['id']])){
                                    $cart['list'][$k]['products']['promotion_list'][$promotionInfo['id']] = [
                                        'name' => $promotionInfo['name'],
                                        'type' => $type
                                    ];
                                }
                                break;
                        }

                    }
                    //只有选中的商品才算促销
                    if($v['is_select']){
                        if(!$key){
                            if($type == 2){
                                $key = true;            //针对某一条商品促销条件，循环购物车的所有商品，只要有一条满足要求就，算，就返回true
                            }
                        }
                    }
                }
                return $key;
            }else{
                $type = $this->$method($params,$cart);
                if($type > 0){
                    switch ($promotionInfo['type']){
                        case $promotionInfo::TYPE_PROMOTION:
                            //这里是订单促销，那么不管怎么样，加入待满足的促销列表里,针对本次促销来说，他永远都是没有或者type等于2的状态
                            if(!isset($cart['promotion_list'][$promotionInfo['id']])){
                                $cart['promotion_list'][$promotionInfo['id']] = [
                                    'name' => $promotionInfo['name'],
                                    'type' => $type
                                ];
                            }
                            break;
                    }

                }
                if($type == 2){
                    return true;
                }else{
                    return false;
                }
            }

        }
        return false;
    }
    //在促销结果中，如果是商品促销结果，调用此方法，判断商品是否符合需求
    public function goods_check($promotion_id,$goods_id,$nums = 1){
        $conditionList = $this->where(['promotion_id'=>$promotion_id])->select();
        foreach($conditionList as $k => $v){
            if($this->code[$v['code']]['type'] == 'goods'){
                $method = 'condition_'.$v['code'];
                $params = json_decode($v['params'],true);
                $type = $this->$method($params,$goods_id,$nums);
                if($type != 2){
                    return $type;
                }
            }
        }

        return 2;
    }

    //因为计算过促销条件后啊，前面有些是满足条件的，所以，他们的type是2，后面有不满足条件的时候呢，要把前面满足条件的回滚成不满足条件的
    public function promotionFalse(&$cart,$promotionInfo){
        switch ($promotionInfo['type']){
            case $promotionInfo::TYPE_PROMOTION:
                //订单促销回滚
                if(isset($cart['promotion_list'][$promotionInfo['id']])){
                    $cart['promotion_list'][$promotionInfo['id']] = [
                        'name' => $promotionInfo['name'],
                        'type' => 1
                    ];
                }
                //商品回滚
                foreach($cart['list'] as $k => $v){
                    if(isset($cart['list'][$k]['products']['promotion_list'][$promotionInfo['id']])){
                        $cart['list'][$k]['products']['promotion_list'][$promotionInfo['id']] = [
                            'name' => $promotionInfo['name'],
                            'type' => 1
                        ];
                    }

                }
                break;
        }

    }



    //订单满XX金额时满足条件
    private function condition_ORDER_FULL($params,&$cart){
        if($cart['amount']>=$params['money']){
            return 2;
        }else{
            return 1;
        }
    }

    //所有商品满足条件
    private function condition_GOODS_ALL($params,$goods_id,$nums){
        return true;
    }

    //指定某些商品满足条件
    private function condition_GOODS_IDS($params,$goods_id,$nums){
        if($goods_id == $params['goods_id']){
            return 2;
        }else{
            return 0;
        }
    }

    //指定商品分类满足条件
    private function condition_GOODS_CATS($params,$goods_id,$nums){
        $goodsModel = new Goods();
        $goodsCatModel = new GoodsCat();
        $goodsInfo = $goodsModel->find($goods_id);
        if(!$goodsInfo || !$goodsInfo['goods_cat_id']){
            return 0;
        }
        if($goodsCatModel->isChild($params['cat_id'],$goodsInfo['goods_cat_id'])){
            return 2;
        }else{
            return 0;
        }
    }

    //指定商品品牌满足条件
    private function condition_GOODS_BRANDS($params,$goods_id,$nums){
        $goodsModel = new Goods();
        $goodsCatModel = new GoodsCat();
        $goodsInfo = $goodsModel->find($goods_id);
        if(!$goodsInfo){
            return 0;
        }
        if($goodsInfo->brand && $goodsInfo->brand['id'] == $params['brand_id']){
            if($nums>= $params['nums']){
                return 2;
            }else{
                return 1;
            }
        }else{
            return 0;
        }
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
            ->alias('pc')
            ->join(config('database.prefix').'promotion p','p.id = pc.promotion_id')
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
            $result['where']['pc.promotion_id'] = $post['promotion_id'];
        }

        if(isset($post['field'])){
            $result['field'] = $post['field'];
        }else{
            $result['field'] = "pc.*";
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
        $where['pc.id'] = $id;
        $info = $this
            ->field('pc.*')
            ->alias('pc')
            ->join(config('database.prefix').'promotion p','p.id = pc.promotion_id')
            ->where($where)->find();
        if($info){
            $info['params'] = json_decode($info['params'],true);
        }
        return $info;
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

    //添加或者编辑的时候，校验信息
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
            return error_code(15004);
        }
        switch ($data['code'])
        {
            case 'GOODS_IDS':
                if(!preg_match("/^[1-9][0-9]*$/",$data['params']['nums'])){
                    $result['msg'] = "数量必须输入正整数";
                    return $result;
                }
                if($data['params']['goods_id'] == ''){
                    $result['msg'] = "请选择商品";
                    return $result;
                }
                break;
            case 'GOODS_CATS':
                if(!preg_match("/^[1-9][0-9]*$/",$data['params']['nums'])){
                    $result['msg'] = "数量必须输入正整数";
                    return $result;
                }
                if($data['params']['cat_id'] == ''){
                    $result['msg'] = "请选择商品分类";
                    return $result;
                }
                break;
            case 'GOODS_BRANDS':
                if(!preg_match("/^[1-9][0-9]*$/",$data['params']['nums'])){
                    $result['msg'] = "数量必须输入正整数";
                    return $result;
                }
                if($data['params']['brand_id'] == ''){
                    $result['msg'] = "请选择商品";
                    return $result;
                }
                break;
            case 'ORDER_FULL':
                if(!preg_match("/^[0-9]+(.[0-9]{1,2})?$/",$data['params']['money'])){
                    $result['msg'] = "请正确输入金额，最多2位小数";
                    return $result;
                }
                if($data['params']['money'] == ''){
                    $result['msg'] = "请输入金额";
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

}