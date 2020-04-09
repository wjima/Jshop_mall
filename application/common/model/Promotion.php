<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class Promotion extends Common
{
    use SoftDelete;
    protected $deleteTime = 'isdel';

    const EXCLUSIVE_NO = 1;
    const EXCLUSIVE_YES = 2;
    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;

    const TYPE_PROMOTION = 1;           //类型，促销
    const TYPE_COUPON = 2;              //类型，优惠券
    const TYPE_GROUP = 3;              //类型，团购
    const TYPE_SKILL = 4;              //类型，秒杀

    const AUTO_RECEIVE_YES = 1;     //自动领取
    const AUTO_RECEIVE_NO = 2;      //不自动领取

    const ACTIVITY_STATUS_NO_START = 1;//促销活动未开始
    const ACTIVITY_STATUS_PROGRESS = 2;//促销活动进行中
    const ACTIVITY_STATUS_END = 3;//促销活动已结束

    //购物车的数据传过来，然后去算促销
    public function toPromotion($cart, $type = self::TYPE_PROMOTION)
    {
        //按照权重取所有已生效的促销列表
        $where[] = ['status', 'eq', self::STATUS_OPEN];
        $where[] = ['stime', 'lt', time()];
        $where[] = ['etime', 'gt', time()];
        $where[] = ['type', 'eq', $type];
        $list    = $this->where($where)->order('sort', 'asc')->select();
        foreach ($list as $v) {
            $this->setPromotion($v, $cart);
            //团购秒杀不排他
            if(self::TYPE_GROUP||self::TYPE_SKILL){

            }else{
                //如果排他，就跳出循环，不执行下面的促销了
                if ($v['exclusive'] == self::EXCLUSIVE_YES) {
                    break;
                }
            }
        }
        return $cart;
    }

    //购物车的数据传过来，然后去算优惠券
    public function toCoupon(&$cart, $promotion_arr)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''

        ];
        foreach ($promotion_arr as $k => $v) {
            //按照权重取所有已生效的促销列表
            $where   = [];
            $where[] = ['status', 'eq', self::STATUS_OPEN];
            $where[] = ['stime', 'lt', time()];
            $where[] = ['etime', 'gt', time()];
            $where[] = ['type', 'eq', self::TYPE_COUPON];
            $where[] = ['id', 'eq', $v['promotion_id']];
            $info    = $this->where($where)->find();

            if (!$info) {
                return error_code(15014);
            }
            if ($this->setPromotion($info, $cart)) {
                $cart['coupon'][$k] = $v['name'];
            } else {
                return error_code(15014);
            }
        }
        $result['status'] = true;
        return $result;
    }


    //根据促销信息，去计算购物车的促销情况
    private function setPromotion($promotionInfo, &$cart)
    {
        $conditionModel        = new PromotionCondition();
        $where['promotion_id'] = $promotionInfo['id'];
        $conditionList         = $conditionModel->field('*')->where($where)->select();
        //循环取出所有的促销条件，有一条不满足，就不行，就返回false
        $key = true;

        foreach ($conditionList as $v) {
            $re = $conditionModel->check($v, $cart, $promotionInfo);
            if ($key) {
                if (!$re) {
                    $key = false;    //多个促销条件中，如果有一个不满足，整体就不满足，但是为了显示完整的促销标签，还是要运算完所有的促销条件
                }
            }
        }

        if ($key) {
            //走到这一步就说明所有的促销条件都符合，那么就去计算结果
            $resultModel = new PromotionResult();
            $resultList  = $resultModel->where($where)->select();

            foreach ($resultList as $v) {
                $resultModel->toResult($v, $cart, $promotionInfo);
            }
        } else {
            //如果不满足需求，就要统一标准，把有些满足条件的（2），变成1
            $conditionModel->promotionFalse($cart, $promotionInfo);
        }
        return $key;
    }

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
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;

        return $re;
    }

    protected function tableWhere($post)
    {

        $where = [];
        if (is_array($post['type'])) {
            $where[] = ['type', 'in', $post['type']];
        } else {
            $where[] = ['type', 'eq', $post['type']];
        }

        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', '%' . $post['name'] . '%'];
        }
        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }
        if (isset($post['exclusive']) && $post['exclusive'] != "") {
            $where[] = ['exclusive', 'eq', $post['exclusive']];
        }
        if (input('?param.date')) {
            $theDate = explode(' 到 ', input('param.date'));
            if (count($theDate) == 2) {
                $where[] = ['stime', '<', strtotime($theDate[1])];
                $where[] = ['etime', '>', strtotime($theDate[0])];
            }
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id' => 'desc', 'sort' => 'asc']; //默认最新添加靠前，排序越小越靠前
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
        foreach ($list as $k => $v) {
            if ($v['stime']) {
                $list[$k]['stime'] = getTime($v['stime']);
            }
            if ($v['etime']) {
                $list[$k]['etime'] = getTime($v['etime']);
            }
            //            if($v['status']){
            //                $list[$k]['status'] = config('params.promotion.status')[$v['status']];
            //            }
            //            if($v['exclusive']){
            //                $list[$k]['exclusive'] = config('params.promotion.exclusive')[$v['exclusive']];
            //            }
        }
        return $list;
    }

    /**
     * 获取可领取的优惠券
     * @param int $limit
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function receiveCouponList($limit = 3)
    {
        $where[] = ['etime', '>', time()];                //判断优惠券失效时间 是否可领取
        $where[] = ['status', 'eq', self::STATUS_OPEN];   //启用状态
        $where[] = ['type', 'eq', self::TYPE_COUPON];     //促销 类型
        $where[] = ['auto_receive', 'eq', self::AUTO_RECEIVE_YES];    //自动领取状态
        $data    = $this->field('id,name,status,exclusive,stime,etime')
            ->where($where)
            ->limit($limit)
            ->select();

        if ($data !== false) {
            if (count($data) > 0) {
                $conditionModel = new PromotionCondition();
                $resultModel    = new PromotionResult();
                foreach ($data as $k => $v) {
                    $pcondition  = $conditionModel->getConditionList($v['id']);
                    $presult     = $resultModel->getResultList($v['id']);
                    $expression1 = '';
                    $expression2 = '';
                    foreach ($pcondition as $kk => $vv) {
                        $expression1 .= $conditionModel->getConditionMsg($vv['code'], $vv['params']);
                    }
                    foreach ($presult as $kk => $vv) {
                        $expression2 .= $resultModel->getResultMsg($vv['code'], $vv['params']);
                    }
                    $data[$k]['expression1'] = $expression1;
                    $data[$k]['expression2'] = $expression2;
                    $data[$k]['stime']       = date('Y-m-d', $v['stime']);
                    $data[$k]['etime']       = date('Y-m-d', $v['etime']);
                }
            }
        }
        return $data;
    }

    /**
     *
     *  获取指定id 的优惠券是否可以领取
     * @param $promotion_id
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function receiveCoupon($promotion_id)
    {
        $where[]        = ['etime', '>', time()];                //判断优惠券失效时间 是否可领取
        $where[]        = ['status', 'eq', self::STATUS_OPEN];   //启用状态
        $where[]        = ['type', 'eq', self::TYPE_COUPON];     //促销 类型
        $where[]        = ['auto_receive', 'eq', self::AUTO_RECEIVE_YES];    //自动领取状态
        $where[]        = ['id', 'eq', $promotion_id];
        $info           = $this->field('id,name,status,exclusive,stime,etime,params')->where($where)->find();
        $info['params'] = json_decode($info['params'], true);
        if ($info['params']) {
            //判断最大领取数量
            if (isset($info['params']['max_nums']) && $info['params']['max_nums'] != 0) {
                $couponModel   = new Coupon();
                $receive_count = $couponModel->where([['promotion_id', '=', $promotion_id]])->count();
                if ($info['params']['max_nums'] > $receive_count) {
                    return $info;
                } else {
                    return false;
                }
            }
        }
        return $info;
    }

    /**
     * 获取团购秒杀商品列表
     * @param array $params
     * @return array
     */
    public function getGroupList($params = [])
    {
        $type           = isset($params['type']) ? $params['type'] : self::TYPE_GROUP;
        $where[]        = ['type', '=', $type];
        $where[]        = ['status', '=', self::STATUS_OPEN];
        $where[]        = ['stime', 'lt', time()];
        $where[]        = ['etime', 'gt', time()];
        $list           = $this->field('*')->where($where)->order('sort asc')->select();
        $activeGoods    = []; //活动商品列表
        $conditionModel = new PromotionCondition();
        $goodsModel     = new Goods();
        $orderItem      = new OrderItems();

        if (!$list->isEmpty()) {
            $i = 0;
            foreach ((array)$list->toArray() as $key => $value) {
                $extendParams           = json_decode($value['params'], true);
                $filter['promotion_id'] = $value['id'];
                $condition              = $conditionModel->field('*')->where($filter)->find();
                if ($condition) {
                    $params = json_decode($condition['params'], true);
                    $res    = $goodsModel->getGoodsDetial($params['goods_id'], '*', '');
                    if ($res['status']) {

                        $activeGoods[$i]             = $res['data'];
                        $activeGoods[$i]['group_id'] = $value['id'];
                        $activeGoods[$i]['status']   = $value['status'];
                        $activeGoods[$i]['time']     = time();
                        $activeGoods[$i]['stime']    = $value['stime'];
                        $activeGoods[$i]['etime']    = $value['etime'];
                        $activeGoods[$i]['lasttime'] = secondConversionArray($value['etime'] - time());
                        $i++;
                    }
                }
            }
        }

        return $activeGoods;
    }

    /**
     * 获取团购&秒杀商品详情
     * @param int $goods_id 商品id
     * @param string $token 登录信息
     * @param string $fields 查询字段
     * @return array
     */
    public function getGroupDetial($goods_id = 0, $token = '',$fields = '*')
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '关键参数丢失',
        ];
        if (!$goods_id) {
            return $result;
        }
        if (!isInGroup($goods_id, $group_id, $condition)) {
            return $result;
        }

        $goodsModel = new Goods();
        $goods_type = 'group';
        if ($condition['type'] == self::TYPE_SKILL) {
            $goods_type = 'skill';
        }
        $goods = $goodsModel->getGoodsDetial($goods_id, $fields, $token, $goods_type);

        if (!$goods['data']) {
            $result['msg'] = '商品不存在';
            return $result;
        }
        if ($goods_id['data']['marketable'] == $goodsModel::MARKETABLE_DOWN) {
            $result['msg'] = '商品已下架';
            return $result;
        }
        $extendParams = json_decode($condition['params'], true);
        //调整前台显示数量
        $orderModel = new Order();
        $order_type = $orderModel::ORDER_TYPE_GROUP;
        if ($condition['type'] == self::TYPE_SKILL) {
            $order_type = $orderModel::ORDER_TYPE_SKILL;
        }
        $check_order = $orderModel->findLimitOrder($goods['data']['product']['id'], 0, $condition, $order_type);

        if (isset($extendParams['max_goods_nums']) && $extendParams['max_goods_nums'] != 0) {
            $goods['data']['stock'] = $extendParams['max_goods_nums'];
            //活动销售件数
            $goods['data']['product']['stock']    = $extendParams['max_goods_nums'] - $check_order['data']['total_orders'];
            $goods['data']['buy_promotion_count'] = $check_order['data']['total_orders'];
        } else {
            $goods['data']['buy_promotion_count'] = $check_order['data']['total_orders'];
        }
        $goods['data']['group_id']        = $condition['id'];
        $goods['data']['group_type']      = $condition['type'];
        $goods['data']['status']          = $condition['status'];
        $goods['data']['time']            = time();
        $goods['data']['stime']           = $condition['stime'];
        $goods['data']['etime']           = $condition['etime'];
        $goods['data']['activity_status'] = self::ACTIVITY_STATUS_NO_START;

        if ($goods['data']['time'] >= $goods['data']['stime'] && $goods['data']['time'] < $goods['data']['etime']) {
            $goods['data']['activity_status'] = self::ACTIVITY_STATUS_PROGRESS;
            $goods['data']['lasttime']        = secondConversionArray($condition['etime'] - time());
        } elseif ($goods['data']['time'] > $goods['data']['etime']) {
            $goods['data']['activity_status'] = self::ACTIVITY_STATUS_END;
            $goods['data']['lasttime']        = secondConversionArray($condition['etime'] - time());
        } else {
            $goods['data']['lasttime'] = secondConversionArray($condition['stime'] - time());
        }
        return $goods;
    }


    /**
     * 获取名称
     * @param $promotion_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCouponName($promotion_id)
    {
        $where[] = ['id', 'eq', $promotion_id];
        $info    = $this->field('name')
            ->where($where)
            ->find();
        return $info['name'] ? $info['name'] : '';
    }


    /**
     * 优惠券列表
     * @param $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCouponList($field)
    {
        $return = [
            'status' => false,
            'msg'    => '失败',
            'data'   => []
        ];

        $where[]        = ['status', 'eq', self::STATUS_OPEN];
        $where[]        = ['type', 'eq', self::TYPE_COUPON];
        $return['data'] = $this->field($field)
            ->where($where)
            ->order('sort DESC')
            ->select();

        if ($return['data'] !== false) {
            $return['status'] = true;
            $return['msg']    = '成功';
        }

        return $return;
    }


    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableGroupData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableGroupWhere($post);

        $list = $this
            ->alias("pr")
            ->field("pr.*,gg.goods_id,g.name as goods_name,g.image_id as goods_image_id")
            ->join("group_goods gg", "gg.rule_id = pr.id")
            ->join("goods g", "g.id = gg.goods_id")
            ->where($tableWhere['where'])
            ->order($tableWhere['order'])
            ->paginate($limit);
        $data = $this->tableGroupFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;
        return $re;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list  array格式的collection
     * @return mixed
     */
    protected function tableGroupFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['stime']) {
                $list[$k]['stime'] = getTime($v['stime']);
            }
            if ($v['etime']) {
                $list[$k]['etime'] = getTime($v['etime']);
            }
            if(isset($v['goods_image_id']) && $v['goods_image_id']){
                $list[$k]['image_url'] = _sImage($v['goods_image_id']);

            }
            //            if($v['status']){
            //                $list[$k]['status'] = config('params.promotion.status')[$v['status']];
            //            }
            //            if($v['exclusive']){
            //                $list[$k]['exclusive'] = config('params.promotion.exclusive')[$v['exclusive']];
            //            }
        }
        return $list;
    }


    protected function tableGroupWhere($post)
    {

        $where = [];
        if (is_array($post['type'])) {
            $where[] = ['pr.type', 'in', $post['type']];
        } else {
            $where[] = ['pr.type', 'eq', $post['type']];
        }

        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['g.name', 'like', '%' . $post['name'] . '%'];
        }
        if (isset($post['pr.status']) && $post['status'] != "") {
            $where[] = ['pr.status', 'eq', $post['status']];
        }
        if (isset($post['pr.exclusive']) && $post['exclusive'] != "") {
            $where[] = ['pr.exclusive', 'eq', $post['exclusive']];
        }
        if (input('?param.date')) {
            $theDate = explode(' 到 ', input('param.date'));
            if (count($theDate) == 2) {
                $where[] = ['pr.stime', '<', strtotime($theDate[1])];
                $where[] = ['pr.etime', '>', strtotime($theDate[0])];
            }
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['pr.stime' => 'desc']; //默认最新添加靠前，排序越小越靠前
        return $result;
    }


}
