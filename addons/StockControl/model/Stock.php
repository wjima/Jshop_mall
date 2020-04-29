<?php

namespace addons\StockControl\model;

use app\common\model\Common;
use app\common\model\Goods;
use app\common\model\Products;
use think\Db;

class Stock extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    const TYPE_IN = 1;  //入库
    const TYPE_OUT = 2; //出库
    const GET_IN = 1;  //入库
    const GET_OUT = 2; //出库
    const GET_ALL = 3;


    public function getCtimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getStockList($type = 1, $params = [])
    {
        $query = $this->getListQuery($type, $params);
        $page = isset($params['page']) ? $params['page']: 1;
        $limit = isset($params['limit']) ? $params['limit']: 10;
        $data = $query->page($page, $limit)->select()->toArray();
        $count = $query->count();
        return [
            'code' => 0,
            'msg' => '',
            'data' => $data,
            'count' => $count
        ];
    }

    public function addStock($type, $params)
    {
        $res = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];
        $product_ids = $params['product_id'];
        $nums = $params['nums'];
        $stockData = [
            'id' => $this->createCode($type),
            'memo' => mb_substr($params['memo'],0,200), //截取前200个字符
            'type' => $type
        ];
        $stockLogData = [];
        $productData = [];
        $error_msg = $this->formatData($stockData['id'], $type, $product_ids, $nums, $productData, $stockLogData);
        if(!empty($error_msg)) {
            $res['msg'] = $error_msg;
            return $res;
        }
        try{
            Db::startTrans();
            $this->create($stockData);
            if(!empty($productData)){
                $productModel = new Products();
                $productModel->saveAll($productData);
            }
            if(!empty($stockLogData)){
                $stockLogModel = new StockLog();
                $stockLogModel->isUpdate(false)->saveAll($stockLogData);
            }

            Db::commit();
            $res['status'] = true;
            $res['msg'] = '新增成功';
        }catch (\Throwable $e){
            Db::rollback();
            $res['status'] = false;
            $res['msg'] = $e->getMessage();
        }
        return $res;

    }

    /**
     * 整理库存修改记录和修改库存的数据
     * @param $stock_id
     * @param $type
     * @param $product_ids
     * @param $nums
     * @param $productData
     * @param $stockLogData
     * @return string
     * @throws \think\Exception\DbException
     */
    private function formatData($stock_id, $type, $product_ids, $nums, &$productData, &$stockLogData)
    {
        $product_ids  = array_diff($product_ids,['']);
        if(empty($product_ids)) return '至少选择一个货品哦';
        $productModel = new Products();
        foreach ($product_ids as $k => $product_id) {
            //判断此货品是否存在
            $product = $productModel->field(['id', 'stock','goods_id'])->get($product_id);
            if (!empty($product) && (int)$nums[$k] > 0) {
                if ($type == self::TYPE_IN) {
                    $stock = $product['stock'] + (int)$nums[$k];
                } else {
                    $stock = $product['stock'] - (int)$nums[$k];
                    if($stock < 0) return '第'.($k+1).'个货品最大出库数量为：'.$product['stock'];
                }
                $productData[] = [
                    'id' => $product_id,
                    'stock' => $stock
                ];
                $stockLogData[] = [
                    'stock_id'=>$stock_id,
                    'product_id'=>$product_id,
                    'goods_id'=>$product['goods_id'],
                    'nums'=>(int)$nums[$k]
                ];
            }else{
                return '请检查第'.($k+1).'个货品或数量是否正确';
            }
        }
        return '';
    }

    /**
     * 生成唯一单号
     * @param $type
     * @return string
     */
    private function createCode($type)
    {
        while (true){
            if ($type == self::TYPE_IN) {
                $str = 's_i';
            } else {
                $str = 's_o';
            }
            $str .= substr(msectime() . rand(0, 9), 1);
            if($this->where('id',$str)->where('type',$type)->count() == 0){
                return $str;
            }
        }
    }

    public function getListQuery($type, $params)
    {
        $where = [];
        switch ($type) {
            case self::GET_IN:
                $where[] = ['type', '=', self::TYPE_IN];
                break;
            case self::GET_OUT:
                $where[] = ['type', '=', self::TYPE_OUT];
                break;
            case self::GET_ALL:
                break;
        }
        if(isset($params['id']) && !empty($params['id'])){
            $where[] = ['id','=',$params['id']];
        }
        if(isset($params['date']) && !empty($params['date'])){
            $date_string = $params['date'];
            $date_array = explode(' 到 ', urldecode($date_string));
            $sdate = strtotime($date_array[0] . ' 00:00:00');
            $edate = strtotime($date_array[1] . ' 23:59:59');
            $where[] = ['ctime', ['>=', $sdate], ['<=', $edate], 'and'];
        }

        if (empty($where)) return $this;
        return $this->where($where);
    }

    public function stockInfo($id,$type){
        $info = $this->with(['log'=>['goods'=>function($query){
            $query->field(['name','id']);
        },'product'=>function($query){
            $query->field(['spes_desc','id','sn']);
        }]])->where('type',$type)->where('id',$id)->find();
        if(empty($info)) return [];
        return $info;
    }

    public function log(){
        return $this->hasMany(StockLog::class,'stock_id','id');
    }

    public function getStockCheck($params){
        $query = $this->unionTable();
        $query = $this->_where($query,$params);
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] :  10;
        $data = $query->field(['u.*','g.name','g.bn','p.sn','p.spes_desc'])->page($page, $limit)->select()->toArray();
        $count = $query->count();
        return [
            'code' => 0,
            'msg' => '',
            'data' => empty($data)?[]:$this->formatUnionData($data),
            'count' => $count
        ];
    }
    private function unionTable(){
        $sql = <<<heredoc
                    (select product_id,goods_id,nums,ctime,stock_id as relation_id,s.type type
                    from jshop_stock_log	sl
                    join jshop_stock s
                    on s.id = sl.stock_id
                    union
                    select product_id,goods_id,nums,d.ctime ctime,d.delivery_id as relation_id,3 type
                    from jshop_bill_delivery_items	di
                    join jshop_bill_delivery d
                    on d.delivery_id = di.delivery_id
                    union
                    select product_id,goods_id,nums,r.ctime ctime,r.reship_id as relation_id,4 type
                    from jshop_bill_reship_items	ri
                    join jshop_bill_reship r
                    on r.reship_id = ri.reship_id) u
heredoc;


        return Db::table($sql)
            ->leftJoin(app(Goods::class)->getTable().' g','u.goods_id=g.id')
            ->leftJoin(app(Products::class)->getTable().' p','u.product_id=p.id')
            ->whereNull('g.isdel')
            ->whereNull('p.isdel')
            ->order('u.ctime','desc');
    }
    private function _where($query,$params){
        $where = [];
        if(isset($params['type']) && !empty($params['type'])){
            $where[] = ['u.type','=',(int)$params['type']];
        }
        if(isset($params['bn']) && !empty($params['bn'])){
            $where[] = ['g.bn','=',$params['bn']];
        }
        if(isset($params['sn']) && !empty($params['sn'])){
            $where[] = ['p.sn','=',$params['sn']];
        }
        if(isset($params['date']) && !empty($params['date'])){
            $date_string = $params['date'];
            $date_array = explode(' 到 ', urldecode($date_string));
            $sdate = strtotime($date_array[0] . ' 00:00:00');
            $edate = strtotime($date_array[1] . ' 23:59:59');
            $where[] = ['u.ctime', ['>=', $sdate], ['<=', $edate], 'and'];
        }
        if(empty($params)) return $query;
        return $query->where($where);
    }

    private function formatUnionData($list){
        foreach ($list as &$val){
            $val['ctime'] = date('Y-m-d H:i:s',$val['ctime']);
        }
        return $list;
    }
}