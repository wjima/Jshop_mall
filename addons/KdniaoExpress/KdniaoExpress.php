<?php
namespace addons\KdniaoExpress;    // 注意命名空间规范

use addons\KdniaoExpress\lib\kdniao;
use app\common\model\BillDelivery;
use app\common\model\Order;
use app\common\model\OrderItems;
use myxland\addons\Addons;
use app\common\model\Addons as addonsModel;
use think\Db;

/**
 * 快递鸟快递查询以及即时查询接口
 * 本地请在lib/kdniao.php的get_ip方法中填写自己的外网IP
 */
class KdniaoExpress extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name'         => 'KdniaoExpress',    // 插件标识
        'title'        => '快递鸟插件',    // 插件名称
        'description'  => '快递鸟快递查询以及订单打印插件，请勿和其它打印插件一起使用。',    // 插件简介
        'status'       => 0,    // 状态
        'author'       => 'mark',
        'version'      => '0.5',
        'dialog_width' => '600px',
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $db = new Db();
        //清空物流公司编码表
        $sql = "truncate table " . config('database.prefix') . "logistics;";
        //插入常用物流公司编码
        $sql .= 'INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1000, \'顺丰速运\', \'SF\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1001, \'百世快递\', \'HTKY\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1100, \'中通快递\', \'ZTO\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1101, \'申通快递\', \'STO\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1102, \'圆通速递\', \'YTO\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1103, \'韵达速递\', \'YD\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1104, \'邮政快递包裹\', \'YZPY\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1105, \'EMS\', \'EMS\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1106, \'天天快递\', \'HHTT\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1107, \'京东快递\', \'JD\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1108, \'优速快递\', \'UC\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1109, \'德邦快递\', \'DBL\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1110, \'宅急送\', \'ZJS\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1111, \'DHL\', \'DHL\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1112, \'FEDEX联邦(国内件）\', \'FEDEX\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1113, \'FEDEX联邦(国际件）\', \'FEDEX_GJ\', 100);';//插入常用的编码

        //创建打印信息表
        $sql .= "CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "print_express` (
          `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `order_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单号',
          `shipper_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '快递公司编码',
          `logistic_code` varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '快递单号',
          `print_template` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '面单打印模板内容(html格式)',
          `ctime` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
          `utime` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
          PRIMARY KEY (`id`) USING BTREE,
          INDEX `order_id`(`order_id`) USING BTREE
        ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;";

        //电子面单表
        $sql .= "CREATE TABLE IF NOT EXISTS `" . config('database.prefix') . "face_sheet` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
          `logi_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '物流公司编码',
          `customer_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '快递公司账号',
          `customer_pwd` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '快递公司密码',
          `send_site` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '快递公司网点地址',
          `template_size` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '快递单模板',
          PRIMARY KEY (`id`) USING BTREE
        ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '电子面单账户表' ROW_FORMAT = Dynamic;";

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
        $db  = new Db();
        $sql = "truncate table " . config('database.prefix') . "logistics;";
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'print_express ;';
        $sql .= 'DROP TABLE IF EXISTS  ' . config('database.prefix') . 'face_sheet ;';
        // 还原快递100公司编码
        $sql .= 'INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1000, \'顺丰速运\', \'shunfeng\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1001, \'百世快递\', \'huitongkuaidi\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1100, \'中通快递\', \'zhongtong\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1101, \'申通快递\', \'shentong\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1102, \'圆通速递\', \'yuantong\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1103, \'韵达速递\', \'yunda\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1104, \'邮政快递包裹\', \'youzhengguonei\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1105, \'EMS\', \'ems\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1106, \'天天快递\', \'tiantian\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1107, \'京东快递\', \'jd\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1108, \'优速快递\', \'youshuwuliu\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1109, \'德邦快递\', \'debangkuaidi\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1110, \'宅急送\', \'zhaijisong\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1111, \'DHL\', \'dhl\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1112, \'FEDEX联邦(国内件）\', \'fedexcn\', 100);
