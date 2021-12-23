<?php

namespace addons\FreePackage;    // 注意命名空间规范

use addons\WelfarePro\model\WelfareproCoupon;
use addons\WelfarePro\model\WelfareproCouponLog;
use addons\WelfarePro\model\WelfareproHb;
use app\common\model\Cart;
use app\common\model\Goods;
use app\common\model\User;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use think\Db;

/**
 * 商品免单插件
 */
class FreePackage extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'FreePackage',    // 插件标识
        'title' => '免单营销插件',    // 插件名称
        'description' => '商品大于等于3件，价格最低免单',    // 插件简介
        'status' => 0,    // 状态
        'author' => 'fx',
        'version' => '1.0'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();

        $sql = file_get_contents(ADDON_PATH . 'FreePackage/SQL/install.sql');
        $sql = str_replace("`jshop_", '`' . config('database.prefix'), $sql);

        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if (trim($list[$i])) {
                $db::execute(trim($list[$i]));
            }
        }
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        $db = new Db();
        $sql = file_get_contents(ADDON_PATH . 'FreePackage/SQL/uninstall.sql');
        $sql = str_replace("`jshop_", '`' . config('database.prefix'), $sql);
        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if (trim($list[$i])) {
                $db::execute(trim($list[$i]));
            }
        }
        return true;
    }

    /**
     * 实现的menu钩子方法
     * @return mixed
     */
    public function menu($params)
    {
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);
        if (isset($setting['menu'])) {
            return $setting['menu'];
        }
        return true;
    }
    public function apiAddonsConf($obj)
    {
        $obj->data[$this->info['name']] = true;
        return true;
    }

    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }

    public function afterShip($delivery_id)
    {
        return true;
    }

    /**
     * 商品添加页面
     */
    public function addgoodfreepackage()
    {
        $tpl = $this->fetch(ADDON_PATH . 'FreePackage' . '/view/tpl/goods_add.html');
        echo $tpl;
    }

    /**
     * 添加商品后
     * @param $goods
     * @return bool
     */
    public function addgoodsafterfreepackage($goods)
    {
        $goods_id = $goods['0']['goods_id'];
        $data = input('freepackage/a', []);
        if ($data) {
            $GoodsModel = new Goods();
            $GoodsModel->where("id", $goods_id)->update($data);
        }
        return true;
    }


    /**
     * 商品编辑页面
     * @param $goods
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editgoodsfreepackage($goods)
    {
        $this->view->engine->layout(false);
        $goods_id = $goods['id'];
        $GoodsModel = new Goods();
        $is_combo = $GoodsModel->where(["id"=>$goods_id])->value("is_combo");
        $this->assign('is_combo', $is_combo);

        $tpl = $this->fetch(ADDON_PATH . 'FreePackage' . '/view/tpl/goods_edit.html');
        echo $tpl;
    }

    public function editgoodsafterfreepackage($goods)
    {
        $data = input('freepackage/a', []);
        $goods_id = $goods['goods']['id'];
        if ($data && $goods_id) {
            $GoodsModel = new Goods();
            $GoodsModel->where("id", $goods_id)->update($data);
        }

        $cartModel = new Cart();
        $cartModel->where(['product_id'=>$goods['product']['id']])->delete();
        return true;
    }

    /**
     * 订单支付后添加记录
     * @param $order
     */
    public function orderpayedafter($order){
        if(!$order){
            return true;
        }
        $freePackageModel = new \addons\FreePackage\model\FreePackage();
        $freePackageModel->setLog($order);
        return true;
    }


}
