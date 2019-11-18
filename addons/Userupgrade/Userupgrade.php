<?php
namespace addons\Userupgrade;	// 注意命名空间规范

use app\common\model\BillRefund;
use app\common\model\Order;
use app\common\model\User;
use app\common\model\UserGrade;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use think\Db;

/**
 * 用户自动升级会员等级插件
 */
class Userupgrade extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'Userupgrade',	// 插件标识
        'title' => '用户自动升级等级插件',	// 插件名称
        'description' => '用户购买金额达到XX元自动升级成指定用户等级',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'sin',
        'version' => '1.0'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();
        $sql = "ALTER TABLE `" . config('database.prefix') . "user_grade` ADD `money` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单升级金额' ;";
        $db::execute($sql);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        $db = new Db();
        $sql = "ALTER TABLE `" . config('database.prefix') . "user_grade` DROP `money`;";
        $db::execute($sql);
        return true;
    }

    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }

    public function orderpayed($order_id){
        $orderModel = new Order();
        $info = $orderModel->where('order_id',$order_id)->find();
        if(!$info){
            return true;
        }
        $userModel = new User();
        $userInfo = $userModel->where('id',$info['user_id'])->find();
        if(!$userInfo){
            return true;
        }
        $payed_money = $this->orderpayedmoney($info['user_id']);
        $refund_money = $this->orderrefundmoney($info['user_id']);
        $money = $payed_money - $refund_money;

        //取所有用户等级信息
        $userGradeModel = new UserGrade();
        $list = $userGradeModel->order('id asc')->select();
        $id = 0;
        foreach($list as $v){
            if($v['money'] > 0 && $v['money']<= $money){
                $id = $v['id'];
            }
        }
        if($userInfo['grade']< $id){
            $userModel->save(['grade'=>$id],['id'=>$userInfo['id']]);
        }
        return true;
    }
    //订单支付的金额
    private function orderpayedmoney($user_id){
        $orderModel = new Order();
        $where[] = ['pay_status' ,'<>',$orderModel::ALL_PENDING_PAYMENT];
        $where[] = ['user_id', '=', $user_id];
        $payed_money = $orderModel->where($where)->sum('order_amount');
        return $payed_money;
    }
    //订单退款金额
    private function orderrefundmoney($user_id){
        $refundModel = new BillRefund();
        $where[] = ['type', '=', $refundModel::TYPE_ORDER];
        $where[] = ['user_id', '=',$user_id];
        $where[] = ['status', '<>', $refundModel::STATUS_REFUSE];
        $refund_money =  $refundModel->where($where)->sum('money');
        return $refund_money;
    }

}