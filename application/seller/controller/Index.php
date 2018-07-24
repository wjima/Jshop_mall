<?php
namespace app\seller\controller;

use app\common\controller\Seller;
use app\common\model\BillAftersales;
use app\common\model\Operation;
use app\common\model\Order;
use app\common\model\SellerSetting;
use think\facade\Cache;
use app\common\model\WeixinAuthor;
use app\common\model\Goods;
use app\common\model\Brand;


class Index extends Seller
{
    public function index()
    {
        $orderModel = new Order();
        //未发货数量
        $unpaid_count = $orderModel->where(['seller_id'=>$this->sellerId,'status'=>1,'pay_status'=>1,'ship_status'=>1])->count();
        //待发货数量
        $unship_count = $orderModel->where(['seller_id'=>$this->sellerId,'status'=>1,'pay_status'=>2,'ship_status'=>1])->count();
        //待售后数量
        $billAfterSalesModel = new BillAftersales();
        $afterSales_count = $billAfterSalesModel->where('seller_id',$this->sellerId)->count();

        //hook('testhook', $params);//php中钩子

        $this->assign('unpaid_count',$unpaid_count);
        $this->assign('unship_count',$unship_count);
        $this->assign('after_sales_count',$afterSales_count);

        $goodsModel = new Goods();
        $goodsStatics=$goodsModel->staticGoods(['seller_id'=>$this->sellerId]);
        $this->assign('goods_statics',$goodsStatics);

        //是否关闭弹窗
        $closeauthor = Cache::get($this->sellerId."closeauthor",'false');
        $this->assign('closeauthor',$closeauthor);
        //获取是否授权
        $weixinAuthorModel = new WeixinAuthor();
        $authorInfo = $weixinAuthorModel->getAuthorInfo($this->sellerId,'b2c','bind_type,id,head_img');
        if($authorInfo){
            $this->assign('is_author','true');
        }else{
            $this->assign('is_author','false');
        }
        //店铺类型
        $store_type = $_SESSION['jshop']['seller']['store_type'];
        $this->assign('store_type', $store_type);

        hook('sellerindex', $this);

        return $this->fetch();
    }

    /**
     * 供tag标签选择品牌的时候使用
     */
    public function tagSelectBrands()
    {
        $this->view->engine->layout(false);
        if(input('param.type') != 'show'){
            $request = input('param.');
            $request['seller_id'] = $this->sellerId;
            $brandModel = new Brand();
            return $brandModel->tableData($request);

        }else{
            return $this->fetch('tagSelectBrands');
        }

    }
    /**
     * 供tag标签选择商品的时候使用
     */
    public function tagSelectGoods()
    {
        $this->view->engine->layout(false);
        if(input('param.type') != 'show'){
            $request = input('param.');
            $request['seller_id'] = $this->sellerId;
            $goodModel = new Goods();
            $request['marketable'] = $goodModel::MARKETABLE_UP;     //必须是上架的商品
            return $goodModel->tableData($request);

        }else{
            return $this->fetch('tagSelectGoods');
        }

    }

}
