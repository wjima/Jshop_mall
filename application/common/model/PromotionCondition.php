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
        'USER_GRADE' => [
            'name' => '用户符合指定等级',
            'type' => 'user'
        ]
    ];


    /**
     * @param $code
     * @param array $params
     * @return string
     */
    public function getConditionMsg($code, $params = [])
    {
        switch($code)
        {
            case 'GOODS_ALL':
                $msg = '购买所有商品 ';
                break;
            case 'GOODS_IDS':
                $msg = '购买指定商品 ';
                break;
            case 'GOODS_CATS':
                $msg = '购买指定分类商品 ';
                break;
            case 'GOODS_BRANDS':
                $msg = '购买指定品牌商品 ';
                break;
            case 'ORDER_FULL':
                $msg = '购买订单满'.$params['money'].'元 ';
                break;
            case 'USER_GRADE':
                $msg = '用户符合指定等级';
                break;
        }
        return $msg;
    }


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
                        if(!isset($cart['list'][$k]['products']['promotion_list'][$promotionInfo['id']])){
                            $cart['list'][$k]['products']['promotion_list'][$promotionInfo['id']] = [
                                'name' => $promotionInfo['name'],
                                'type' => $type
                            ];
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
            }elseif($this->code[$conditionInfo['code']]['type'] == 'order'){
                $type = $this->$method($params,$cart);
                if($type > 0){
                    //这里是订单促销，那么不管怎么样，加入待满足的促销列表里,针对本次促销来说，他永远都是没有或者type等于2的状态
                    if(!isset($cart['promotion_list'][$promotionInfo['id']])){
                        $cart['promotion_list'][$promotionInfo['id']] = [
                            'name' => $promotionInfo['name'],
                            'type' => $type
                        ];
                    }

                }
                if($type == 2){
                    return true;
                }else{
                    return false;
                }
            }elseif($this->code[$conditionInfo['code']]['type'] == 'user'){
                $type = $this->$method($params,$cart['user_id']);
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
        return 2;
    }

    //指定某些商品满足条件
    private function condition_GOODS_IDS($params,$goods_id,$nums){
        $goods_ids = explode(',',$params['goods_id']);
        if(in_array($goods_id, $goods_ids)){
            if($nums>= $params['nums']){
                return 2;
            }else{
                return 1;
            }
            //return 2;
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
            if($nums>= $params['nums']){
                return 2;
            }else{
                return 1;
            }
            //return 2;
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

    //指定用户等级满足条件
    private function condition_USER_GRADE($params,$user_id){
        $userModel = new User();
        $where[] = ['id', 'eq', $user_id];
        $info = $userModel->where($where)->find();
        if(!$info){
            return 0;
        }
        foreach($params as $k => $v){
            if($info['grade'] == $k){
                return 2;
            }
        }
        return 0;

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

    public function getConditionList($id)
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

        $data['params'] = json_encode($data['params']);
        if($data['id'] != ''){
            //更新
            $info = $this->getInfo($data['id']);
            if ($info) {
                if ($this->allowField(true)->save($data, ['id' => $data['id']]) !== false) {
                    $result['status'] = true;
                } else {
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