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

        $params = array (
      'order_id' => '15411317984321',
      'goods_amount' => 450.0,
      'order_amount' => 190.0,
      'cost_freight' => '10.00',
      'user_id' => 29,
      'ship_area_id' => 410102,
      'ship_address' => '荣成大厦',
      'ship_name' => '豆芽',
      'ship_mobile' => '18530801653',
      'logistics_id' => 10001,
      'point' => 0,
      'point_money' => 0,
      'weight' => 400.0,
      'order_pmt' => 0,
      'goods_pmt' => 270.0,
      'coupon_pmt' => 0,
      'coupon' => '[]',
      'memo' => '',
      'ip' => '0.0.0.0',
      'ship_id' => '顺丰',
      'ship_addr' => '河南省 郑州市 中原区 荣成大厦',
    );
        hook('sendwxmessage', ['params' => [
            'user_id'   => 29,
            'code'      => 'create_order',
            'params'    => $params,
        ]]);

        die();
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
