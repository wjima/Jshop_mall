<?php

namespace app\common\model;

use think\Validate;
use think\Model;

class PintuanRule extends Common{


    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    protected $rule = [
        'goods'=>'require',
        'date' => 'require',
        'sort' => 'integer|gt:0',
        'people_number' => 'require|integer|between:2,8',
        'significant_interval' => 'require|integer|gt:0',
        'discount_amount' => 'require|float',

    ];
    protected $msg = [

        'goods' => '请选择商品',
        'date' => '起止时间必须',
        'sort.integer' => '权重请给定一个合理的数值',
        'sort.gt' => '权重数值不能小于零',
        'people_number.require'=>'开团人数必须',
        'people_number.integer' => '开团人数请给定一个合理的数值',
        'people_number.between' => '开团人数范围，2-8人之间',
        'significant_interval.require' =>'拼团有效时长必须',
        'significant_interval.integer' => '拼团有效时长请给定一个合理的数值',
        'significant_interval.gt' => '拼团有效时长不能小于零',
        'discount_amount.require' => '优惠金额必须',
        'discount_amount.float' => '优惠金额请给定一个合理的数值',
    ];

    const STATUS_ON = 1; //启用
    const STATUS_OFF = 2; //禁用


    protected function tableWhere($post)
    {

        $where = [];
        if(isset($post['status']) && $post['status'] != ""){
            $where[] = ['status', 'eq', $post['status']];
        }

        if(input('?param.date')){
            $theDate = explode(' 到 ',input('param.date'));
            if(count($theDate) == 2){
                $where[] = ['stime', '<', strtotime($theDate[1])];
                $where[] = ['etime', '>', strtotime($theDate[0])];
            }
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort Asc'];
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
            $list[$k]['stime'] = getTime($v['stime']);
            $list[$k]['etime'] = getTime($v['etime']);
            $list[$k]['ctime'] = getTime($v['ctime']);
            $list[$k]['utime'] = getTime($v['utime']);
        }
        return $list;
    }



    //添加&编辑
    public function toAdd($data){
        $result  = [
            'status' => false,
            'msg' => '',
            'data' => []
        ];

        // 校验数据
        $validate = new Validate($this->rule, $this->msg);
        if(!$validate->check($data)){
            $result['msg'] = $validate->getError();
            return $result;
        }
        $theDate = explode(' 到 ', $data['date']);

        $pGoods = new PintuanGoods();
        //判断商品是否有参加过拼团
        $where[] = ['goods_id','in',$data['goods']];
        if(isset($data['id'])){
            $where[] = ['rule_id', 'neq', $data['id']];
        }
        $re = $pGoods->where($where)->find();
        if($re){
            $goodsModel = new Goods();
            $goodsInfo = $goodsModel->getGoodsDetial($re['goods_id']);
            if($goodsInfo['status']){
                $result['msg'] = "商品：".$goodsInfo['data']['name']." 参加过拼团了";
                return $result;
            }else{
                return error_code(10000);
            }
        }

        if (count($theDate) != 2) {
            return error_code(15002);
        }
        $data['stime']  = strtotime($theDate[0]);
        $data['etime']  = strtotime($theDate[1]);

        if(isset($data['id'])){
            $this->allowField(true)->save($data,$data['id']);
        }else{
            $this->allowField(true)->save($data);
        }

        //保存商品信息

        $pGoods->saveGoods($this->id,$data['goods']);

        $result['status'] = true;
        $result['msg'] = '操作成功';

        return $result;
    }
//    //获取拼团信息
//    public function getInfo($id){
//        $result  = [
//            'status' => false,
//            'msg' => '获取失败',
//            'data' => []
//        ];
//        $rel = $this->with('goods')->where('id','eq',$id)->find();
//        if($rel){
//            $rel['date'] = date("Y-m-d h:i:s",$rel['start_time']). ' 到 ' .date("Y-m-d h:i:s",$rel['end_time']);
//            $result['status'] = true;
//            $result['msg'] = '获取成功';
//            $result['data'] = $rel;
//        }
//        return $result;
//    }


