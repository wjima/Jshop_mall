<?php

namespace addons\DistributionCenter;

use addons\DistributionCenter\model\Distribution;
use addons\DistributionCenter\model\DistributionOrder;
use app\common\model\Balance;
use app\common\model\BillAftersales;
use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\User;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use think\Db;

/**
 * 分销插件
 */
class DistributionCenter extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'DistributionCenter',
        'title' => '分销中心',
        'description' => '三级分销高级版，包括分销设置，三级分销',
        'status' => 0,
        'author' => 'Mark',
        'version' => '1.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();
        //创建分销商表
        $sql = "CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "distribution` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分销商名称',
  `grade_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '分销等级',
  `mobile` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `weixin` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '微信号',
  `qq` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'qq号',
  `store_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '店铺名称',
  `store_logo` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '店铺logo',
  `store_banner` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '店铺banner',
  `store_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '店铺简介',
  `verify` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '审核状态，1审核通过，2待审核，3审核拒绝',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `verify_time` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分销商表' ROW_FORMAT = Compact;";
        //分销商等级升级条件
        $sql .= "CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "distribution_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '会员等级ID',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '升级条件编码',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '其它参数',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `promotion_id`(`grade_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分销商等级升级条件' ROW_FORMAT = Compact;";

        //分销商等级表
        $sql .= "CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "distribution_grade` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `grade_id` int(10) NULL DEFAULT NULL COMMENT '会员等级id',
  `is_default` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否默认会员等级，1是，2否',
  `upgrade` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否可以自动升级，1是，2否',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分销商等级设置表' ROW_FORMAT = Compact;";
        //分销商订单记录表
        $sql .= "CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "distribution_order` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户分销商id',
  `buy_user_id` int(10) NOT NULL,
  `order_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单编号',
  `amount` decimal(20, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '结算金额',
  `is_settlement` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否结算，1已结算，2未结算，3已失效',
  `level` int(10) UNSIGNED NULL DEFAULT 1 COMMENT '层级',
  `ctime` bigint(12) NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分销商订单记录表' ROW_FORMAT = Compact;";

        //分销商等级佣金
        $sql .= "CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "distribution_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '会员等级ID',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '佣金编码',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '佣金设置序列化参数',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `promotion_id`(`grade_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '等级拥挤表' ROW_FORMAT = Compact;";
        //是否成为分销商品
        $sql .= "ALTER TABLE `" . config('database.prefix') . "goods` 
ADD COLUMN `distribution_type` int(1) NULL DEFAULT 1 COMMENT '是否为分销商品 1为是2为否';";
        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]) {
                $db::execute($list[$i]);
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
        $sql = 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'distribution ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'distribution_condition ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'distribution_grade ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'distribution_order ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'distribution_result ;';
        $sql .= 'ALTER TABLE `' . config('database.prefix') . 'goods` DROP `distribution_type`;';
        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]) {
                $db::execute($list[$i]);
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
        $setting = $addonModel->getSetting($this->info['name']);
        if (isset($setting['menu'])) {
            return $setting['menu'];
        }
        return true;
    }

    public function config($params = [])
    {
        $config = $this->getConfig();
        $this->assign('config', $config);
        $this->assign('config_params', $params);
        return $this->fetch('config');
    }

    public function loginAfter($params)
    {
        //判断是否可以成为分销商
        $addonModel = new addonsModel();
        $config = $addonModel->getSetting($this->info['name']);
        if ($config['setting']['distribution_type'] == '3') {//无需审核，但是要满足提交
            $distributionModel = new Distribution();
            $info = [];
            $distributionModel->checkCondition($config, $info, $params['id']);
            if ($info['condition_status'] == true && $info['condition_progress'] == 100) {
                //添加用户
                if ($params['mobile']) {
                    $iData['mobile'] = $params['mobile'];
                }
                $iData['name'] = isset($params['nickname']) ? $params['nickname'] : $params['mobile'];
                $iData['user_id'] = $params['id'];
                $iData['verify'] = $distributionModel::VERIFY_YES;
                $iData['verify_time'] = time();//审核时间
                $distributionModel->addData($iData, $params['id']);
            }
        }
        //判断是否可以升级
        return true;
    }

    /**
     * 添加用户后触发
     * @param $params
     * @return bool
     */
    public function addUserAfter($params)
    {
        //判断是否可以成为分销商
        $addonModel = new addonsModel();
        $config = $addonModel->getSetting($this->info['name']);
        if ($config['setting']['distribution_type'] == '3') {//无需审核，但是要满足提交
            $distributionModel = new Distribution();
            $info = [];
            $distributionModel->checkCondition($config, $info, $params['id']);
            if ($info['condition_status'] == true && $info['condition_progress'] == 100) {
                //添加用户
                if ($params['mobile']) {
                    $iData['mobile'] = $params['mobile'];
                }
                $iData['name'] = isset($params['nickname']) ? $params['nickname'] : $params['mobile'];
                $iData['user_id'] = $params['id'];
                $iData['verify'] = $distributionModel::VERIFY_YES;
                $iData['verify_time'] = time();//审核时间
                $distributionModel->addData($iData, $params['id']);
            }
        }
        //判断是否可以升级
        return true;
    }

    /**
     * 订单支付后
     */
    public function orderpayed($params)
    {
        //生成订单日志
        $orderModel = new Order();
        $order = $orderModel->get($params);
        $distributionOrderModel = new DistributionOrder();
        $distributionOrderModel->addData($order);//添加订单日志
        //判断是否可以成为分销商
        $distributionModel = new Distribution();
        $check = $distributionModel->where([['user_id', '=', $order['user_id']]])->field('id')->find();
        $addonModel = new addonsModel();
        $config = $addonModel->getSetting($this->info['name']);
        if ($config['setting']['distribution_type'] == '3') {//无需审核，但是要满足提交
            $info = [];
            //判断是否分销商

            if (!$check) {
                $distributionModel->checkCondition($config, $info, $order['user_id']);
                if ($info['condition_status'] == true && $info['condition_progress'] == 100) {
                    //添加用户
                    $mobile = get_user_info($order['user_id'], 'mobile');
                    if ($mobile) {
                        $iData['mobile'] = $mobile;
                    }
                    $iData['name'] = isset($params['nickname']) ? $params['nickname'] : $mobile;
                    $iData['user_id'] = $order['user_id'];
                    $iData['verify'] = $distributionModel::VERIFY_YES;
                    $iData['verify_time'] = time();//审核时间
                    $distributionModel->addData($iData, $order['user_id']);
                }
            }
        }
        //判断是否可以升级
        if ($check) {
            $distributionModel->checkUpdate($order['user_id']);
        }

        return true;
    }

    /**
     * 售后审核通过后
     */
    public function aftersalesreview($params)
    {
        $billAftersalesModel = new BillAftersales();
        $info = $billAftersalesModel->field('aftersales_id,order_id,user_id')->where(['aftersales_id' => $params])->find();
        if ($info) {
            $distributionOrderModel = new DistributionOrder();
            $distributionOrderModel->cancleOrderByOrderId($info['order_id']);
        }
        return true;
    }

    /**
     * 订单完成时，结算该订单
     * @param $params
     * @return array
     */
    public function orderFinish($params)
    {
        $result = [
            'status' => true,
            'msg' => '结算成功',
            'data' => [],
        ];
        if (!$params) {
            return $result;
        }
        $distributionOrderModel = new DistributionOrder();
        $distributionOrderModel->finishOrder($params);
        return true;
    }

    public function apiAddonsConf($obj)
    {
        $addonModel = new addonsModel();
        $config = $addonModel->getSetting($this->info['name']);
        $data = [
            'distribution_notes' => $config['setting']['notes'],
            'distribution_agreement' => $config['setting']['agreement'],
            'distribution_store' => $config['setting']['distribution_store']
        ];
        $obj->data[$this->info['name']] = $data;
        return true;
    }

    /**
     * 商品添加页面
     */
    public function addgooddistribution()
    {
        $tpl = $this->fetch(ADDON_PATH . 'DistributionCenter' . '/view/tpl/goodsadd.html');
        echo $tpl;
    }


    /**
     * 商品编辑页面
     * @param $goods
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editgoodsdistribution($goods)
    {
        $this->view->engine->layout(false);
        $goods_id = $goods['id'];
        $GoodsModel = new Goods();
        $type = $GoodsModel->where("id", $goods_id)->value("distribution_type");
        $this->assign('distribution_type', $type);

        $tpl = $this->fetch(ADDON_PATH . 'DistributionCenter' . '/view/tpl/goodsedit.html');
        echo $tpl;
    }


    /**
     * 商品添加后
     * @param $goods
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addgoodsafterdistribution($goods)
    {
        $goods_id = $goods['0']['goods_id'];
        $params = input('distribution/a', []);
        if ($params) {
            $GoodsModel = new Goods();
            $data = [
                "distribution_type" => $params["distribution_type"]
            ];
            $GoodsModel->where("id", $goods_id)->update($data);
        }
        return true;

    }


    /**
     * 商品编辑后
     * @param $goods
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editgoodsafterdistribution($goods)
    {

        $params = input('distribution/a', []);
        $goods_id = $goods['goods']['id'];
        if ($params) {
            $GoodsModel = new Goods();
            $data = [
                "distribution_type" => $params["distribution_type"]
            ];
            $GoodsModel->where("id", $goods_id)->update($data);
        }
        return true;
    }


}