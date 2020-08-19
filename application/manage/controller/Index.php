<?php

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\BillAftersales;
use app\common\model\Notice;
use app\common\model\Operation;
use app\common\model\Order;
use app\common\model\PintuanGoods;
use app\common\model\Promotion;
use app\common\model\User;
use app\common\model\UserGrade;
use think\Console;
use think\facade\Cache;
use app\common\model\WeixinAuthor;
use app\common\model\Goods;
use app\common\model\Brand;
use app\common\model\Products;


class Index extends Manage
{

    public function index()
    {
        $operationModel = new Operation();
        $this->assign('menu', $operationModel->manageMenu(session('manage')['id']));

        return $this->fetch('index');
    }

    public function welcome()
    {
        $orderModel = new Order();
        //未发货数量
        $unpaid_count = $orderModel->where(['status' => 1, 'pay_status' => 1, 'ship_status' => 1])->count();
        //待发货数量
        $unship_count = $orderModel->where(['status' => 1, 'pay_status' => 2, 'ship_status' => [1, 2]])->count();
        //待售后数量
        $billAfterSalesModel = new BillAftersales();
        $afterSales_count    = $billAfterSalesModel->getCount();

        $this->assign('unpaid_count', $unpaid_count);
        $this->assign('unship_count', $unship_count);
        $this->assign('after_sales_count', $afterSales_count);

        $goodsModel   = new Goods();
        $goodsStatics = $goodsModel->staticGoods();
        $this->assign('goods_statics', $goodsStatics);
        hook('adminindex', $this); //后台首页钩子
        return $this->fetch('welcome');
    }

    /**
     * 供tag标签选择品牌的时候使用
     */
    public function tagSelectBrands()
    {
        $this->view->engine->layout(false);
        if (input('param.type') != 'show') {
            $request    = input('param.');
            $brandModel = new Brand();
            return $brandModel->tableData($request);
        } else {
            $num = input('num/d', 1);
            $this->assign('allow_mul', $num > 1 ? 1 : 2);
            return $this->fetch('tagSelectBrands');
        }
    }

    /**
     * 供tag标签选择商品的时候使用
     */
    public function tagSelectGoods()
    {
        $this->view->engine->layout(false);
        if (input('param.type') != 'show') {
            $request               = input('param.');
            $goodModel             = new Goods();
            $request['marketable'] = $goodModel::MARKETABLE_UP;     //必须是上架的商品
            $request['field'] = 'id,name,bn,brief,price,mktprice,image_id,goods_cat_id,goods_type_id,brand_id,is_nomal_virtual,marketable,stock,weight,unit,spes_desc,params,comments_count,view_count,buy_count,sort,is_recommend,is_hot,label_ids';
            return $goodModel->tableData($request);
        } else {
            $num = input('num/d', 1);
            $this->assign('allow_mul', $num > 1 ? 1 : 2);
            return $this->fetch('tagSelectGoods');
        }
    }
    /**
     * 供tag标签选择货品的时候使用
     */
    public function tagSelectProducts()
    {
        $this->view->engine->layout(false);
        if (input('param.type') != 'show') {
            $request               = input('param.');
            $goodModel             = new Products();
            $request['marketable'] = $goodModel::MARKETABLE_UP;     //必须是上架的商品
            $request['field'] = '*';
            return $goodModel->tableData($request);
        } else {
            return $this->fetch('tagSelectProducts');
        }
    }

    /**
     * 清除整站全部缓存
     * 如果其它地方写了缓存的读写方法，一定要有判断是否有缓存的情况！！！
     */
    public function clearCache()
    {
        Cache::clear(); //TODO 如果开启其他缓存，记得这里要配置缓存配置信息
        Console::call('clear', ['--cache', '--dir']); //清除缓存文件
        Console::call('clear', ['--path', ROOT_PATH . '\\runtime\\temp\\']); //清除模板缓存
        //删除海报
        del_dir_and_file(ROOT_PATH . 'public' . DS . 'static' . DS . 'poster');
        $this->success('清除缓存成功', 'index/welcome');
    }

    /**
     * 供tag标签选择公告的时候用
     */
    public function tagSelectNotice()
    {
        $this->view->engine->layout(false);
        if (input('param.type') != 'show') {
            $request     = input('param.');
            $noticeModel = new Notice();
            return $noticeModel->tableData($request);
        } else {
            $num = input('num/d', 1);
            $this->assign('allow_mul', $num > 1 ? 1 : 2);

            return $this->fetch('tagSelectNotice');
        }
    }

    /**
     * 供tag标签选择团购秒杀的时候使用
     */
    public function tagSelectGroup()
    {
        $this->view->engine->layout(false);
        if (input('param.type') != 'show') {
            $request         = input('param.');
            $promotionModel  = new Promotion();
            $request['type'] = [$promotionModel::TYPE_GROUP, $promotionModel::TYPE_SKILL];
            return $promotionModel->tableGroupData($request);
        } else {
            $num = input('num/d', 1);
            $this->assign('allow_mul', $num > 1 ? 1 : 2);
            return $this->fetch('tagSelectGroup');
        }
    }

    /**
     * 供tag标签选择拼团商品的时候使用
     */
    public function tagPintuan()
    {
        $this->view->engine->layout(false);
        if (input('param.type') != 'show') {
            $request   = input('param.');
            $goodModel = new PintuanGoods();
            //$request['marketable'] = $goodModel::MARKETABLE_UP;     //必须是上架的商品
            return $goodModel->tableData([]);
        } else {
            return $this->fetch('tagPintuan');
        }
    }

    /**
     * 供tag标签选择会员的时候使用
     */
    public function tagSelectUser()
    {
        $this->view->engine->layout(false);
        if (input('param.type') != 'show') {
            $request   = input('param.');
            $userModel = new User();
            return $userModel->tableData($request);
        } else {
            $grade = input('grade', '');
            if (empty($grade)) {
                $userGrade = UserGrade::field(['id', 'name'])->select()->toArray();
                $this->assign('userGrade', $userGrade);
            }
            $this->assign('grade', $grade);
            $num = input('num/d', 1);

            $this->assign('allow_mul', $num > 1 ? 1 : 2);
            return $this->fetch('tagSelectUser');
        }
    }
}
