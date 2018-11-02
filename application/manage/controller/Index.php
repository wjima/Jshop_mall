<?php
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillAftersales;
use app\common\model\Operation;
use app\common\model\Order;
use think\facade\Cache;
use app\common\model\WeixinAuthor;
use app\common\model\Goods;
use app\common\model\Brand;


class Index extends Manage
{
    public function index()
    {
        $orderModel = new Order();
        //未发货数量
        $unpaid_count = $orderModel->where(['status'=>1,'pay_status'=>1,'ship_status'=>1])->count();
        //待发货数量
        $unship_count = $orderModel->where(['status'=>1,'pay_status'=>2,'ship_status'=>1])->count();
        //待售后数量
        $billAfterSalesModel = new BillAftersales();
        $afterSales_count = $billAfterSalesModel->getCount();

        $this->assign('unpaid_count',$unpaid_count);
        $this->assign('unship_count',$unship_count);
        $this->assign('after_sales_count',$afterSales_count);

        $goodsModel = new Goods();
        $goodsStatics=$goodsModel->staticGoods();
        $this->assign('goods_statics',$goodsStatics);

        //是否关闭弹窗
        $closeauthor = Cache::get("closeauthor",'false');
        $this->assign('closeauthor',$closeauthor);
        //获取是否授权
        $weixinAuthorModel = new WeixinAuthor();
        $authorInfo = $weixinAuthorModel->getAuthorInfo('b2c','bind_type,id,head_img');
        if($authorInfo){
            $this->assign('is_author','true');
        }else{
            $this->assign('is_author','false');
        }

        hook('adminindex', $this);//后台首页钩子

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
            $goodModel = new Goods();
            $request['marketable'] = $goodModel::MARKETABLE_UP;     //必须是上架的商品
            return $goodModel->tableData($request);

        }else{
            return $this->fetch('tagSelectGoods');
        }

    }

}
