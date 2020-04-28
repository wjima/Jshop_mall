<?php

namespace addons\StockControl\model;

use app\common\model\Common;
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
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 10;
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
            'memo' => $params['memo'],
            'type' => $type
        ];
        $stockLogData = [];
        $productData = [];
        $this->formatData($stockData['id'], $type, $product_ids, $nums, $productData, $stockLogData);
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

    private function formatData($stock_id, $type, $product_ids, $nums, &$productData, &$stockLogData)
    {
        $productModel = new Products();
        foreach ($product_ids as $k => $product_id) {
            //判断此货品是否存在
            $product = $productModel->field(['id', 'stock'])->get($product_id);
            if (!empty($product) && (int)$nums[$k] > 0) {
                if ($type == self::TYPE_IN) {
                    $stock = $product['stock'] + (int)$nums[$k];
                } else {
                    $stock = $product['stock'] - (int)$nums[$k];
                }
                $productData[] = [
                    'id' => $product_id,
                    'stock' => $stock
                ];
                $stockLogData[] = [
                    'stock_id'=>$stock_id,
                    'product_id'=>$product_id,
                    'nums'=>(int)$nums[$k]
                ];
            }

        }
    }

    private function createCode($type)
    {

        if ($type == self::TYPE_IN) {
            $str = 's_i';
        } else {
            $str = 's_o';
        }
        $str .= substr(msectime() . rand(0, 9), 1);
        return $str;
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

        if (empty($where)) return $this;
        return $this->where($where);
    }
}