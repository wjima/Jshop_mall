<?php

namespace app\common\model;

class PromotionCondition extends Common
{
    public $code = [
        'GOODS_ALL' => [
            'name' => '所有商品满足条件',
            'type' => 'goods',
            'sort' => 100,
            'class' => 'GoodsAll',
        ],
        'GOODS_IDS' => [
            'name' => '指定某些商品满足条件',
            'type' => 'goods',
            'sort' => 100,
            'class' => 'GoodsIds',
        ],
        'GOODS_CATS' => [
            'name' => '指定商品分类满足条件',
            'type' => 'goods',
            'sort' => 100,
            'class' => 'GoodsCats',
        ],
        'GOODS_BRANDS' => [
            'name' => '指定商品品牌满足条件',
            'type' => 'goods',
            'sort' => 100,
            'class' => 'GoodsBrands',
        ],
        'ORDER_FULL' => [
            'name' => '订单满XX金额满足条件',
            'type' => 'order',
            'sort' => 100,
            'class' => 'OrderFull',
        ],
        'USER_GRADE' => [
            'name' => '用户符合指定等级',
            'type' => 'user',
            'sort' => 100,
            'class' => 'UserGrade'
        ],
        'USER_IDS' => [
            'name' => '指定某些用户',
            'type' => 'user',
            'sort' => 100,
            'class' => 'UserIds',
        ],
        'GOODS_NUM' => [
            'name' => '指定商品满足数量',
            'type' => 'goods',
            'sort' => 200,              //排序一定要小于上面的商品促销条件
            'class' => 'GoodsNum',
        ],
        'GOODS_P_NUM' => [
            'name' => '指定单品满足数量',
            'type' => 'goods',
            'sort' => 200,              //排序一定要小于上面的商品促销条件
            'class' => 'GoodsPNum',
        ],
    ];


    /**
     * @param $code
     * @param array $params
     * @return string
     */
    public function getConditionMsg($code, $params = [])
    {
        if(!$this->code[$code]){
            return "";
        }
        $code = '\\org\\promotion\\condition\\'.$this->code[$code]['class'];
        $condition =  new $code();
        return $condition->getMsg($params);
    }



    //检查是否满足条件
    public function check($conditionInfo,&$cart,$promotionInfo)
    {
        if(!$this->code[$conditionInfo['code']]){
            return false;
        }
        $params = json_decode($conditionInfo['params'],true);
        $code = '\\org\\promotion\\condition\\'.$this->code[$conditionInfo['code']]['class'];
        $condition =  new $code();
        return $condition->jshop($params,$cart,$promotionInfo);
    }



    /**
     * 促销条件循环的时候，有一些是false，标示这个商品不满足条件，当促销条件执行结束的时候，要把这些false都删掉，防止影响前台
     * @param $cart
     * @param $promotionInfo
     * @param $type true&false    此条促销记录是否满足所有条件，如果不满足，就要把此促销信息都删掉，如果满足，就单纯的把商品列表上的不满足删掉
     * @return bool
     */
    public function promotionFalse(&$cart, $promotionInfo, $type){
        if(!$type){
            unset($cart['promotion_list'][$promotionInfo['id']]);
        }

        foreach($cart['list'] as $k => &$v){
            if(isset($v['products']['promotion_list'][$promotionInfo['id']])){
                if($type){
                    if(!$v['products']['promotion_list'][$promotionInfo['id']]){
                        unset($v['products']['promotion_list'][$promotionInfo['id']]);
                    }
                }else{
                    unset($v['products']['promotion_list'][$promotionInfo['id']]);
                }
            }
        }
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
            ->alias('pc')
            ->join(config('database.prefix').'promotion p','p.id = pc.promotion_id')
            ->where($tableWhere['where'])
            ->order(['sort'=>'ASC','id'=>'DESC'])
            ->select();

        $data = $this->tableFormat($list);

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = count($list);
        $re['data'] = $data;
        // $re['sql'] = $this->getLastSql();

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

        $data['sort'] = $this->code[$data['code']]['sort'];
        $data['params'] = json_encode($data['params']);
        if($data['id'] != ''){
            //更新
            $info = $this->getInfo($data['id']);
            if ($info) {
                if ($this->allowField(true)->save($data, ['id' => $data['id']]) !== false) {
                    $result['status'] = true;
                } else {
                    $result['msg'] = error_code(10004,true);
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
                    $result['msg'] = error_code(10004,true);
                }
                return $result;
            }else{
                $result['msg'] = error_code(15019,true);
                return $result;
            }
        }
    }

    //添加或者编辑的时候，校验信息
    private function addCheck($data)
    {
        if(!isset($data['code']) || !isset($data['promotion_id']) || !isset($data['params'])){
            return error_code(10003);
        }
        if(!isset($this->code[$data['code']])){
            return error_code(15004);
        }

        $code = '\\org\\promotion\\condition\\'.$this->code[$data['code']]['class'];
        $condition =  new $code();
        return $condition->manageCheck($data['params']);
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

}