<?php
namespace addons\Distribution;

use app\common\model\Balance;
use app\common\model\Order;
use app\common\model\User;
use myxland\addons\Addons;
/**
 * 分销插件
 */
class Distribution extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'Distribution',
        'title' => '三级分销系统插件',
        'description' => '用户购买商品支付成功后，直推和直推的直推会拿一部分佣金奖励',
        'status' => 0,
        'author' => 'sin',
        'version' => '1.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
    //订单支付成功后的插件
    public function orderpayed($order_id)
    {
        $balanceModel = new Balance();
        $userModel = new User();
        $orderModel = new Order();
        $addonModel = new \app\common\model\Addons();
        $config    = $addonModel->getSetting($this->info['name']);


        $order_info = $orderModel->where(['order_id'=>$order_id])->find();
        if(!$order_info){
            return;
        }
        $info = $userModel->where(['id'=>$order_info['user_id']])->find();
        if(!$info){
            return;
        }
        //给直推返利
        if($info['pid'] == '0'){
            return;
        }
        if($info['pid'] == $info['id']){
            //直推不给自己返
            return;
        }
        $pinfo = $userModel->where(['id'=>$info['pid']])->find();
        if(!$pinfo){
            return;
        }
        $balanceModel->change($pinfo['id'], $balanceModel::TYPE_DISTRIBUTION, floatval($order_info['order_amount']) * floatval($config['level1']), $order_id);
        //去给次推返利
        if($pinfo['pid'] == '0'){
            return;
        }
        if($pinfo['pid'] == $info['id']){
            //次推不给自己返
            return;
        }
        if($pinfo['pid'] == $pinfo['id']){
            //次推不给次推返
            return;
        }
        $ppinfo = $userModel->where(['id'=>$pinfo['pid']])->find();
        if(!$ppinfo){
            return;
        }
        $balanceModel->change($ppinfo['id'], $balanceModel::TYPE_DISTRIBUTION, floatval($order_info['order_amount']) * floatval($config['level2']), $order_id);
        return;

    }

    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        $this->assign('config_params', $params);
        return $this->fetch('config');
    }

}