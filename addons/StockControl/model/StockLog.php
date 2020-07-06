<?php
namespace addons\StockControl\model;

use app\common\model\Common;
use app\common\model\Goods;
use app\common\model\Products;

class StockLog extends Common
{

    public function product(){
        return $this->belongsTo(Products::class,'product_id','id');
    }

    public function goods(){
        return $this->belongsTo(Goods::class,'goods_id','id');
    }
}