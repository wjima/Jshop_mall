<?php

namespace addons\StockControl\model;

use app\common\model\Common;
use app\common\model\Goods;
use app\common\model\Manage;
use app\common\model\Products;
use think\Db;

class Stock extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    const TYPE_IN = 1;  //入库
    const TYPE_OUT = 2; //出库
    const TYPE_UPDATE = 3; //库存盘点
    const GET_IN = 1;  //入库
    const GET_OUT = 2; //出库
    const GET_ALL = 3;


    public function getCtimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
    public function manage()
    {
        return $this->belongsTo(Manage::class, 'manage_id', 'id')->bind(['username']);
    }

    public function stockIndex($params)
    {
        $productsModel = new Products();
        $query = $productsModel->alias('p')->leftJoin(app(Goods::class)->getTable() . ' g', 'g.id=p.goods_id');
        $query = $this->_where($query, $params, 2);
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : config('jshop.page_limit');
        $data = $query->field(['g.name goods_name', 'g.bn', 'p.sn', 'p.id', 'p.spes_desc', 'p.stock', 'p.freeze_stock', 'g.unit'])->page($page, $limit)->select()->toArray();
        $count = $query->count();
        return [
            'code' => 0,
            'msg' => '',
            'data' => $data,
            'count' => $count
        ];
    }
    public function editStock($product_id, $stock)
    {
        $res = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];
        $productsModel = new Products();
        $products = $productsModel->with(['goods' => function ($query) {
            $query->field(['id', 'name', 'bn']);
        }])->field(['stock', 'goods_id', 'sn', 'spes_desc'])->get($product_id);
        if (empty($product_id)) {
            $res['msg'] = "错误的商品";
            $res['data'] = 0;
            return $res;
        }
        $o_stock = $products['stock'];
        $nums = $stock - $products['stock'];
        if ($nums == 0) {
            $res['status'] = true;
            $res['msg'] = "库存未修改";
            return $res;
        } else if ($nums < 0) {
            $msg = "库存盘点：库存减少" . abs($nums);
        } else {
            $msg = "库存盘点：库存增加" . $nums;
        }
        $stockData = [
            'id' => $this->createCode(self::TYPE_UPDATE),
            'memo' => mb_substr($msg, 0, 200), //截取前200个字符
            'type' => self::TYPE_UPDATE,
            'manage_id' => session('manage.id')
        ];
        $stockLogData = [
            'stock_id' => $stockData['id'],
            'product_id' => $product_id,
            'goods_id' => $products['goods_id'],
            'nums' => $nums,
            'goods_name' => $products['goods']['name'],
            'sn' => $products['sn'],
            'bn' => $products['goods']['bn'],
            'spes_desc' => $products['spes_desc'],
            'remnant_stock' => $stock,    // 剩余库存
        ];
        try {
            Db::startTrans();
            //修改库存
            $products->stock = $stock;
            $products->save();
            $this->create($stockData);
            $stockLogModel = new StockLog();
            $stockLogModel->create($stockLogData);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            $res['msg'] = $e->getMessage();
            $res['data'] = $o_stock;
            return $res;
        }
        $res['status'] = true;
        $res['msg'] = "库存修改成功";
        return $res;
    }

    public function getStockList($type = 1, $params = [])
    {
        $query = $this->getListQuery($type, $params);
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $data = $query->with(['manage'])->page($page, $limit)->select()->toArray();
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
            'memo' => mb_substr($params['memo'], 0, 200), //截取前200个字符
            'type' => $type,
            'manage_id' => session('manage.id')
        ];
        $stockLogData = [];
        $productData = [];
        $error_msg = $this->formatData($stockData['id'], $type, $product_ids, $nums, $productData, $stockLogData);
        if (!empty($error_msg)) {
            $res['msg'] = $error_msg;
            return $res;
        }
        try {
            Db::startTrans();
            $this->create($stockData);
            if (!empty($productData)) {
                $productModel = new Products();
                $productModel->saveAll($productData);
            }
            if (!empty($stockLogData)) {
                $stockLogModel = new StockLog();
                $stockLogModel->isUpdate(false)->saveAll($stockLogData);
            }

            Db::commit();
            $res['status'] = true;
            $res['msg'] = '新增成功';
        } catch (\Throwable $e) {
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
        $product_ids  = array_diff($product_ids, ['']);
        if (empty($product_ids)) return '至少选择一个货品哦';
        $productModel = new Products();
        foreach ($product_ids as $k => $product_id) {
            //判断此货品是否存在
            $product = $productModel->with(['goods' => function ($query) {
                $query->field(['id', 'name', 'bn']);
            }])->field(['id', 'stock', 'goods_id', 'sn', 'spes_desc'])->get($product_id);
            if (!empty($product) && (int) $nums[$k] > 0) {
                if ($type == self::TYPE_IN) {
                    $stock = $product['stock'] + (int) $nums[$k];
                } else {
                    $stock = $product['stock'] - (int) $nums[$k];
                    if ($stock < 0) return '第' . ($k + 1) . '个货品最大出库数量为：' . $product['stock'];
                }
                $productData[] = [
                    'id' => $product_id,
                    'stock' => $stock
                ];
                $stockLogData[] = [
                    'stock_id' => $stock_id,
                    'product_id' => $product_id,
                    'goods_id' => $product['goods_id'],
                    'nums' => (int) $nums[$k],
                    'goods_name' => $product['goods']['name'],
                    'sn' => $product['sn'],
                    'bn' => $product['goods']['bn'],
                    'spes_desc' => $product['spes_desc'],
                    'remnant_stock' => $stock

                ];
            } else {
                return '请检查第' . ($k + 1) . '个货品或数量是否正确';
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
        while (true) {
            if ($type == self::TYPE_IN) {
                $str = 's_i';
            } elseif ($type == self::TYPE_OUT) {
                $str = 's_o';
            } else {
                $str = 's_u';
            }
            $str .= substr(msectime() . rand(0, 9), 1);
            if ($this->where('id', $str)->where('type', $type)->count() == 0) {
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
        if (isset($params['id']) && !empty($params['id'])) {
            $where[] = ['id', '=', $params['id']];
        }
        if (isset($params['date']) && !empty($params['date'])) {
            $date_string = $params['date'];
            $date_array = explode(' 到 ', urldecode($date_string));
            $sdate = strtotime($date_array[0] . ' 00:00:00');
            $edate = strtotime($date_array[1] . ' 23:59:59');
            $where[] = ['ctime', ['>=', $sdate], ['<=', $edate], 'and'];
        }

        if (empty($where)) return $this;
        return $this->where($where);
    }

    public function stockInfo($id, $type)
    {
        $info = $this->with(['log' => ['goods' => function ($query) {
            $query->field(['name', 'id']);
        }, 'product' => function ($query) {
            $query->field(['spes_desc', 'id', 'sn']);
        }], 'manage'])->where('type', $type)->where('id', $id)->find();
        if (empty($info)) return [];
        return $info;
    }

    public function log()
    {
        return $this->hasMany(StockLog::class, 'stock_id', 'id');
    }

    public function getStockCheck($params)
    {
        $query = $this->unionTable();
        $query = $this->_where($query, $params);
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] :  10;
        $data = $query->field(['u.*', 'g.unit'])->page($page, $limit)->select();
        $count = $query->count();
        return [
            'code' => 0,
            'msg' => '',
            'data' => $data ? $this->formatUnionData($data) : [],
            'count' => $count
        ];
    }
    /**
     * @param $query
     * @param $params
     * @param int $type 1 默认是库存记录的筛选 2 库存盘点的筛选
     * @return mixed
     */
    private function _where($query, $params, $type = 1)
    {
        $where = [];
        if ($type == 1) {
            if (isset($params['type']) && !empty($params['type'])) {
                $where[] = ['u.type', '=', (int) $params['type']];
            }
            if (isset($params['relation_id']) && !empty($params['relation_id'])) {
                $where[] = ['u.relation_id', '=', $params['relation_id']];
            }
            if (isset($params['bn']) && !empty($params['bn'])) {
                $where[] = ['u.bn', '=', $params['bn']];
            }
            if (isset($params['sn']) && !empty($params['sn'])) {
                $where[] = ['u.sn', '=', $params['sn']];
            }
            if (isset($params['date']) && !empty($params['date'])) {
                $date_string = $params['date'];
                $date_array = explode(' 到 ', urldecode($date_string));
                $sdate = strtotime($date_array[0] . ' 00:00:00');
                $edate = strtotime($date_array[1] . ' 23:59:59');
                $where[] = ['u.ctime', ['>=', $sdate], ['<=', $edate], 'and'];
            }
        } else {
            if (isset($params['bn']) && !empty($params['bn'])) {
                $where[] = ['g.bn', '=', $params['bn']];
            }
            if (isset($params['sn']) && !empty($params['sn'])) {
                $where[] = ['p.sn', '=', $params['sn']];
            }
            if (isset($params['goods_name']) && !empty($params['goods_name'])) {
                $where[] = ['g.name', 'like', '%' . $params['goods_name'] . '%'];
            }
        }

        if (empty($params)) return $query;
        return $query->where($where);
    }

    private function unionTable()
    {
        $sql = <<<heredoc
                    (select sl.goods_name,sl.bn,sl.sn,sl.spes_desc,sl.nums,s.ctime,sl.stock_id as relation_id,s.type type
                    from jshop_stock_log	sl
                    join jshop_stock s
                    on s.id = sl.stock_id
                    union
                    select di.name goods_name,di.bn,di.sn,di.addon spes_desc,di.nums,d.ctime ctime,d.delivery_id as relation_id,4 type
                    from jshop_bill_delivery_items	di
                    join jshop_bill_delivery d
                    on d.delivery_id = di.delivery_id
                    group by di.id
                    union
                    select ri.name goods_name,ri.bn,ri.sn,ri.addon spes_desc,ri.nums,r.utime ctime,r.reship_id as relation_id,5 type
                    from jshop_bill_reship_items	ri
                    join jshop_bill_reship r
                    on r.reship_id = ri.reship_id)
heredoc;
        $sql = str_replace("jshop_",  config('database.prefix'), $sql);
        return Db::table($sql)->alias('u')
            ->leftJoin(app(Goods::class)->getTable() . ' g', 'u.bn=g.bn')
            //            ->leftJoin(app(Products::class)->getTable().' p','u.product_id=p.id')
            //            ->whereNull('g.isdel')
            //            ->whereNull('p.isdel')
            ->order('u.ctime', 'desc');
    }

    private function formatUnionData($list)
    {
        $data = [];
        foreach ($list as $val) {
            $val['ctime'] = date('Y-m-d H:i:s', $val['ctime']);
            $data[] = $val;
        }
        unset($list);
        return $data;
    }
}