    /**
     * 接口上获取拼团所有商品
     * @param array $params
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getPintuanList($id = 0)
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];

        $pintuanGoodsModel = new PintuanGoods();
        $where[] = ['status','eq',self::STATUS_ON];
        $where[] = ['stime','lt',time()];
        $where[] = ['etime','gt',time()];
        if($id != 0){
            $where[] = ['id', 'eq', $id];
        }


        $list = $pintuanGoodsModel
            ->alias('pg')
            ->join('pintuan_rule pr','pr.id = pg.rule_id')
            ->where($where)
            ->order('sort asc')
            ->select();

        if (!$list->isEmpty()) {
            $list = $list->toArray();
        }
        $goods = [];
        foreach ($list as $k =>$v) {
            $res = $pintuanGoodsModel->getGoodsInfo($v['goods_id']);
            if ($res['status']) {
                $goods[] = $res['data'];
            }
        }
        $result['data'] = $goods;

        return $result;
    }

    /**
     * 在加入购物车的时候，判断是否有参加拼团的商品
     * @param $product_id
     * @return array
     */
    public function addCart($product_id){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $productModel = new Products();
        $info = $productModel->where(['id'=>$product_id])->find();
        if(!$info){
            return error_code(10000);
        }
        $pintuanGoodsModel = new PintuanGoods();
        $where[] = ['status','eq',self::STATUS_ON];
        //$where[] = ['stime','lt',time()];
        //$where[] = ['etime','gt',time()];
        $where[] = ['goods_id', 'eq', $info['goods_id']];


        $pinfo = $pintuanGoodsModel
            ->alias('pg')
            ->join('pintuan_rule pr','pr.id = pg.rule_id')
            ->where($where)
            ->order('sort asc')
            ->find();
        if(!$pinfo){
            return error_code(10000);
        }
        if($pinfo['stime']>time()){
            return error_code(15601);
        }
        if($pinfo['etime'] < time()){
            return error_code(15602);
        }

        $result['status'] = true;
        return $result;
    }

    /**
     * 取购物车数据的时候，更新价格
     * @param $result
     */
    public function pintuanInfo(&$list){
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $pintuanGoodsModel = new PintuanGoods();
        foreach ($list as $k => $v){
            $where = [];
            $where[] = ['status','eq',self::STATUS_ON];
            $where[] = ['goods_id', 'eq', $v['products']['goods_id']];
            $pinfo = $pintuanGoodsModel
                ->alias('pg')
                ->join('pintuan_rule pr','pr.id = pg.rule_id')
                ->where($where)
                ->find();
            if(!$pinfo){
                return error_code(15603);
            }
            if($pinfo['stime']>time()){
                return error_code(15601);
            }
            if($pinfo['etime'] < time()){
                return error_code(15602);
            }

            $list[$k]['products']['price'] -= $pinfo['discount_amount'];
            if($list[$k]['products']['price'] < 0){
                return error_code(10000);
            }
        }
        $result['status'] = true;
        return $result;
    }

    /**
     * 根据商品id获取拼团规则信息
     * @param $goods_id
     * @return array|null|\PDOStatement|string|Model
     */
    public function getPintuanInfo($goods_id){
        $where = [];
        //取得规则id
        $where[] = ['status','eq',$this::STATUS_ON];
        $where[] = ['goods_id', 'eq', $goods_id];
        $where[] = ['etime','>',time()];
        $pintuanGoodsModel = new PintuanGoods();
        $pinfo = $pintuanGoodsModel
            ->alias('pg')
            ->join('pintuan_rule pr','pr.id = pg.rule_id')
            ->join('goods g','pg.goods_id = g.id')
            ->where($where)
            ->find();
        return $pinfo;
    }

}