INSERT INTO `' . config('database.prefix') . 'logistics`(`id`, `logi_name`, `logi_code`, `sort`) VALUES (1113, \'FEDEX联邦(国际件）\', \'fedex\', 100);';//插入常用的编码

        $list = explode(';', $sql);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]) {
                $db::execute($list[$i]);
            }
        }
        return true;
    }


    public function config($params = [])
    {
        $this->assign('config', $params);
        return $this->fetch('config');
    }

    /**
     * 实现的menu钩子方法
     * @return mixed
     */
    public function menu($params)
    {
        $addonModel = new addonsModel();
        $setting    = $addonModel->getSetting($this->info['name']);
        if(isset($setting['menu'])){
            return $setting['menu'];
        }
        return true;
    }

    /**
     * 只创建电子面单
     * @param $params
     * @return array
     */
    public function createKdApiEOrder($params)
    {
        $return            = [
            'msg'    => '关键参数错误',
            'data'   => '',
            'status' => false
        ];
        $addonModel        = new addonsModel();
        $setting           = $addonModel->getSetting($this->info['name']);
        $printExpressModel = new \addons\KdniaoExpress\model\PrintExpress();
        $print_template    = $printExpressModel->field('shipper_code,logistic_code,order_id')->where(['order_id' => $params['order_id']])->find();
        if ($print_template) {
            $return['msg']    = '已获取过';
            $return['status'] = true;
            return $return;
        }
        $ebusinessid = $setting['ebusinessid'];//电商ID
        $AppKey      = $setting['apikey'];//加密私钥，快递鸟提供
        $kdniao      = new kdniao($ebusinessid, $AppKey);

        $kdniao->setNotice($setting['is_notice']);//设置是否上门揽件

        $data = $kdniao->getKdApiEOrder($params);
        if ($data['status']) {
            //历史订单，不会传ShipperCode，使用默认选择的快递编码
            $shipperCode = isset($data['data']['order']['ShipperCode']) ? $data['data']['order']['ShipperCode'] : $params['logi_code'];
            $iData       = [
                'order_id'       => $params['order_id'],
                'shipper_code'   => $shipperCode,
                'logistic_code'  => $data['data']['order']['LogisticCode'],
                'print_template' => $data['data']['printTemplate'],
            ];
            $printExpressModel->save($iData);
            $return['msg']    = '生成成功';
            $return['status'] = true;
            return $return;
        } else {
            $return['msg']    = $data['msg'];
            $return['status'] = false;
            return $return;
        }
    }

    /**
     * 打印电子面单
     */
    public function printExpress($ids)
    {
        $addonModel  = new addonsModel();
        $setting     = $addonModel->getSetting($this->info['name']);
        $ebusinessid = $setting['ebusinessid'];//电商ID
        $AppKey      = $setting['apikey'];//加密私钥，快递鸟提供
        $is_notice   = $setting['is_notice'];
        $kdniao      = new kdniao($ebusinessid, $AppKey, $is_notice);
        $list        = explode(',', $ids);
        $printData   = [];
        foreach ($list as $key => $value) {
            if ($value) {
                $printData[] = [
                    'order_id' => $value,
                ];
                if ($setting['auto_send']) {
                    $printExpressModel = new \addons\KdniaoExpress\model\PrintExpress();
                    $print_template    = $printExpressModel->field('shipper_code,logistic_code,order_id')->where(['order_id' => $value])->find();
                    $this->autoSend($value, $print_template['shipper_code'], $print_template['logistic_code']);
                }
            }
        }

        //todo 自动发货
        if ($printData) {
            $kdniao->build_form($printData, $setting['print_name'], $setting['is_priview']);
        }
    }

    /**
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function printOrder($params)
    {
        $return = [
            'msg'    => '关键参数错误',
            'data'   => '',
            'status' => false
        ];

        $addonModel        = new addonsModel();
        $setting           = $addonModel->getSetting($this->info['name']);
        $printExpressModel = new \addons\KdniaoExpress\model\PrintExpress();
        $print_template    = $printExpressModel->field('shipper_code,logistic_code,order_id')->where(['order_id' => $params['order_id']])->find();
        $ebusinessid       = $setting['ebusinessid'];//电商ID
        $AppKey            = $setting['apikey'];//加密私钥，快递鸟提供
        $kdniao            = new kdniao($ebusinessid, $AppKey);
        $kdniao->setNotice($setting['is_notice']);

        if ($params['bt'] == 3 && !$print_template) {
            $return['msg'] = '请先获取单号';
            return $return;
        }

        if ($print_template && ($params['bt'] == '1' || $params['bt'] == '3')) {
            $printData[] = [
                'order_id' => $print_template['order_id'],
            ];
            if ($setting['auto_send']) {
                $this->autoSend($print_template['order_id'], $print_template['shipper_code'], $print_template['logistic_code']);
            }
            $kdniao->build_form($printData, $setting['print_name'], $setting['is_priview']);
            exit();
        } else {
            try {
                $data = $kdniao->getKdApiEOrder($params);
                if (isset($data['status']) && $data['status']) {
                    $iData = [
                        'order_id'       => $params['order_id'],
                        'shipper_code'   => $data['data']['order']['ShipperCode'],
                        'logistic_code'  => $data['data']['order']['LogisticCode'],
                        'print_template' => $data['data']['printTemplate'],
                    ];

                    $res = $printExpressModel->save($iData);

                    if ($res && ($params['bt'] == '1' || $params['bt'] == '3')) {
                        $printData[] = [
                            'order_id' => $params['order_id'],
                        ];
                        if ($setting['auto_send']) {
                            $this->autoSend($params['order_id'], $iData['shipper_code'], $iData['logistic_code']);
                        }
                        $kdniao->build_form($printData, $setting['print_name'], $setting['is_priview']);
                        exit();
                    }
                    $return['msg']    = '获取成功';
                    $return['status'] = true;
                    return $return;
                } else {
                    $return['msg']    = $data['msg'];
                    $return['status'] = false;
                    return $return;
                }
            } catch (Exception $e) {
                echo  $e->getMessage();
                $return['msg']    = $e->getMessage();
                $return['status'] = false;
                return $return;
            }
        }

    }

    /**
     * 获取电子面单信息
     * @param $params
     * @return array
     */
    public function getPrintExpressInfo($params)
    {
        $return            = [
            'msg'    => '暂无数据',
            'data'   => [],
            'status' => false
        ];
        $printExpressModel = new \addons\KdniaoExpress\model\PrintExpress();
        $print_template    = $printExpressModel->field('shipper_code,logistic_code,order_id')->where(['order_id' => $params['order_id']])->find();
        if (!$print_template) {
            return $return;
        }
        $return['data']   = $print_template;
        $return['status'] = true;
        $return['msg']    = '获取成功';
        return $return;
    }

    /**
     * 自动发货
     * @param string $order_id
     * @param string $logi_code
     * @param string $logi_no
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function autoSend($order_id = '', $logi_code = '', $logi_no = '')
    {
        $ship_data  = [];
        $orderModel = new Order();
        $order_info = $orderModel->where('order_id', $order_id)->field('store_id,ship_name,ship_mobile,ship_area_id,ship_address')->find();

        $orderItemsModel = new OrderItems();
        $items           = $orderItemsModel->field('product_id,id,nums')->where(['order_id' => $order_id])->select();
        foreach ($items as $key => $val) {
            $ship_data = [
                $val['product_id'] => $val['nums']
            ];
        }
        $billDeliveryModel = new BillDelivery();
        $billDeliveryModel->ship($order_id, $logi_code, $logi_no, $ship_data, $order_info['store_id'], $order_info['ship_name'], $order_info['ship_mobile'], $order_info['ship_area_id'], $order_info['ship_address'], '快递鸟自动发货');
        return true;
    }

    /**
     * 批量打印按钮
     */
    public function orderExtBtn()
    {
        echo '<button class="layui-btn layui-btn-sm" id="batch-print-order"><i class="iconfont icon-cancel"></i>批量打印快递单</button>';
    }

    /**
     * 批量打印JS
     */
    public function orderExtJs()
    {
        $str = " $('#batch-print-order').on('click', function () {
            var checkStatus = tables.checkStatus('order'), data = checkStatus.data;
            if (data.length < 1) {
                layer.msg('请先选中订单');
                return false;
            }
            var ids = '';
            $.each(data, function () {
                ids += this.order_id + ',';
            });
            ids = ids.substring(0, ids.length - 1);
            var url = '" . get_addon_url('KdniaoExpress://Order/batchPrint') . "?ids=' + ids;
            var totalPage = data.length;
            var page = 1;
            if (ids) {
                JsGet('" . url("order/print_form") . "?order_id=' + ids, function (e) {
                    window_box = layer.open({
                        type: 1,
                        title: '选择快递',
                        area: ['450px', '305px'], //宽高
                        content: e.data,
                        btnAlign: 'c',
                        btn: ['开始打印'],
                        yes: function (index, layero) {
                            layer.closeAll();
                            var data = getFormData();
                            if(data){
                                window_box = layer.open({
                                    type: 2,
                                    title: '获取电子面单',
                                    area: ['430px', '320px'], //宽高
                                    content: url+\"&\"+data
                                });
                            }
                        }
                    })});
            }
            return false;
        });";
        echo $str;
    }

    /**
     * 快递查询
     * @param array $params
     * @return array
     */
    public function logisticsQuery($params = [])
    {
        $addonModel  = new addonsModel();
        $setting     = $addonModel->getSetting($this->info['name']);
        $ebusinessid = $setting['ebusinessid'];//电商ID
        $AppKey      = $setting['apikey'];//加密私钥，快递鸟提供
        $kdniao      = new kdniao($ebusinessid, $AppKey);
        return $kdniao->getOrderTracesByJson($params['code'], $params['no']);
    }
}
