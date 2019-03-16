/*
 Navicat Premium Data Transfer

 Source Server         : jshop
 Source Server Type    : MySQL
 Source Server Version : 50557
 Source Host           : 127.0.0.1:3306
 Source Schema         : clean_b2c

 Target Server Type    : MySQL
 Target Server Version : 50557
 File Encoding         : 65001

 Date: 16/03/2019 17:13:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jshop_addons
-- ----------------------------
DROP TABLE IF EXISTS `jshop_addons`;
CREATE TABLE `jshop_addons`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '配置',
  `author` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '作者',
  `version` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '版本号',
  `ctime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '安装时间',
  `utime` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '插件表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_addons
-- ----------------------------
INSERT INTO `jshop_addons` VALUES (3, 'Appletmessage', '微信小程序模板消息', '微信小程序模板消息', 1, '{\"display\":\"1\",\"template\":{\"create_order\":{\"title\":\"\\u4e0b\\u5355\\u6210\\u529f\",\"data\":[{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\"},{\"title\":\"\\u5546\\u6237\\u540d\",\"code\":\"seller_name\"},{\"title\":\"\\u8ba2\\u5355\\u7f16\\u53f7\",\"code\":\"order_id\"},{\"title\":\"\\u4e0b\\u5355\\u65f6\\u95f4\",\"code\":\"ctime\"},{\"title\":\"\\u8ba2\\u5355\\u72b6\\u6001\",\"code\":\"status\"},{\"title\":\"\\u8ba2\\u5355\\u91d1\\u989d\",\"code\":\"order_amount\"}]},\"order_payed\":{\"title\":\"\\u652f\\u4ed8\\u6210\\u529f\",\"data\":[{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\"},{\"title\":\"\\u5546\\u6237\\u540d\",\"code\":\"seller_name\"},{\"title\":\"\\u8ba2\\u5355\\u7f16\\u53f7\",\"code\":\"order_id\"},{\"title\":\"\\u4e0b\\u5355\\u65f6\\u95f4\",\"code\":\"ctime\"},{\"title\":\"\\u8ba2\\u5355\\u72b6\\u6001\",\"code\":\"status\"},{\"title\":\"\\u8ba2\\u5355\\u91d1\\u989d\",\"code\":\"order_amount\"}]},\"delivery_notice\":{\"title\":\"\\u8ba2\\u5355\\u53d1\\u8d27\",\"data\":[{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\"},{\"title\":\"\\u4f1a\\u5458\\u540d\\u79f0\",\"code\":\"user_name\"},{\"title\":\"\\u8ba2\\u5355\\u7f16\\u53f7\",\"code\":\"order_id\"},{\"title\":\"\\u4e0b\\u5355\\u65f6\\u95f4\",\"code\":\"ctime\"},{\"title\":\"\\u7269\\u6d41\\u516c\\u53f8\",\"code\":\"logi_name\"},{\"title\":\"\\u7269\\u6d41\\u5355\\u53f7\",\"code\":\"logi_no\"},{\"title\":\"\\u6536\\u8d27\\u5730\\u5740\",\"code\":\"rec_address\"},{\"title\":\"\\u5907\\u6ce8\",\"code\":\"memo\"}]},\"refund_success\":{\"title\":\"\\u8ba2\\u5355\\u9000\\u6b3e\",\"data\":[{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\"},{\"title\":\"\\u8ba2\\u5355\\u7f16\\u53f7\",\"code\":\"order_id\"},{\"title\":\"\\u9000\\u6b3e\\u91d1\\u989d\",\"code\":\"refund_money\"},{\"title\":\"\\u9000\\u6b3e\\u65f6\\u95f4\",\"code\":\"refund_time\"},{\"title\":\"\\u9000\\u6b3e\\u539f\\u56e0\",\"code\":\"refund_reason\"},{\"title\":\"\\u9000\\u6b3e\\u72b6\\u6001\",\"code\":\"refund_status\"}]}}}', 'mark', '0.1', 1538029414, 1538029414);
INSERT INTO `jshop_addons` VALUES (6, 'Aliyunsms', '阿里云短信通道', '阿里云发送短信插件，请勿和其它短信通道一起使用', 1, '{\"accessKeyId\":\"\",\"accessKeySecret\":\"\",\"aliyunPrefix\":\"\",\"template\":{\"login\":{\"title\":\"\\u7528\\u6237\\u767b\\u5f55\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u9a8c\\u8bc1\\u7801:${code}\",\"variable\":{\"code\":\"code\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u60a8\\u6b63\\u5728\\u767b\\u9646\\u8d26\\u53f7\\uff0c\\u9a8c\\u8bc1\\u7801\\u662f${code}\\uff0c\\u8bf7\\u52ff\\u544a\\u8bc9\\u4ed6\\u4eba\\u3002\"}}},\"reg\":{\"title\":\"\\u7528\\u6237\\u6ce8\\u518c\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u9a8c\\u8bc1\\u7801:${code}\",\"variable\":{\"code\":\"code\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u60a8\\u6b63\\u5728\\u6ce8\\u518c\\u8d26\\u53f7\\uff0c\\u9a8c\\u8bc1\\u7801\\u662f${code}\\uff0c\\u8bf7\\u52ff\\u544a\\u8bc9\\u4ed6\\u4eba\\u3002\"}}},\"veri\":{\"title\":\"\\u7528\\u6237\\u9a8c\\u8bc1\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u9a8c\\u8bc1\\u7801:${code}\",\"variable\":{\"code\":\"code\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u60a8\\u7684\\u9a8c\\u8bc1\\u7801\\u662f${code}\\uff0c\\u8bf7\\u52ff\\u544a\\u8bc9\\u4ed6\\u4eba\\u3002\"}}},\"create_order\":{\"title\":\"\\u4e0b\\u5355\\u6210\\u529f\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u8ba2\\u5355\\u53f7 :${order_id}\\uff0c\\u603b\\u4ef7 :${order_amount}\\uff0c\\u914d\\u9001\\u65b9\\u5f0f :${ship_id}\\u6536\\u8d27\\u4eba\\u624b\\u673a :${ship_mobile}\\uff0c\\u6536\\u8d27\\u4eba\\u5730\\u5740 :${ship_addr}\\uff0c\\u6536\\u8d27\\u4eba\\u59d3\\u540d :${ship_name}\",\"variable\":{\"order_id\":\"order_id\",\"order_amount\":\"order_amount\",\"ship_id\":\"ship_id\",\"ship_mobile\":\"ship_mobile\",\"ship_addr\":\"ship_addr\",\"ship_name\":\"ship_name\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u606d\\u559c\\u60a8\\uff0c\\u8ba2\\u5355\\u521b\\u5efa\\u6210\\u529f,\\u795d\\u60a8\\u8d2d\\u7269\\u6109\\u5feb\\u3002\"}}},\"order_payed\":{\"title\":\"\\u652f\\u4ed8\\u6210\\u529f\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u8ba2\\u5355\\u53f7 :${order_id}>,\\u4ed8\\u6b3e\\u4eba :${user_name},\\u4ed8\\u6b3e\\u65f6\\u95f4 :${pay_time},\\u4ed8\\u6b3e\\u91d1\\u989d :${money}\",\"variable\":{\"order_id\":\"order_id\",\"user_name\":\"user_name\",\"pay_time\":\"pay_time\",\"money\":\"money\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u606d\\u559c\\u60a8\\uff0c\\u8ba2\\u5355\\u652f\\u4ed8\\u6210\\u529f,\\u795d\\u60a8\\u8d2d\\u7269\\u6109\\u5feb\\u3002\"}}},\"remind_order_pay\":{\"title\":\"\\u50ac\\u4ed8\\u63d0\\u9192\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u8ba2\\u5355\\u53f7:${order_id},\\u914d\\u9001\\u65b9\\u5f0f:${ship_id},\\u7269\\u6d41\\u516c\\u53f8:${logistics_name},\\u7269\\u6d41\\u5355\\u53f7:${ship_no},\\u6536\\u8d27\\u4eba\\u59d3\\u540d:${ship_name},\\u6536\\u8d27\\u4eba\\u5730\\u5740:${ship_addr},\\u6536\\u8d27\\u4eba\\u624b\\u673a:${ship_mobile},\\u5907\\u6ce8:${memo}\",\"variable\":{\"order_id\":\"order_id\",\"ship_id\":\"ship_id\",\"logistics_name\":\"logistics_name\",\"ship_no\":\"ship_no\",\"ship_name\":\"ship_name\",\"ship_addr\":\"ship_addr\",\"ship_mobile\":\"ship_mobile\",\"memo\":\"memo\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u606d\\u559c\\u60a8\\uff0c\\u8ba2\\u5355\\u521b\\u5efa\\u6210\\u529f,\\u795d\\u60a8\\u8d2d\\u7269\\u6109\\u5feb\\u3002\"}}},\"delivery_notice\":{\"title\":\"\\u8ba2\\u5355\\u53d1\\u8d27\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u8ba2\\u5355\\u53f7:${order_id},\\u914d\\u9001\\u65b9\\u5f0f:${ship_id},\\u7269\\u6d41\\u516c\\u53f8:${logistics_name},\\u7269\\u6d41\\u5355\\u53f7:${ship_no},\\u6536\\u8d27\\u4eba\\u59d3\\u540d:${ship_name},\\u6536\\u8d27\\u4eba\\u5730\\u5740:${ship_addr},\\u6536\\u8d27\\u4eba\\u624b\\u673a:${ship_mobile},\\u5907\\u6ce8:${memo}\",\"variable\":{\"order_id\":\"order_id\",\"ship_id\":\"ship_id\",\"logistics_name\":\"logistics_name\",\"ship_no\":\"ship_no\",\"ship_name\":\"ship_name\",\"ship_addr\":\"ship_addr\",\"ship_mobile\":\"ship_mobile\",\"memo\":\"memo\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u606d\\u559c\\u60a8\\uff0c\\u8ba2\\u5355\\u521b\\u5efa\\u6210\\u529f,\\u795d\\u60a8\\u8d2d\\u7269\\u6109\\u5feb\\u3002\"}}},\"aftersales_pass\":{\"title\":\"\\u552e\\u540e\\u5ba1\\u6838\\u901a\\u8fc7\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u8ba2\\u5355\\u53f7:${order_id},\\u552e\\u540e\\u5355\\u53f7:${aftersales_id},\\u5ba1\\u6838\\u72b6\\u6001:${aftersales_status},\\u5ba1\\u6838\\u5907\\u6ce8${mark}\",\"variable\":{\"order_id\":\"order_id\",\"aftersales_id\":\"aftersales_id\",\"status\":\"aftersales_status\",\"mark\":\"mark\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u606d\\u559c\\u60a8\\uff0c\\u8ba2\\u5355\\u521b\\u5efa\\u6210\\u529f,\\u795d\\u60a8\\u8d2d\\u7269\\u6109\\u5feb\\u3002\"}}},\"refund_success\":{\"title\":\"\\u9000\\u6b3e\\u6210\\u529f\",\"data\":{\"title\":{\"title\":\"\\u6a21\\u677fID\",\"code\":\"template_id\",\"value\":\"\"},\"template\":{\"title\":\"\\u6a21\\u677f\\u53d8\\u91cf\",\"content\":\"\\u8ba2\\u5355\\u53f7 :${order_id}\\uff0c\\u9000\\u6b3e\\u7f16\\u53f7 :${refund_id},\\u9000\\u6b3e\\u91d1\\u989d :${refund}\",\"variable\":{\"order_id\":\"source_id\",\"refund_id\":\"refund_id\",\"refund\":\"money\"}},\"content\":{\"title\":\"\\u6a21\\u677f\\u5185\\u5bb9\",\"value\":\"\\u606d\\u559c\\u60a8\\uff0c\\u8ba2\\u5355\\u521b\\u5efa\\u6210\\u529f,\\u795d\\u60a8\\u8d2d\\u7269\\u6109\\u5feb\\u3002\"}}}}}', 'mark', '0.1', 1541147133, 1541147133);
INSERT INTO `jshop_addons` VALUES (7, 'Trustlogin', 'H5信任登录插件', '信任登录插件，目前只有微信快捷登录，后续增加其他快捷登录', 1, '{\"weixin\":\"1\"}', 'mark', '0.1', 1541772405, 1541772405);

-- ----------------------------
-- Table structure for jshop_advert_position
-- ----------------------------
DROP TABLE IF EXISTS `jshop_advert_position`;
CREATE TABLE `jshop_advert_position`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '幻灯片名称',
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '广告位置编码',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '添加时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `state` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '1 启用  2禁用',
  `sort` tinyint(3) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '广告位置表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_advertisement
-- ----------------------------
DROP TABLE IF EXISTS `jshop_advertisement`;
CREATE TABLE `jshop_advertisement`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `position_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '广告位置id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '广告名称',
  `img` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '广告图片id',
  `val` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '链接属性值',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '从小到大 越小越靠前',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '添加时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '广告位置编码',
  `type` tinyint(3) UNSIGNED NULL DEFAULT NULL COMMENT '类型  1url  2商品  3文章',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '广告表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_area
-- ----------------------------
DROP TABLE IF EXISTS `jshop_area`;
CREATE TABLE `jshop_area`  (
  `id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地区ID',
  `parent_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '父级ID',
  `depth` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '地区深度',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '地区名称',
  `postal_code` int(7) UNSIGNED NOT NULL,
  `sort` int(10) NOT NULL DEFAULT 100 COMMENT '地区排序',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`, `parent_id`, `name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '地区表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_area
-- ----------------------------
INSERT INTO `jshop_area` VALUES (110000, 0, 1, '北京市', 100000, 100);
INSERT INTO `jshop_area` VALUES (110100, 110000, 2, '北京市', 100000, 100);
INSERT INTO `jshop_area` VALUES (110101, 110100, 3, '东城区', 0, 100);
INSERT INTO `jshop_area` VALUES (110102, 110100, 3, '西城区', 0, 100);
INSERT INTO `jshop_area` VALUES (110105, 110100, 3, '朝阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (110106, 110100, 3, '丰台区', 0, 100);
INSERT INTO `jshop_area` VALUES (110107, 110100, 3, '石景山区', 0, 100);
INSERT INTO `jshop_area` VALUES (110108, 110100, 3, '海淀区', 0, 100);
INSERT INTO `jshop_area` VALUES (110109, 110100, 3, '门头沟区', 0, 100);
INSERT INTO `jshop_area` VALUES (110111, 110100, 3, '房山区', 0, 100);
INSERT INTO `jshop_area` VALUES (110112, 110100, 3, '通州区', 0, 100);
INSERT INTO `jshop_area` VALUES (110113, 110100, 3, '顺义区', 0, 100);
INSERT INTO `jshop_area` VALUES (110114, 110100, 3, '昌平区', 0, 100);
INSERT INTO `jshop_area` VALUES (110115, 110100, 3, '大兴区', 0, 100);
INSERT INTO `jshop_area` VALUES (110116, 110100, 3, '怀柔区', 0, 100);
INSERT INTO `jshop_area` VALUES (110117, 110100, 3, '平谷区', 0, 100);
INSERT INTO `jshop_area` VALUES (110118, 110100, 3, '密云区', 0, 100);
INSERT INTO `jshop_area` VALUES (110119, 110100, 3, '延庆区', 0, 100);
INSERT INTO `jshop_area` VALUES (120000, 0, 1, '天津市', 0, 100);
INSERT INTO `jshop_area` VALUES (120100, 120000, 2, '天津市', 0, 100);
INSERT INTO `jshop_area` VALUES (120101, 120100, 3, '和平区', 0, 100);
INSERT INTO `jshop_area` VALUES (120102, 120100, 3, '河东区', 0, 100);
INSERT INTO `jshop_area` VALUES (120103, 120100, 3, '河西区', 0, 100);
INSERT INTO `jshop_area` VALUES (120104, 120100, 3, '南开区', 0, 100);
INSERT INTO `jshop_area` VALUES (120105, 120100, 3, '河北区', 0, 100);
INSERT INTO `jshop_area` VALUES (120106, 120100, 3, '红桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (120110, 120100, 3, '东丽区', 0, 100);
INSERT INTO `jshop_area` VALUES (120111, 120100, 3, '西青区', 0, 100);
INSERT INTO `jshop_area` VALUES (120112, 120100, 3, '津南区', 0, 100);
INSERT INTO `jshop_area` VALUES (120113, 120100, 3, '北辰区', 0, 100);
INSERT INTO `jshop_area` VALUES (120114, 120100, 3, '武清区', 0, 100);
INSERT INTO `jshop_area` VALUES (120115, 120100, 3, '宝坻区', 0, 100);
INSERT INTO `jshop_area` VALUES (120116, 120100, 3, '滨海新区', 0, 100);
INSERT INTO `jshop_area` VALUES (120117, 120100, 3, '宁河区', 0, 100);
INSERT INTO `jshop_area` VALUES (120118, 120100, 3, '静海区', 0, 100);
INSERT INTO `jshop_area` VALUES (120119, 120100, 3, '蓟州区', 0, 100);
INSERT INTO `jshop_area` VALUES (130000, 0, 1, '河北省', 0, 100);
INSERT INTO `jshop_area` VALUES (130100, 130000, 2, '石家庄市', 0, 100);
INSERT INTO `jshop_area` VALUES (130101, 130100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130102, 130100, 3, '长安区', 0, 100);
INSERT INTO `jshop_area` VALUES (130104, 130100, 3, '桥西区', 0, 100);
INSERT INTO `jshop_area` VALUES (130105, 130100, 3, '新华区', 0, 100);
INSERT INTO `jshop_area` VALUES (130107, 130100, 3, '井陉矿区', 0, 100);
INSERT INTO `jshop_area` VALUES (130108, 130100, 3, '裕华区', 0, 100);
INSERT INTO `jshop_area` VALUES (130109, 130100, 3, '藁城区', 0, 100);
INSERT INTO `jshop_area` VALUES (130110, 130100, 3, '鹿泉区', 0, 100);
INSERT INTO `jshop_area` VALUES (130111, 130100, 3, '栾城区', 0, 100);
INSERT INTO `jshop_area` VALUES (130121, 130100, 3, '井陉县', 0, 100);
INSERT INTO `jshop_area` VALUES (130123, 130100, 3, '正定县', 0, 100);
INSERT INTO `jshop_area` VALUES (130125, 130100, 3, '行唐县', 0, 100);
INSERT INTO `jshop_area` VALUES (130126, 130100, 3, '灵寿县', 0, 100);
INSERT INTO `jshop_area` VALUES (130127, 130100, 3, '高邑县', 0, 100);
INSERT INTO `jshop_area` VALUES (130128, 130100, 3, '深泽县', 0, 100);
INSERT INTO `jshop_area` VALUES (130129, 130100, 3, '赞皇县', 0, 100);
INSERT INTO `jshop_area` VALUES (130130, 130100, 3, '无极县', 0, 100);
INSERT INTO `jshop_area` VALUES (130131, 130100, 3, '平山县', 0, 100);
INSERT INTO `jshop_area` VALUES (130132, 130100, 3, '元氏县', 0, 100);
INSERT INTO `jshop_area` VALUES (130133, 130100, 3, '赵县', 0, 100);
INSERT INTO `jshop_area` VALUES (130183, 130100, 3, '晋州市', 0, 100);
INSERT INTO `jshop_area` VALUES (130184, 130100, 3, '新乐市', 0, 100);
INSERT INTO `jshop_area` VALUES (130200, 130000, 2, '唐山市', 0, 100);
INSERT INTO `jshop_area` VALUES (130201, 130200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130202, 130200, 3, '路南区', 0, 100);
INSERT INTO `jshop_area` VALUES (130203, 130200, 3, '路北区', 0, 100);
INSERT INTO `jshop_area` VALUES (130204, 130200, 3, '古冶区', 0, 100);
INSERT INTO `jshop_area` VALUES (130205, 130200, 3, '开平区', 0, 100);
INSERT INTO `jshop_area` VALUES (130207, 130200, 3, '丰南区', 0, 100);
INSERT INTO `jshop_area` VALUES (130208, 130200, 3, '丰润区', 0, 100);
INSERT INTO `jshop_area` VALUES (130209, 130200, 3, '曹妃甸区', 0, 100);
INSERT INTO `jshop_area` VALUES (130223, 130200, 3, '滦县', 0, 100);
INSERT INTO `jshop_area` VALUES (130224, 130200, 3, '滦南县', 0, 100);
INSERT INTO `jshop_area` VALUES (130225, 130200, 3, '乐亭县', 0, 100);
INSERT INTO `jshop_area` VALUES (130227, 130200, 3, '迁西县', 0, 100);
INSERT INTO `jshop_area` VALUES (130229, 130200, 3, '玉田县', 0, 100);
INSERT INTO `jshop_area` VALUES (130281, 130200, 3, '遵化市', 0, 100);
INSERT INTO `jshop_area` VALUES (130283, 130200, 3, '迁安市', 0, 100);
INSERT INTO `jshop_area` VALUES (130300, 130000, 2, '秦皇岛市', 0, 100);
INSERT INTO `jshop_area` VALUES (130301, 130300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130302, 130300, 3, '海港区', 0, 100);
INSERT INTO `jshop_area` VALUES (130303, 130300, 3, '山海关区', 0, 100);
INSERT INTO `jshop_area` VALUES (130304, 130300, 3, '北戴河区', 0, 100);
INSERT INTO `jshop_area` VALUES (130306, 130300, 3, '抚宁区', 0, 100);
INSERT INTO `jshop_area` VALUES (130321, 130300, 3, '青龙满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (130322, 130300, 3, '昌黎县', 0, 100);
INSERT INTO `jshop_area` VALUES (130324, 130300, 3, '卢龙县', 0, 100);
INSERT INTO `jshop_area` VALUES (130400, 130000, 2, '邯郸市', 0, 100);
INSERT INTO `jshop_area` VALUES (130401, 130400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130402, 130400, 3, '邯山区', 0, 100);
INSERT INTO `jshop_area` VALUES (130403, 130400, 3, '丛台区', 0, 100);
INSERT INTO `jshop_area` VALUES (130404, 130400, 3, '复兴区', 0, 100);
INSERT INTO `jshop_area` VALUES (130406, 130400, 3, '峰峰矿区', 0, 100);
INSERT INTO `jshop_area` VALUES (130421, 130400, 3, '邯郸县', 0, 100);
INSERT INTO `jshop_area` VALUES (130423, 130400, 3, '临漳县', 0, 100);
INSERT INTO `jshop_area` VALUES (130424, 130400, 3, '成安县', 0, 100);
INSERT INTO `jshop_area` VALUES (130425, 130400, 3, '大名县', 0, 100);
INSERT INTO `jshop_area` VALUES (130426, 130400, 3, '涉县', 0, 100);
INSERT INTO `jshop_area` VALUES (130427, 130400, 3, '磁县', 0, 100);
INSERT INTO `jshop_area` VALUES (130428, 130400, 3, '肥乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (130429, 130400, 3, '永年县', 0, 100);
INSERT INTO `jshop_area` VALUES (130430, 130400, 3, '邱县', 0, 100);
INSERT INTO `jshop_area` VALUES (130431, 130400, 3, '鸡泽县', 0, 100);
INSERT INTO `jshop_area` VALUES (130432, 130400, 3, '广平县', 0, 100);
INSERT INTO `jshop_area` VALUES (130433, 130400, 3, '馆陶县', 0, 100);
INSERT INTO `jshop_area` VALUES (130434, 130400, 3, '魏县', 0, 100);
INSERT INTO `jshop_area` VALUES (130435, 130400, 3, '曲周县', 0, 100);
INSERT INTO `jshop_area` VALUES (130481, 130400, 3, '武安市', 0, 100);
INSERT INTO `jshop_area` VALUES (130500, 130000, 2, '邢台市', 0, 100);
INSERT INTO `jshop_area` VALUES (130501, 130500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130502, 130500, 3, '桥东区', 0, 100);
INSERT INTO `jshop_area` VALUES (130503, 130500, 3, '桥西区', 0, 100);
INSERT INTO `jshop_area` VALUES (130521, 130500, 3, '邢台县', 0, 100);
INSERT INTO `jshop_area` VALUES (130522, 130500, 3, '临城县', 0, 100);
INSERT INTO `jshop_area` VALUES (130523, 130500, 3, '内丘县', 0, 100);
INSERT INTO `jshop_area` VALUES (130524, 130500, 3, '柏乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (130525, 130500, 3, '隆尧县', 0, 100);
INSERT INTO `jshop_area` VALUES (130526, 130500, 3, '任县', 0, 100);
INSERT INTO `jshop_area` VALUES (130527, 130500, 3, '南和县', 0, 100);
INSERT INTO `jshop_area` VALUES (130528, 130500, 3, '宁晋县', 0, 100);
INSERT INTO `jshop_area` VALUES (130529, 130500, 3, '巨鹿县', 0, 100);
INSERT INTO `jshop_area` VALUES (130530, 130500, 3, '新河县', 0, 100);
INSERT INTO `jshop_area` VALUES (130531, 130500, 3, '广宗县', 0, 100);
INSERT INTO `jshop_area` VALUES (130532, 130500, 3, '平乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (130533, 130500, 3, '威县', 0, 100);
INSERT INTO `jshop_area` VALUES (130534, 130500, 3, '清河县', 0, 100);
INSERT INTO `jshop_area` VALUES (130535, 130500, 3, '临西县', 0, 100);
INSERT INTO `jshop_area` VALUES (130581, 130500, 3, '南宫市', 0, 100);
INSERT INTO `jshop_area` VALUES (130582, 130500, 3, '沙河市', 0, 100);
INSERT INTO `jshop_area` VALUES (130600, 130000, 2, '保定市', 0, 100);
INSERT INTO `jshop_area` VALUES (130601, 130600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130602, 130600, 3, '竞秀区', 0, 100);
INSERT INTO `jshop_area` VALUES (130606, 130600, 3, '莲池区', 0, 100);
INSERT INTO `jshop_area` VALUES (130607, 130600, 3, '满城区', 0, 100);
INSERT INTO `jshop_area` VALUES (130608, 130600, 3, '清苑区', 0, 100);
INSERT INTO `jshop_area` VALUES (130609, 130600, 3, '徐水区', 0, 100);
INSERT INTO `jshop_area` VALUES (130623, 130600, 3, '涞水县', 0, 100);
INSERT INTO `jshop_area` VALUES (130624, 130600, 3, '阜平县', 0, 100);
INSERT INTO `jshop_area` VALUES (130626, 130600, 3, '定兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (130627, 130600, 3, '唐县', 0, 100);
INSERT INTO `jshop_area` VALUES (130628, 130600, 3, '高阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (130629, 130600, 3, '容城县', 0, 100);
INSERT INTO `jshop_area` VALUES (130630, 130600, 3, '涞源县', 0, 100);
INSERT INTO `jshop_area` VALUES (130631, 130600, 3, '望都县', 0, 100);
INSERT INTO `jshop_area` VALUES (130632, 130600, 3, '安新县', 0, 100);
INSERT INTO `jshop_area` VALUES (130633, 130600, 3, '易县', 0, 100);
INSERT INTO `jshop_area` VALUES (130634, 130600, 3, '曲阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (130635, 130600, 3, '蠡县', 0, 100);
INSERT INTO `jshop_area` VALUES (130636, 130600, 3, '顺平县', 0, 100);
INSERT INTO `jshop_area` VALUES (130637, 130600, 3, '博野县', 0, 100);
INSERT INTO `jshop_area` VALUES (130638, 130600, 3, '雄县', 0, 100);
INSERT INTO `jshop_area` VALUES (130681, 130600, 3, '涿州市', 0, 100);
INSERT INTO `jshop_area` VALUES (130683, 130600, 3, '安国市', 0, 100);
INSERT INTO `jshop_area` VALUES (130684, 130600, 3, '高碑店市', 0, 100);
INSERT INTO `jshop_area` VALUES (130700, 130000, 2, '张家口市', 0, 100);
INSERT INTO `jshop_area` VALUES (130701, 130700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130702, 130700, 3, '桥东区', 0, 100);
INSERT INTO `jshop_area` VALUES (130703, 130700, 3, '桥西区', 0, 100);
INSERT INTO `jshop_area` VALUES (130705, 130700, 3, '宣化区', 0, 100);
INSERT INTO `jshop_area` VALUES (130706, 130700, 3, '下花园区', 0, 100);
INSERT INTO `jshop_area` VALUES (130708, 130700, 3, '万全区', 0, 100);
INSERT INTO `jshop_area` VALUES (130709, 130700, 3, '崇礼区', 0, 100);
INSERT INTO `jshop_area` VALUES (130722, 130700, 3, '张北县', 0, 100);
INSERT INTO `jshop_area` VALUES (130723, 130700, 3, '康保县', 0, 100);
INSERT INTO `jshop_area` VALUES (130724, 130700, 3, '沽源县', 0, 100);
INSERT INTO `jshop_area` VALUES (130725, 130700, 3, '尚义县', 0, 100);
INSERT INTO `jshop_area` VALUES (130726, 130700, 3, '蔚县', 0, 100);
INSERT INTO `jshop_area` VALUES (130727, 130700, 3, '阳原县', 0, 100);
INSERT INTO `jshop_area` VALUES (130728, 130700, 3, '怀安县', 0, 100);
INSERT INTO `jshop_area` VALUES (130730, 130700, 3, '怀来县', 0, 100);
INSERT INTO `jshop_area` VALUES (130731, 130700, 3, '涿鹿县', 0, 100);
INSERT INTO `jshop_area` VALUES (130732, 130700, 3, '赤城县', 0, 100);
INSERT INTO `jshop_area` VALUES (130800, 130000, 2, '承德市', 0, 100);
INSERT INTO `jshop_area` VALUES (130801, 130800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130802, 130800, 3, '双桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (130803, 130800, 3, '双滦区', 0, 100);
INSERT INTO `jshop_area` VALUES (130804, 130800, 3, '鹰手营子矿区', 0, 100);
INSERT INTO `jshop_area` VALUES (130821, 130800, 3, '承德县', 0, 100);
INSERT INTO `jshop_area` VALUES (130822, 130800, 3, '兴隆县', 0, 100);
INSERT INTO `jshop_area` VALUES (130823, 130800, 3, '平泉县', 0, 100);
INSERT INTO `jshop_area` VALUES (130824, 130800, 3, '滦平县', 0, 100);
INSERT INTO `jshop_area` VALUES (130825, 130800, 3, '隆化县', 0, 100);
INSERT INTO `jshop_area` VALUES (130826, 130800, 3, '丰宁满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (130827, 130800, 3, '宽城满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (130828, 130800, 3, '围场满族蒙古族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (130900, 130000, 2, '沧州市', 0, 100);
INSERT INTO `jshop_area` VALUES (130901, 130900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (130902, 130900, 3, '新华区', 0, 100);
INSERT INTO `jshop_area` VALUES (130903, 130900, 3, '运河区', 0, 100);
INSERT INTO `jshop_area` VALUES (130921, 130900, 3, '沧县', 0, 100);
INSERT INTO `jshop_area` VALUES (130922, 130900, 3, '青县', 0, 100);
INSERT INTO `jshop_area` VALUES (130923, 130900, 3, '东光县', 0, 100);
INSERT INTO `jshop_area` VALUES (130924, 130900, 3, '海兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (130925, 130900, 3, '盐山县', 0, 100);
INSERT INTO `jshop_area` VALUES (130926, 130900, 3, '肃宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (130927, 130900, 3, '南皮县', 0, 100);
INSERT INTO `jshop_area` VALUES (130928, 130900, 3, '吴桥县', 0, 100);
INSERT INTO `jshop_area` VALUES (130929, 130900, 3, '献县', 0, 100);
INSERT INTO `jshop_area` VALUES (130930, 130900, 3, '孟村回族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (130981, 130900, 3, '泊头市', 0, 100);
INSERT INTO `jshop_area` VALUES (130982, 130900, 3, '任丘市', 0, 100);
INSERT INTO `jshop_area` VALUES (130983, 130900, 3, '黄骅市', 0, 100);
INSERT INTO `jshop_area` VALUES (130984, 130900, 3, '河间市', 0, 100);
INSERT INTO `jshop_area` VALUES (131000, 130000, 2, '廊坊市', 0, 100);
INSERT INTO `jshop_area` VALUES (131001, 131000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (131002, 131000, 3, '安次区', 0, 100);
INSERT INTO `jshop_area` VALUES (131003, 131000, 3, '广阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (131022, 131000, 3, '固安县', 0, 100);
INSERT INTO `jshop_area` VALUES (131023, 131000, 3, '永清县', 0, 100);
INSERT INTO `jshop_area` VALUES (131024, 131000, 3, '香河县', 0, 100);
INSERT INTO `jshop_area` VALUES (131025, 131000, 3, '大城县', 0, 100);
INSERT INTO `jshop_area` VALUES (131026, 131000, 3, '文安县', 0, 100);
INSERT INTO `jshop_area` VALUES (131028, 131000, 3, '大厂回族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (131081, 131000, 3, '霸州市', 0, 100);
INSERT INTO `jshop_area` VALUES (131082, 131000, 3, '三河市', 0, 100);
INSERT INTO `jshop_area` VALUES (131100, 130000, 2, '衡水市', 0, 100);
INSERT INTO `jshop_area` VALUES (131101, 131100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (131102, 131100, 3, '桃城区', 0, 100);
INSERT INTO `jshop_area` VALUES (131103, 131100, 3, '冀州区', 0, 100);
INSERT INTO `jshop_area` VALUES (131121, 131100, 3, '枣强县', 0, 100);
INSERT INTO `jshop_area` VALUES (131122, 131100, 3, '武邑县', 0, 100);
INSERT INTO `jshop_area` VALUES (131123, 131100, 3, '武强县', 0, 100);
INSERT INTO `jshop_area` VALUES (131124, 131100, 3, '饶阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (131125, 131100, 3, '安平县', 0, 100);
INSERT INTO `jshop_area` VALUES (131126, 131100, 3, '故城县', 0, 100);
INSERT INTO `jshop_area` VALUES (131127, 131100, 3, '景县', 0, 100);
INSERT INTO `jshop_area` VALUES (131128, 131100, 3, '阜城县', 0, 100);
INSERT INTO `jshop_area` VALUES (131182, 131100, 3, '深州市', 0, 100);
INSERT INTO `jshop_area` VALUES (139000, 130000, 2, '省直辖县级行政区划', 0, 100);
INSERT INTO `jshop_area` VALUES (139001, 139000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (139002, 139000, 3, '辛集市', 0, 100);
INSERT INTO `jshop_area` VALUES (140000, 0, 1, '山西省', 0, 100);
INSERT INTO `jshop_area` VALUES (140100, 140000, 2, '太原市', 0, 100);
INSERT INTO `jshop_area` VALUES (140101, 140100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140105, 140100, 3, '小店区', 0, 100);
INSERT INTO `jshop_area` VALUES (140106, 140100, 3, '迎泽区', 0, 100);
INSERT INTO `jshop_area` VALUES (140107, 140100, 3, '杏花岭区', 0, 100);
INSERT INTO `jshop_area` VALUES (140108, 140100, 3, '尖草坪区', 0, 100);
INSERT INTO `jshop_area` VALUES (140109, 140100, 3, '万柏林区', 0, 100);
INSERT INTO `jshop_area` VALUES (140110, 140100, 3, '晋源区', 0, 100);
INSERT INTO `jshop_area` VALUES (140121, 140100, 3, '清徐县', 0, 100);
INSERT INTO `jshop_area` VALUES (140122, 140100, 3, '阳曲县', 0, 100);
INSERT INTO `jshop_area` VALUES (140123, 140100, 3, '娄烦县', 0, 100);
INSERT INTO `jshop_area` VALUES (140181, 140100, 3, '古交市', 0, 100);
INSERT INTO `jshop_area` VALUES (140200, 140000, 2, '大同市', 0, 100);
INSERT INTO `jshop_area` VALUES (140201, 140200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140202, 140200, 3, '城区', 0, 100);
INSERT INTO `jshop_area` VALUES (140203, 140200, 3, '矿区', 0, 100);
INSERT INTO `jshop_area` VALUES (140211, 140200, 3, '南郊区', 0, 100);
INSERT INTO `jshop_area` VALUES (140212, 140200, 3, '新荣区', 0, 100);
INSERT INTO `jshop_area` VALUES (140221, 140200, 3, '阳高县', 0, 100);
INSERT INTO `jshop_area` VALUES (140222, 140200, 3, '天镇县', 0, 100);
INSERT INTO `jshop_area` VALUES (140223, 140200, 3, '广灵县', 0, 100);
INSERT INTO `jshop_area` VALUES (140224, 140200, 3, '灵丘县', 0, 100);
INSERT INTO `jshop_area` VALUES (140225, 140200, 3, '浑源县', 0, 100);
INSERT INTO `jshop_area` VALUES (140226, 140200, 3, '左云县', 0, 100);
INSERT INTO `jshop_area` VALUES (140227, 140200, 3, '大同县', 0, 100);
INSERT INTO `jshop_area` VALUES (140300, 140000, 2, '阳泉市', 0, 100);
INSERT INTO `jshop_area` VALUES (140301, 140300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140302, 140300, 3, '城区', 0, 100);
INSERT INTO `jshop_area` VALUES (140303, 140300, 3, '矿区', 0, 100);
INSERT INTO `jshop_area` VALUES (140311, 140300, 3, '郊区', 0, 100);
INSERT INTO `jshop_area` VALUES (140321, 140300, 3, '平定县', 0, 100);
INSERT INTO `jshop_area` VALUES (140322, 140300, 3, '盂县', 0, 100);
INSERT INTO `jshop_area` VALUES (140400, 140000, 2, '长治市', 0, 100);
INSERT INTO `jshop_area` VALUES (140401, 140400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140402, 140400, 3, '城区', 0, 100);
INSERT INTO `jshop_area` VALUES (140411, 140400, 3, '郊区', 0, 100);
INSERT INTO `jshop_area` VALUES (140421, 140400, 3, '长治县', 0, 100);
INSERT INTO `jshop_area` VALUES (140423, 140400, 3, '襄垣县', 0, 100);
INSERT INTO `jshop_area` VALUES (140424, 140400, 3, '屯留县', 0, 100);
INSERT INTO `jshop_area` VALUES (140425, 140400, 3, '平顺县', 0, 100);
INSERT INTO `jshop_area` VALUES (140426, 140400, 3, '黎城县', 0, 100);
INSERT INTO `jshop_area` VALUES (140427, 140400, 3, '壶关县', 0, 100);
INSERT INTO `jshop_area` VALUES (140428, 140400, 3, '长子县', 0, 100);
INSERT INTO `jshop_area` VALUES (140429, 140400, 3, '武乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (140430, 140400, 3, '沁县', 0, 100);
INSERT INTO `jshop_area` VALUES (140431, 140400, 3, '沁源县', 0, 100);
INSERT INTO `jshop_area` VALUES (140481, 140400, 3, '潞城市', 0, 100);
INSERT INTO `jshop_area` VALUES (140500, 140000, 2, '晋城市', 0, 100);
INSERT INTO `jshop_area` VALUES (140501, 140500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140502, 140500, 3, '城区', 0, 100);
INSERT INTO `jshop_area` VALUES (140521, 140500, 3, '沁水县', 0, 100);
INSERT INTO `jshop_area` VALUES (140522, 140500, 3, '阳城县', 0, 100);
INSERT INTO `jshop_area` VALUES (140524, 140500, 3, '陵川县', 0, 100);
INSERT INTO `jshop_area` VALUES (140525, 140500, 3, '泽州县', 0, 100);
INSERT INTO `jshop_area` VALUES (140581, 140500, 3, '高平市', 0, 100);
INSERT INTO `jshop_area` VALUES (140600, 140000, 2, '朔州市', 0, 100);
INSERT INTO `jshop_area` VALUES (140601, 140600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140602, 140600, 3, '朔城区', 0, 100);
INSERT INTO `jshop_area` VALUES (140603, 140600, 3, '平鲁区', 0, 100);
INSERT INTO `jshop_area` VALUES (140621, 140600, 3, '山阴县', 0, 100);
INSERT INTO `jshop_area` VALUES (140622, 140600, 3, '应县', 0, 100);
INSERT INTO `jshop_area` VALUES (140623, 140600, 3, '右玉县', 0, 100);
INSERT INTO `jshop_area` VALUES (140624, 140600, 3, '怀仁县', 0, 100);
INSERT INTO `jshop_area` VALUES (140700, 140000, 2, '晋中市', 0, 100);
INSERT INTO `jshop_area` VALUES (140701, 140700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140702, 140700, 3, '榆次区', 0, 100);
INSERT INTO `jshop_area` VALUES (140721, 140700, 3, '榆社县', 0, 100);
INSERT INTO `jshop_area` VALUES (140722, 140700, 3, '左权县', 0, 100);
INSERT INTO `jshop_area` VALUES (140723, 140700, 3, '和顺县', 0, 100);
INSERT INTO `jshop_area` VALUES (140724, 140700, 3, '昔阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (140725, 140700, 3, '寿阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (140726, 140700, 3, '太谷县', 0, 100);
INSERT INTO `jshop_area` VALUES (140727, 140700, 3, '祁县', 0, 100);
INSERT INTO `jshop_area` VALUES (140728, 140700, 3, '平遥县', 0, 100);
INSERT INTO `jshop_area` VALUES (140729, 140700, 3, '灵石县', 0, 100);
INSERT INTO `jshop_area` VALUES (140781, 140700, 3, '介休市', 0, 100);
INSERT INTO `jshop_area` VALUES (140800, 140000, 2, '运城市', 0, 100);
INSERT INTO `jshop_area` VALUES (140801, 140800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140802, 140800, 3, '盐湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140821, 140800, 3, '临猗县', 0, 100);
INSERT INTO `jshop_area` VALUES (140822, 140800, 3, '万荣县', 0, 100);
INSERT INTO `jshop_area` VALUES (140823, 140800, 3, '闻喜县', 0, 100);
INSERT INTO `jshop_area` VALUES (140824, 140800, 3, '稷山县', 0, 100);
INSERT INTO `jshop_area` VALUES (140825, 140800, 3, '新绛县', 0, 100);
INSERT INTO `jshop_area` VALUES (140826, 140800, 3, '绛县', 0, 100);
INSERT INTO `jshop_area` VALUES (140827, 140800, 3, '垣曲县', 0, 100);
INSERT INTO `jshop_area` VALUES (140828, 140800, 3, '夏县', 0, 100);
INSERT INTO `jshop_area` VALUES (140829, 140800, 3, '平陆县', 0, 100);
INSERT INTO `jshop_area` VALUES (140830, 140800, 3, '芮城县', 0, 100);
INSERT INTO `jshop_area` VALUES (140881, 140800, 3, '永济市', 0, 100);
INSERT INTO `jshop_area` VALUES (140882, 140800, 3, '河津市', 0, 100);
INSERT INTO `jshop_area` VALUES (140900, 140000, 2, '忻州市', 0, 100);
INSERT INTO `jshop_area` VALUES (140901, 140900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (140902, 140900, 3, '忻府区', 0, 100);
INSERT INTO `jshop_area` VALUES (140921, 140900, 3, '定襄县', 0, 100);
INSERT INTO `jshop_area` VALUES (140922, 140900, 3, '五台县', 0, 100);
INSERT INTO `jshop_area` VALUES (140923, 140900, 3, '代县', 0, 100);
INSERT INTO `jshop_area` VALUES (140924, 140900, 3, '繁峙县', 0, 100);
INSERT INTO `jshop_area` VALUES (140925, 140900, 3, '宁武县', 0, 100);
INSERT INTO `jshop_area` VALUES (140926, 140900, 3, '静乐县', 0, 100);
INSERT INTO `jshop_area` VALUES (140927, 140900, 3, '神池县', 0, 100);
INSERT INTO `jshop_area` VALUES (140928, 140900, 3, '五寨县', 0, 100);
INSERT INTO `jshop_area` VALUES (140929, 140900, 3, '岢岚县', 0, 100);
INSERT INTO `jshop_area` VALUES (140930, 140900, 3, '河曲县', 0, 100);
INSERT INTO `jshop_area` VALUES (140931, 140900, 3, '保德县', 0, 100);
INSERT INTO `jshop_area` VALUES (140932, 140900, 3, '偏关县', 0, 100);
INSERT INTO `jshop_area` VALUES (140981, 140900, 3, '原平市', 0, 100);
INSERT INTO `jshop_area` VALUES (141000, 140000, 2, '临汾市', 0, 100);
INSERT INTO `jshop_area` VALUES (141001, 141000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (141002, 141000, 3, '尧都区', 0, 100);
INSERT INTO `jshop_area` VALUES (141021, 141000, 3, '曲沃县', 0, 100);
INSERT INTO `jshop_area` VALUES (141022, 141000, 3, '翼城县', 0, 100);
INSERT INTO `jshop_area` VALUES (141023, 141000, 3, '襄汾县', 0, 100);
INSERT INTO `jshop_area` VALUES (141024, 141000, 3, '洪洞县', 0, 100);
INSERT INTO `jshop_area` VALUES (141025, 141000, 3, '古县', 0, 100);
INSERT INTO `jshop_area` VALUES (141026, 141000, 3, '安泽县', 0, 100);
INSERT INTO `jshop_area` VALUES (141027, 141000, 3, '浮山县', 0, 100);
INSERT INTO `jshop_area` VALUES (141028, 141000, 3, '吉县', 0, 100);
INSERT INTO `jshop_area` VALUES (141029, 141000, 3, '乡宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (141030, 141000, 3, '大宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (141031, 141000, 3, '隰县', 0, 100);
INSERT INTO `jshop_area` VALUES (141032, 141000, 3, '永和县', 0, 100);
INSERT INTO `jshop_area` VALUES (141033, 141000, 3, '蒲县', 0, 100);
INSERT INTO `jshop_area` VALUES (141034, 141000, 3, '汾西县', 0, 100);
INSERT INTO `jshop_area` VALUES (141081, 141000, 3, '侯马市', 0, 100);
INSERT INTO `jshop_area` VALUES (141082, 141000, 3, '霍州市', 0, 100);
INSERT INTO `jshop_area` VALUES (141100, 140000, 2, '吕梁市', 0, 100);
INSERT INTO `jshop_area` VALUES (141101, 141100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (141102, 141100, 3, '离石区', 0, 100);
INSERT INTO `jshop_area` VALUES (141121, 141100, 3, '文水县', 0, 100);
INSERT INTO `jshop_area` VALUES (141122, 141100, 3, '交城县', 0, 100);
INSERT INTO `jshop_area` VALUES (141123, 141100, 3, '兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (141124, 141100, 3, '临县', 0, 100);
INSERT INTO `jshop_area` VALUES (141125, 141100, 3, '柳林县', 0, 100);
INSERT INTO `jshop_area` VALUES (141126, 141100, 3, '石楼县', 0, 100);
INSERT INTO `jshop_area` VALUES (141127, 141100, 3, '岚县', 0, 100);
INSERT INTO `jshop_area` VALUES (141128, 141100, 3, '方山县', 0, 100);
INSERT INTO `jshop_area` VALUES (141129, 141100, 3, '中阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (141130, 141100, 3, '交口县', 0, 100);
INSERT INTO `jshop_area` VALUES (141181, 141100, 3, '孝义市', 0, 100);
INSERT INTO `jshop_area` VALUES (141182, 141100, 3, '汾阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (150000, 0, 1, '内蒙古自治区', 0, 100);
INSERT INTO `jshop_area` VALUES (150100, 150000, 2, '呼和浩特市', 0, 100);
INSERT INTO `jshop_area` VALUES (150101, 150100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150102, 150100, 3, '新城区', 0, 100);
INSERT INTO `jshop_area` VALUES (150103, 150100, 3, '回民区', 0, 100);
INSERT INTO `jshop_area` VALUES (150104, 150100, 3, '玉泉区', 0, 100);
INSERT INTO `jshop_area` VALUES (150105, 150100, 3, '赛罕区', 0, 100);
INSERT INTO `jshop_area` VALUES (150121, 150100, 3, '土默特左旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150122, 150100, 3, '托克托县', 0, 100);
INSERT INTO `jshop_area` VALUES (150123, 150100, 3, '和林格尔县', 0, 100);
INSERT INTO `jshop_area` VALUES (150124, 150100, 3, '清水河县', 0, 100);
INSERT INTO `jshop_area` VALUES (150125, 150100, 3, '武川县', 0, 100);
INSERT INTO `jshop_area` VALUES (150200, 150000, 2, '包头市', 0, 100);
INSERT INTO `jshop_area` VALUES (150201, 150200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150202, 150200, 3, '东河区', 0, 100);
INSERT INTO `jshop_area` VALUES (150203, 150200, 3, '昆都仑区', 0, 100);
INSERT INTO `jshop_area` VALUES (150204, 150200, 3, '青山区', 0, 100);
INSERT INTO `jshop_area` VALUES (150205, 150200, 3, '石拐区', 0, 100);
INSERT INTO `jshop_area` VALUES (150206, 150200, 3, '白云鄂博矿区', 0, 100);
INSERT INTO `jshop_area` VALUES (150207, 150200, 3, '九原区', 0, 100);
INSERT INTO `jshop_area` VALUES (150221, 150200, 3, '土默特右旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150222, 150200, 3, '固阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (150223, 150200, 3, '达尔罕茂明安联合旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150300, 150000, 2, '乌海市', 0, 100);
INSERT INTO `jshop_area` VALUES (150301, 150300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150302, 150300, 3, '海勃湾区', 0, 100);
INSERT INTO `jshop_area` VALUES (150303, 150300, 3, '海南区', 0, 100);
INSERT INTO `jshop_area` VALUES (150304, 150300, 3, '乌达区', 0, 100);
INSERT INTO `jshop_area` VALUES (150400, 150000, 2, '赤峰市', 0, 100);
INSERT INTO `jshop_area` VALUES (150401, 150400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150402, 150400, 3, '红山区', 0, 100);
INSERT INTO `jshop_area` VALUES (150403, 150400, 3, '元宝山区', 0, 100);
INSERT INTO `jshop_area` VALUES (150404, 150400, 3, '松山区', 0, 100);
INSERT INTO `jshop_area` VALUES (150421, 150400, 3, '阿鲁科尔沁旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150422, 150400, 3, '巴林左旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150423, 150400, 3, '巴林右旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150424, 150400, 3, '林西县', 0, 100);
INSERT INTO `jshop_area` VALUES (150425, 150400, 3, '克什克腾旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150426, 150400, 3, '翁牛特旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150428, 150400, 3, '喀喇沁旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150429, 150400, 3, '宁城县', 0, 100);
INSERT INTO `jshop_area` VALUES (150430, 150400, 3, '敖汉旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150500, 150000, 2, '通辽市', 0, 100);
INSERT INTO `jshop_area` VALUES (150501, 150500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150502, 150500, 3, '科尔沁区', 0, 100);
INSERT INTO `jshop_area` VALUES (150521, 150500, 3, '科尔沁左翼中旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150522, 150500, 3, '科尔沁左翼后旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150523, 150500, 3, '开鲁县', 0, 100);
INSERT INTO `jshop_area` VALUES (150524, 150500, 3, '库伦旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150525, 150500, 3, '奈曼旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150526, 150500, 3, '扎鲁特旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150581, 150500, 3, '霍林郭勒市', 0, 100);
INSERT INTO `jshop_area` VALUES (150600, 150000, 2, '鄂尔多斯市', 0, 100);
INSERT INTO `jshop_area` VALUES (150601, 150600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150602, 150600, 3, '东胜区', 0, 100);
INSERT INTO `jshop_area` VALUES (150603, 150600, 3, '康巴什区', 0, 100);
INSERT INTO `jshop_area` VALUES (150621, 150600, 3, '达拉特旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150622, 150600, 3, '准格尔旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150623, 150600, 3, '鄂托克前旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150624, 150600, 3, '鄂托克旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150625, 150600, 3, '杭锦旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150626, 150600, 3, '乌审旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150627, 150600, 3, '伊金霍洛旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150700, 150000, 2, '呼伦贝尔市', 0, 100);
INSERT INTO `jshop_area` VALUES (150701, 150700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150702, 150700, 3, '海拉尔区', 0, 100);
INSERT INTO `jshop_area` VALUES (150703, 150700, 3, '扎赉诺尔区', 0, 100);
INSERT INTO `jshop_area` VALUES (150721, 150700, 3, '阿荣旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150722, 150700, 3, '莫力达瓦达斡尔族自治旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150723, 150700, 3, '鄂伦春自治旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150724, 150700, 3, '鄂温克族自治旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150725, 150700, 3, '陈巴尔虎旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150726, 150700, 3, '新巴尔虎左旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150727, 150700, 3, '新巴尔虎右旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150781, 150700, 3, '满洲里市', 0, 100);
INSERT INTO `jshop_area` VALUES (150782, 150700, 3, '牙克石市', 0, 100);
INSERT INTO `jshop_area` VALUES (150783, 150700, 3, '扎兰屯市', 0, 100);
INSERT INTO `jshop_area` VALUES (150784, 150700, 3, '额尔古纳市', 0, 100);
INSERT INTO `jshop_area` VALUES (150785, 150700, 3, '根河市', 0, 100);
INSERT INTO `jshop_area` VALUES (150800, 150000, 2, '巴彦淖尔市', 0, 100);
INSERT INTO `jshop_area` VALUES (150801, 150800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150802, 150800, 3, '临河区', 0, 100);
INSERT INTO `jshop_area` VALUES (150821, 150800, 3, '五原县', 0, 100);
INSERT INTO `jshop_area` VALUES (150822, 150800, 3, '磴口县', 0, 100);
INSERT INTO `jshop_area` VALUES (150823, 150800, 3, '乌拉特前旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150824, 150800, 3, '乌拉特中旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150825, 150800, 3, '乌拉特后旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150826, 150800, 3, '杭锦后旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150900, 150000, 2, '乌兰察布市', 0, 100);
INSERT INTO `jshop_area` VALUES (150901, 150900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (150902, 150900, 3, '集宁区', 0, 100);
INSERT INTO `jshop_area` VALUES (150921, 150900, 3, '卓资县', 0, 100);
INSERT INTO `jshop_area` VALUES (150922, 150900, 3, '化德县', 0, 100);
INSERT INTO `jshop_area` VALUES (150923, 150900, 3, '商都县', 0, 100);
INSERT INTO `jshop_area` VALUES (150924, 150900, 3, '兴和县', 0, 100);
INSERT INTO `jshop_area` VALUES (150925, 150900, 3, '凉城县', 0, 100);
INSERT INTO `jshop_area` VALUES (150926, 150900, 3, '察哈尔右翼前旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150927, 150900, 3, '察哈尔右翼中旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150928, 150900, 3, '察哈尔右翼后旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150929, 150900, 3, '四子王旗', 0, 100);
INSERT INTO `jshop_area` VALUES (150981, 150900, 3, '丰镇市', 0, 100);
INSERT INTO `jshop_area` VALUES (152200, 150000, 2, '兴安盟', 0, 100);
INSERT INTO `jshop_area` VALUES (152201, 152200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (152202, 152200, 3, '阿尔山市', 0, 100);
INSERT INTO `jshop_area` VALUES (152221, 152200, 3, '科尔沁右翼前旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152222, 152200, 3, '科尔沁右翼中旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152223, 152200, 3, '扎赉特旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152224, 152200, 3, '突泉县', 0, 100);
INSERT INTO `jshop_area` VALUES (152500, 150000, 2, '锡林郭勒盟', 0, 100);
INSERT INTO `jshop_area` VALUES (152501, 152500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (152502, 152500, 3, '锡林浩特市', 0, 100);
INSERT INTO `jshop_area` VALUES (152522, 152500, 3, '阿巴嘎旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152523, 152500, 3, '苏尼特左旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152524, 152500, 3, '苏尼特右旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152525, 152500, 3, '东乌珠穆沁旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152526, 152500, 3, '西乌珠穆沁旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152527, 152500, 3, '太仆寺旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152528, 152500, 3, '镶黄旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152529, 152500, 3, '正镶白旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152530, 152500, 3, '正蓝旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152531, 152500, 3, '多伦县', 0, 100);
INSERT INTO `jshop_area` VALUES (152900, 150000, 2, '阿拉善盟', 0, 100);
INSERT INTO `jshop_area` VALUES (152921, 152900, 3, '阿拉善左旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152922, 152900, 3, '阿拉善右旗', 0, 100);
INSERT INTO `jshop_area` VALUES (152923, 152900, 3, '额济纳旗', 0, 100);
INSERT INTO `jshop_area` VALUES (210000, 0, 1, '辽宁省', 0, 100);
INSERT INTO `jshop_area` VALUES (210100, 210000, 2, '沈阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (210101, 210100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210102, 210100, 3, '和平区', 0, 100);
INSERT INTO `jshop_area` VALUES (210103, 210100, 3, '沈河区', 0, 100);
INSERT INTO `jshop_area` VALUES (210104, 210100, 3, '大东区', 0, 100);
INSERT INTO `jshop_area` VALUES (210105, 210100, 3, '皇姑区', 0, 100);
INSERT INTO `jshop_area` VALUES (210106, 210100, 3, '铁西区', 0, 100);
INSERT INTO `jshop_area` VALUES (210111, 210100, 3, '苏家屯区', 0, 100);
INSERT INTO `jshop_area` VALUES (210112, 210100, 3, '浑南区', 0, 100);
INSERT INTO `jshop_area` VALUES (210113, 210100, 3, '沈北新区', 0, 100);
INSERT INTO `jshop_area` VALUES (210114, 210100, 3, '于洪区', 0, 100);
INSERT INTO `jshop_area` VALUES (210115, 210100, 3, '辽中区', 0, 100);
INSERT INTO `jshop_area` VALUES (210123, 210100, 3, '康平县', 0, 100);
INSERT INTO `jshop_area` VALUES (210124, 210100, 3, '法库县', 0, 100);
INSERT INTO `jshop_area` VALUES (210181, 210100, 3, '新民市', 0, 100);
INSERT INTO `jshop_area` VALUES (210200, 210000, 2, '大连市', 0, 100);
INSERT INTO `jshop_area` VALUES (210201, 210200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210202, 210200, 3, '中山区', 0, 100);
INSERT INTO `jshop_area` VALUES (210203, 210200, 3, '西岗区', 0, 100);
INSERT INTO `jshop_area` VALUES (210204, 210200, 3, '沙河口区', 0, 100);
INSERT INTO `jshop_area` VALUES (210211, 210200, 3, '甘井子区', 0, 100);
INSERT INTO `jshop_area` VALUES (210212, 210200, 3, '旅顺口区', 0, 100);
INSERT INTO `jshop_area` VALUES (210213, 210200, 3, '金州区', 0, 100);
INSERT INTO `jshop_area` VALUES (210214, 210200, 3, '普兰店区', 0, 100);
INSERT INTO `jshop_area` VALUES (210224, 210200, 3, '长海县', 0, 100);
INSERT INTO `jshop_area` VALUES (210281, 210200, 3, '瓦房店市', 0, 100);
INSERT INTO `jshop_area` VALUES (210283, 210200, 3, '庄河市', 0, 100);
INSERT INTO `jshop_area` VALUES (210300, 210000, 2, '鞍山市', 0, 100);
INSERT INTO `jshop_area` VALUES (210301, 210300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210302, 210300, 3, '铁东区', 0, 100);
INSERT INTO `jshop_area` VALUES (210303, 210300, 3, '铁西区', 0, 100);
INSERT INTO `jshop_area` VALUES (210304, 210300, 3, '立山区', 0, 100);
INSERT INTO `jshop_area` VALUES (210311, 210300, 3, '千山区', 0, 100);
INSERT INTO `jshop_area` VALUES (210321, 210300, 3, '台安县', 0, 100);
INSERT INTO `jshop_area` VALUES (210323, 210300, 3, '岫岩满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (210381, 210300, 3, '海城市', 0, 100);
INSERT INTO `jshop_area` VALUES (210400, 210000, 2, '抚顺市', 0, 100);
INSERT INTO `jshop_area` VALUES (210401, 210400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210402, 210400, 3, '新抚区', 0, 100);
INSERT INTO `jshop_area` VALUES (210403, 210400, 3, '东洲区', 0, 100);
INSERT INTO `jshop_area` VALUES (210404, 210400, 3, '望花区', 0, 100);
INSERT INTO `jshop_area` VALUES (210411, 210400, 3, '顺城区', 0, 100);
INSERT INTO `jshop_area` VALUES (210421, 210400, 3, '抚顺县', 0, 100);
INSERT INTO `jshop_area` VALUES (210422, 210400, 3, '新宾满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (210423, 210400, 3, '清原满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (210500, 210000, 2, '本溪市', 0, 100);
INSERT INTO `jshop_area` VALUES (210501, 210500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210502, 210500, 3, '平山区', 0, 100);
INSERT INTO `jshop_area` VALUES (210503, 210500, 3, '溪湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210504, 210500, 3, '明山区', 0, 100);
INSERT INTO `jshop_area` VALUES (210505, 210500, 3, '南芬区', 0, 100);
INSERT INTO `jshop_area` VALUES (210521, 210500, 3, '本溪满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (210522, 210500, 3, '桓仁满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (210600, 210000, 2, '丹东市', 0, 100);
INSERT INTO `jshop_area` VALUES (210601, 210600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210602, 210600, 3, '元宝区', 0, 100);
INSERT INTO `jshop_area` VALUES (210603, 210600, 3, '振兴区', 0, 100);
INSERT INTO `jshop_area` VALUES (210604, 210600, 3, '振安区', 0, 100);
INSERT INTO `jshop_area` VALUES (210624, 210600, 3, '宽甸满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (210681, 210600, 3, '东港市', 0, 100);
INSERT INTO `jshop_area` VALUES (210682, 210600, 3, '凤城市', 0, 100);
INSERT INTO `jshop_area` VALUES (210700, 210000, 2, '锦州市', 0, 100);
INSERT INTO `jshop_area` VALUES (210701, 210700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210702, 210700, 3, '古塔区', 0, 100);
INSERT INTO `jshop_area` VALUES (210703, 210700, 3, '凌河区', 0, 100);
INSERT INTO `jshop_area` VALUES (210711, 210700, 3, '太和区', 0, 100);
INSERT INTO `jshop_area` VALUES (210726, 210700, 3, '黑山县', 0, 100);
INSERT INTO `jshop_area` VALUES (210727, 210700, 3, '义县', 0, 100);
INSERT INTO `jshop_area` VALUES (210781, 210700, 3, '凌海市', 0, 100);
INSERT INTO `jshop_area` VALUES (210782, 210700, 3, '北镇市', 0, 100);
INSERT INTO `jshop_area` VALUES (210800, 210000, 2, '营口市', 0, 100);
INSERT INTO `jshop_area` VALUES (210801, 210800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210802, 210800, 3, '站前区', 0, 100);
INSERT INTO `jshop_area` VALUES (210803, 210800, 3, '西市区', 0, 100);
INSERT INTO `jshop_area` VALUES (210804, 210800, 3, '鲅鱼圈区', 0, 100);
INSERT INTO `jshop_area` VALUES (210811, 210800, 3, '老边区', 0, 100);
INSERT INTO `jshop_area` VALUES (210881, 210800, 3, '盖州市', 0, 100);
INSERT INTO `jshop_area` VALUES (210882, 210800, 3, '大石桥市', 0, 100);
INSERT INTO `jshop_area` VALUES (210900, 210000, 2, '阜新市', 0, 100);
INSERT INTO `jshop_area` VALUES (210901, 210900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (210902, 210900, 3, '海州区', 0, 100);
INSERT INTO `jshop_area` VALUES (210903, 210900, 3, '新邱区', 0, 100);
INSERT INTO `jshop_area` VALUES (210904, 210900, 3, '太平区', 0, 100);
INSERT INTO `jshop_area` VALUES (210905, 210900, 3, '清河门区', 0, 100);
INSERT INTO `jshop_area` VALUES (210911, 210900, 3, '细河区', 0, 100);
INSERT INTO `jshop_area` VALUES (210921, 210900, 3, '阜新蒙古族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (210922, 210900, 3, '彰武县', 0, 100);
INSERT INTO `jshop_area` VALUES (211000, 210000, 2, '辽阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (211001, 211000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (211002, 211000, 3, '白塔区', 0, 100);
INSERT INTO `jshop_area` VALUES (211003, 211000, 3, '文圣区', 0, 100);
INSERT INTO `jshop_area` VALUES (211004, 211000, 3, '宏伟区', 0, 100);
INSERT INTO `jshop_area` VALUES (211005, 211000, 3, '弓长岭区', 0, 100);
INSERT INTO `jshop_area` VALUES (211011, 211000, 3, '太子河区', 0, 100);
INSERT INTO `jshop_area` VALUES (211021, 211000, 3, '辽阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (211081, 211000, 3, '灯塔市', 0, 100);
INSERT INTO `jshop_area` VALUES (211100, 210000, 2, '盘锦市', 0, 100);
INSERT INTO `jshop_area` VALUES (211101, 211100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (211102, 211100, 3, '双台子区', 0, 100);
INSERT INTO `jshop_area` VALUES (211103, 211100, 3, '兴隆台区', 0, 100);
INSERT INTO `jshop_area` VALUES (211104, 211100, 3, '大洼区', 0, 100);
INSERT INTO `jshop_area` VALUES (211122, 211100, 3, '盘山县', 0, 100);
INSERT INTO `jshop_area` VALUES (211200, 210000, 2, '铁岭市', 0, 100);
INSERT INTO `jshop_area` VALUES (211201, 211200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (211202, 211200, 3, '银州区', 0, 100);
INSERT INTO `jshop_area` VALUES (211204, 211200, 3, '清河区', 0, 100);
INSERT INTO `jshop_area` VALUES (211221, 211200, 3, '铁岭县', 0, 100);
INSERT INTO `jshop_area` VALUES (211223, 211200, 3, '西丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (211224, 211200, 3, '昌图县', 0, 100);
INSERT INTO `jshop_area` VALUES (211281, 211200, 3, '调兵山市', 0, 100);
INSERT INTO `jshop_area` VALUES (211282, 211200, 3, '开原市', 0, 100);
INSERT INTO `jshop_area` VALUES (211300, 210000, 2, '朝阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (211301, 211300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (211302, 211300, 3, '双塔区', 0, 100);
INSERT INTO `jshop_area` VALUES (211303, 211300, 3, '龙城区', 0, 100);
INSERT INTO `jshop_area` VALUES (211321, 211300, 3, '朝阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (211322, 211300, 3, '建平县', 0, 100);
INSERT INTO `jshop_area` VALUES (211324, 211300, 3, '喀喇沁左翼蒙古族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (211381, 211300, 3, '北票市', 0, 100);
INSERT INTO `jshop_area` VALUES (211382, 211300, 3, '凌源市', 0, 100);
INSERT INTO `jshop_area` VALUES (211400, 210000, 2, '葫芦岛市', 0, 100);
INSERT INTO `jshop_area` VALUES (211401, 211400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (211402, 211400, 3, '连山区', 0, 100);
INSERT INTO `jshop_area` VALUES (211403, 211400, 3, '龙港区', 0, 100);
INSERT INTO `jshop_area` VALUES (211404, 211400, 3, '南票区', 0, 100);
INSERT INTO `jshop_area` VALUES (211421, 211400, 3, '绥中县', 0, 100);
INSERT INTO `jshop_area` VALUES (211422, 211400, 3, '建昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (211481, 211400, 3, '兴城市', 0, 100);
INSERT INTO `jshop_area` VALUES (220000, 0, 1, '吉林省', 0, 100);
INSERT INTO `jshop_area` VALUES (220100, 220000, 2, '长春市', 0, 100);
INSERT INTO `jshop_area` VALUES (220101, 220100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (220102, 220100, 3, '南关区', 0, 100);
INSERT INTO `jshop_area` VALUES (220103, 220100, 3, '宽城区', 0, 100);
INSERT INTO `jshop_area` VALUES (220104, 220100, 3, '朝阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (220105, 220100, 3, '二道区', 0, 100);
INSERT INTO `jshop_area` VALUES (220106, 220100, 3, '绿园区', 0, 100);
INSERT INTO `jshop_area` VALUES (220112, 220100, 3, '双阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (220113, 220100, 3, '九台区', 0, 100);
INSERT INTO `jshop_area` VALUES (220122, 220100, 3, '农安县', 0, 100);
INSERT INTO `jshop_area` VALUES (220182, 220100, 3, '榆树市', 0, 100);
INSERT INTO `jshop_area` VALUES (220183, 220100, 3, '德惠市', 0, 100);
INSERT INTO `jshop_area` VALUES (220200, 220000, 2, '吉林市', 0, 100);
INSERT INTO `jshop_area` VALUES (220201, 220200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (220202, 220200, 3, '昌邑区', 0, 100);
INSERT INTO `jshop_area` VALUES (220203, 220200, 3, '龙潭区', 0, 100);
INSERT INTO `jshop_area` VALUES (220204, 220200, 3, '船营区', 0, 100);
INSERT INTO `jshop_area` VALUES (220211, 220200, 3, '丰满区', 0, 100);
INSERT INTO `jshop_area` VALUES (220221, 220200, 3, '永吉县', 0, 100);
INSERT INTO `jshop_area` VALUES (220281, 220200, 3, '蛟河市', 0, 100);
INSERT INTO `jshop_area` VALUES (220282, 220200, 3, '桦甸市', 0, 100);
INSERT INTO `jshop_area` VALUES (220283, 220200, 3, '舒兰市', 0, 100);
INSERT INTO `jshop_area` VALUES (220284, 220200, 3, '磐石市', 0, 100);
INSERT INTO `jshop_area` VALUES (220300, 220000, 2, '四平市', 0, 100);
INSERT INTO `jshop_area` VALUES (220301, 220300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (220302, 220300, 3, '铁西区', 0, 100);
INSERT INTO `jshop_area` VALUES (220303, 220300, 3, '铁东区', 0, 100);
INSERT INTO `jshop_area` VALUES (220322, 220300, 3, '梨树县', 0, 100);
INSERT INTO `jshop_area` VALUES (220323, 220300, 3, '伊通满族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (220381, 220300, 3, '公主岭市', 0, 100);
INSERT INTO `jshop_area` VALUES (220382, 220300, 3, '双辽市', 0, 100);
INSERT INTO `jshop_area` VALUES (220400, 220000, 2, '辽源市', 0, 100);
INSERT INTO `jshop_area` VALUES (220401, 220400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (220402, 220400, 3, '龙山区', 0, 100);
INSERT INTO `jshop_area` VALUES (220403, 220400, 3, '西安区', 0, 100);
INSERT INTO `jshop_area` VALUES (220421, 220400, 3, '东丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (220422, 220400, 3, '东辽县', 0, 100);
INSERT INTO `jshop_area` VALUES (220500, 220000, 2, '通化市', 0, 100);
INSERT INTO `jshop_area` VALUES (220501, 220500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (220502, 220500, 3, '东昌区', 0, 100);
INSERT INTO `jshop_area` VALUES (220503, 220500, 3, '二道江区', 0, 100);
INSERT INTO `jshop_area` VALUES (220521, 220500, 3, '通化县', 0, 100);
INSERT INTO `jshop_area` VALUES (220523, 220500, 3, '辉南县', 0, 100);
INSERT INTO `jshop_area` VALUES (220524, 220500, 3, '柳河县', 0, 100);
INSERT INTO `jshop_area` VALUES (220581, 220500, 3, '梅河口市', 0, 100);
INSERT INTO `jshop_area` VALUES (220582, 220500, 3, '集安市', 0, 100);
INSERT INTO `jshop_area` VALUES (220600, 220000, 2, '白山市', 0, 100);
INSERT INTO `jshop_area` VALUES (220601, 220600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (220602, 220600, 3, '浑江区', 0, 100);
INSERT INTO `jshop_area` VALUES (220605, 220600, 3, '江源区', 0, 100);
INSERT INTO `jshop_area` VALUES (220621, 220600, 3, '抚松县', 0, 100);
INSERT INTO `jshop_area` VALUES (220622, 220600, 3, '靖宇县', 0, 100);
INSERT INTO `jshop_area` VALUES (220623, 220600, 3, '长白朝鲜族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (220681, 220600, 3, '临江市', 0, 100);
INSERT INTO `jshop_area` VALUES (220700, 220000, 2, '松原市', 0, 100);
INSERT INTO `jshop_area` VALUES (220701, 220700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (220702, 220700, 3, '宁江区', 0, 100);
INSERT INTO `jshop_area` VALUES (220721, 220700, 3, '前郭尔罗斯蒙古族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (220722, 220700, 3, '长岭县', 0, 100);
INSERT INTO `jshop_area` VALUES (220723, 220700, 3, '乾安县', 0, 100);
INSERT INTO `jshop_area` VALUES (220781, 220700, 3, '扶余市', 0, 100);
INSERT INTO `jshop_area` VALUES (220800, 220000, 2, '白城市', 0, 100);
INSERT INTO `jshop_area` VALUES (220801, 220800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (220802, 220800, 3, '洮北区', 0, 100);
INSERT INTO `jshop_area` VALUES (220821, 220800, 3, '镇赉县', 0, 100);
INSERT INTO `jshop_area` VALUES (220822, 220800, 3, '通榆县', 0, 100);
INSERT INTO `jshop_area` VALUES (220881, 220800, 3, '洮南市', 0, 100);
INSERT INTO `jshop_area` VALUES (220882, 220800, 3, '大安市', 0, 100);
INSERT INTO `jshop_area` VALUES (222400, 220000, 2, '延边朝鲜族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (222401, 222400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (222402, 222400, 3, '图们市', 0, 100);
INSERT INTO `jshop_area` VALUES (222403, 222400, 3, '敦化市', 0, 100);
INSERT INTO `jshop_area` VALUES (222404, 222400, 3, '珲春市', 0, 100);
INSERT INTO `jshop_area` VALUES (222405, 222400, 3, '龙井市', 0, 100);
INSERT INTO `jshop_area` VALUES (222406, 222400, 3, '和龙市', 0, 100);
INSERT INTO `jshop_area` VALUES (222424, 222400, 3, '汪清县', 0, 100);
INSERT INTO `jshop_area` VALUES (222426, 222400, 3, '安图县', 0, 100);
INSERT INTO `jshop_area` VALUES (230000, 0, 1, '黑龙江省', 0, 100);
INSERT INTO `jshop_area` VALUES (230100, 230000, 2, '哈尔滨市', 0, 100);
INSERT INTO `jshop_area` VALUES (230101, 230100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230102, 230100, 3, '道里区', 0, 100);
INSERT INTO `jshop_area` VALUES (230103, 230100, 3, '南岗区', 0, 100);
INSERT INTO `jshop_area` VALUES (230104, 230100, 3, '道外区', 0, 100);
INSERT INTO `jshop_area` VALUES (230108, 230100, 3, '平房区', 0, 100);
INSERT INTO `jshop_area` VALUES (230109, 230100, 3, '松北区', 0, 100);
INSERT INTO `jshop_area` VALUES (230110, 230100, 3, '香坊区', 0, 100);
INSERT INTO `jshop_area` VALUES (230111, 230100, 3, '呼兰区', 0, 100);
INSERT INTO `jshop_area` VALUES (230112, 230100, 3, '阿城区', 0, 100);
INSERT INTO `jshop_area` VALUES (230113, 230100, 3, '双城区', 0, 100);
INSERT INTO `jshop_area` VALUES (230123, 230100, 3, '依兰县', 0, 100);
INSERT INTO `jshop_area` VALUES (230124, 230100, 3, '方正县', 0, 100);
INSERT INTO `jshop_area` VALUES (230125, 230100, 3, '宾县', 0, 100);
INSERT INTO `jshop_area` VALUES (230126, 230100, 3, '巴彦县', 0, 100);
INSERT INTO `jshop_area` VALUES (230127, 230100, 3, '木兰县', 0, 100);
INSERT INTO `jshop_area` VALUES (230128, 230100, 3, '通河县', 0, 100);
INSERT INTO `jshop_area` VALUES (230129, 230100, 3, '延寿县', 0, 100);
INSERT INTO `jshop_area` VALUES (230183, 230100, 3, '尚志市', 0, 100);
INSERT INTO `jshop_area` VALUES (230184, 230100, 3, '五常市', 0, 100);
INSERT INTO `jshop_area` VALUES (230200, 230000, 2, '齐齐哈尔市', 0, 100);
INSERT INTO `jshop_area` VALUES (230201, 230200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230202, 230200, 3, '龙沙区', 0, 100);
INSERT INTO `jshop_area` VALUES (230203, 230200, 3, '建华区', 0, 100);
INSERT INTO `jshop_area` VALUES (230204, 230200, 3, '铁锋区', 0, 100);
INSERT INTO `jshop_area` VALUES (230205, 230200, 3, '昂昂溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (230206, 230200, 3, '富拉尔基区', 0, 100);
INSERT INTO `jshop_area` VALUES (230207, 230200, 3, '碾子山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230208, 230200, 3, '梅里斯达斡尔族区', 0, 100);
INSERT INTO `jshop_area` VALUES (230221, 230200, 3, '龙江县', 0, 100);
INSERT INTO `jshop_area` VALUES (230223, 230200, 3, '依安县', 0, 100);
INSERT INTO `jshop_area` VALUES (230224, 230200, 3, '泰来县', 0, 100);
INSERT INTO `jshop_area` VALUES (230225, 230200, 3, '甘南县', 0, 100);
INSERT INTO `jshop_area` VALUES (230227, 230200, 3, '富裕县', 0, 100);
INSERT INTO `jshop_area` VALUES (230229, 230200, 3, '克山县', 0, 100);
INSERT INTO `jshop_area` VALUES (230230, 230200, 3, '克东县', 0, 100);
INSERT INTO `jshop_area` VALUES (230231, 230200, 3, '拜泉县', 0, 100);
INSERT INTO `jshop_area` VALUES (230281, 230200, 3, '讷河市', 0, 100);
INSERT INTO `jshop_area` VALUES (230300, 230000, 2, '鸡西市', 0, 100);
INSERT INTO `jshop_area` VALUES (230301, 230300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230302, 230300, 3, '鸡冠区', 0, 100);
INSERT INTO `jshop_area` VALUES (230303, 230300, 3, '恒山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230304, 230300, 3, '滴道区', 0, 100);
INSERT INTO `jshop_area` VALUES (230305, 230300, 3, '梨树区', 0, 100);
INSERT INTO `jshop_area` VALUES (230306, 230300, 3, '城子河区', 0, 100);
INSERT INTO `jshop_area` VALUES (230307, 230300, 3, '麻山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230321, 230300, 3, '鸡东县', 0, 100);
INSERT INTO `jshop_area` VALUES (230381, 230300, 3, '虎林市', 0, 100);
INSERT INTO `jshop_area` VALUES (230382, 230300, 3, '密山市', 0, 100);
INSERT INTO `jshop_area` VALUES (230400, 230000, 2, '鹤岗市', 0, 100);
INSERT INTO `jshop_area` VALUES (230401, 230400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230402, 230400, 3, '向阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (230403, 230400, 3, '工农区', 0, 100);
INSERT INTO `jshop_area` VALUES (230404, 230400, 3, '南山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230405, 230400, 3, '兴安区', 0, 100);
INSERT INTO `jshop_area` VALUES (230406, 230400, 3, '东山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230407, 230400, 3, '兴山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230421, 230400, 3, '萝北县', 0, 100);
INSERT INTO `jshop_area` VALUES (230422, 230400, 3, '绥滨县', 0, 100);
INSERT INTO `jshop_area` VALUES (230500, 230000, 2, '双鸭山市', 0, 100);
INSERT INTO `jshop_area` VALUES (230501, 230500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230502, 230500, 3, '尖山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230503, 230500, 3, '岭东区', 0, 100);
INSERT INTO `jshop_area` VALUES (230505, 230500, 3, '四方台区', 0, 100);
INSERT INTO `jshop_area` VALUES (230506, 230500, 3, '宝山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230521, 230500, 3, '集贤县', 0, 100);
INSERT INTO `jshop_area` VALUES (230522, 230500, 3, '友谊县', 0, 100);
INSERT INTO `jshop_area` VALUES (230523, 230500, 3, '宝清县', 0, 100);
INSERT INTO `jshop_area` VALUES (230524, 230500, 3, '饶河县', 0, 100);
INSERT INTO `jshop_area` VALUES (230600, 230000, 2, '大庆市', 0, 100);
INSERT INTO `jshop_area` VALUES (230601, 230600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230602, 230600, 3, '萨尔图区', 0, 100);
INSERT INTO `jshop_area` VALUES (230603, 230600, 3, '龙凤区', 0, 100);
INSERT INTO `jshop_area` VALUES (230604, 230600, 3, '让胡路区', 0, 100);
INSERT INTO `jshop_area` VALUES (230605, 230600, 3, '红岗区', 0, 100);
INSERT INTO `jshop_area` VALUES (230606, 230600, 3, '大同区', 0, 100);
INSERT INTO `jshop_area` VALUES (230621, 230600, 3, '肇州县', 0, 100);
INSERT INTO `jshop_area` VALUES (230622, 230600, 3, '肇源县', 0, 100);
INSERT INTO `jshop_area` VALUES (230623, 230600, 3, '林甸县', 0, 100);
INSERT INTO `jshop_area` VALUES (230624, 230600, 3, '杜尔伯特蒙古族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (230700, 230000, 2, '伊春市', 0, 100);
INSERT INTO `jshop_area` VALUES (230701, 230700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230702, 230700, 3, '伊春区', 0, 100);
INSERT INTO `jshop_area` VALUES (230703, 230700, 3, '南岔区', 0, 100);
INSERT INTO `jshop_area` VALUES (230704, 230700, 3, '友好区', 0, 100);
INSERT INTO `jshop_area` VALUES (230705, 230700, 3, '西林区', 0, 100);
INSERT INTO `jshop_area` VALUES (230706, 230700, 3, '翠峦区', 0, 100);
INSERT INTO `jshop_area` VALUES (230707, 230700, 3, '新青区', 0, 100);
INSERT INTO `jshop_area` VALUES (230708, 230700, 3, '美溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (230709, 230700, 3, '金山屯区', 0, 100);
INSERT INTO `jshop_area` VALUES (230710, 230700, 3, '五营区', 0, 100);
INSERT INTO `jshop_area` VALUES (230711, 230700, 3, '乌马河区', 0, 100);
INSERT INTO `jshop_area` VALUES (230712, 230700, 3, '汤旺河区', 0, 100);
INSERT INTO `jshop_area` VALUES (230713, 230700, 3, '带岭区', 0, 100);
INSERT INTO `jshop_area` VALUES (230714, 230700, 3, '乌伊岭区', 0, 100);
INSERT INTO `jshop_area` VALUES (230715, 230700, 3, '红星区', 0, 100);
INSERT INTO `jshop_area` VALUES (230716, 230700, 3, '上甘岭区', 0, 100);
INSERT INTO `jshop_area` VALUES (230722, 230700, 3, '嘉荫县', 0, 100);
INSERT INTO `jshop_area` VALUES (230781, 230700, 3, '铁力市', 0, 100);
INSERT INTO `jshop_area` VALUES (230800, 230000, 2, '佳木斯市', 0, 100);
INSERT INTO `jshop_area` VALUES (230801, 230800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230803, 230800, 3, '向阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (230804, 230800, 3, '前进区', 0, 100);
INSERT INTO `jshop_area` VALUES (230805, 230800, 3, '东风区', 0, 100);
INSERT INTO `jshop_area` VALUES (230811, 230800, 3, '郊区', 0, 100);
INSERT INTO `jshop_area` VALUES (230822, 230800, 3, '桦南县', 0, 100);
INSERT INTO `jshop_area` VALUES (230826, 230800, 3, '桦川县', 0, 100);
INSERT INTO `jshop_area` VALUES (230828, 230800, 3, '汤原县', 0, 100);
INSERT INTO `jshop_area` VALUES (230881, 230800, 3, '同江市', 0, 100);
INSERT INTO `jshop_area` VALUES (230882, 230800, 3, '富锦市', 0, 100);
INSERT INTO `jshop_area` VALUES (230883, 230800, 3, '抚远市', 0, 100);
INSERT INTO `jshop_area` VALUES (230900, 230000, 2, '七台河市', 0, 100);
INSERT INTO `jshop_area` VALUES (230901, 230900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (230902, 230900, 3, '新兴区', 0, 100);
INSERT INTO `jshop_area` VALUES (230903, 230900, 3, '桃山区', 0, 100);
INSERT INTO `jshop_area` VALUES (230904, 230900, 3, '茄子河区', 0, 100);
INSERT INTO `jshop_area` VALUES (230921, 230900, 3, '勃利县', 0, 100);
INSERT INTO `jshop_area` VALUES (231000, 230000, 2, '牡丹江市', 0, 100);
INSERT INTO `jshop_area` VALUES (231001, 231000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (231002, 231000, 3, '东安区', 0, 100);
INSERT INTO `jshop_area` VALUES (231003, 231000, 3, '阳明区', 0, 100);
INSERT INTO `jshop_area` VALUES (231004, 231000, 3, '爱民区', 0, 100);
INSERT INTO `jshop_area` VALUES (231005, 231000, 3, '西安区', 0, 100);
INSERT INTO `jshop_area` VALUES (231025, 231000, 3, '林口县', 0, 100);
INSERT INTO `jshop_area` VALUES (231081, 231000, 3, '绥芬河市', 0, 100);
INSERT INTO `jshop_area` VALUES (231083, 231000, 3, '海林市', 0, 100);
INSERT INTO `jshop_area` VALUES (231084, 231000, 3, '宁安市', 0, 100);
INSERT INTO `jshop_area` VALUES (231085, 231000, 3, '穆棱市', 0, 100);
INSERT INTO `jshop_area` VALUES (231086, 231000, 3, '东宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (231100, 230000, 2, '黑河市', 0, 100);
INSERT INTO `jshop_area` VALUES (231101, 231100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (231102, 231100, 3, '爱辉区', 0, 100);
INSERT INTO `jshop_area` VALUES (231121, 231100, 3, '嫩江县', 0, 100);
INSERT INTO `jshop_area` VALUES (231123, 231100, 3, '逊克县', 0, 100);
INSERT INTO `jshop_area` VALUES (231124, 231100, 3, '孙吴县', 0, 100);
INSERT INTO `jshop_area` VALUES (231181, 231100, 3, '北安市', 0, 100);
INSERT INTO `jshop_area` VALUES (231182, 231100, 3, '五大连池市', 0, 100);
INSERT INTO `jshop_area` VALUES (231200, 230000, 2, '绥化市', 0, 100);
INSERT INTO `jshop_area` VALUES (231201, 231200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (231202, 231200, 3, '北林区', 0, 100);
INSERT INTO `jshop_area` VALUES (231221, 231200, 3, '望奎县', 0, 100);
INSERT INTO `jshop_area` VALUES (231222, 231200, 3, '兰西县', 0, 100);
INSERT INTO `jshop_area` VALUES (231223, 231200, 3, '青冈县', 0, 100);
INSERT INTO `jshop_area` VALUES (231224, 231200, 3, '庆安县', 0, 100);
INSERT INTO `jshop_area` VALUES (231225, 231200, 3, '明水县', 0, 100);
INSERT INTO `jshop_area` VALUES (231226, 231200, 3, '绥棱县', 0, 100);
INSERT INTO `jshop_area` VALUES (231281, 231200, 3, '安达市', 0, 100);
INSERT INTO `jshop_area` VALUES (231282, 231200, 3, '肇东市', 0, 100);
INSERT INTO `jshop_area` VALUES (231283, 231200, 3, '海伦市', 0, 100);
INSERT INTO `jshop_area` VALUES (232700, 230000, 2, '大兴安岭地区', 0, 100);
INSERT INTO `jshop_area` VALUES (232721, 232700, 3, '呼玛县', 0, 100);
INSERT INTO `jshop_area` VALUES (232722, 232700, 3, '塔河县', 0, 100);
INSERT INTO `jshop_area` VALUES (232723, 232700, 3, '漠河县', 0, 100);
INSERT INTO `jshop_area` VALUES (310000, 0, 1, '上海市', 0, 100);
INSERT INTO `jshop_area` VALUES (310100, 310000, 2, '上海市', 0, 100);
INSERT INTO `jshop_area` VALUES (310101, 310100, 3, '黄浦区', 0, 100);
INSERT INTO `jshop_area` VALUES (310104, 310100, 3, '徐汇区', 0, 100);
INSERT INTO `jshop_area` VALUES (310105, 310100, 3, '长宁区', 0, 100);
INSERT INTO `jshop_area` VALUES (310106, 310100, 3, '静安区', 0, 100);
INSERT INTO `jshop_area` VALUES (310107, 310100, 3, '普陀区', 0, 100);
INSERT INTO `jshop_area` VALUES (310109, 310100, 3, '虹口区', 0, 100);
INSERT INTO `jshop_area` VALUES (310110, 310100, 3, '杨浦区', 0, 100);
INSERT INTO `jshop_area` VALUES (310112, 310100, 3, '闵行区', 0, 100);
INSERT INTO `jshop_area` VALUES (310113, 310100, 3, '宝山区', 0, 100);
INSERT INTO `jshop_area` VALUES (310114, 310100, 3, '嘉定区', 0, 100);
INSERT INTO `jshop_area` VALUES (310115, 310100, 3, '浦东新区', 0, 100);
INSERT INTO `jshop_area` VALUES (310116, 310100, 3, '金山区', 0, 100);
INSERT INTO `jshop_area` VALUES (310117, 310100, 3, '松江区', 0, 100);
INSERT INTO `jshop_area` VALUES (310118, 310100, 3, '青浦区', 0, 100);
INSERT INTO `jshop_area` VALUES (310120, 310100, 3, '奉贤区', 0, 100);
INSERT INTO `jshop_area` VALUES (310151, 310100, 3, '崇明区', 0, 100);
INSERT INTO `jshop_area` VALUES (320000, 0, 1, '江苏省', 0, 100);
INSERT INTO `jshop_area` VALUES (320100, 320000, 2, '南京市', 0, 100);
INSERT INTO `jshop_area` VALUES (320101, 320100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320102, 320100, 3, '玄武区', 0, 100);
INSERT INTO `jshop_area` VALUES (320104, 320100, 3, '秦淮区', 0, 100);
INSERT INTO `jshop_area` VALUES (320105, 320100, 3, '建邺区', 0, 100);
INSERT INTO `jshop_area` VALUES (320106, 320100, 3, '鼓楼区', 0, 100);
INSERT INTO `jshop_area` VALUES (320111, 320100, 3, '浦口区', 0, 100);
INSERT INTO `jshop_area` VALUES (320113, 320100, 3, '栖霞区', 0, 100);
INSERT INTO `jshop_area` VALUES (320114, 320100, 3, '雨花台区', 0, 100);
INSERT INTO `jshop_area` VALUES (320115, 320100, 3, '江宁区', 0, 100);
INSERT INTO `jshop_area` VALUES (320116, 320100, 3, '六合区', 0, 100);
INSERT INTO `jshop_area` VALUES (320117, 320100, 3, '溧水区', 0, 100);
INSERT INTO `jshop_area` VALUES (320118, 320100, 3, '高淳区', 0, 100);
INSERT INTO `jshop_area` VALUES (320200, 320000, 2, '无锡市', 0, 100);
INSERT INTO `jshop_area` VALUES (320201, 320200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320205, 320200, 3, '锡山区', 0, 100);
INSERT INTO `jshop_area` VALUES (320206, 320200, 3, '惠山区', 0, 100);
INSERT INTO `jshop_area` VALUES (320211, 320200, 3, '滨湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320213, 320200, 3, '梁溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (320214, 320200, 3, '新吴区', 0, 100);
INSERT INTO `jshop_area` VALUES (320281, 320200, 3, '江阴市', 0, 100);
INSERT INTO `jshop_area` VALUES (320282, 320200, 3, '宜兴市', 0, 100);
INSERT INTO `jshop_area` VALUES (320300, 320000, 2, '徐州市', 0, 100);
INSERT INTO `jshop_area` VALUES (320301, 320300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320302, 320300, 3, '鼓楼区', 0, 100);
INSERT INTO `jshop_area` VALUES (320303, 320300, 3, '云龙区', 0, 100);
INSERT INTO `jshop_area` VALUES (320305, 320300, 3, '贾汪区', 0, 100);
INSERT INTO `jshop_area` VALUES (320311, 320300, 3, '泉山区', 0, 100);
INSERT INTO `jshop_area` VALUES (320312, 320300, 3, '铜山区', 0, 100);
INSERT INTO `jshop_area` VALUES (320321, 320300, 3, '丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (320322, 320300, 3, '沛县', 0, 100);
INSERT INTO `jshop_area` VALUES (320324, 320300, 3, '睢宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (320381, 320300, 3, '新沂市', 0, 100);
INSERT INTO `jshop_area` VALUES (320382, 320300, 3, '邳州市', 0, 100);
INSERT INTO `jshop_area` VALUES (320400, 320000, 2, '常州市', 0, 100);
INSERT INTO `jshop_area` VALUES (320401, 320400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320402, 320400, 3, '天宁区', 0, 100);
INSERT INTO `jshop_area` VALUES (320404, 320400, 3, '钟楼区', 0, 100);
INSERT INTO `jshop_area` VALUES (320411, 320400, 3, '新北区', 0, 100);
INSERT INTO `jshop_area` VALUES (320412, 320400, 3, '武进区', 0, 100);
INSERT INTO `jshop_area` VALUES (320413, 320400, 3, '金坛区', 0, 100);
INSERT INTO `jshop_area` VALUES (320481, 320400, 3, '溧阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (320500, 320000, 2, '苏州市', 0, 100);
INSERT INTO `jshop_area` VALUES (320501, 320500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320505, 320500, 3, '虎丘区', 0, 100);
INSERT INTO `jshop_area` VALUES (320506, 320500, 3, '吴中区', 0, 100);
INSERT INTO `jshop_area` VALUES (320507, 320500, 3, '相城区', 0, 100);
INSERT INTO `jshop_area` VALUES (320508, 320500, 3, '姑苏区', 0, 100);
INSERT INTO `jshop_area` VALUES (320509, 320500, 3, '吴江区', 0, 100);
INSERT INTO `jshop_area` VALUES (320581, 320500, 3, '常熟市', 0, 100);
INSERT INTO `jshop_area` VALUES (320582, 320500, 3, '张家港市', 0, 100);
INSERT INTO `jshop_area` VALUES (320583, 320500, 3, '昆山市', 0, 100);
INSERT INTO `jshop_area` VALUES (320585, 320500, 3, '太仓市', 0, 100);
INSERT INTO `jshop_area` VALUES (320600, 320000, 2, '南通市', 0, 100);
INSERT INTO `jshop_area` VALUES (320601, 320600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320602, 320600, 3, '崇川区', 0, 100);
INSERT INTO `jshop_area` VALUES (320611, 320600, 3, '港闸区', 0, 100);
INSERT INTO `jshop_area` VALUES (320612, 320600, 3, '通州区', 0, 100);
INSERT INTO `jshop_area` VALUES (320621, 320600, 3, '海安县', 0, 100);
INSERT INTO `jshop_area` VALUES (320623, 320600, 3, '如东县', 0, 100);
INSERT INTO `jshop_area` VALUES (320681, 320600, 3, '启东市', 0, 100);
INSERT INTO `jshop_area` VALUES (320682, 320600, 3, '如皋市', 0, 100);
INSERT INTO `jshop_area` VALUES (320684, 320600, 3, '海门市', 0, 100);
INSERT INTO `jshop_area` VALUES (320700, 320000, 2, '连云港市', 0, 100);
INSERT INTO `jshop_area` VALUES (320701, 320700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320703, 320700, 3, '连云区', 0, 100);
INSERT INTO `jshop_area` VALUES (320706, 320700, 3, '海州区', 0, 100);
INSERT INTO `jshop_area` VALUES (320707, 320700, 3, '赣榆区', 0, 100);
INSERT INTO `jshop_area` VALUES (320722, 320700, 3, '东海县', 0, 100);
INSERT INTO `jshop_area` VALUES (320723, 320700, 3, '灌云县', 0, 100);
INSERT INTO `jshop_area` VALUES (320724, 320700, 3, '灌南县', 0, 100);
INSERT INTO `jshop_area` VALUES (320800, 320000, 2, '淮安市', 0, 100);
INSERT INTO `jshop_area` VALUES (320801, 320800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320803, 320800, 3, '淮安区', 0, 100);
INSERT INTO `jshop_area` VALUES (320804, 320800, 3, '淮阴区', 0, 100);
INSERT INTO `jshop_area` VALUES (320812, 320800, 3, '清江浦区', 0, 100);
INSERT INTO `jshop_area` VALUES (320813, 320800, 3, '洪泽区', 0, 100);
INSERT INTO `jshop_area` VALUES (320826, 320800, 3, '涟水县', 0, 100);
INSERT INTO `jshop_area` VALUES (320830, 320800, 3, '盱眙县', 0, 100);
INSERT INTO `jshop_area` VALUES (320831, 320800, 3, '金湖县', 0, 100);
INSERT INTO `jshop_area` VALUES (320900, 320000, 2, '盐城市', 0, 100);
INSERT INTO `jshop_area` VALUES (320901, 320900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320902, 320900, 3, '亭湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (320903, 320900, 3, '盐都区', 0, 100);
INSERT INTO `jshop_area` VALUES (320904, 320900, 3, '大丰区', 0, 100);
INSERT INTO `jshop_area` VALUES (320921, 320900, 3, '响水县', 0, 100);
INSERT INTO `jshop_area` VALUES (320922, 320900, 3, '滨海县', 0, 100);
INSERT INTO `jshop_area` VALUES (320923, 320900, 3, '阜宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (320924, 320900, 3, '射阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (320925, 320900, 3, '建湖县', 0, 100);
INSERT INTO `jshop_area` VALUES (320981, 320900, 3, '东台市', 0, 100);
INSERT INTO `jshop_area` VALUES (321000, 320000, 2, '扬州市', 0, 100);
INSERT INTO `jshop_area` VALUES (321001, 321000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (321002, 321000, 3, '广陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (321003, 321000, 3, '邗江区', 0, 100);
INSERT INTO `jshop_area` VALUES (321012, 321000, 3, '江都区', 0, 100);
INSERT INTO `jshop_area` VALUES (321023, 321000, 3, '宝应县', 0, 100);
INSERT INTO `jshop_area` VALUES (321081, 321000, 3, '仪征市', 0, 100);
INSERT INTO `jshop_area` VALUES (321084, 321000, 3, '高邮市', 0, 100);
INSERT INTO `jshop_area` VALUES (321100, 320000, 2, '镇江市', 0, 100);
INSERT INTO `jshop_area` VALUES (321101, 321100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (321102, 321100, 3, '京口区', 0, 100);
INSERT INTO `jshop_area` VALUES (321111, 321100, 3, '润州区', 0, 100);
INSERT INTO `jshop_area` VALUES (321112, 321100, 3, '丹徒区', 0, 100);
INSERT INTO `jshop_area` VALUES (321181, 321100, 3, '丹阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (321182, 321100, 3, '扬中市', 0, 100);
INSERT INTO `jshop_area` VALUES (321183, 321100, 3, '句容市', 0, 100);
INSERT INTO `jshop_area` VALUES (321200, 320000, 2, '泰州市', 0, 100);
INSERT INTO `jshop_area` VALUES (321201, 321200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (321202, 321200, 3, '海陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (321203, 321200, 3, '高港区', 0, 100);
INSERT INTO `jshop_area` VALUES (321204, 321200, 3, '姜堰区', 0, 100);
INSERT INTO `jshop_area` VALUES (321281, 321200, 3, '兴化市', 0, 100);
INSERT INTO `jshop_area` VALUES (321282, 321200, 3, '靖江市', 0, 100);
INSERT INTO `jshop_area` VALUES (321283, 321200, 3, '泰兴市', 0, 100);
INSERT INTO `jshop_area` VALUES (321300, 320000, 2, '宿迁市', 0, 100);
INSERT INTO `jshop_area` VALUES (321301, 321300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (321302, 321300, 3, '宿城区', 0, 100);
INSERT INTO `jshop_area` VALUES (321311, 321300, 3, '宿豫区', 0, 100);
INSERT INTO `jshop_area` VALUES (321322, 321300, 3, '沭阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (321323, 321300, 3, '泗阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (321324, 321300, 3, '泗洪县', 0, 100);
INSERT INTO `jshop_area` VALUES (330000, 0, 1, '浙江省', 0, 100);
INSERT INTO `jshop_area` VALUES (330100, 330000, 2, '杭州市', 0, 100);
INSERT INTO `jshop_area` VALUES (330101, 330100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330102, 330100, 3, '上城区', 0, 100);
INSERT INTO `jshop_area` VALUES (330103, 330100, 3, '下城区', 0, 100);
INSERT INTO `jshop_area` VALUES (330104, 330100, 3, '江干区', 0, 100);
INSERT INTO `jshop_area` VALUES (330105, 330100, 3, '拱墅区', 0, 100);
INSERT INTO `jshop_area` VALUES (330106, 330100, 3, '西湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330108, 330100, 3, '滨江区', 0, 100);
INSERT INTO `jshop_area` VALUES (330109, 330100, 3, '萧山区', 0, 100);
INSERT INTO `jshop_area` VALUES (330110, 330100, 3, '余杭区', 0, 100);
INSERT INTO `jshop_area` VALUES (330111, 330100, 3, '富阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (330122, 330100, 3, '桐庐县', 0, 100);
INSERT INTO `jshop_area` VALUES (330127, 330100, 3, '淳安县', 0, 100);
INSERT INTO `jshop_area` VALUES (330182, 330100, 3, '建德市', 0, 100);
INSERT INTO `jshop_area` VALUES (330185, 330100, 3, '临安市', 0, 100);
INSERT INTO `jshop_area` VALUES (330200, 330000, 2, '宁波市', 0, 100);
INSERT INTO `jshop_area` VALUES (330201, 330200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330203, 330200, 3, '海曙区', 0, 100);
INSERT INTO `jshop_area` VALUES (330204, 330200, 3, '江东区', 0, 100);
INSERT INTO `jshop_area` VALUES (330205, 330200, 3, '江北区', 0, 100);
INSERT INTO `jshop_area` VALUES (330206, 330200, 3, '北仑区', 0, 100);
INSERT INTO `jshop_area` VALUES (330211, 330200, 3, '镇海区', 0, 100);
INSERT INTO `jshop_area` VALUES (330212, 330200, 3, '鄞州区', 0, 100);
INSERT INTO `jshop_area` VALUES (330225, 330200, 3, '象山县', 0, 100);
INSERT INTO `jshop_area` VALUES (330226, 330200, 3, '宁海县', 0, 100);
INSERT INTO `jshop_area` VALUES (330281, 330200, 3, '余姚市', 0, 100);
INSERT INTO `jshop_area` VALUES (330282, 330200, 3, '慈溪市', 0, 100);
INSERT INTO `jshop_area` VALUES (330283, 330200, 3, '奉化市', 0, 100);
INSERT INTO `jshop_area` VALUES (330300, 330000, 2, '温州市', 0, 100);
INSERT INTO `jshop_area` VALUES (330301, 330300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330302, 330300, 3, '鹿城区', 0, 100);
INSERT INTO `jshop_area` VALUES (330303, 330300, 3, '龙湾区', 0, 100);
INSERT INTO `jshop_area` VALUES (330304, 330300, 3, '瓯海区', 0, 100);
INSERT INTO `jshop_area` VALUES (330305, 330300, 3, '洞头区', 0, 100);
INSERT INTO `jshop_area` VALUES (330324, 330300, 3, '永嘉县', 0, 100);
INSERT INTO `jshop_area` VALUES (330326, 330300, 3, '平阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (330327, 330300, 3, '苍南县', 0, 100);
INSERT INTO `jshop_area` VALUES (330328, 330300, 3, '文成县', 0, 100);
INSERT INTO `jshop_area` VALUES (330329, 330300, 3, '泰顺县', 0, 100);
INSERT INTO `jshop_area` VALUES (330381, 330300, 3, '瑞安市', 0, 100);
INSERT INTO `jshop_area` VALUES (330382, 330300, 3, '乐清市', 0, 100);
INSERT INTO `jshop_area` VALUES (330400, 330000, 2, '嘉兴市', 0, 100);
INSERT INTO `jshop_area` VALUES (330401, 330400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330402, 330400, 3, '南湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330411, 330400, 3, '秀洲区', 0, 100);
INSERT INTO `jshop_area` VALUES (330421, 330400, 3, '嘉善县', 0, 100);
INSERT INTO `jshop_area` VALUES (330424, 330400, 3, '海盐县', 0, 100);
INSERT INTO `jshop_area` VALUES (330481, 330400, 3, '海宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (330482, 330400, 3, '平湖市', 0, 100);
INSERT INTO `jshop_area` VALUES (330483, 330400, 3, '桐乡市', 0, 100);
INSERT INTO `jshop_area` VALUES (330500, 330000, 2, '湖州市', 0, 100);
INSERT INTO `jshop_area` VALUES (330501, 330500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330502, 330500, 3, '吴兴区', 0, 100);
INSERT INTO `jshop_area` VALUES (330503, 330500, 3, '南浔区', 0, 100);
INSERT INTO `jshop_area` VALUES (330521, 330500, 3, '德清县', 0, 100);
INSERT INTO `jshop_area` VALUES (330522, 330500, 3, '长兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (330523, 330500, 3, '安吉县', 0, 100);
INSERT INTO `jshop_area` VALUES (330600, 330000, 2, '绍兴市', 0, 100);
INSERT INTO `jshop_area` VALUES (330601, 330600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330602, 330600, 3, '越城区', 0, 100);
INSERT INTO `jshop_area` VALUES (330603, 330600, 3, '柯桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (330604, 330600, 3, '上虞区', 0, 100);
INSERT INTO `jshop_area` VALUES (330624, 330600, 3, '新昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (330681, 330600, 3, '诸暨市', 0, 100);
INSERT INTO `jshop_area` VALUES (330683, 330600, 3, '嵊州市', 0, 100);
INSERT INTO `jshop_area` VALUES (330700, 330000, 2, '金华市', 0, 100);
INSERT INTO `jshop_area` VALUES (330701, 330700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330702, 330700, 3, '婺城区', 0, 100);
INSERT INTO `jshop_area` VALUES (330703, 330700, 3, '金东区', 0, 100);
INSERT INTO `jshop_area` VALUES (330723, 330700, 3, '武义县', 0, 100);
INSERT INTO `jshop_area` VALUES (330726, 330700, 3, '浦江县', 0, 100);
INSERT INTO `jshop_area` VALUES (330727, 330700, 3, '磐安县', 0, 100);
INSERT INTO `jshop_area` VALUES (330781, 330700, 3, '兰溪市', 0, 100);
INSERT INTO `jshop_area` VALUES (330782, 330700, 3, '义乌市', 0, 100);
INSERT INTO `jshop_area` VALUES (330783, 330700, 3, '东阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (330784, 330700, 3, '永康市', 0, 100);
INSERT INTO `jshop_area` VALUES (330800, 330000, 2, '衢州市', 0, 100);
INSERT INTO `jshop_area` VALUES (330801, 330800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330802, 330800, 3, '柯城区', 0, 100);
INSERT INTO `jshop_area` VALUES (330803, 330800, 3, '衢江区', 0, 100);
INSERT INTO `jshop_area` VALUES (330822, 330800, 3, '常山县', 0, 100);
INSERT INTO `jshop_area` VALUES (330824, 330800, 3, '开化县', 0, 100);
INSERT INTO `jshop_area` VALUES (330825, 330800, 3, '龙游县', 0, 100);
INSERT INTO `jshop_area` VALUES (330881, 330800, 3, '江山市', 0, 100);
INSERT INTO `jshop_area` VALUES (330900, 330000, 2, '舟山市', 0, 100);
INSERT INTO `jshop_area` VALUES (330901, 330900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (330902, 330900, 3, '定海区', 0, 100);
INSERT INTO `jshop_area` VALUES (330903, 330900, 3, '普陀区', 0, 100);
INSERT INTO `jshop_area` VALUES (330921, 330900, 3, '岱山县', 0, 100);
INSERT INTO `jshop_area` VALUES (330922, 330900, 3, '嵊泗县', 0, 100);
INSERT INTO `jshop_area` VALUES (331000, 330000, 2, '台州市', 0, 100);
INSERT INTO `jshop_area` VALUES (331001, 331000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (331002, 331000, 3, '椒江区', 0, 100);
INSERT INTO `jshop_area` VALUES (331003, 331000, 3, '黄岩区', 0, 100);
INSERT INTO `jshop_area` VALUES (331004, 331000, 3, '路桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (331021, 331000, 3, '玉环县', 0, 100);
INSERT INTO `jshop_area` VALUES (331022, 331000, 3, '三门县', 0, 100);
INSERT INTO `jshop_area` VALUES (331023, 331000, 3, '天台县', 0, 100);
INSERT INTO `jshop_area` VALUES (331024, 331000, 3, '仙居县', 0, 100);
INSERT INTO `jshop_area` VALUES (331081, 331000, 3, '温岭市', 0, 100);
INSERT INTO `jshop_area` VALUES (331082, 331000, 3, '临海市', 0, 100);
INSERT INTO `jshop_area` VALUES (331100, 330000, 2, '丽水市', 0, 100);
INSERT INTO `jshop_area` VALUES (331101, 331100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (331102, 331100, 3, '莲都区', 0, 100);
INSERT INTO `jshop_area` VALUES (331121, 331100, 3, '青田县', 0, 100);
INSERT INTO `jshop_area` VALUES (331122, 331100, 3, '缙云县', 0, 100);
INSERT INTO `jshop_area` VALUES (331123, 331100, 3, '遂昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (331124, 331100, 3, '松阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (331125, 331100, 3, '云和县', 0, 100);
INSERT INTO `jshop_area` VALUES (331126, 331100, 3, '庆元县', 0, 100);
INSERT INTO `jshop_area` VALUES (331127, 331100, 3, '景宁畲族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (331181, 331100, 3, '龙泉市', 0, 100);
INSERT INTO `jshop_area` VALUES (340000, 0, 1, '安徽省', 0, 100);
INSERT INTO `jshop_area` VALUES (340100, 340000, 2, '合肥市', 0, 100);
INSERT INTO `jshop_area` VALUES (340101, 340100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340102, 340100, 3, '瑶海区', 0, 100);
INSERT INTO `jshop_area` VALUES (340103, 340100, 3, '庐阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (340104, 340100, 3, '蜀山区', 0, 100);
INSERT INTO `jshop_area` VALUES (340111, 340100, 3, '包河区', 0, 100);
INSERT INTO `jshop_area` VALUES (340121, 340100, 3, '长丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (340122, 340100, 3, '肥东县', 0, 100);
INSERT INTO `jshop_area` VALUES (340123, 340100, 3, '肥西县', 0, 100);
INSERT INTO `jshop_area` VALUES (340124, 340100, 3, '庐江县', 0, 100);
INSERT INTO `jshop_area` VALUES (340181, 340100, 3, '巢湖市', 0, 100);
INSERT INTO `jshop_area` VALUES (340200, 340000, 2, '芜湖市', 0, 100);
INSERT INTO `jshop_area` VALUES (340201, 340200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340202, 340200, 3, '镜湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340203, 340200, 3, '弋江区', 0, 100);
INSERT INTO `jshop_area` VALUES (340207, 340200, 3, '鸠江区', 0, 100);
INSERT INTO `jshop_area` VALUES (340208, 340200, 3, '三山区', 0, 100);
INSERT INTO `jshop_area` VALUES (340221, 340200, 3, '芜湖县', 0, 100);
INSERT INTO `jshop_area` VALUES (340222, 340200, 3, '繁昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (340223, 340200, 3, '南陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (340225, 340200, 3, '无为县', 0, 100);
INSERT INTO `jshop_area` VALUES (340300, 340000, 2, '蚌埠市', 0, 100);
INSERT INTO `jshop_area` VALUES (340301, 340300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340302, 340300, 3, '龙子湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340303, 340300, 3, '蚌山区', 0, 100);
INSERT INTO `jshop_area` VALUES (340304, 340300, 3, '禹会区', 0, 100);
INSERT INTO `jshop_area` VALUES (340311, 340300, 3, '淮上区', 0, 100);
INSERT INTO `jshop_area` VALUES (340321, 340300, 3, '怀远县', 0, 100);
INSERT INTO `jshop_area` VALUES (340322, 340300, 3, '五河县', 0, 100);
INSERT INTO `jshop_area` VALUES (340323, 340300, 3, '固镇县', 0, 100);
INSERT INTO `jshop_area` VALUES (340400, 340000, 2, '淮南市', 0, 100);
INSERT INTO `jshop_area` VALUES (340401, 340400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340402, 340400, 3, '大通区', 0, 100);
INSERT INTO `jshop_area` VALUES (340403, 340400, 3, '田家庵区', 0, 100);
INSERT INTO `jshop_area` VALUES (340404, 340400, 3, '谢家集区', 0, 100);
INSERT INTO `jshop_area` VALUES (340405, 340400, 3, '八公山区', 0, 100);
INSERT INTO `jshop_area` VALUES (340406, 340400, 3, '潘集区', 0, 100);
INSERT INTO `jshop_area` VALUES (340421, 340400, 3, '凤台县', 0, 100);
INSERT INTO `jshop_area` VALUES (340422, 340400, 3, '寿县', 0, 100);
INSERT INTO `jshop_area` VALUES (340500, 340000, 2, '马鞍山市', 0, 100);
INSERT INTO `jshop_area` VALUES (340501, 340500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340503, 340500, 3, '花山区', 0, 100);
INSERT INTO `jshop_area` VALUES (340504, 340500, 3, '雨山区', 0, 100);
INSERT INTO `jshop_area` VALUES (340506, 340500, 3, '博望区', 0, 100);
INSERT INTO `jshop_area` VALUES (340521, 340500, 3, '当涂县', 0, 100);
INSERT INTO `jshop_area` VALUES (340522, 340500, 3, '含山县', 0, 100);
INSERT INTO `jshop_area` VALUES (340523, 340500, 3, '和县', 0, 100);
INSERT INTO `jshop_area` VALUES (340600, 340000, 2, '淮北市', 0, 100);
INSERT INTO `jshop_area` VALUES (340601, 340600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340602, 340600, 3, '杜集区', 0, 100);
INSERT INTO `jshop_area` VALUES (340603, 340600, 3, '相山区', 0, 100);
INSERT INTO `jshop_area` VALUES (340604, 340600, 3, '烈山区', 0, 100);
INSERT INTO `jshop_area` VALUES (340621, 340600, 3, '濉溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (340700, 340000, 2, '铜陵市', 0, 100);
INSERT INTO `jshop_area` VALUES (340701, 340700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340705, 340700, 3, '铜官区', 0, 100);
INSERT INTO `jshop_area` VALUES (340706, 340700, 3, '义安区', 0, 100);
INSERT INTO `jshop_area` VALUES (340711, 340700, 3, '郊区', 0, 100);
INSERT INTO `jshop_area` VALUES (340722, 340700, 3, '枞阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (340800, 340000, 2, '安庆市', 0, 100);
INSERT INTO `jshop_area` VALUES (340801, 340800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (340802, 340800, 3, '迎江区', 0, 100);
INSERT INTO `jshop_area` VALUES (340803, 340800, 3, '大观区', 0, 100);
INSERT INTO `jshop_area` VALUES (340811, 340800, 3, '宜秀区', 0, 100);
INSERT INTO `jshop_area` VALUES (340822, 340800, 3, '怀宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (340824, 340800, 3, '潜山县', 0, 100);
INSERT INTO `jshop_area` VALUES (340825, 340800, 3, '太湖县', 0, 100);
INSERT INTO `jshop_area` VALUES (340826, 340800, 3, '宿松县', 0, 100);
INSERT INTO `jshop_area` VALUES (340827, 340800, 3, '望江县', 0, 100);
INSERT INTO `jshop_area` VALUES (340828, 340800, 3, '岳西县', 0, 100);
INSERT INTO `jshop_area` VALUES (340881, 340800, 3, '桐城市', 0, 100);
INSERT INTO `jshop_area` VALUES (341000, 340000, 2, '黄山市', 0, 100);
INSERT INTO `jshop_area` VALUES (341001, 341000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (341002, 341000, 3, '屯溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (341003, 341000, 3, '黄山区', 0, 100);
INSERT INTO `jshop_area` VALUES (341004, 341000, 3, '徽州区', 0, 100);
INSERT INTO `jshop_area` VALUES (341021, 341000, 3, '歙县', 0, 100);
INSERT INTO `jshop_area` VALUES (341022, 341000, 3, '休宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (341023, 341000, 3, '黟县', 0, 100);
INSERT INTO `jshop_area` VALUES (341024, 341000, 3, '祁门县', 0, 100);
INSERT INTO `jshop_area` VALUES (341100, 340000, 2, '滁州市', 0, 100);
INSERT INTO `jshop_area` VALUES (341101, 341100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (341102, 341100, 3, '琅琊区', 0, 100);
INSERT INTO `jshop_area` VALUES (341103, 341100, 3, '南谯区', 0, 100);
INSERT INTO `jshop_area` VALUES (341122, 341100, 3, '来安县', 0, 100);
INSERT INTO `jshop_area` VALUES (341124, 341100, 3, '全椒县', 0, 100);
INSERT INTO `jshop_area` VALUES (341125, 341100, 3, '定远县', 0, 100);
INSERT INTO `jshop_area` VALUES (341126, 341100, 3, '凤阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (341181, 341100, 3, '天长市', 0, 100);
INSERT INTO `jshop_area` VALUES (341182, 341100, 3, '明光市', 0, 100);
INSERT INTO `jshop_area` VALUES (341200, 340000, 2, '阜阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (341201, 341200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (341202, 341200, 3, '颍州区', 0, 100);
INSERT INTO `jshop_area` VALUES (341203, 341200, 3, '颍东区', 0, 100);
INSERT INTO `jshop_area` VALUES (341204, 341200, 3, '颍泉区', 0, 100);
INSERT INTO `jshop_area` VALUES (341221, 341200, 3, '临泉县', 0, 100);
INSERT INTO `jshop_area` VALUES (341222, 341200, 3, '太和县', 0, 100);
INSERT INTO `jshop_area` VALUES (341225, 341200, 3, '阜南县', 0, 100);
INSERT INTO `jshop_area` VALUES (341226, 341200, 3, '颍上县', 0, 100);
INSERT INTO `jshop_area` VALUES (341282, 341200, 3, '界首市', 0, 100);
INSERT INTO `jshop_area` VALUES (341300, 340000, 2, '宿州市', 0, 100);
INSERT INTO `jshop_area` VALUES (341301, 341300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (341302, 341300, 3, '埇桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (341321, 341300, 3, '砀山县', 0, 100);
INSERT INTO `jshop_area` VALUES (341322, 341300, 3, '萧县', 0, 100);
INSERT INTO `jshop_area` VALUES (341323, 341300, 3, '灵璧县', 0, 100);
INSERT INTO `jshop_area` VALUES (341324, 341300, 3, '泗县', 0, 100);
INSERT INTO `jshop_area` VALUES (341500, 340000, 2, '六安市', 0, 100);
INSERT INTO `jshop_area` VALUES (341501, 341500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (341502, 341500, 3, '金安区', 0, 100);
INSERT INTO `jshop_area` VALUES (341503, 341500, 3, '裕安区', 0, 100);
INSERT INTO `jshop_area` VALUES (341504, 341500, 3, '叶集区', 0, 100);
INSERT INTO `jshop_area` VALUES (341522, 341500, 3, '霍邱县', 0, 100);
INSERT INTO `jshop_area` VALUES (341523, 341500, 3, '舒城县', 0, 100);
INSERT INTO `jshop_area` VALUES (341524, 341500, 3, '金寨县', 0, 100);
INSERT INTO `jshop_area` VALUES (341525, 341500, 3, '霍山县', 0, 100);
INSERT INTO `jshop_area` VALUES (341600, 340000, 2, '亳州市', 0, 100);
INSERT INTO `jshop_area` VALUES (341601, 341600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (341602, 341600, 3, '谯城区', 0, 100);
INSERT INTO `jshop_area` VALUES (341621, 341600, 3, '涡阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (341622, 341600, 3, '蒙城县', 0, 100);
INSERT INTO `jshop_area` VALUES (341623, 341600, 3, '利辛县', 0, 100);
INSERT INTO `jshop_area` VALUES (341700, 340000, 2, '池州市', 0, 100);
INSERT INTO `jshop_area` VALUES (341701, 341700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (341702, 341700, 3, '贵池区', 0, 100);
INSERT INTO `jshop_area` VALUES (341721, 341700, 3, '东至县', 0, 100);
INSERT INTO `jshop_area` VALUES (341722, 341700, 3, '石台县', 0, 100);
INSERT INTO `jshop_area` VALUES (341723, 341700, 3, '青阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (341800, 340000, 2, '宣城市', 0, 100);
INSERT INTO `jshop_area` VALUES (341801, 341800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (341802, 341800, 3, '宣州区', 0, 100);
INSERT INTO `jshop_area` VALUES (341821, 341800, 3, '郎溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (341822, 341800, 3, '广德县', 0, 100);
INSERT INTO `jshop_area` VALUES (341823, 341800, 3, '泾县', 0, 100);
INSERT INTO `jshop_area` VALUES (341824, 341800, 3, '绩溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (341825, 341800, 3, '旌德县', 0, 100);
INSERT INTO `jshop_area` VALUES (341881, 341800, 3, '宁国市', 0, 100);
INSERT INTO `jshop_area` VALUES (350000, 0, 1, '福建省', 0, 100);
INSERT INTO `jshop_area` VALUES (350100, 350000, 2, '福州市', 0, 100);
INSERT INTO `jshop_area` VALUES (350101, 350100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350102, 350100, 3, '鼓楼区', 0, 100);
INSERT INTO `jshop_area` VALUES (350103, 350100, 3, '台江区', 0, 100);
INSERT INTO `jshop_area` VALUES (350104, 350100, 3, '仓山区', 0, 100);
INSERT INTO `jshop_area` VALUES (350105, 350100, 3, '马尾区', 0, 100);
INSERT INTO `jshop_area` VALUES (350111, 350100, 3, '晋安区', 0, 100);
INSERT INTO `jshop_area` VALUES (350121, 350100, 3, '闽侯县', 0, 100);
INSERT INTO `jshop_area` VALUES (350122, 350100, 3, '连江县', 0, 100);
INSERT INTO `jshop_area` VALUES (350123, 350100, 3, '罗源县', 0, 100);
INSERT INTO `jshop_area` VALUES (350124, 350100, 3, '闽清县', 0, 100);
INSERT INTO `jshop_area` VALUES (350125, 350100, 3, '永泰县', 0, 100);
INSERT INTO `jshop_area` VALUES (350128, 350100, 3, '平潭县', 0, 100);
INSERT INTO `jshop_area` VALUES (350181, 350100, 3, '福清市', 0, 100);
INSERT INTO `jshop_area` VALUES (350182, 350100, 3, '长乐市', 0, 100);
INSERT INTO `jshop_area` VALUES (350200, 350000, 2, '厦门市', 0, 100);
INSERT INTO `jshop_area` VALUES (350201, 350200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350203, 350200, 3, '思明区', 0, 100);
INSERT INTO `jshop_area` VALUES (350205, 350200, 3, '海沧区', 0, 100);
INSERT INTO `jshop_area` VALUES (350206, 350200, 3, '湖里区', 0, 100);
INSERT INTO `jshop_area` VALUES (350211, 350200, 3, '集美区', 0, 100);
INSERT INTO `jshop_area` VALUES (350212, 350200, 3, '同安区', 0, 100);
INSERT INTO `jshop_area` VALUES (350213, 350200, 3, '翔安区', 0, 100);
INSERT INTO `jshop_area` VALUES (350300, 350000, 2, '莆田市', 0, 100);
INSERT INTO `jshop_area` VALUES (350301, 350300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350302, 350300, 3, '城厢区', 0, 100);
INSERT INTO `jshop_area` VALUES (350303, 350300, 3, '涵江区', 0, 100);
INSERT INTO `jshop_area` VALUES (350304, 350300, 3, '荔城区', 0, 100);
INSERT INTO `jshop_area` VALUES (350305, 350300, 3, '秀屿区', 0, 100);
INSERT INTO `jshop_area` VALUES (350322, 350300, 3, '仙游县', 0, 100);
INSERT INTO `jshop_area` VALUES (350400, 350000, 2, '三明市', 0, 100);
INSERT INTO `jshop_area` VALUES (350401, 350400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350402, 350400, 3, '梅列区', 0, 100);
INSERT INTO `jshop_area` VALUES (350403, 350400, 3, '三元区', 0, 100);
INSERT INTO `jshop_area` VALUES (350421, 350400, 3, '明溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (350423, 350400, 3, '清流县', 0, 100);
INSERT INTO `jshop_area` VALUES (350424, 350400, 3, '宁化县', 0, 100);
INSERT INTO `jshop_area` VALUES (350425, 350400, 3, '大田县', 0, 100);
INSERT INTO `jshop_area` VALUES (350426, 350400, 3, '尤溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (350427, 350400, 3, '沙县', 0, 100);
INSERT INTO `jshop_area` VALUES (350428, 350400, 3, '将乐县', 0, 100);
INSERT INTO `jshop_area` VALUES (350429, 350400, 3, '泰宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (350430, 350400, 3, '建宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (350481, 350400, 3, '永安市', 0, 100);
INSERT INTO `jshop_area` VALUES (350500, 350000, 2, '泉州市', 0, 100);
INSERT INTO `jshop_area` VALUES (350501, 350500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350502, 350500, 3, '鲤城区', 0, 100);
INSERT INTO `jshop_area` VALUES (350503, 350500, 3, '丰泽区', 0, 100);
INSERT INTO `jshop_area` VALUES (350504, 350500, 3, '洛江区', 0, 100);
INSERT INTO `jshop_area` VALUES (350505, 350500, 3, '泉港区', 0, 100);
INSERT INTO `jshop_area` VALUES (350521, 350500, 3, '惠安县', 0, 100);
INSERT INTO `jshop_area` VALUES (350524, 350500, 3, '安溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (350525, 350500, 3, '永春县', 0, 100);
INSERT INTO `jshop_area` VALUES (350526, 350500, 3, '德化县', 0, 100);
INSERT INTO `jshop_area` VALUES (350527, 350500, 3, '金门县', 0, 100);
INSERT INTO `jshop_area` VALUES (350581, 350500, 3, '石狮市', 0, 100);
INSERT INTO `jshop_area` VALUES (350582, 350500, 3, '晋江市', 0, 100);
INSERT INTO `jshop_area` VALUES (350583, 350500, 3, '南安市', 0, 100);
INSERT INTO `jshop_area` VALUES (350600, 350000, 2, '漳州市', 0, 100);
INSERT INTO `jshop_area` VALUES (350601, 350600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350602, 350600, 3, '芗城区', 0, 100);
INSERT INTO `jshop_area` VALUES (350603, 350600, 3, '龙文区', 0, 100);
INSERT INTO `jshop_area` VALUES (350622, 350600, 3, '云霄县', 0, 100);
INSERT INTO `jshop_area` VALUES (350623, 350600, 3, '漳浦县', 0, 100);
INSERT INTO `jshop_area` VALUES (350624, 350600, 3, '诏安县', 0, 100);
INSERT INTO `jshop_area` VALUES (350625, 350600, 3, '长泰县', 0, 100);
INSERT INTO `jshop_area` VALUES (350626, 350600, 3, '东山县', 0, 100);
INSERT INTO `jshop_area` VALUES (350627, 350600, 3, '南靖县', 0, 100);
INSERT INTO `jshop_area` VALUES (350628, 350600, 3, '平和县', 0, 100);
INSERT INTO `jshop_area` VALUES (350629, 350600, 3, '华安县', 0, 100);
INSERT INTO `jshop_area` VALUES (350681, 350600, 3, '龙海市', 0, 100);
INSERT INTO `jshop_area` VALUES (350700, 350000, 2, '南平市', 0, 100);
INSERT INTO `jshop_area` VALUES (350701, 350700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350702, 350700, 3, '延平区', 0, 100);
INSERT INTO `jshop_area` VALUES (350703, 350700, 3, '建阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (350721, 350700, 3, '顺昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (350722, 350700, 3, '浦城县', 0, 100);
INSERT INTO `jshop_area` VALUES (350723, 350700, 3, '光泽县', 0, 100);
INSERT INTO `jshop_area` VALUES (350724, 350700, 3, '松溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (350725, 350700, 3, '政和县', 0, 100);
INSERT INTO `jshop_area` VALUES (350781, 350700, 3, '邵武市', 0, 100);
INSERT INTO `jshop_area` VALUES (350782, 350700, 3, '武夷山市', 0, 100);
INSERT INTO `jshop_area` VALUES (350783, 350700, 3, '建瓯市', 0, 100);
INSERT INTO `jshop_area` VALUES (350800, 350000, 2, '龙岩市', 0, 100);
INSERT INTO `jshop_area` VALUES (350801, 350800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350802, 350800, 3, '新罗区', 0, 100);
INSERT INTO `jshop_area` VALUES (350803, 350800, 3, '永定区', 0, 100);
INSERT INTO `jshop_area` VALUES (350821, 350800, 3, '长汀县', 0, 100);
INSERT INTO `jshop_area` VALUES (350823, 350800, 3, '上杭县', 0, 100);
INSERT INTO `jshop_area` VALUES (350824, 350800, 3, '武平县', 0, 100);
INSERT INTO `jshop_area` VALUES (350825, 350800, 3, '连城县', 0, 100);
INSERT INTO `jshop_area` VALUES (350881, 350800, 3, '漳平市', 0, 100);
INSERT INTO `jshop_area` VALUES (350900, 350000, 2, '宁德市', 0, 100);
INSERT INTO `jshop_area` VALUES (350901, 350900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (350902, 350900, 3, '蕉城区', 0, 100);
INSERT INTO `jshop_area` VALUES (350921, 350900, 3, '霞浦县', 0, 100);
INSERT INTO `jshop_area` VALUES (350922, 350900, 3, '古田县', 0, 100);
INSERT INTO `jshop_area` VALUES (350923, 350900, 3, '屏南县', 0, 100);
INSERT INTO `jshop_area` VALUES (350924, 350900, 3, '寿宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (350925, 350900, 3, '周宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (350926, 350900, 3, '柘荣县', 0, 100);
INSERT INTO `jshop_area` VALUES (350981, 350900, 3, '福安市', 0, 100);
INSERT INTO `jshop_area` VALUES (350982, 350900, 3, '福鼎市', 0, 100);
INSERT INTO `jshop_area` VALUES (360000, 0, 1, '江西省', 0, 100);
INSERT INTO `jshop_area` VALUES (360100, 360000, 2, '南昌市', 0, 100);
INSERT INTO `jshop_area` VALUES (360101, 360100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360102, 360100, 3, '东湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360103, 360100, 3, '西湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360104, 360100, 3, '青云谱区', 0, 100);
INSERT INTO `jshop_area` VALUES (360105, 360100, 3, '湾里区', 0, 100);
INSERT INTO `jshop_area` VALUES (360111, 360100, 3, '青山湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360112, 360100, 3, '新建区', 0, 100);
INSERT INTO `jshop_area` VALUES (360121, 360100, 3, '南昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (360123, 360100, 3, '安义县', 0, 100);
INSERT INTO `jshop_area` VALUES (360124, 360100, 3, '进贤县', 0, 100);
INSERT INTO `jshop_area` VALUES (360200, 360000, 2, '景德镇市', 0, 100);
INSERT INTO `jshop_area` VALUES (360201, 360200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360202, 360200, 3, '昌江区', 0, 100);
INSERT INTO `jshop_area` VALUES (360203, 360200, 3, '珠山区', 0, 100);
INSERT INTO `jshop_area` VALUES (360222, 360200, 3, '浮梁县', 0, 100);
INSERT INTO `jshop_area` VALUES (360281, 360200, 3, '乐平市', 0, 100);
INSERT INTO `jshop_area` VALUES (360300, 360000, 2, '萍乡市', 0, 100);
INSERT INTO `jshop_area` VALUES (360301, 360300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360302, 360300, 3, '安源区', 0, 100);
INSERT INTO `jshop_area` VALUES (360313, 360300, 3, '湘东区', 0, 100);
INSERT INTO `jshop_area` VALUES (360321, 360300, 3, '莲花县', 0, 100);
INSERT INTO `jshop_area` VALUES (360322, 360300, 3, '上栗县', 0, 100);
INSERT INTO `jshop_area` VALUES (360323, 360300, 3, '芦溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (360400, 360000, 2, '九江市', 0, 100);
INSERT INTO `jshop_area` VALUES (360401, 360400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360402, 360400, 3, '濂溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (360403, 360400, 3, '浔阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (360421, 360400, 3, '九江县', 0, 100);
INSERT INTO `jshop_area` VALUES (360423, 360400, 3, '武宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (360424, 360400, 3, '修水县', 0, 100);
INSERT INTO `jshop_area` VALUES (360425, 360400, 3, '永修县', 0, 100);
INSERT INTO `jshop_area` VALUES (360426, 360400, 3, '德安县', 0, 100);
INSERT INTO `jshop_area` VALUES (360428, 360400, 3, '都昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (360429, 360400, 3, '湖口县', 0, 100);
INSERT INTO `jshop_area` VALUES (360430, 360400, 3, '彭泽县', 0, 100);
INSERT INTO `jshop_area` VALUES (360481, 360400, 3, '瑞昌市', 0, 100);
INSERT INTO `jshop_area` VALUES (360482, 360400, 3, '共青城市', 0, 100);
INSERT INTO `jshop_area` VALUES (360483, 360400, 3, '庐山市', 0, 100);
INSERT INTO `jshop_area` VALUES (360500, 360000, 2, '新余市', 0, 100);
INSERT INTO `jshop_area` VALUES (360501, 360500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360502, 360500, 3, '渝水区', 0, 100);
INSERT INTO `jshop_area` VALUES (360521, 360500, 3, '分宜县', 0, 100);
INSERT INTO `jshop_area` VALUES (360600, 360000, 2, '鹰潭市', 0, 100);
INSERT INTO `jshop_area` VALUES (360601, 360600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360602, 360600, 3, '月湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360622, 360600, 3, '余江县', 0, 100);
INSERT INTO `jshop_area` VALUES (360681, 360600, 3, '贵溪市', 0, 100);
INSERT INTO `jshop_area` VALUES (360700, 360000, 2, '赣州市', 0, 100);
INSERT INTO `jshop_area` VALUES (360701, 360700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360702, 360700, 3, '章贡区', 0, 100);
INSERT INTO `jshop_area` VALUES (360703, 360700, 3, '南康区', 0, 100);
INSERT INTO `jshop_area` VALUES (360721, 360700, 3, '赣县', 0, 100);
INSERT INTO `jshop_area` VALUES (360722, 360700, 3, '信丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (360723, 360700, 3, '大余县', 0, 100);
INSERT INTO `jshop_area` VALUES (360724, 360700, 3, '上犹县', 0, 100);
INSERT INTO `jshop_area` VALUES (360725, 360700, 3, '崇义县', 0, 100);
INSERT INTO `jshop_area` VALUES (360726, 360700, 3, '安远县', 0, 100);
INSERT INTO `jshop_area` VALUES (360727, 360700, 3, '龙南县', 0, 100);
INSERT INTO `jshop_area` VALUES (360728, 360700, 3, '定南县', 0, 100);
INSERT INTO `jshop_area` VALUES (360729, 360700, 3, '全南县', 0, 100);
INSERT INTO `jshop_area` VALUES (360730, 360700, 3, '宁都县', 0, 100);
INSERT INTO `jshop_area` VALUES (360731, 360700, 3, '于都县', 0, 100);
INSERT INTO `jshop_area` VALUES (360732, 360700, 3, '兴国县', 0, 100);
INSERT INTO `jshop_area` VALUES (360733, 360700, 3, '会昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (360734, 360700, 3, '寻乌县', 0, 100);
INSERT INTO `jshop_area` VALUES (360735, 360700, 3, '石城县', 0, 100);
INSERT INTO `jshop_area` VALUES (360781, 360700, 3, '瑞金市', 0, 100);
INSERT INTO `jshop_area` VALUES (360800, 360000, 2, '吉安市', 0, 100);
INSERT INTO `jshop_area` VALUES (360801, 360800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360802, 360800, 3, '吉州区', 0, 100);
INSERT INTO `jshop_area` VALUES (360803, 360800, 3, '青原区', 0, 100);
INSERT INTO `jshop_area` VALUES (360821, 360800, 3, '吉安县', 0, 100);
INSERT INTO `jshop_area` VALUES (360822, 360800, 3, '吉水县', 0, 100);
INSERT INTO `jshop_area` VALUES (360823, 360800, 3, '峡江县', 0, 100);
INSERT INTO `jshop_area` VALUES (360824, 360800, 3, '新干县', 0, 100);
INSERT INTO `jshop_area` VALUES (360825, 360800, 3, '永丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (360826, 360800, 3, '泰和县', 0, 100);
INSERT INTO `jshop_area` VALUES (360827, 360800, 3, '遂川县', 0, 100);
INSERT INTO `jshop_area` VALUES (360828, 360800, 3, '万安县', 0, 100);
INSERT INTO `jshop_area` VALUES (360829, 360800, 3, '安福县', 0, 100);
INSERT INTO `jshop_area` VALUES (360830, 360800, 3, '永新县', 0, 100);
INSERT INTO `jshop_area` VALUES (360881, 360800, 3, '井冈山市', 0, 100);
INSERT INTO `jshop_area` VALUES (360900, 360000, 2, '宜春市', 0, 100);
INSERT INTO `jshop_area` VALUES (360901, 360900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (360902, 360900, 3, '袁州区', 0, 100);
INSERT INTO `jshop_area` VALUES (360921, 360900, 3, '奉新县', 0, 100);
INSERT INTO `jshop_area` VALUES (360922, 360900, 3, '万载县', 0, 100);
INSERT INTO `jshop_area` VALUES (360923, 360900, 3, '上高县', 0, 100);
INSERT INTO `jshop_area` VALUES (360924, 360900, 3, '宜丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (360925, 360900, 3, '靖安县', 0, 100);
INSERT INTO `jshop_area` VALUES (360926, 360900, 3, '铜鼓县', 0, 100);
INSERT INTO `jshop_area` VALUES (360981, 360900, 3, '丰城市', 0, 100);
INSERT INTO `jshop_area` VALUES (360982, 360900, 3, '樟树市', 0, 100);
INSERT INTO `jshop_area` VALUES (360983, 360900, 3, '高安市', 0, 100);
INSERT INTO `jshop_area` VALUES (361000, 360000, 2, '抚州市', 0, 100);
INSERT INTO `jshop_area` VALUES (361001, 361000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (361002, 361000, 3, '临川区', 0, 100);
INSERT INTO `jshop_area` VALUES (361021, 361000, 3, '南城县', 0, 100);
INSERT INTO `jshop_area` VALUES (361022, 361000, 3, '黎川县', 0, 100);
INSERT INTO `jshop_area` VALUES (361023, 361000, 3, '南丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (361024, 361000, 3, '崇仁县', 0, 100);
INSERT INTO `jshop_area` VALUES (361025, 361000, 3, '乐安县', 0, 100);
INSERT INTO `jshop_area` VALUES (361026, 361000, 3, '宜黄县', 0, 100);
INSERT INTO `jshop_area` VALUES (361027, 361000, 3, '金溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (361028, 361000, 3, '资溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (361029, 361000, 3, '东乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (361030, 361000, 3, '广昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (361100, 360000, 2, '上饶市', 0, 100);
INSERT INTO `jshop_area` VALUES (361101, 361100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (361102, 361100, 3, '信州区', 0, 100);
INSERT INTO `jshop_area` VALUES (361103, 361100, 3, '广丰区', 0, 100);
INSERT INTO `jshop_area` VALUES (361121, 361100, 3, '上饶县', 0, 100);
INSERT INTO `jshop_area` VALUES (361123, 361100, 3, '玉山县', 0, 100);
INSERT INTO `jshop_area` VALUES (361124, 361100, 3, '铅山县', 0, 100);
INSERT INTO `jshop_area` VALUES (361125, 361100, 3, '横峰县', 0, 100);
INSERT INTO `jshop_area` VALUES (361126, 361100, 3, '弋阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (361127, 361100, 3, '余干县', 0, 100);
INSERT INTO `jshop_area` VALUES (361128, 361100, 3, '鄱阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (361129, 361100, 3, '万年县', 0, 100);
INSERT INTO `jshop_area` VALUES (361130, 361100, 3, '婺源县', 0, 100);
INSERT INTO `jshop_area` VALUES (361181, 361100, 3, '德兴市', 0, 100);
INSERT INTO `jshop_area` VALUES (370000, 0, 1, '山东省', 0, 100);
INSERT INTO `jshop_area` VALUES (370100, 370000, 2, '济南市', 0, 100);
INSERT INTO `jshop_area` VALUES (370101, 370100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370102, 370100, 3, '历下区', 0, 100);
INSERT INTO `jshop_area` VALUES (370103, 370100, 3, '市中区', 0, 100);
INSERT INTO `jshop_area` VALUES (370104, 370100, 3, '槐荫区', 0, 100);
INSERT INTO `jshop_area` VALUES (370105, 370100, 3, '天桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (370112, 370100, 3, '历城区', 0, 100);
INSERT INTO `jshop_area` VALUES (370113, 370100, 3, '长清区', 0, 100);
INSERT INTO `jshop_area` VALUES (370124, 370100, 3, '平阴县', 0, 100);
INSERT INTO `jshop_area` VALUES (370125, 370100, 3, '济阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (370126, 370100, 3, '商河县', 0, 100);
INSERT INTO `jshop_area` VALUES (370181, 370100, 3, '章丘市', 0, 100);
INSERT INTO `jshop_area` VALUES (370200, 370000, 2, '青岛市', 0, 100);
INSERT INTO `jshop_area` VALUES (370201, 370200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370202, 370200, 3, '市南区', 0, 100);
INSERT INTO `jshop_area` VALUES (370203, 370200, 3, '市北区', 0, 100);
INSERT INTO `jshop_area` VALUES (370211, 370200, 3, '黄岛区', 0, 100);
INSERT INTO `jshop_area` VALUES (370212, 370200, 3, '崂山区', 0, 100);
INSERT INTO `jshop_area` VALUES (370213, 370200, 3, '李沧区', 0, 100);
INSERT INTO `jshop_area` VALUES (370214, 370200, 3, '城阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (370281, 370200, 3, '胶州市', 0, 100);
INSERT INTO `jshop_area` VALUES (370282, 370200, 3, '即墨市', 0, 100);
INSERT INTO `jshop_area` VALUES (370283, 370200, 3, '平度市', 0, 100);
INSERT INTO `jshop_area` VALUES (370285, 370200, 3, '莱西市', 0, 100);
INSERT INTO `jshop_area` VALUES (370300, 370000, 2, '淄博市', 0, 100);
INSERT INTO `jshop_area` VALUES (370301, 370300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370302, 370300, 3, '淄川区', 0, 100);
INSERT INTO `jshop_area` VALUES (370303, 370300, 3, '张店区', 0, 100);
INSERT INTO `jshop_area` VALUES (370304, 370300, 3, '博山区', 0, 100);
INSERT INTO `jshop_area` VALUES (370305, 370300, 3, '临淄区', 0, 100);
INSERT INTO `jshop_area` VALUES (370306, 370300, 3, '周村区', 0, 100);
INSERT INTO `jshop_area` VALUES (370321, 370300, 3, '桓台县', 0, 100);
INSERT INTO `jshop_area` VALUES (370322, 370300, 3, '高青县', 0, 100);
INSERT INTO `jshop_area` VALUES (370323, 370300, 3, '沂源县', 0, 100);
INSERT INTO `jshop_area` VALUES (370400, 370000, 2, '枣庄市', 0, 100);
INSERT INTO `jshop_area` VALUES (370401, 370400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370402, 370400, 3, '市中区', 0, 100);
INSERT INTO `jshop_area` VALUES (370403, 370400, 3, '薛城区', 0, 100);
INSERT INTO `jshop_area` VALUES (370404, 370400, 3, '峄城区', 0, 100);
INSERT INTO `jshop_area` VALUES (370405, 370400, 3, '台儿庄区', 0, 100);
INSERT INTO `jshop_area` VALUES (370406, 370400, 3, '山亭区', 0, 100);
INSERT INTO `jshop_area` VALUES (370481, 370400, 3, '滕州市', 0, 100);
INSERT INTO `jshop_area` VALUES (370500, 370000, 2, '东营市', 0, 100);
INSERT INTO `jshop_area` VALUES (370501, 370500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370502, 370500, 3, '东营区', 0, 100);
INSERT INTO `jshop_area` VALUES (370503, 370500, 3, '河口区', 0, 100);
INSERT INTO `jshop_area` VALUES (370505, 370500, 3, '垦利区', 0, 100);
INSERT INTO `jshop_area` VALUES (370522, 370500, 3, '利津县', 0, 100);
INSERT INTO `jshop_area` VALUES (370523, 370500, 3, '广饶县', 0, 100);
INSERT INTO `jshop_area` VALUES (370600, 370000, 2, '烟台市', 0, 100);
INSERT INTO `jshop_area` VALUES (370601, 370600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370602, 370600, 3, '芝罘区', 0, 100);
INSERT INTO `jshop_area` VALUES (370611, 370600, 3, '福山区', 0, 100);
INSERT INTO `jshop_area` VALUES (370612, 370600, 3, '牟平区', 0, 100);
INSERT INTO `jshop_area` VALUES (370613, 370600, 3, '莱山区', 0, 100);
INSERT INTO `jshop_area` VALUES (370634, 370600, 3, '长岛县', 0, 100);
INSERT INTO `jshop_area` VALUES (370681, 370600, 3, '龙口市', 0, 100);
INSERT INTO `jshop_area` VALUES (370682, 370600, 3, '莱阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (370683, 370600, 3, '莱州市', 0, 100);
INSERT INTO `jshop_area` VALUES (370684, 370600, 3, '蓬莱市', 0, 100);
INSERT INTO `jshop_area` VALUES (370685, 370600, 3, '招远市', 0, 100);
INSERT INTO `jshop_area` VALUES (370686, 370600, 3, '栖霞市', 0, 100);
INSERT INTO `jshop_area` VALUES (370687, 370600, 3, '海阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (370700, 370000, 2, '潍坊市', 0, 100);
INSERT INTO `jshop_area` VALUES (370701, 370700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370702, 370700, 3, '潍城区', 0, 100);
INSERT INTO `jshop_area` VALUES (370703, 370700, 3, '寒亭区', 0, 100);
INSERT INTO `jshop_area` VALUES (370704, 370700, 3, '坊子区', 0, 100);
INSERT INTO `jshop_area` VALUES (370705, 370700, 3, '奎文区', 0, 100);
INSERT INTO `jshop_area` VALUES (370724, 370700, 3, '临朐县', 0, 100);
INSERT INTO `jshop_area` VALUES (370725, 370700, 3, '昌乐县', 0, 100);
INSERT INTO `jshop_area` VALUES (370781, 370700, 3, '青州市', 0, 100);
INSERT INTO `jshop_area` VALUES (370782, 370700, 3, '诸城市', 0, 100);
INSERT INTO `jshop_area` VALUES (370783, 370700, 3, '寿光市', 0, 100);
INSERT INTO `jshop_area` VALUES (370784, 370700, 3, '安丘市', 0, 100);
INSERT INTO `jshop_area` VALUES (370785, 370700, 3, '高密市', 0, 100);
INSERT INTO `jshop_area` VALUES (370786, 370700, 3, '昌邑市', 0, 100);
INSERT INTO `jshop_area` VALUES (370800, 370000, 2, '济宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (370801, 370800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370811, 370800, 3, '任城区', 0, 100);
INSERT INTO `jshop_area` VALUES (370812, 370800, 3, '兖州区', 0, 100);
INSERT INTO `jshop_area` VALUES (370826, 370800, 3, '微山县', 0, 100);
INSERT INTO `jshop_area` VALUES (370827, 370800, 3, '鱼台县', 0, 100);
INSERT INTO `jshop_area` VALUES (370828, 370800, 3, '金乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (370829, 370800, 3, '嘉祥县', 0, 100);
INSERT INTO `jshop_area` VALUES (370830, 370800, 3, '汶上县', 0, 100);
INSERT INTO `jshop_area` VALUES (370831, 370800, 3, '泗水县', 0, 100);
INSERT INTO `jshop_area` VALUES (370832, 370800, 3, '梁山县', 0, 100);
INSERT INTO `jshop_area` VALUES (370881, 370800, 3, '曲阜市', 0, 100);
INSERT INTO `jshop_area` VALUES (370883, 370800, 3, '邹城市', 0, 100);
INSERT INTO `jshop_area` VALUES (370900, 370000, 2, '泰安市', 0, 100);
INSERT INTO `jshop_area` VALUES (370901, 370900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (370902, 370900, 3, '泰山区', 0, 100);
INSERT INTO `jshop_area` VALUES (370911, 370900, 3, '岱岳区', 0, 100);
INSERT INTO `jshop_area` VALUES (370921, 370900, 3, '宁阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (370923, 370900, 3, '东平县', 0, 100);
INSERT INTO `jshop_area` VALUES (370982, 370900, 3, '新泰市', 0, 100);
INSERT INTO `jshop_area` VALUES (370983, 370900, 3, '肥城市', 0, 100);
INSERT INTO `jshop_area` VALUES (371000, 370000, 2, '威海市', 0, 100);
INSERT INTO `jshop_area` VALUES (371001, 371000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (371002, 371000, 3, '环翠区', 0, 100);
INSERT INTO `jshop_area` VALUES (371003, 371000, 3, '文登区', 0, 100);
INSERT INTO `jshop_area` VALUES (371082, 371000, 3, '荣成市', 0, 100);
INSERT INTO `jshop_area` VALUES (371083, 371000, 3, '乳山市', 0, 100);
INSERT INTO `jshop_area` VALUES (371100, 370000, 2, '日照市', 0, 100);
INSERT INTO `jshop_area` VALUES (371101, 371100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (371102, 371100, 3, '东港区', 0, 100);
INSERT INTO `jshop_area` VALUES (371103, 371100, 3, '岚山区', 0, 100);
INSERT INTO `jshop_area` VALUES (371121, 371100, 3, '五莲县', 0, 100);
INSERT INTO `jshop_area` VALUES (371122, 371100, 3, '莒县', 0, 100);
INSERT INTO `jshop_area` VALUES (371200, 370000, 2, '莱芜市', 0, 100);
INSERT INTO `jshop_area` VALUES (371201, 371200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (371202, 371200, 3, '莱城区', 0, 100);
INSERT INTO `jshop_area` VALUES (371203, 371200, 3, '钢城区', 0, 100);
INSERT INTO `jshop_area` VALUES (371300, 370000, 2, '临沂市', 0, 100);
INSERT INTO `jshop_area` VALUES (371301, 371300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (371302, 371300, 3, '兰山区', 0, 100);
INSERT INTO `jshop_area` VALUES (371311, 371300, 3, '罗庄区', 0, 100);
INSERT INTO `jshop_area` VALUES (371312, 371300, 3, '河东区', 0, 100);
INSERT INTO `jshop_area` VALUES (371321, 371300, 3, '沂南县', 0, 100);
INSERT INTO `jshop_area` VALUES (371322, 371300, 3, '郯城县', 0, 100);
INSERT INTO `jshop_area` VALUES (371323, 371300, 3, '沂水县', 0, 100);
INSERT INTO `jshop_area` VALUES (371324, 371300, 3, '兰陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (371325, 371300, 3, '费县', 0, 100);
INSERT INTO `jshop_area` VALUES (371326, 371300, 3, '平邑县', 0, 100);
INSERT INTO `jshop_area` VALUES (371327, 371300, 3, '莒南县', 0, 100);
INSERT INTO `jshop_area` VALUES (371328, 371300, 3, '蒙阴县', 0, 100);
INSERT INTO `jshop_area` VALUES (371329, 371300, 3, '临沭县', 0, 100);
INSERT INTO `jshop_area` VALUES (371400, 370000, 2, '德州市', 0, 100);
INSERT INTO `jshop_area` VALUES (371401, 371400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (371402, 371400, 3, '德城区', 0, 100);
INSERT INTO `jshop_area` VALUES (371403, 371400, 3, '陵城区', 0, 100);
INSERT INTO `jshop_area` VALUES (371422, 371400, 3, '宁津县', 0, 100);
INSERT INTO `jshop_area` VALUES (371423, 371400, 3, '庆云县', 0, 100);
INSERT INTO `jshop_area` VALUES (371424, 371400, 3, '临邑县', 0, 100);
INSERT INTO `jshop_area` VALUES (371425, 371400, 3, '齐河县', 0, 100);
INSERT INTO `jshop_area` VALUES (371426, 371400, 3, '平原县', 0, 100);
INSERT INTO `jshop_area` VALUES (371427, 371400, 3, '夏津县', 0, 100);
INSERT INTO `jshop_area` VALUES (371428, 371400, 3, '武城县', 0, 100);
INSERT INTO `jshop_area` VALUES (371481, 371400, 3, '乐陵市', 0, 100);
INSERT INTO `jshop_area` VALUES (371482, 371400, 3, '禹城市', 0, 100);
INSERT INTO `jshop_area` VALUES (371500, 370000, 2, '聊城市', 0, 100);
INSERT INTO `jshop_area` VALUES (371501, 371500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (371502, 371500, 3, '东昌府区', 0, 100);
INSERT INTO `jshop_area` VALUES (371521, 371500, 3, '阳谷县', 0, 100);
INSERT INTO `jshop_area` VALUES (371522, 371500, 3, '莘县', 0, 100);
INSERT INTO `jshop_area` VALUES (371523, 371500, 3, '茌平县', 0, 100);
INSERT INTO `jshop_area` VALUES (371524, 371500, 3, '东阿县', 0, 100);
INSERT INTO `jshop_area` VALUES (371525, 371500, 3, '冠县', 0, 100);
INSERT INTO `jshop_area` VALUES (371526, 371500, 3, '高唐县', 0, 100);
INSERT INTO `jshop_area` VALUES (371581, 371500, 3, '临清市', 0, 100);
INSERT INTO `jshop_area` VALUES (371600, 370000, 2, '滨州市', 0, 100);
INSERT INTO `jshop_area` VALUES (371601, 371600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (371602, 371600, 3, '滨城区', 0, 100);
INSERT INTO `jshop_area` VALUES (371603, 371600, 3, '沾化区', 0, 100);
INSERT INTO `jshop_area` VALUES (371621, 371600, 3, '惠民县', 0, 100);
INSERT INTO `jshop_area` VALUES (371622, 371600, 3, '阳信县', 0, 100);
INSERT INTO `jshop_area` VALUES (371623, 371600, 3, '无棣县', 0, 100);
INSERT INTO `jshop_area` VALUES (371625, 371600, 3, '博兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (371626, 371600, 3, '邹平县', 0, 100);
INSERT INTO `jshop_area` VALUES (371700, 370000, 2, '菏泽市', 0, 100);
INSERT INTO `jshop_area` VALUES (371701, 371700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (371702, 371700, 3, '牡丹区', 0, 100);
INSERT INTO `jshop_area` VALUES (371703, 371700, 3, '定陶区', 0, 100);
INSERT INTO `jshop_area` VALUES (371721, 371700, 3, '曹县', 0, 100);
INSERT INTO `jshop_area` VALUES (371722, 371700, 3, '单县', 0, 100);
INSERT INTO `jshop_area` VALUES (371723, 371700, 3, '成武县', 0, 100);
INSERT INTO `jshop_area` VALUES (371724, 371700, 3, '巨野县', 0, 100);
INSERT INTO `jshop_area` VALUES (371725, 371700, 3, '郓城县', 0, 100);
INSERT INTO `jshop_area` VALUES (371726, 371700, 3, '鄄城县', 0, 100);
INSERT INTO `jshop_area` VALUES (371728, 371700, 3, '东明县', 0, 100);
INSERT INTO `jshop_area` VALUES (410000, 0, 1, '河南省', 0, 100);
INSERT INTO `jshop_area` VALUES (410100, 410000, 2, '郑州市', 0, 100);
INSERT INTO `jshop_area` VALUES (410101, 410100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410102, 410100, 3, '中原区', 0, 100);
INSERT INTO `jshop_area` VALUES (410103, 410100, 3, '二七区', 0, 100);
INSERT INTO `jshop_area` VALUES (410104, 410100, 3, '管城回族区', 0, 100);
INSERT INTO `jshop_area` VALUES (410105, 410100, 3, '金水区', 0, 100);
INSERT INTO `jshop_area` VALUES (410106, 410100, 3, '上街区', 0, 100);
INSERT INTO `jshop_area` VALUES (410108, 410100, 3, '惠济区', 0, 100);
INSERT INTO `jshop_area` VALUES (410122, 410100, 3, '中牟县', 0, 100);
INSERT INTO `jshop_area` VALUES (410181, 410100, 3, '巩义市', 0, 100);
INSERT INTO `jshop_area` VALUES (410182, 410100, 3, '荥阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (410183, 410100, 3, '新密市', 0, 100);
INSERT INTO `jshop_area` VALUES (410184, 410100, 3, '新郑市', 0, 100);
INSERT INTO `jshop_area` VALUES (410185, 410100, 3, '登封市', 0, 100);
INSERT INTO `jshop_area` VALUES (410200, 410000, 2, '开封市', 0, 100);
INSERT INTO `jshop_area` VALUES (410201, 410200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410202, 410200, 3, '龙亭区', 0, 100);
INSERT INTO `jshop_area` VALUES (410203, 410200, 3, '顺河回族区', 0, 100);
INSERT INTO `jshop_area` VALUES (410204, 410200, 3, '鼓楼区', 0, 100);
INSERT INTO `jshop_area` VALUES (410205, 410200, 3, '禹王台区', 0, 100);
INSERT INTO `jshop_area` VALUES (410211, 410200, 3, '金明区', 0, 100);
INSERT INTO `jshop_area` VALUES (410212, 410200, 3, '祥符区', 0, 100);
INSERT INTO `jshop_area` VALUES (410221, 410200, 3, '杞县', 0, 100);
INSERT INTO `jshop_area` VALUES (410222, 410200, 3, '通许县', 0, 100);
INSERT INTO `jshop_area` VALUES (410223, 410200, 3, '尉氏县', 0, 100);
INSERT INTO `jshop_area` VALUES (410225, 410200, 3, '兰考县', 0, 100);
INSERT INTO `jshop_area` VALUES (410300, 410000, 2, '洛阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (410301, 410300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410302, 410300, 3, '老城区', 0, 100);
INSERT INTO `jshop_area` VALUES (410303, 410300, 3, '西工区', 0, 100);
INSERT INTO `jshop_area` VALUES (410304, 410300, 3, '瀍河回族区', 0, 100);
INSERT INTO `jshop_area` VALUES (410305, 410300, 3, '涧西区', 0, 100);
INSERT INTO `jshop_area` VALUES (410306, 410300, 3, '吉利区', 0, 100);
INSERT INTO `jshop_area` VALUES (410311, 410300, 3, '洛龙区', 0, 100);
INSERT INTO `jshop_area` VALUES (410322, 410300, 3, '孟津县', 0, 100);
INSERT INTO `jshop_area` VALUES (410323, 410300, 3, '新安县', 0, 100);
INSERT INTO `jshop_area` VALUES (410324, 410300, 3, '栾川县', 0, 100);
INSERT INTO `jshop_area` VALUES (410325, 410300, 3, '嵩县', 0, 100);
INSERT INTO `jshop_area` VALUES (410326, 410300, 3, '汝阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (410327, 410300, 3, '宜阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (410328, 410300, 3, '洛宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (410329, 410300, 3, '伊川县', 0, 100);
INSERT INTO `jshop_area` VALUES (410381, 410300, 3, '偃师市', 0, 100);
INSERT INTO `jshop_area` VALUES (410400, 410000, 2, '平顶山市', 0, 100);
INSERT INTO `jshop_area` VALUES (410401, 410400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410402, 410400, 3, '新华区', 0, 100);
INSERT INTO `jshop_area` VALUES (410403, 410400, 3, '卫东区', 0, 100);
INSERT INTO `jshop_area` VALUES (410404, 410400, 3, '石龙区', 0, 100);
INSERT INTO `jshop_area` VALUES (410411, 410400, 3, '湛河区', 0, 100);
INSERT INTO `jshop_area` VALUES (410421, 410400, 3, '宝丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (410422, 410400, 3, '叶县', 0, 100);
INSERT INTO `jshop_area` VALUES (410423, 410400, 3, '鲁山县', 0, 100);
INSERT INTO `jshop_area` VALUES (410425, 410400, 3, '郏县', 0, 100);
INSERT INTO `jshop_area` VALUES (410481, 410400, 3, '舞钢市', 0, 100);
INSERT INTO `jshop_area` VALUES (410482, 410400, 3, '汝州市', 0, 100);
INSERT INTO `jshop_area` VALUES (410500, 410000, 2, '安阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (410501, 410500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410502, 410500, 3, '文峰区', 0, 100);
INSERT INTO `jshop_area` VALUES (410503, 410500, 3, '北关区', 0, 100);
INSERT INTO `jshop_area` VALUES (410505, 410500, 3, '殷都区', 0, 100);
INSERT INTO `jshop_area` VALUES (410506, 410500, 3, '龙安区', 0, 100);
INSERT INTO `jshop_area` VALUES (410522, 410500, 3, '安阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (410523, 410500, 3, '汤阴县', 0, 100);
INSERT INTO `jshop_area` VALUES (410526, 410500, 3, '滑县', 0, 100);
INSERT INTO `jshop_area` VALUES (410527, 410500, 3, '内黄县', 0, 100);
INSERT INTO `jshop_area` VALUES (410581, 410500, 3, '林州市', 0, 100);
INSERT INTO `jshop_area` VALUES (410600, 410000, 2, '鹤壁市', 0, 100);
INSERT INTO `jshop_area` VALUES (410601, 410600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410602, 410600, 3, '鹤山区', 0, 100);
INSERT INTO `jshop_area` VALUES (410603, 410600, 3, '山城区', 0, 100);
INSERT INTO `jshop_area` VALUES (410611, 410600, 3, '淇滨区', 0, 100);
INSERT INTO `jshop_area` VALUES (410621, 410600, 3, '浚县', 0, 100);
INSERT INTO `jshop_area` VALUES (410622, 410600, 3, '淇县', 0, 100);
INSERT INTO `jshop_area` VALUES (410700, 410000, 2, '新乡市', 0, 100);
INSERT INTO `jshop_area` VALUES (410701, 410700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410702, 410700, 3, '红旗区', 0, 100);
INSERT INTO `jshop_area` VALUES (410703, 410700, 3, '卫滨区', 0, 100);
INSERT INTO `jshop_area` VALUES (410704, 410700, 3, '凤泉区', 0, 100);
INSERT INTO `jshop_area` VALUES (410711, 410700, 3, '牧野区', 0, 100);
INSERT INTO `jshop_area` VALUES (410721, 410700, 3, '新乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (410724, 410700, 3, '获嘉县', 0, 100);
INSERT INTO `jshop_area` VALUES (410725, 410700, 3, '原阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (410726, 410700, 3, '延津县', 0, 100);
INSERT INTO `jshop_area` VALUES (410727, 410700, 3, '封丘县', 0, 100);
INSERT INTO `jshop_area` VALUES (410728, 410700, 3, '长垣县', 0, 100);
INSERT INTO `jshop_area` VALUES (410781, 410700, 3, '卫辉市', 0, 100);
INSERT INTO `jshop_area` VALUES (410782, 410700, 3, '辉县市', 0, 100);
INSERT INTO `jshop_area` VALUES (410800, 410000, 2, '焦作市', 0, 100);
INSERT INTO `jshop_area` VALUES (410801, 410800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410802, 410800, 3, '解放区', 0, 100);
INSERT INTO `jshop_area` VALUES (410803, 410800, 3, '中站区', 0, 100);
INSERT INTO `jshop_area` VALUES (410804, 410800, 3, '马村区', 0, 100);
INSERT INTO `jshop_area` VALUES (410811, 410800, 3, '山阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (410821, 410800, 3, '修武县', 0, 100);
INSERT INTO `jshop_area` VALUES (410822, 410800, 3, '博爱县', 0, 100);
INSERT INTO `jshop_area` VALUES (410823, 410800, 3, '武陟县', 0, 100);
INSERT INTO `jshop_area` VALUES (410825, 410800, 3, '温县', 0, 100);
INSERT INTO `jshop_area` VALUES (410882, 410800, 3, '沁阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (410883, 410800, 3, '孟州市', 0, 100);
INSERT INTO `jshop_area` VALUES (410900, 410000, 2, '濮阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (410901, 410900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (410902, 410900, 3, '华龙区', 0, 100);
INSERT INTO `jshop_area` VALUES (410922, 410900, 3, '清丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (410923, 410900, 3, '南乐县', 0, 100);
INSERT INTO `jshop_area` VALUES (410926, 410900, 3, '范县', 0, 100);
INSERT INTO `jshop_area` VALUES (410927, 410900, 3, '台前县', 0, 100);
INSERT INTO `jshop_area` VALUES (410928, 410900, 3, '濮阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (411000, 410000, 2, '许昌市', 0, 100);
INSERT INTO `jshop_area` VALUES (411001, 411000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (411002, 411000, 3, '魏都区', 0, 100);
INSERT INTO `jshop_area` VALUES (411023, 411000, 3, '许昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (411024, 411000, 3, '鄢陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (411025, 411000, 3, '襄城县', 0, 100);
INSERT INTO `jshop_area` VALUES (411081, 411000, 3, '禹州市', 0, 100);
INSERT INTO `jshop_area` VALUES (411082, 411000, 3, '长葛市', 0, 100);
INSERT INTO `jshop_area` VALUES (411100, 410000, 2, '漯河市', 0, 100);
INSERT INTO `jshop_area` VALUES (411101, 411100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (411102, 411100, 3, '源汇区', 0, 100);
INSERT INTO `jshop_area` VALUES (411103, 411100, 3, '郾城区', 0, 100);
INSERT INTO `jshop_area` VALUES (411104, 411100, 3, '召陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (411121, 411100, 3, '舞阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (411122, 411100, 3, '临颍县', 0, 100);
INSERT INTO `jshop_area` VALUES (411200, 410000, 2, '三门峡市', 0, 100);
INSERT INTO `jshop_area` VALUES (411201, 411200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (411202, 411200, 3, '湖滨区', 0, 100);
INSERT INTO `jshop_area` VALUES (411203, 411200, 3, '陕州区', 0, 100);
INSERT INTO `jshop_area` VALUES (411221, 411200, 3, '渑池县', 0, 100);
INSERT INTO `jshop_area` VALUES (411224, 411200, 3, '卢氏县', 0, 100);
INSERT INTO `jshop_area` VALUES (411281, 411200, 3, '义马市', 0, 100);
INSERT INTO `jshop_area` VALUES (411282, 411200, 3, '灵宝市', 0, 100);
INSERT INTO `jshop_area` VALUES (411300, 410000, 2, '南阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (411301, 411300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (411302, 411300, 3, '宛城区', 0, 100);
INSERT INTO `jshop_area` VALUES (411303, 411300, 3, '卧龙区', 0, 100);
INSERT INTO `jshop_area` VALUES (411321, 411300, 3, '南召县', 0, 100);
INSERT INTO `jshop_area` VALUES (411322, 411300, 3, '方城县', 0, 100);
INSERT INTO `jshop_area` VALUES (411323, 411300, 3, '西峡县', 0, 100);
INSERT INTO `jshop_area` VALUES (411324, 411300, 3, '镇平县', 0, 100);
INSERT INTO `jshop_area` VALUES (411325, 411300, 3, '内乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (411326, 411300, 3, '淅川县', 0, 100);
INSERT INTO `jshop_area` VALUES (411327, 411300, 3, '社旗县', 0, 100);
INSERT INTO `jshop_area` VALUES (411328, 411300, 3, '唐河县', 0, 100);
INSERT INTO `jshop_area` VALUES (411329, 411300, 3, '新野县', 0, 100);
INSERT INTO `jshop_area` VALUES (411330, 411300, 3, '桐柏县', 0, 100);
INSERT INTO `jshop_area` VALUES (411381, 411300, 3, '邓州市', 0, 100);
INSERT INTO `jshop_area` VALUES (411400, 410000, 2, '商丘市', 0, 100);
INSERT INTO `jshop_area` VALUES (411401, 411400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (411402, 411400, 3, '梁园区', 0, 100);
INSERT INTO `jshop_area` VALUES (411403, 411400, 3, '睢阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (411421, 411400, 3, '民权县', 0, 100);
INSERT INTO `jshop_area` VALUES (411422, 411400, 3, '睢县', 0, 100);
INSERT INTO `jshop_area` VALUES (411423, 411400, 3, '宁陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (411424, 411400, 3, '柘城县', 0, 100);
INSERT INTO `jshop_area` VALUES (411425, 411400, 3, '虞城县', 0, 100);
INSERT INTO `jshop_area` VALUES (411426, 411400, 3, '夏邑县', 0, 100);
INSERT INTO `jshop_area` VALUES (411481, 411400, 3, '永城市', 0, 100);
INSERT INTO `jshop_area` VALUES (411500, 410000, 2, '信阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (411501, 411500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (411502, 411500, 3, '浉河区', 0, 100);
INSERT INTO `jshop_area` VALUES (411503, 411500, 3, '平桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (411521, 411500, 3, '罗山县', 0, 100);
INSERT INTO `jshop_area` VALUES (411522, 411500, 3, '光山县', 0, 100);
INSERT INTO `jshop_area` VALUES (411523, 411500, 3, '新县', 0, 100);
INSERT INTO `jshop_area` VALUES (411524, 411500, 3, '商城县', 0, 100);
INSERT INTO `jshop_area` VALUES (411525, 411500, 3, '固始县', 0, 100);
INSERT INTO `jshop_area` VALUES (411526, 411500, 3, '潢川县', 0, 100);
INSERT INTO `jshop_area` VALUES (411527, 411500, 3, '淮滨县', 0, 100);
INSERT INTO `jshop_area` VALUES (411528, 411500, 3, '息县', 0, 100);
INSERT INTO `jshop_area` VALUES (411600, 410000, 2, '周口市', 0, 100);
INSERT INTO `jshop_area` VALUES (411601, 411600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (411602, 411600, 3, '川汇区', 0, 100);
INSERT INTO `jshop_area` VALUES (411621, 411600, 3, '扶沟县', 0, 100);
INSERT INTO `jshop_area` VALUES (411622, 411600, 3, '西华县', 0, 100);
INSERT INTO `jshop_area` VALUES (411623, 411600, 3, '商水县', 0, 100);
INSERT INTO `jshop_area` VALUES (411624, 411600, 3, '沈丘县', 0, 100);
INSERT INTO `jshop_area` VALUES (411625, 411600, 3, '郸城县', 0, 100);
INSERT INTO `jshop_area` VALUES (411626, 411600, 3, '淮阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (411627, 411600, 3, '太康县', 0, 100);
INSERT INTO `jshop_area` VALUES (411628, 411600, 3, '鹿邑县', 0, 100);
INSERT INTO `jshop_area` VALUES (411681, 411600, 3, '项城市', 0, 100);
INSERT INTO `jshop_area` VALUES (411700, 410000, 2, '驻马店市', 0, 100);
INSERT INTO `jshop_area` VALUES (411701, 411700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (411702, 411700, 3, '驿城区', 0, 100);
INSERT INTO `jshop_area` VALUES (411721, 411700, 3, '西平县', 0, 100);
INSERT INTO `jshop_area` VALUES (411722, 411700, 3, '上蔡县', 0, 100);
INSERT INTO `jshop_area` VALUES (411723, 411700, 3, '平舆县', 0, 100);
INSERT INTO `jshop_area` VALUES (411724, 411700, 3, '正阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (411725, 411700, 3, '确山县', 0, 100);
INSERT INTO `jshop_area` VALUES (411726, 411700, 3, '泌阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (411727, 411700, 3, '汝南县', 0, 100);
INSERT INTO `jshop_area` VALUES (411728, 411700, 3, '遂平县', 0, 100);
INSERT INTO `jshop_area` VALUES (411729, 411700, 3, '新蔡县', 0, 100);
INSERT INTO `jshop_area` VALUES (419000, 410000, 2, '省直辖县级行政区划', 0, 100);
INSERT INTO `jshop_area` VALUES (419001, 419000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420000, 0, 1, '湖北省', 0, 100);
INSERT INTO `jshop_area` VALUES (420100, 420000, 2, '武汉市', 0, 100);
INSERT INTO `jshop_area` VALUES (420101, 420100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420102, 420100, 3, '江岸区', 0, 100);
INSERT INTO `jshop_area` VALUES (420103, 420100, 3, '江汉区', 0, 100);
INSERT INTO `jshop_area` VALUES (420104, 420100, 3, '硚口区', 0, 100);
INSERT INTO `jshop_area` VALUES (420105, 420100, 3, '汉阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (420106, 420100, 3, '武昌区', 0, 100);
INSERT INTO `jshop_area` VALUES (420107, 420100, 3, '青山区', 0, 100);
INSERT INTO `jshop_area` VALUES (420111, 420100, 3, '洪山区', 0, 100);
INSERT INTO `jshop_area` VALUES (420112, 420100, 3, '东西湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420113, 420100, 3, '汉南区', 0, 100);
INSERT INTO `jshop_area` VALUES (420114, 420100, 3, '蔡甸区', 0, 100);
INSERT INTO `jshop_area` VALUES (420115, 420100, 3, '江夏区', 0, 100);
INSERT INTO `jshop_area` VALUES (420116, 420100, 3, '黄陂区', 0, 100);
INSERT INTO `jshop_area` VALUES (420117, 420100, 3, '新洲区', 0, 100);
INSERT INTO `jshop_area` VALUES (420200, 420000, 2, '黄石市', 0, 100);
INSERT INTO `jshop_area` VALUES (420201, 420200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420202, 420200, 3, '黄石港区', 0, 100);
INSERT INTO `jshop_area` VALUES (420203, 420200, 3, '西塞山区', 0, 100);
INSERT INTO `jshop_area` VALUES (420204, 420200, 3, '下陆区', 0, 100);
INSERT INTO `jshop_area` VALUES (420205, 420200, 3, '铁山区', 0, 100);
INSERT INTO `jshop_area` VALUES (420222, 420200, 3, '阳新县', 0, 100);
INSERT INTO `jshop_area` VALUES (420281, 420200, 3, '大冶市', 0, 100);
INSERT INTO `jshop_area` VALUES (420300, 420000, 2, '十堰市', 0, 100);
INSERT INTO `jshop_area` VALUES (420301, 420300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420302, 420300, 3, '茅箭区', 0, 100);
INSERT INTO `jshop_area` VALUES (420303, 420300, 3, '张湾区', 0, 100);
INSERT INTO `jshop_area` VALUES (420304, 420300, 3, '郧阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (420322, 420300, 3, '郧西县', 0, 100);
INSERT INTO `jshop_area` VALUES (420323, 420300, 3, '竹山县', 0, 100);
INSERT INTO `jshop_area` VALUES (420324, 420300, 3, '竹溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (420325, 420300, 3, '房县', 0, 100);
INSERT INTO `jshop_area` VALUES (420381, 420300, 3, '丹江口市', 0, 100);
INSERT INTO `jshop_area` VALUES (420500, 420000, 2, '宜昌市', 0, 100);
INSERT INTO `jshop_area` VALUES (420501, 420500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420502, 420500, 3, '西陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (420503, 420500, 3, '伍家岗区', 0, 100);
INSERT INTO `jshop_area` VALUES (420504, 420500, 3, '点军区', 0, 100);
INSERT INTO `jshop_area` VALUES (420505, 420500, 3, '猇亭区', 0, 100);
INSERT INTO `jshop_area` VALUES (420506, 420500, 3, '夷陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (420525, 420500, 3, '远安县', 0, 100);
INSERT INTO `jshop_area` VALUES (420526, 420500, 3, '兴山县', 0, 100);
INSERT INTO `jshop_area` VALUES (420527, 420500, 3, '秭归县', 0, 100);
INSERT INTO `jshop_area` VALUES (420528, 420500, 3, '长阳土家族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (420529, 420500, 3, '五峰土家族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (420581, 420500, 3, '宜都市', 0, 100);
INSERT INTO `jshop_area` VALUES (420582, 420500, 3, '当阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (420583, 420500, 3, '枝江市', 0, 100);
INSERT INTO `jshop_area` VALUES (420600, 420000, 2, '襄阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (420601, 420600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420602, 420600, 3, '襄城区', 0, 100);
INSERT INTO `jshop_area` VALUES (420606, 420600, 3, '樊城区', 0, 100);
INSERT INTO `jshop_area` VALUES (420607, 420600, 3, '襄州区', 0, 100);
INSERT INTO `jshop_area` VALUES (420624, 420600, 3, '南漳县', 0, 100);
INSERT INTO `jshop_area` VALUES (420625, 420600, 3, '谷城县', 0, 100);
INSERT INTO `jshop_area` VALUES (420626, 420600, 3, '保康县', 0, 100);
INSERT INTO `jshop_area` VALUES (420682, 420600, 3, '老河口市', 0, 100);
INSERT INTO `jshop_area` VALUES (420683, 420600, 3, '枣阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (420684, 420600, 3, '宜城市', 0, 100);
INSERT INTO `jshop_area` VALUES (420700, 420000, 2, '鄂州市', 0, 100);
INSERT INTO `jshop_area` VALUES (420701, 420700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420702, 420700, 3, '梁子湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420703, 420700, 3, '华容区', 0, 100);
INSERT INTO `jshop_area` VALUES (420704, 420700, 3, '鄂城区', 0, 100);
INSERT INTO `jshop_area` VALUES (420800, 420000, 2, '荆门市', 0, 100);
INSERT INTO `jshop_area` VALUES (420801, 420800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420802, 420800, 3, '东宝区', 0, 100);
INSERT INTO `jshop_area` VALUES (420804, 420800, 3, '掇刀区', 0, 100);
INSERT INTO `jshop_area` VALUES (420821, 420800, 3, '京山县', 0, 100);
INSERT INTO `jshop_area` VALUES (420822, 420800, 3, '沙洋县', 0, 100);
INSERT INTO `jshop_area` VALUES (420881, 420800, 3, '钟祥市', 0, 100);
INSERT INTO `jshop_area` VALUES (420900, 420000, 2, '孝感市', 0, 100);
INSERT INTO `jshop_area` VALUES (420901, 420900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (420902, 420900, 3, '孝南区', 0, 100);
INSERT INTO `jshop_area` VALUES (420921, 420900, 3, '孝昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (420922, 420900, 3, '大悟县', 0, 100);
INSERT INTO `jshop_area` VALUES (420923, 420900, 3, '云梦县', 0, 100);
INSERT INTO `jshop_area` VALUES (420981, 420900, 3, '应城市', 0, 100);
INSERT INTO `jshop_area` VALUES (420982, 420900, 3, '安陆市', 0, 100);
INSERT INTO `jshop_area` VALUES (420984, 420900, 3, '汉川市', 0, 100);
INSERT INTO `jshop_area` VALUES (421000, 420000, 2, '荆州市', 0, 100);
INSERT INTO `jshop_area` VALUES (421001, 421000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (421002, 421000, 3, '沙市区', 0, 100);
INSERT INTO `jshop_area` VALUES (421003, 421000, 3, '荆州区', 0, 100);
INSERT INTO `jshop_area` VALUES (421022, 421000, 3, '公安县', 0, 100);
INSERT INTO `jshop_area` VALUES (421023, 421000, 3, '监利县', 0, 100);
INSERT INTO `jshop_area` VALUES (421024, 421000, 3, '江陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (421081, 421000, 3, '石首市', 0, 100);
INSERT INTO `jshop_area` VALUES (421083, 421000, 3, '洪湖市', 0, 100);
INSERT INTO `jshop_area` VALUES (421087, 421000, 3, '松滋市', 0, 100);
INSERT INTO `jshop_area` VALUES (421100, 420000, 2, '黄冈市', 0, 100);
INSERT INTO `jshop_area` VALUES (421101, 421100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (421102, 421100, 3, '黄州区', 0, 100);
INSERT INTO `jshop_area` VALUES (421121, 421100, 3, '团风县', 0, 100);
INSERT INTO `jshop_area` VALUES (421122, 421100, 3, '红安县', 0, 100);
INSERT INTO `jshop_area` VALUES (421123, 421100, 3, '罗田县', 0, 100);
INSERT INTO `jshop_area` VALUES (421124, 421100, 3, '英山县', 0, 100);
INSERT INTO `jshop_area` VALUES (421125, 421100, 3, '浠水县', 0, 100);
INSERT INTO `jshop_area` VALUES (421126, 421100, 3, '蕲春县', 0, 100);
INSERT INTO `jshop_area` VALUES (421127, 421100, 3, '黄梅县', 0, 100);
INSERT INTO `jshop_area` VALUES (421181, 421100, 3, '麻城市', 0, 100);
INSERT INTO `jshop_area` VALUES (421182, 421100, 3, '武穴市', 0, 100);
INSERT INTO `jshop_area` VALUES (421200, 420000, 2, '咸宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (421201, 421200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (421202, 421200, 3, '咸安区', 0, 100);
INSERT INTO `jshop_area` VALUES (421221, 421200, 3, '嘉鱼县', 0, 100);
INSERT INTO `jshop_area` VALUES (421222, 421200, 3, '通城县', 0, 100);
INSERT INTO `jshop_area` VALUES (421223, 421200, 3, '崇阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (421224, 421200, 3, '通山县', 0, 100);
INSERT INTO `jshop_area` VALUES (421281, 421200, 3, '赤壁市', 0, 100);
INSERT INTO `jshop_area` VALUES (421300, 420000, 2, '随州市', 0, 100);
INSERT INTO `jshop_area` VALUES (421301, 421300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (421303, 421300, 3, '曾都区', 0, 100);
INSERT INTO `jshop_area` VALUES (421321, 421300, 3, '随县', 0, 100);
INSERT INTO `jshop_area` VALUES (421381, 421300, 3, '广水市', 0, 100);
INSERT INTO `jshop_area` VALUES (422800, 420000, 2, '恩施土家族苗族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (422801, 422800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (422802, 422800, 3, '利川市', 0, 100);
INSERT INTO `jshop_area` VALUES (422822, 422800, 3, '建始县', 0, 100);
INSERT INTO `jshop_area` VALUES (422823, 422800, 3, '巴东县', 0, 100);
INSERT INTO `jshop_area` VALUES (422825, 422800, 3, '宣恩县', 0, 100);
INSERT INTO `jshop_area` VALUES (422826, 422800, 3, '咸丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (422827, 422800, 3, '来凤县', 0, 100);
INSERT INTO `jshop_area` VALUES (422828, 422800, 3, '鹤峰县', 0, 100);
INSERT INTO `jshop_area` VALUES (429000, 420000, 2, '省直辖县级行政区划', 0, 100);
INSERT INTO `jshop_area` VALUES (429004, 429000, 3, '仙桃市', 0, 100);
INSERT INTO `jshop_area` VALUES (429005, 429000, 3, '潜江市', 0, 100);
INSERT INTO `jshop_area` VALUES (429006, 429000, 3, '天门市', 0, 100);
INSERT INTO `jshop_area` VALUES (429021, 429000, 3, '神农架林区', 0, 100);
INSERT INTO `jshop_area` VALUES (430000, 0, 1, '湖南省', 0, 100);
INSERT INTO `jshop_area` VALUES (430100, 430000, 2, '长沙市', 0, 100);
INSERT INTO `jshop_area` VALUES (430101, 430100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430102, 430100, 3, '芙蓉区', 0, 100);
INSERT INTO `jshop_area` VALUES (430103, 430100, 3, '天心区', 0, 100);
INSERT INTO `jshop_area` VALUES (430104, 430100, 3, '岳麓区', 0, 100);
INSERT INTO `jshop_area` VALUES (430105, 430100, 3, '开福区', 0, 100);
INSERT INTO `jshop_area` VALUES (430111, 430100, 3, '雨花区', 0, 100);
INSERT INTO `jshop_area` VALUES (430112, 430100, 3, '望城区', 0, 100);
INSERT INTO `jshop_area` VALUES (430121, 430100, 3, '长沙县', 0, 100);
INSERT INTO `jshop_area` VALUES (430124, 430100, 3, '宁乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (430181, 430100, 3, '浏阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (430200, 430000, 2, '株洲市', 0, 100);
INSERT INTO `jshop_area` VALUES (430201, 430200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430202, 430200, 3, '荷塘区', 0, 100);
INSERT INTO `jshop_area` VALUES (430203, 430200, 3, '芦淞区', 0, 100);
INSERT INTO `jshop_area` VALUES (430204, 430200, 3, '石峰区', 0, 100);
INSERT INTO `jshop_area` VALUES (430211, 430200, 3, '天元区', 0, 100);
INSERT INTO `jshop_area` VALUES (430221, 430200, 3, '株洲县', 0, 100);
INSERT INTO `jshop_area` VALUES (430223, 430200, 3, '攸县', 0, 100);
INSERT INTO `jshop_area` VALUES (430224, 430200, 3, '茶陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (430225, 430200, 3, '炎陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (430281, 430200, 3, '醴陵市', 0, 100);
INSERT INTO `jshop_area` VALUES (430300, 430000, 2, '湘潭市', 0, 100);
INSERT INTO `jshop_area` VALUES (430301, 430300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430302, 430300, 3, '雨湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430304, 430300, 3, '岳塘区', 0, 100);
INSERT INTO `jshop_area` VALUES (430321, 430300, 3, '湘潭县', 0, 100);
INSERT INTO `jshop_area` VALUES (430381, 430300, 3, '湘乡市', 0, 100);
INSERT INTO `jshop_area` VALUES (430382, 430300, 3, '韶山市', 0, 100);
INSERT INTO `jshop_area` VALUES (430400, 430000, 2, '衡阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (430401, 430400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430405, 430400, 3, '珠晖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430406, 430400, 3, '雁峰区', 0, 100);
INSERT INTO `jshop_area` VALUES (430407, 430400, 3, '石鼓区', 0, 100);
INSERT INTO `jshop_area` VALUES (430408, 430400, 3, '蒸湘区', 0, 100);
INSERT INTO `jshop_area` VALUES (430412, 430400, 3, '南岳区', 0, 100);
INSERT INTO `jshop_area` VALUES (430421, 430400, 3, '衡阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (430422, 430400, 3, '衡南县', 0, 100);
INSERT INTO `jshop_area` VALUES (430423, 430400, 3, '衡山县', 0, 100);
INSERT INTO `jshop_area` VALUES (430424, 430400, 3, '衡东县', 0, 100);
INSERT INTO `jshop_area` VALUES (430426, 430400, 3, '祁东县', 0, 100);
INSERT INTO `jshop_area` VALUES (430481, 430400, 3, '耒阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (430482, 430400, 3, '常宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (430500, 430000, 2, '邵阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (430501, 430500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430502, 430500, 3, '双清区', 0, 100);
INSERT INTO `jshop_area` VALUES (430503, 430500, 3, '大祥区', 0, 100);
INSERT INTO `jshop_area` VALUES (430511, 430500, 3, '北塔区', 0, 100);
INSERT INTO `jshop_area` VALUES (430521, 430500, 3, '邵东县', 0, 100);
INSERT INTO `jshop_area` VALUES (430522, 430500, 3, '新邵县', 0, 100);
INSERT INTO `jshop_area` VALUES (430523, 430500, 3, '邵阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (430524, 430500, 3, '隆回县', 0, 100);
INSERT INTO `jshop_area` VALUES (430525, 430500, 3, '洞口县', 0, 100);
INSERT INTO `jshop_area` VALUES (430527, 430500, 3, '绥宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (430528, 430500, 3, '新宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (430529, 430500, 3, '城步苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (430581, 430500, 3, '武冈市', 0, 100);
INSERT INTO `jshop_area` VALUES (430600, 430000, 2, '岳阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (430601, 430600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430602, 430600, 3, '岳阳楼区', 0, 100);
INSERT INTO `jshop_area` VALUES (430603, 430600, 3, '云溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (430611, 430600, 3, '君山区', 0, 100);
INSERT INTO `jshop_area` VALUES (430621, 430600, 3, '岳阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (430623, 430600, 3, '华容县', 0, 100);
INSERT INTO `jshop_area` VALUES (430624, 430600, 3, '湘阴县', 0, 100);
INSERT INTO `jshop_area` VALUES (430626, 430600, 3, '平江县', 0, 100);
INSERT INTO `jshop_area` VALUES (430681, 430600, 3, '汨罗市', 0, 100);
INSERT INTO `jshop_area` VALUES (430682, 430600, 3, '临湘市', 0, 100);
INSERT INTO `jshop_area` VALUES (430700, 430000, 2, '常德市', 0, 100);
INSERT INTO `jshop_area` VALUES (430701, 430700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430702, 430700, 3, '武陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (430703, 430700, 3, '鼎城区', 0, 100);
INSERT INTO `jshop_area` VALUES (430721, 430700, 3, '安乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (430722, 430700, 3, '汉寿县', 0, 100);
INSERT INTO `jshop_area` VALUES (430723, 430700, 3, '澧县', 0, 100);
INSERT INTO `jshop_area` VALUES (430724, 430700, 3, '临澧县', 0, 100);
INSERT INTO `jshop_area` VALUES (430725, 430700, 3, '桃源县', 0, 100);
INSERT INTO `jshop_area` VALUES (430726, 430700, 3, '石门县', 0, 100);
INSERT INTO `jshop_area` VALUES (430781, 430700, 3, '津市市', 0, 100);
INSERT INTO `jshop_area` VALUES (430800, 430000, 2, '张家界市', 0, 100);
INSERT INTO `jshop_area` VALUES (430801, 430800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430802, 430800, 3, '永定区', 0, 100);
INSERT INTO `jshop_area` VALUES (430811, 430800, 3, '武陵源区', 0, 100);
INSERT INTO `jshop_area` VALUES (430821, 430800, 3, '慈利县', 0, 100);
INSERT INTO `jshop_area` VALUES (430822, 430800, 3, '桑植县', 0, 100);
INSERT INTO `jshop_area` VALUES (430900, 430000, 2, '益阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (430901, 430900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (430902, 430900, 3, '资阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (430903, 430900, 3, '赫山区', 0, 100);
INSERT INTO `jshop_area` VALUES (430921, 430900, 3, '南县', 0, 100);
INSERT INTO `jshop_area` VALUES (430922, 430900, 3, '桃江县', 0, 100);
INSERT INTO `jshop_area` VALUES (430923, 430900, 3, '安化县', 0, 100);
INSERT INTO `jshop_area` VALUES (430981, 430900, 3, '沅江市', 0, 100);
INSERT INTO `jshop_area` VALUES (431000, 430000, 2, '郴州市', 0, 100);
INSERT INTO `jshop_area` VALUES (431001, 431000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (431002, 431000, 3, '北湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (431003, 431000, 3, '苏仙区', 0, 100);
INSERT INTO `jshop_area` VALUES (431021, 431000, 3, '桂阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (431022, 431000, 3, '宜章县', 0, 100);
INSERT INTO `jshop_area` VALUES (431023, 431000, 3, '永兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (431024, 431000, 3, '嘉禾县', 0, 100);
INSERT INTO `jshop_area` VALUES (431025, 431000, 3, '临武县', 0, 100);
INSERT INTO `jshop_area` VALUES (431026, 431000, 3, '汝城县', 0, 100);
INSERT INTO `jshop_area` VALUES (431027, 431000, 3, '桂东县', 0, 100);
INSERT INTO `jshop_area` VALUES (431028, 431000, 3, '安仁县', 0, 100);
INSERT INTO `jshop_area` VALUES (431081, 431000, 3, '资兴市', 0, 100);
INSERT INTO `jshop_area` VALUES (431100, 430000, 2, '永州市', 0, 100);
INSERT INTO `jshop_area` VALUES (431101, 431100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (431102, 431100, 3, '零陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (431103, 431100, 3, '冷水滩区', 0, 100);
INSERT INTO `jshop_area` VALUES (431121, 431100, 3, '祁阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (431122, 431100, 3, '东安县', 0, 100);
INSERT INTO `jshop_area` VALUES (431123, 431100, 3, '双牌县', 0, 100);
INSERT INTO `jshop_area` VALUES (431124, 431100, 3, '道县', 0, 100);
INSERT INTO `jshop_area` VALUES (431125, 431100, 3, '江永县', 0, 100);
INSERT INTO `jshop_area` VALUES (431126, 431100, 3, '宁远县', 0, 100);
INSERT INTO `jshop_area` VALUES (431127, 431100, 3, '蓝山县', 0, 100);
INSERT INTO `jshop_area` VALUES (431128, 431100, 3, '新田县', 0, 100);
INSERT INTO `jshop_area` VALUES (431129, 431100, 3, '江华瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (431200, 430000, 2, '怀化市', 0, 100);
INSERT INTO `jshop_area` VALUES (431201, 431200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (431202, 431200, 3, '鹤城区', 0, 100);
INSERT INTO `jshop_area` VALUES (431221, 431200, 3, '中方县', 0, 100);
INSERT INTO `jshop_area` VALUES (431222, 431200, 3, '沅陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (431223, 431200, 3, '辰溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (431224, 431200, 3, '溆浦县', 0, 100);
INSERT INTO `jshop_area` VALUES (431225, 431200, 3, '会同县', 0, 100);
INSERT INTO `jshop_area` VALUES (431226, 431200, 3, '麻阳苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (431227, 431200, 3, '新晃侗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (431228, 431200, 3, '芷江侗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (431229, 431200, 3, '靖州苗族侗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (431230, 431200, 3, '通道侗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (431281, 431200, 3, '洪江市', 0, 100);
INSERT INTO `jshop_area` VALUES (431300, 430000, 2, '娄底市', 0, 100);
INSERT INTO `jshop_area` VALUES (431301, 431300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (431302, 431300, 3, '娄星区', 0, 100);
INSERT INTO `jshop_area` VALUES (431321, 431300, 3, '双峰县', 0, 100);
INSERT INTO `jshop_area` VALUES (431322, 431300, 3, '新化县', 0, 100);
INSERT INTO `jshop_area` VALUES (431381, 431300, 3, '冷水江市', 0, 100);
INSERT INTO `jshop_area` VALUES (431382, 431300, 3, '涟源市', 0, 100);
INSERT INTO `jshop_area` VALUES (433100, 430000, 2, '湘西土家族苗族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (433101, 433100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (433122, 433100, 3, '泸溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (433123, 433100, 3, '凤凰县', 0, 100);
INSERT INTO `jshop_area` VALUES (433124, 433100, 3, '花垣县', 0, 100);
INSERT INTO `jshop_area` VALUES (433125, 433100, 3, '保靖县', 0, 100);
INSERT INTO `jshop_area` VALUES (433126, 433100, 3, '古丈县', 0, 100);
INSERT INTO `jshop_area` VALUES (433127, 433100, 3, '永顺县', 0, 100);
INSERT INTO `jshop_area` VALUES (433130, 433100, 3, '龙山县', 0, 100);
INSERT INTO `jshop_area` VALUES (440000, 0, 1, '广东省', 0, 100);
INSERT INTO `jshop_area` VALUES (440100, 440000, 2, '广州市', 0, 100);
INSERT INTO `jshop_area` VALUES (440101, 440100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440103, 440100, 3, '荔湾区', 0, 100);
INSERT INTO `jshop_area` VALUES (440104, 440100, 3, '越秀区', 0, 100);
INSERT INTO `jshop_area` VALUES (440105, 440100, 3, '海珠区', 0, 100);
INSERT INTO `jshop_area` VALUES (440106, 440100, 3, '天河区', 0, 100);
INSERT INTO `jshop_area` VALUES (440111, 440100, 3, '白云区', 0, 100);
INSERT INTO `jshop_area` VALUES (440112, 440100, 3, '黄埔区', 0, 100);
INSERT INTO `jshop_area` VALUES (440113, 440100, 3, '番禺区', 0, 100);
INSERT INTO `jshop_area` VALUES (440114, 440100, 3, '花都区', 0, 100);
INSERT INTO `jshop_area` VALUES (440115, 440100, 3, '南沙区', 0, 100);
INSERT INTO `jshop_area` VALUES (440117, 440100, 3, '从化区', 0, 100);
INSERT INTO `jshop_area` VALUES (440118, 440100, 3, '增城区', 0, 100);
INSERT INTO `jshop_area` VALUES (440200, 440000, 2, '韶关市', 0, 100);
INSERT INTO `jshop_area` VALUES (440201, 440200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440203, 440200, 3, '武江区', 0, 100);
INSERT INTO `jshop_area` VALUES (440204, 440200, 3, '浈江区', 0, 100);
INSERT INTO `jshop_area` VALUES (440205, 440200, 3, '曲江区', 0, 100);
INSERT INTO `jshop_area` VALUES (440222, 440200, 3, '始兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (440224, 440200, 3, '仁化县', 0, 100);
INSERT INTO `jshop_area` VALUES (440229, 440200, 3, '翁源县', 0, 100);
INSERT INTO `jshop_area` VALUES (440232, 440200, 3, '乳源瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (440233, 440200, 3, '新丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (440281, 440200, 3, '乐昌市', 0, 100);
INSERT INTO `jshop_area` VALUES (440282, 440200, 3, '南雄市', 0, 100);
INSERT INTO `jshop_area` VALUES (440300, 440000, 2, '深圳市', 0, 100);
INSERT INTO `jshop_area` VALUES (440301, 440300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440303, 440300, 3, '罗湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440304, 440300, 3, '福田区', 0, 100);
INSERT INTO `jshop_area` VALUES (440305, 440300, 3, '南山区', 0, 100);
INSERT INTO `jshop_area` VALUES (440306, 440300, 3, '宝安区', 0, 100);
INSERT INTO `jshop_area` VALUES (440307, 440300, 3, '龙岗区', 0, 100);
INSERT INTO `jshop_area` VALUES (440308, 440300, 3, '盐田区', 0, 100);
INSERT INTO `jshop_area` VALUES (440400, 440000, 2, '珠海市', 0, 100);
INSERT INTO `jshop_area` VALUES (440401, 440400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440402, 440400, 3, '香洲区', 0, 100);
INSERT INTO `jshop_area` VALUES (440403, 440400, 3, '斗门区', 0, 100);
INSERT INTO `jshop_area` VALUES (440404, 440400, 3, '金湾区', 0, 100);
INSERT INTO `jshop_area` VALUES (440500, 440000, 2, '汕头市', 0, 100);
INSERT INTO `jshop_area` VALUES (440501, 440500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440507, 440500, 3, '龙湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440511, 440500, 3, '金平区', 0, 100);
INSERT INTO `jshop_area` VALUES (440512, 440500, 3, '濠江区', 0, 100);
INSERT INTO `jshop_area` VALUES (440513, 440500, 3, '潮阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (440514, 440500, 3, '潮南区', 0, 100);
INSERT INTO `jshop_area` VALUES (440515, 440500, 3, '澄海区', 0, 100);
INSERT INTO `jshop_area` VALUES (440523, 440500, 3, '南澳县', 0, 100);
INSERT INTO `jshop_area` VALUES (440600, 440000, 2, '佛山市', 0, 100);
INSERT INTO `jshop_area` VALUES (440601, 440600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440604, 440600, 3, '禅城区', 0, 100);
INSERT INTO `jshop_area` VALUES (440605, 440600, 3, '南海区', 0, 100);
INSERT INTO `jshop_area` VALUES (440606, 440600, 3, '顺德区', 0, 100);
INSERT INTO `jshop_area` VALUES (440607, 440600, 3, '三水区', 0, 100);
INSERT INTO `jshop_area` VALUES (440608, 440600, 3, '高明区', 0, 100);
INSERT INTO `jshop_area` VALUES (440700, 440000, 2, '江门市', 0, 100);
INSERT INTO `jshop_area` VALUES (440701, 440700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440703, 440700, 3, '蓬江区', 0, 100);
INSERT INTO `jshop_area` VALUES (440704, 440700, 3, '江海区', 0, 100);
INSERT INTO `jshop_area` VALUES (440705, 440700, 3, '新会区', 0, 100);
INSERT INTO `jshop_area` VALUES (440781, 440700, 3, '台山市', 0, 100);
INSERT INTO `jshop_area` VALUES (440783, 440700, 3, '开平市', 0, 100);
INSERT INTO `jshop_area` VALUES (440784, 440700, 3, '鹤山市', 0, 100);
INSERT INTO `jshop_area` VALUES (440785, 440700, 3, '恩平市', 0, 100);
INSERT INTO `jshop_area` VALUES (440800, 440000, 2, '湛江市', 0, 100);
INSERT INTO `jshop_area` VALUES (440801, 440800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440802, 440800, 3, '赤坎区', 0, 100);
INSERT INTO `jshop_area` VALUES (440803, 440800, 3, '霞山区', 0, 100);
INSERT INTO `jshop_area` VALUES (440804, 440800, 3, '坡头区', 0, 100);
INSERT INTO `jshop_area` VALUES (440811, 440800, 3, '麻章区', 0, 100);
INSERT INTO `jshop_area` VALUES (440823, 440800, 3, '遂溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (440825, 440800, 3, '徐闻县', 0, 100);
INSERT INTO `jshop_area` VALUES (440881, 440800, 3, '廉江市', 0, 100);
INSERT INTO `jshop_area` VALUES (440882, 440800, 3, '雷州市', 0, 100);
INSERT INTO `jshop_area` VALUES (440883, 440800, 3, '吴川市', 0, 100);
INSERT INTO `jshop_area` VALUES (440900, 440000, 2, '茂名市', 0, 100);
INSERT INTO `jshop_area` VALUES (440901, 440900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (440902, 440900, 3, '茂南区', 0, 100);
INSERT INTO `jshop_area` VALUES (440904, 440900, 3, '电白区', 0, 100);
INSERT INTO `jshop_area` VALUES (440981, 440900, 3, '高州市', 0, 100);
INSERT INTO `jshop_area` VALUES (440982, 440900, 3, '化州市', 0, 100);
INSERT INTO `jshop_area` VALUES (440983, 440900, 3, '信宜市', 0, 100);
INSERT INTO `jshop_area` VALUES (441200, 440000, 2, '肇庆市', 0, 100);
INSERT INTO `jshop_area` VALUES (441201, 441200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (441202, 441200, 3, '端州区', 0, 100);
INSERT INTO `jshop_area` VALUES (441203, 441200, 3, '鼎湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (441204, 441200, 3, '高要区', 0, 100);
INSERT INTO `jshop_area` VALUES (441223, 441200, 3, '广宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (441224, 441200, 3, '怀集县', 0, 100);
INSERT INTO `jshop_area` VALUES (441225, 441200, 3, '封开县', 0, 100);
INSERT INTO `jshop_area` VALUES (441226, 441200, 3, '德庆县', 0, 100);
INSERT INTO `jshop_area` VALUES (441284, 441200, 3, '四会市', 0, 100);
INSERT INTO `jshop_area` VALUES (441300, 440000, 2, '惠州市', 0, 100);
INSERT INTO `jshop_area` VALUES (441301, 441300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (441302, 441300, 3, '惠城区', 0, 100);
INSERT INTO `jshop_area` VALUES (441303, 441300, 3, '惠阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (441322, 441300, 3, '博罗县', 0, 100);
INSERT INTO `jshop_area` VALUES (441323, 441300, 3, '惠东县', 0, 100);
INSERT INTO `jshop_area` VALUES (441324, 441300, 3, '龙门县', 0, 100);
INSERT INTO `jshop_area` VALUES (441400, 440000, 2, '梅州市', 0, 100);
INSERT INTO `jshop_area` VALUES (441401, 441400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (441402, 441400, 3, '梅江区', 0, 100);
INSERT INTO `jshop_area` VALUES (441403, 441400, 3, '梅县区', 0, 100);
INSERT INTO `jshop_area` VALUES (441422, 441400, 3, '大埔县', 0, 100);
INSERT INTO `jshop_area` VALUES (441423, 441400, 3, '丰顺县', 0, 100);
INSERT INTO `jshop_area` VALUES (441424, 441400, 3, '五华县', 0, 100);
INSERT INTO `jshop_area` VALUES (441426, 441400, 3, '平远县', 0, 100);
INSERT INTO `jshop_area` VALUES (441427, 441400, 3, '蕉岭县', 0, 100);
INSERT INTO `jshop_area` VALUES (441481, 441400, 3, '兴宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (441500, 440000, 2, '汕尾市', 0, 100);
INSERT INTO `jshop_area` VALUES (441501, 441500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (441502, 441500, 3, '城区', 0, 100);
INSERT INTO `jshop_area` VALUES (441521, 441500, 3, '海丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (441523, 441500, 3, '陆河县', 0, 100);
INSERT INTO `jshop_area` VALUES (441581, 441500, 3, '陆丰市', 0, 100);
INSERT INTO `jshop_area` VALUES (441600, 440000, 2, '河源市', 0, 100);
INSERT INTO `jshop_area` VALUES (441601, 441600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (441602, 441600, 3, '源城区', 0, 100);
INSERT INTO `jshop_area` VALUES (441621, 441600, 3, '紫金县', 0, 100);
INSERT INTO `jshop_area` VALUES (441622, 441600, 3, '龙川县', 0, 100);
INSERT INTO `jshop_area` VALUES (441623, 441600, 3, '连平县', 0, 100);
INSERT INTO `jshop_area` VALUES (441624, 441600, 3, '和平县', 0, 100);
INSERT INTO `jshop_area` VALUES (441625, 441600, 3, '东源县', 0, 100);
INSERT INTO `jshop_area` VALUES (441700, 440000, 2, '阳江市', 0, 100);
INSERT INTO `jshop_area` VALUES (441701, 441700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (441702, 441700, 3, '江城区', 0, 100);
INSERT INTO `jshop_area` VALUES (441704, 441700, 3, '阳东区', 0, 100);
INSERT INTO `jshop_area` VALUES (441721, 441700, 3, '阳西县', 0, 100);
INSERT INTO `jshop_area` VALUES (441781, 441700, 3, '阳春市', 0, 100);
INSERT INTO `jshop_area` VALUES (441800, 440000, 2, '清远市', 0, 100);
INSERT INTO `jshop_area` VALUES (441801, 441800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (441802, 441800, 3, '清城区', 0, 100);
INSERT INTO `jshop_area` VALUES (441803, 441800, 3, '清新区', 0, 100);
INSERT INTO `jshop_area` VALUES (441821, 441800, 3, '佛冈县', 0, 100);
INSERT INTO `jshop_area` VALUES (441823, 441800, 3, '阳山县', 0, 100);
INSERT INTO `jshop_area` VALUES (441825, 441800, 3, '连山壮族瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (441826, 441800, 3, '连南瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (441881, 441800, 3, '英德市', 0, 100);
INSERT INTO `jshop_area` VALUES (441882, 441800, 3, '连州市', 0, 100);
INSERT INTO `jshop_area` VALUES (441900, 440000, 2, '东莞市', 0, 100);
INSERT INTO `jshop_area` VALUES (442000, 440000, 2, '中山市', 0, 100);
INSERT INTO `jshop_area` VALUES (445100, 440000, 2, '潮州市', 0, 100);
INSERT INTO `jshop_area` VALUES (445101, 445100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (445102, 445100, 3, '湘桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (445103, 445100, 3, '潮安区', 0, 100);
INSERT INTO `jshop_area` VALUES (445122, 445100, 3, '饶平县', 0, 100);
INSERT INTO `jshop_area` VALUES (445200, 440000, 2, '揭阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (445201, 445200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (445202, 445200, 3, '榕城区', 0, 100);
INSERT INTO `jshop_area` VALUES (445203, 445200, 3, '揭东区', 0, 100);
INSERT INTO `jshop_area` VALUES (445222, 445200, 3, '揭西县', 0, 100);
INSERT INTO `jshop_area` VALUES (445224, 445200, 3, '惠来县', 0, 100);
INSERT INTO `jshop_area` VALUES (445281, 445200, 3, '普宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (445300, 440000, 2, '云浮市', 0, 100);
INSERT INTO `jshop_area` VALUES (445301, 445300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (445302, 445300, 3, '云城区', 0, 100);
INSERT INTO `jshop_area` VALUES (445303, 445300, 3, '云安区', 0, 100);
INSERT INTO `jshop_area` VALUES (445321, 445300, 3, '新兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (445322, 445300, 3, '郁南县', 0, 100);
INSERT INTO `jshop_area` VALUES (445381, 445300, 3, '罗定市', 0, 100);
INSERT INTO `jshop_area` VALUES (450000, 0, 1, '广西壮族自治区', 0, 100);
INSERT INTO `jshop_area` VALUES (450100, 450000, 2, '南宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (450101, 450100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450102, 450100, 3, '兴宁区', 0, 100);
INSERT INTO `jshop_area` VALUES (450103, 450100, 3, '青秀区', 0, 100);
INSERT INTO `jshop_area` VALUES (450105, 450100, 3, '江南区', 0, 100);
INSERT INTO `jshop_area` VALUES (450107, 450100, 3, '西乡塘区', 0, 100);
INSERT INTO `jshop_area` VALUES (450108, 450100, 3, '良庆区', 0, 100);
INSERT INTO `jshop_area` VALUES (450109, 450100, 3, '邕宁区', 0, 100);
INSERT INTO `jshop_area` VALUES (450110, 450100, 3, '武鸣区', 0, 100);
INSERT INTO `jshop_area` VALUES (450123, 450100, 3, '隆安县', 0, 100);
INSERT INTO `jshop_area` VALUES (450124, 450100, 3, '马山县', 0, 100);
INSERT INTO `jshop_area` VALUES (450125, 450100, 3, '上林县', 0, 100);
INSERT INTO `jshop_area` VALUES (450126, 450100, 3, '宾阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (450127, 450100, 3, '横县', 0, 100);
INSERT INTO `jshop_area` VALUES (450200, 450000, 2, '柳州市', 0, 100);
INSERT INTO `jshop_area` VALUES (450201, 450200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450202, 450200, 3, '城中区', 0, 100);
INSERT INTO `jshop_area` VALUES (450203, 450200, 3, '鱼峰区', 0, 100);
INSERT INTO `jshop_area` VALUES (450204, 450200, 3, '柳南区', 0, 100);
INSERT INTO `jshop_area` VALUES (450205, 450200, 3, '柳北区', 0, 100);
INSERT INTO `jshop_area` VALUES (450206, 450200, 3, '柳江区', 0, 100);
INSERT INTO `jshop_area` VALUES (450222, 450200, 3, '柳城县', 0, 100);
INSERT INTO `jshop_area` VALUES (450223, 450200, 3, '鹿寨县', 0, 100);
INSERT INTO `jshop_area` VALUES (450224, 450200, 3, '融安县', 0, 100);
INSERT INTO `jshop_area` VALUES (450225, 450200, 3, '融水苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (450226, 450200, 3, '三江侗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (450300, 450000, 2, '桂林市', 0, 100);
INSERT INTO `jshop_area` VALUES (450301, 450300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450302, 450300, 3, '秀峰区', 0, 100);
INSERT INTO `jshop_area` VALUES (450303, 450300, 3, '叠彩区', 0, 100);
INSERT INTO `jshop_area` VALUES (450304, 450300, 3, '象山区', 0, 100);
INSERT INTO `jshop_area` VALUES (450305, 450300, 3, '七星区', 0, 100);
INSERT INTO `jshop_area` VALUES (450311, 450300, 3, '雁山区', 0, 100);
INSERT INTO `jshop_area` VALUES (450312, 450300, 3, '临桂区', 0, 100);
INSERT INTO `jshop_area` VALUES (450321, 450300, 3, '阳朔县', 0, 100);
INSERT INTO `jshop_area` VALUES (450323, 450300, 3, '灵川县', 0, 100);
INSERT INTO `jshop_area` VALUES (450324, 450300, 3, '全州县', 0, 100);
INSERT INTO `jshop_area` VALUES (450325, 450300, 3, '兴安县', 0, 100);
INSERT INTO `jshop_area` VALUES (450326, 450300, 3, '永福县', 0, 100);
INSERT INTO `jshop_area` VALUES (450327, 450300, 3, '灌阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (450328, 450300, 3, '龙胜各族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (450329, 450300, 3, '资源县', 0, 100);
INSERT INTO `jshop_area` VALUES (450330, 450300, 3, '平乐县', 0, 100);
INSERT INTO `jshop_area` VALUES (450331, 450300, 3, '荔浦县', 0, 100);
INSERT INTO `jshop_area` VALUES (450332, 450300, 3, '恭城瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (450400, 450000, 2, '梧州市', 0, 100);
INSERT INTO `jshop_area` VALUES (450401, 450400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450403, 450400, 3, '万秀区', 0, 100);
INSERT INTO `jshop_area` VALUES (450405, 450400, 3, '长洲区', 0, 100);
INSERT INTO `jshop_area` VALUES (450406, 450400, 3, '龙圩区', 0, 100);
INSERT INTO `jshop_area` VALUES (450421, 450400, 3, '苍梧县', 0, 100);
INSERT INTO `jshop_area` VALUES (450422, 450400, 3, '藤县', 0, 100);
INSERT INTO `jshop_area` VALUES (450423, 450400, 3, '蒙山县', 0, 100);
INSERT INTO `jshop_area` VALUES (450481, 450400, 3, '岑溪市', 0, 100);
INSERT INTO `jshop_area` VALUES (450500, 450000, 2, '北海市', 0, 100);
INSERT INTO `jshop_area` VALUES (450501, 450500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450502, 450500, 3, '海城区', 0, 100);
INSERT INTO `jshop_area` VALUES (450503, 450500, 3, '银海区', 0, 100);
INSERT INTO `jshop_area` VALUES (450512, 450500, 3, '铁山港区', 0, 100);
INSERT INTO `jshop_area` VALUES (450521, 450500, 3, '合浦县', 0, 100);
INSERT INTO `jshop_area` VALUES (450600, 450000, 2, '防城港市', 0, 100);
INSERT INTO `jshop_area` VALUES (450601, 450600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450602, 450600, 3, '港口区', 0, 100);
INSERT INTO `jshop_area` VALUES (450603, 450600, 3, '防城区', 0, 100);
INSERT INTO `jshop_area` VALUES (450621, 450600, 3, '上思县', 0, 100);
INSERT INTO `jshop_area` VALUES (450681, 450600, 3, '东兴市', 0, 100);
INSERT INTO `jshop_area` VALUES (450700, 450000, 2, '钦州市', 0, 100);
INSERT INTO `jshop_area` VALUES (450701, 450700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450702, 450700, 3, '钦南区', 0, 100);
INSERT INTO `jshop_area` VALUES (450703, 450700, 3, '钦北区', 0, 100);
INSERT INTO `jshop_area` VALUES (450721, 450700, 3, '灵山县', 0, 100);
INSERT INTO `jshop_area` VALUES (450722, 450700, 3, '浦北县', 0, 100);
INSERT INTO `jshop_area` VALUES (450800, 450000, 2, '贵港市', 0, 100);
INSERT INTO `jshop_area` VALUES (450801, 450800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450802, 450800, 3, '港北区', 0, 100);
INSERT INTO `jshop_area` VALUES (450803, 450800, 3, '港南区', 0, 100);
INSERT INTO `jshop_area` VALUES (450804, 450800, 3, '覃塘区', 0, 100);
INSERT INTO `jshop_area` VALUES (450821, 450800, 3, '平南县', 0, 100);
INSERT INTO `jshop_area` VALUES (450881, 450800, 3, '桂平市', 0, 100);
INSERT INTO `jshop_area` VALUES (450900, 450000, 2, '玉林市', 0, 100);
INSERT INTO `jshop_area` VALUES (450901, 450900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (450902, 450900, 3, '玉州区', 0, 100);
INSERT INTO `jshop_area` VALUES (450903, 450900, 3, '福绵区', 0, 100);
INSERT INTO `jshop_area` VALUES (450921, 450900, 3, '容县', 0, 100);
INSERT INTO `jshop_area` VALUES (450922, 450900, 3, '陆川县', 0, 100);
INSERT INTO `jshop_area` VALUES (450923, 450900, 3, '博白县', 0, 100);
INSERT INTO `jshop_area` VALUES (450924, 450900, 3, '兴业县', 0, 100);
INSERT INTO `jshop_area` VALUES (450981, 450900, 3, '北流市', 0, 100);
INSERT INTO `jshop_area` VALUES (451000, 450000, 2, '百色市', 0, 100);
INSERT INTO `jshop_area` VALUES (451001, 451000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (451002, 451000, 3, '右江区', 0, 100);
INSERT INTO `jshop_area` VALUES (451021, 451000, 3, '田阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (451022, 451000, 3, '田东县', 0, 100);
INSERT INTO `jshop_area` VALUES (451023, 451000, 3, '平果县', 0, 100);
INSERT INTO `jshop_area` VALUES (451024, 451000, 3, '德保县', 0, 100);
INSERT INTO `jshop_area` VALUES (451026, 451000, 3, '那坡县', 0, 100);
INSERT INTO `jshop_area` VALUES (451027, 451000, 3, '凌云县', 0, 100);
INSERT INTO `jshop_area` VALUES (451028, 451000, 3, '乐业县', 0, 100);
INSERT INTO `jshop_area` VALUES (451029, 451000, 3, '田林县', 0, 100);
INSERT INTO `jshop_area` VALUES (451030, 451000, 3, '西林县', 0, 100);
INSERT INTO `jshop_area` VALUES (451031, 451000, 3, '隆林各族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (451081, 451000, 3, '靖西市', 0, 100);
INSERT INTO `jshop_area` VALUES (451100, 450000, 2, '贺州市', 0, 100);
INSERT INTO `jshop_area` VALUES (451101, 451100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (451102, 451100, 3, '八步区', 0, 100);
INSERT INTO `jshop_area` VALUES (451103, 451100, 3, '平桂区', 0, 100);
INSERT INTO `jshop_area` VALUES (451121, 451100, 3, '昭平县', 0, 100);
INSERT INTO `jshop_area` VALUES (451122, 451100, 3, '钟山县', 0, 100);
INSERT INTO `jshop_area` VALUES (451123, 451100, 3, '富川瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (451200, 450000, 2, '河池市', 0, 100);
INSERT INTO `jshop_area` VALUES (451201, 451200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (451202, 451200, 3, '金城江区', 0, 100);
INSERT INTO `jshop_area` VALUES (451221, 451200, 3, '南丹县', 0, 100);
INSERT INTO `jshop_area` VALUES (451222, 451200, 3, '天峨县', 0, 100);
INSERT INTO `jshop_area` VALUES (451223, 451200, 3, '凤山县', 0, 100);
INSERT INTO `jshop_area` VALUES (451224, 451200, 3, '东兰县', 0, 100);
INSERT INTO `jshop_area` VALUES (451225, 451200, 3, '罗城仫佬族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (451226, 451200, 3, '环江毛南族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (451227, 451200, 3, '巴马瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (451228, 451200, 3, '都安瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (451229, 451200, 3, '大化瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (451281, 451200, 3, '宜州市', 0, 100);
INSERT INTO `jshop_area` VALUES (451300, 450000, 2, '来宾市', 0, 100);
INSERT INTO `jshop_area` VALUES (451301, 451300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (451302, 451300, 3, '兴宾区', 0, 100);
INSERT INTO `jshop_area` VALUES (451321, 451300, 3, '忻城县', 0, 100);
INSERT INTO `jshop_area` VALUES (451322, 451300, 3, '象州县', 0, 100);
INSERT INTO `jshop_area` VALUES (451323, 451300, 3, '武宣县', 0, 100);
INSERT INTO `jshop_area` VALUES (451324, 451300, 3, '金秀瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (451381, 451300, 3, '合山市', 0, 100);
INSERT INTO `jshop_area` VALUES (451400, 450000, 2, '崇左市', 0, 100);
INSERT INTO `jshop_area` VALUES (451401, 451400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (451402, 451400, 3, '江州区', 0, 100);
INSERT INTO `jshop_area` VALUES (451421, 451400, 3, '扶绥县', 0, 100);
INSERT INTO `jshop_area` VALUES (451422, 451400, 3, '宁明县', 0, 100);
INSERT INTO `jshop_area` VALUES (451423, 451400, 3, '龙州县', 0, 100);
INSERT INTO `jshop_area` VALUES (451424, 451400, 3, '大新县', 0, 100);
INSERT INTO `jshop_area` VALUES (451425, 451400, 3, '天等县', 0, 100);
INSERT INTO `jshop_area` VALUES (451481, 451400, 3, '凭祥市', 0, 100);
INSERT INTO `jshop_area` VALUES (460000, 0, 1, '海南省', 0, 100);
INSERT INTO `jshop_area` VALUES (460100, 460000, 2, '海口市', 0, 100);
INSERT INTO `jshop_area` VALUES (460101, 460100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (460105, 460100, 3, '秀英区', 0, 100);
INSERT INTO `jshop_area` VALUES (460106, 460100, 3, '龙华区', 0, 100);
INSERT INTO `jshop_area` VALUES (460107, 460100, 3, '琼山区', 0, 100);
INSERT INTO `jshop_area` VALUES (460108, 460100, 3, '美兰区', 0, 100);
INSERT INTO `jshop_area` VALUES (460200, 460000, 2, '三亚市', 0, 100);
INSERT INTO `jshop_area` VALUES (460201, 460200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (460202, 460200, 3, '海棠区', 0, 100);
INSERT INTO `jshop_area` VALUES (460203, 460200, 3, '吉阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (460204, 460200, 3, '天涯区', 0, 100);
INSERT INTO `jshop_area` VALUES (460205, 460200, 3, '崖州区', 0, 100);
INSERT INTO `jshop_area` VALUES (460300, 460000, 2, '三沙市', 0, 100);
INSERT INTO `jshop_area` VALUES (460400, 460000, 2, '儋州市', 0, 100);
INSERT INTO `jshop_area` VALUES (469000, 460000, 2, '省直辖县级行政区划', 0, 100);
INSERT INTO `jshop_area` VALUES (469001, 469000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (469002, 469000, 3, '琼海市', 0, 100);
INSERT INTO `jshop_area` VALUES (469005, 469000, 3, '文昌市', 0, 100);
INSERT INTO `jshop_area` VALUES (469006, 469000, 3, '万宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (469007, 469000, 3, '东方市', 0, 100);
INSERT INTO `jshop_area` VALUES (469021, 469000, 3, '定安县', 0, 100);
INSERT INTO `jshop_area` VALUES (469022, 469000, 3, '屯昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (469023, 469000, 3, '澄迈县', 0, 100);
INSERT INTO `jshop_area` VALUES (469024, 469000, 3, '临高县', 0, 100);
INSERT INTO `jshop_area` VALUES (469025, 469000, 3, '白沙黎族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (469026, 469000, 3, '昌江黎族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (469027, 469000, 3, '乐东黎族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (469028, 469000, 3, '陵水黎族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (469029, 469000, 3, '保亭黎族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (469030, 469000, 3, '琼中黎族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (500000, 0, 1, '重庆市', 0, 100);
INSERT INTO `jshop_area` VALUES (500100, 500000, 2, '重庆市', 0, 100);
INSERT INTO `jshop_area` VALUES (500101, 500100, 3, '万州区', 0, 100);
INSERT INTO `jshop_area` VALUES (500102, 500100, 3, '涪陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (500103, 500100, 3, '渝中区', 0, 100);
INSERT INTO `jshop_area` VALUES (500104, 500100, 3, '大渡口区', 0, 100);
INSERT INTO `jshop_area` VALUES (500105, 500100, 3, '江北区', 0, 100);
INSERT INTO `jshop_area` VALUES (500106, 500100, 3, '沙坪坝区', 0, 100);
INSERT INTO `jshop_area` VALUES (500107, 500100, 3, '九龙坡区', 0, 100);
INSERT INTO `jshop_area` VALUES (500108, 500100, 3, '南岸区', 0, 100);
INSERT INTO `jshop_area` VALUES (500109, 500100, 3, '北碚区', 0, 100);
INSERT INTO `jshop_area` VALUES (500110, 500100, 3, '綦江区', 0, 100);
INSERT INTO `jshop_area` VALUES (500111, 500100, 3, '大足区', 0, 100);
INSERT INTO `jshop_area` VALUES (500112, 500100, 3, '渝北区', 0, 100);
INSERT INTO `jshop_area` VALUES (500113, 500100, 3, '巴南区', 0, 100);
INSERT INTO `jshop_area` VALUES (500114, 500100, 3, '黔江区', 0, 100);
INSERT INTO `jshop_area` VALUES (500115, 500100, 3, '长寿区', 0, 100);
INSERT INTO `jshop_area` VALUES (500116, 500100, 3, '江津区', 0, 100);
INSERT INTO `jshop_area` VALUES (500117, 500100, 3, '合川区', 0, 100);
INSERT INTO `jshop_area` VALUES (500118, 500100, 3, '永川区', 0, 100);
INSERT INTO `jshop_area` VALUES (500119, 500100, 3, '南川区', 0, 100);
INSERT INTO `jshop_area` VALUES (500120, 500100, 3, '璧山区', 0, 100);
INSERT INTO `jshop_area` VALUES (500151, 500100, 3, '铜梁区', 0, 100);
INSERT INTO `jshop_area` VALUES (500152, 500100, 3, '潼南区', 0, 100);
INSERT INTO `jshop_area` VALUES (500153, 500100, 3, '荣昌区', 0, 100);
INSERT INTO `jshop_area` VALUES (500154, 500100, 3, '开州区', 0, 100);
INSERT INTO `jshop_area` VALUES (500200, 500000, 2, '县', 0, 100);
INSERT INTO `jshop_area` VALUES (500228, 500200, 3, '梁平县', 0, 100);
INSERT INTO `jshop_area` VALUES (500229, 500200, 3, '城口县', 0, 100);
INSERT INTO `jshop_area` VALUES (500230, 500200, 3, '丰都县', 0, 100);
INSERT INTO `jshop_area` VALUES (500231, 500200, 3, '垫江县', 0, 100);
INSERT INTO `jshop_area` VALUES (500232, 500200, 3, '武隆县', 0, 100);
INSERT INTO `jshop_area` VALUES (500233, 500200, 3, '忠县', 0, 100);
INSERT INTO `jshop_area` VALUES (500235, 500200, 3, '云阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (500236, 500200, 3, '奉节县', 0, 100);
INSERT INTO `jshop_area` VALUES (500237, 500200, 3, '巫山县', 0, 100);
INSERT INTO `jshop_area` VALUES (500238, 500200, 3, '巫溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (500240, 500200, 3, '石柱土家族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (500241, 500200, 3, '秀山土家族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (500242, 500200, 3, '酉阳土家族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (500243, 500200, 3, '彭水苗族土家族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (510000, 0, 1, '四川省', 0, 100);
INSERT INTO `jshop_area` VALUES (510100, 510000, 2, '成都市', 0, 100);
INSERT INTO `jshop_area` VALUES (510101, 510100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (510104, 510100, 3, '锦江区', 0, 100);
INSERT INTO `jshop_area` VALUES (510105, 510100, 3, '青羊区', 0, 100);
INSERT INTO `jshop_area` VALUES (510106, 510100, 3, '金牛区', 0, 100);
INSERT INTO `jshop_area` VALUES (510107, 510100, 3, '武侯区', 0, 100);
INSERT INTO `jshop_area` VALUES (510108, 510100, 3, '成华区', 0, 100);
INSERT INTO `jshop_area` VALUES (510112, 510100, 3, '龙泉驿区', 0, 100);
INSERT INTO `jshop_area` VALUES (510113, 510100, 3, '青白江区', 0, 100);
INSERT INTO `jshop_area` VALUES (510114, 510100, 3, '新都区', 0, 100);
INSERT INTO `jshop_area` VALUES (510115, 510100, 3, '温江区', 0, 100);
INSERT INTO `jshop_area` VALUES (510116, 510100, 3, '双流区', 0, 100);
INSERT INTO `jshop_area` VALUES (510121, 510100, 3, '金堂县', 0, 100);
INSERT INTO `jshop_area` VALUES (510124, 510100, 3, '郫县', 0, 100);
INSERT INTO `jshop_area` VALUES (510129, 510100, 3, '大邑县', 0, 100);
INSERT INTO `jshop_area` VALUES (510131, 510100, 3, '蒲江县', 0, 100);
INSERT INTO `jshop_area` VALUES (510132, 510100, 3, '新津县', 0, 100);
INSERT INTO `jshop_area` VALUES (510181, 510100, 3, '都江堰市', 0, 100);
INSERT INTO `jshop_area` VALUES (510182, 510100, 3, '彭州市', 0, 100);
INSERT INTO `jshop_area` VALUES (510183, 510100, 3, '邛崃市', 0, 100);
INSERT INTO `jshop_area` VALUES (510184, 510100, 3, '崇州市', 0, 100);
INSERT INTO `jshop_area` VALUES (510185, 510100, 3, '简阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (510300, 510000, 2, '自贡市', 0, 100);
INSERT INTO `jshop_area` VALUES (510301, 510300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (510302, 510300, 3, '自流井区', 0, 100);
INSERT INTO `jshop_area` VALUES (510303, 510300, 3, '贡井区', 0, 100);
INSERT INTO `jshop_area` VALUES (510304, 510300, 3, '大安区', 0, 100);
INSERT INTO `jshop_area` VALUES (510311, 510300, 3, '沿滩区', 0, 100);
INSERT INTO `jshop_area` VALUES (510321, 510300, 3, '荣县', 0, 100);
INSERT INTO `jshop_area` VALUES (510322, 510300, 3, '富顺县', 0, 100);
INSERT INTO `jshop_area` VALUES (510400, 510000, 2, '攀枝花市', 0, 100);
INSERT INTO `jshop_area` VALUES (510401, 510400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (510402, 510400, 3, '东区', 0, 100);
INSERT INTO `jshop_area` VALUES (510403, 510400, 3, '西区', 0, 100);
INSERT INTO `jshop_area` VALUES (510411, 510400, 3, '仁和区', 0, 100);
INSERT INTO `jshop_area` VALUES (510421, 510400, 3, '米易县', 0, 100);
INSERT INTO `jshop_area` VALUES (510422, 510400, 3, '盐边县', 0, 100);
INSERT INTO `jshop_area` VALUES (510500, 510000, 2, '泸州市', 0, 100);
INSERT INTO `jshop_area` VALUES (510501, 510500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (510502, 510500, 3, '江阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (510503, 510500, 3, '纳溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (510504, 510500, 3, '龙马潭区', 0, 100);
INSERT INTO `jshop_area` VALUES (510521, 510500, 3, '泸县', 0, 100);
INSERT INTO `jshop_area` VALUES (510522, 510500, 3, '合江县', 0, 100);
INSERT INTO `jshop_area` VALUES (510524, 510500, 3, '叙永县', 0, 100);
INSERT INTO `jshop_area` VALUES (510525, 510500, 3, '古蔺县', 0, 100);
INSERT INTO `jshop_area` VALUES (510600, 510000, 2, '德阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (510601, 510600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (510603, 510600, 3, '旌阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (510623, 510600, 3, '中江县', 0, 100);
INSERT INTO `jshop_area` VALUES (510626, 510600, 3, '罗江县', 0, 100);
INSERT INTO `jshop_area` VALUES (510681, 510600, 3, '广汉市', 0, 100);
INSERT INTO `jshop_area` VALUES (510682, 510600, 3, '什邡市', 0, 100);
INSERT INTO `jshop_area` VALUES (510683, 510600, 3, '绵竹市', 0, 100);
INSERT INTO `jshop_area` VALUES (510700, 510000, 2, '绵阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (510701, 510700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (510703, 510700, 3, '涪城区', 0, 100);
INSERT INTO `jshop_area` VALUES (510704, 510700, 3, '游仙区', 0, 100);
INSERT INTO `jshop_area` VALUES (510705, 510700, 3, '安州区', 0, 100);
INSERT INTO `jshop_area` VALUES (510722, 510700, 3, '三台县', 0, 100);
INSERT INTO `jshop_area` VALUES (510723, 510700, 3, '盐亭县', 0, 100);
INSERT INTO `jshop_area` VALUES (510725, 510700, 3, '梓潼县', 0, 100);
INSERT INTO `jshop_area` VALUES (510726, 510700, 3, '北川羌族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (510727, 510700, 3, '平武县', 0, 100);
INSERT INTO `jshop_area` VALUES (510781, 510700, 3, '江油市', 0, 100);
INSERT INTO `jshop_area` VALUES (510800, 510000, 2, '广元市', 0, 100);
INSERT INTO `jshop_area` VALUES (510801, 510800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (510802, 510800, 3, '利州区', 0, 100);
INSERT INTO `jshop_area` VALUES (510811, 510800, 3, '昭化区', 0, 100);
INSERT INTO `jshop_area` VALUES (510812, 510800, 3, '朝天区', 0, 100);
INSERT INTO `jshop_area` VALUES (510821, 510800, 3, '旺苍县', 0, 100);
INSERT INTO `jshop_area` VALUES (510822, 510800, 3, '青川县', 0, 100);
INSERT INTO `jshop_area` VALUES (510823, 510800, 3, '剑阁县', 0, 100);
INSERT INTO `jshop_area` VALUES (510824, 510800, 3, '苍溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (510900, 510000, 2, '遂宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (510901, 510900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (510903, 510900, 3, '船山区', 0, 100);
INSERT INTO `jshop_area` VALUES (510904, 510900, 3, '安居区', 0, 100);
INSERT INTO `jshop_area` VALUES (510921, 510900, 3, '蓬溪县', 0, 100);
INSERT INTO `jshop_area` VALUES (510922, 510900, 3, '射洪县', 0, 100);
INSERT INTO `jshop_area` VALUES (510923, 510900, 3, '大英县', 0, 100);
INSERT INTO `jshop_area` VALUES (511000, 510000, 2, '内江市', 0, 100);
INSERT INTO `jshop_area` VALUES (511001, 511000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511002, 511000, 3, '市中区', 0, 100);
INSERT INTO `jshop_area` VALUES (511011, 511000, 3, '东兴区', 0, 100);
INSERT INTO `jshop_area` VALUES (511024, 511000, 3, '威远县', 0, 100);
INSERT INTO `jshop_area` VALUES (511025, 511000, 3, '资中县', 0, 100);
INSERT INTO `jshop_area` VALUES (511028, 511000, 3, '隆昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (511100, 510000, 2, '乐山市', 0, 100);
INSERT INTO `jshop_area` VALUES (511101, 511100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511102, 511100, 3, '市中区', 0, 100);
INSERT INTO `jshop_area` VALUES (511111, 511100, 3, '沙湾区', 0, 100);
INSERT INTO `jshop_area` VALUES (511112, 511100, 3, '五通桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (511113, 511100, 3, '金口河区', 0, 100);
INSERT INTO `jshop_area` VALUES (511123, 511100, 3, '犍为县', 0, 100);
INSERT INTO `jshop_area` VALUES (511124, 511100, 3, '井研县', 0, 100);
INSERT INTO `jshop_area` VALUES (511126, 511100, 3, '夹江县', 0, 100);
INSERT INTO `jshop_area` VALUES (511129, 511100, 3, '沐川县', 0, 100);
INSERT INTO `jshop_area` VALUES (511132, 511100, 3, '峨边彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (511133, 511100, 3, '马边彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (511181, 511100, 3, '峨眉山市', 0, 100);
INSERT INTO `jshop_area` VALUES (511300, 510000, 2, '南充市', 0, 100);
INSERT INTO `jshop_area` VALUES (511301, 511300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511302, 511300, 3, '顺庆区', 0, 100);
INSERT INTO `jshop_area` VALUES (511303, 511300, 3, '高坪区', 0, 100);
INSERT INTO `jshop_area` VALUES (511304, 511300, 3, '嘉陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (511321, 511300, 3, '南部县', 0, 100);
INSERT INTO `jshop_area` VALUES (511322, 511300, 3, '营山县', 0, 100);
INSERT INTO `jshop_area` VALUES (511323, 511300, 3, '蓬安县', 0, 100);
INSERT INTO `jshop_area` VALUES (511324, 511300, 3, '仪陇县', 0, 100);
INSERT INTO `jshop_area` VALUES (511325, 511300, 3, '西充县', 0, 100);
INSERT INTO `jshop_area` VALUES (511381, 511300, 3, '阆中市', 0, 100);
INSERT INTO `jshop_area` VALUES (511400, 510000, 2, '眉山市', 0, 100);
INSERT INTO `jshop_area` VALUES (511401, 511400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511402, 511400, 3, '东坡区', 0, 100);
INSERT INTO `jshop_area` VALUES (511403, 511400, 3, '彭山区', 0, 100);
INSERT INTO `jshop_area` VALUES (511421, 511400, 3, '仁寿县', 0, 100);
INSERT INTO `jshop_area` VALUES (511423, 511400, 3, '洪雅县', 0, 100);
INSERT INTO `jshop_area` VALUES (511424, 511400, 3, '丹棱县', 0, 100);
INSERT INTO `jshop_area` VALUES (511425, 511400, 3, '青神县', 0, 100);
INSERT INTO `jshop_area` VALUES (511500, 510000, 2, '宜宾市', 0, 100);
INSERT INTO `jshop_area` VALUES (511501, 511500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511502, 511500, 3, '翠屏区', 0, 100);
INSERT INTO `jshop_area` VALUES (511503, 511500, 3, '南溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (511521, 511500, 3, '宜宾县', 0, 100);
INSERT INTO `jshop_area` VALUES (511523, 511500, 3, '江安县', 0, 100);
INSERT INTO `jshop_area` VALUES (511524, 511500, 3, '长宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (511525, 511500, 3, '高县', 0, 100);
INSERT INTO `jshop_area` VALUES (511526, 511500, 3, '珙县', 0, 100);
INSERT INTO `jshop_area` VALUES (511527, 511500, 3, '筠连县', 0, 100);
INSERT INTO `jshop_area` VALUES (511528, 511500, 3, '兴文县', 0, 100);
INSERT INTO `jshop_area` VALUES (511529, 511500, 3, '屏山县', 0, 100);
INSERT INTO `jshop_area` VALUES (511600, 510000, 2, '广安市', 0, 100);
INSERT INTO `jshop_area` VALUES (511601, 511600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511602, 511600, 3, '广安区', 0, 100);
INSERT INTO `jshop_area` VALUES (511603, 511600, 3, '前锋区', 0, 100);
INSERT INTO `jshop_area` VALUES (511621, 511600, 3, '岳池县', 0, 100);
INSERT INTO `jshop_area` VALUES (511622, 511600, 3, '武胜县', 0, 100);
INSERT INTO `jshop_area` VALUES (511623, 511600, 3, '邻水县', 0, 100);
INSERT INTO `jshop_area` VALUES (511681, 511600, 3, '华蓥市', 0, 100);
INSERT INTO `jshop_area` VALUES (511700, 510000, 2, '达州市', 0, 100);
INSERT INTO `jshop_area` VALUES (511701, 511700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511702, 511700, 3, '通川区', 0, 100);
INSERT INTO `jshop_area` VALUES (511703, 511700, 3, '达川区', 0, 100);
INSERT INTO `jshop_area` VALUES (511722, 511700, 3, '宣汉县', 0, 100);
INSERT INTO `jshop_area` VALUES (511723, 511700, 3, '开江县', 0, 100);
INSERT INTO `jshop_area` VALUES (511724, 511700, 3, '大竹县', 0, 100);
INSERT INTO `jshop_area` VALUES (511725, 511700, 3, '渠县', 0, 100);
INSERT INTO `jshop_area` VALUES (511781, 511700, 3, '万源市', 0, 100);
INSERT INTO `jshop_area` VALUES (511800, 510000, 2, '雅安市', 0, 100);
INSERT INTO `jshop_area` VALUES (511801, 511800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511802, 511800, 3, '雨城区', 0, 100);
INSERT INTO `jshop_area` VALUES (511803, 511800, 3, '名山区', 0, 100);
INSERT INTO `jshop_area` VALUES (511822, 511800, 3, '荥经县', 0, 100);
INSERT INTO `jshop_area` VALUES (511823, 511800, 3, '汉源县', 0, 100);
INSERT INTO `jshop_area` VALUES (511824, 511800, 3, '石棉县', 0, 100);
INSERT INTO `jshop_area` VALUES (511825, 511800, 3, '天全县', 0, 100);
INSERT INTO `jshop_area` VALUES (511826, 511800, 3, '芦山县', 0, 100);
INSERT INTO `jshop_area` VALUES (511827, 511800, 3, '宝兴县', 0, 100);
INSERT INTO `jshop_area` VALUES (511900, 510000, 2, '巴中市', 0, 100);
INSERT INTO `jshop_area` VALUES (511901, 511900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (511902, 511900, 3, '巴州区', 0, 100);
INSERT INTO `jshop_area` VALUES (511903, 511900, 3, '恩阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (511921, 511900, 3, '通江县', 0, 100);
INSERT INTO `jshop_area` VALUES (511922, 511900, 3, '南江县', 0, 100);
INSERT INTO `jshop_area` VALUES (511923, 511900, 3, '平昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (512000, 510000, 2, '资阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (512001, 512000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (512002, 512000, 3, '雁江区', 0, 100);
INSERT INTO `jshop_area` VALUES (512021, 512000, 3, '安岳县', 0, 100);
INSERT INTO `jshop_area` VALUES (512022, 512000, 3, '乐至县', 0, 100);
INSERT INTO `jshop_area` VALUES (513200, 510000, 2, '阿坝藏族羌族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (513201, 513200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (513221, 513200, 3, '汶川县', 0, 100);
INSERT INTO `jshop_area` VALUES (513222, 513200, 3, '理县', 0, 100);
INSERT INTO `jshop_area` VALUES (513223, 513200, 3, '茂县', 0, 100);
INSERT INTO `jshop_area` VALUES (513224, 513200, 3, '松潘县', 0, 100);
INSERT INTO `jshop_area` VALUES (513225, 513200, 3, '九寨沟县', 0, 100);
INSERT INTO `jshop_area` VALUES (513226, 513200, 3, '金川县', 0, 100);
INSERT INTO `jshop_area` VALUES (513227, 513200, 3, '小金县', 0, 100);
INSERT INTO `jshop_area` VALUES (513228, 513200, 3, '黑水县', 0, 100);
INSERT INTO `jshop_area` VALUES (513230, 513200, 3, '壤塘县', 0, 100);
INSERT INTO `jshop_area` VALUES (513231, 513200, 3, '阿坝县', 0, 100);
INSERT INTO `jshop_area` VALUES (513232, 513200, 3, '若尔盖县', 0, 100);
INSERT INTO `jshop_area` VALUES (513233, 513200, 3, '红原县', 0, 100);
INSERT INTO `jshop_area` VALUES (513300, 510000, 2, '甘孜藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (513301, 513300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (513322, 513300, 3, '泸定县', 0, 100);
INSERT INTO `jshop_area` VALUES (513323, 513300, 3, '丹巴县', 0, 100);
INSERT INTO `jshop_area` VALUES (513324, 513300, 3, '九龙县', 0, 100);
INSERT INTO `jshop_area` VALUES (513325, 513300, 3, '雅江县', 0, 100);
INSERT INTO `jshop_area` VALUES (513326, 513300, 3, '道孚县', 0, 100);
INSERT INTO `jshop_area` VALUES (513327, 513300, 3, '炉霍县', 0, 100);
INSERT INTO `jshop_area` VALUES (513328, 513300, 3, '甘孜县', 0, 100);
INSERT INTO `jshop_area` VALUES (513329, 513300, 3, '新龙县', 0, 100);
INSERT INTO `jshop_area` VALUES (513330, 513300, 3, '德格县', 0, 100);
INSERT INTO `jshop_area` VALUES (513331, 513300, 3, '白玉县', 0, 100);
INSERT INTO `jshop_area` VALUES (513332, 513300, 3, '石渠县', 0, 100);
INSERT INTO `jshop_area` VALUES (513333, 513300, 3, '色达县', 0, 100);
INSERT INTO `jshop_area` VALUES (513334, 513300, 3, '理塘县', 0, 100);
INSERT INTO `jshop_area` VALUES (513335, 513300, 3, '巴塘县', 0, 100);
INSERT INTO `jshop_area` VALUES (513336, 513300, 3, '乡城县', 0, 100);
INSERT INTO `jshop_area` VALUES (513337, 513300, 3, '稻城县', 0, 100);
INSERT INTO `jshop_area` VALUES (513338, 513300, 3, '得荣县', 0, 100);
INSERT INTO `jshop_area` VALUES (513400, 510000, 2, '凉山彝族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (513401, 513400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (513422, 513400, 3, '木里藏族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (513423, 513400, 3, '盐源县', 0, 100);
INSERT INTO `jshop_area` VALUES (513424, 513400, 3, '德昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (513425, 513400, 3, '会理县', 0, 100);
INSERT INTO `jshop_area` VALUES (513426, 513400, 3, '会东县', 0, 100);
INSERT INTO `jshop_area` VALUES (513427, 513400, 3, '宁南县', 0, 100);
INSERT INTO `jshop_area` VALUES (513428, 513400, 3, '普格县', 0, 100);
INSERT INTO `jshop_area` VALUES (513429, 513400, 3, '布拖县', 0, 100);
INSERT INTO `jshop_area` VALUES (513430, 513400, 3, '金阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (513431, 513400, 3, '昭觉县', 0, 100);
INSERT INTO `jshop_area` VALUES (513432, 513400, 3, '喜德县', 0, 100);
INSERT INTO `jshop_area` VALUES (513433, 513400, 3, '冕宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (513434, 513400, 3, '越西县', 0, 100);
INSERT INTO `jshop_area` VALUES (513435, 513400, 3, '甘洛县', 0, 100);
INSERT INTO `jshop_area` VALUES (513436, 513400, 3, '美姑县', 0, 100);
INSERT INTO `jshop_area` VALUES (513437, 513400, 3, '雷波县', 0, 100);
INSERT INTO `jshop_area` VALUES (520000, 0, 1, '贵州省', 0, 100);
INSERT INTO `jshop_area` VALUES (520100, 520000, 2, '贵阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (520101, 520100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (520102, 520100, 3, '南明区', 0, 100);
INSERT INTO `jshop_area` VALUES (520103, 520100, 3, '云岩区', 0, 100);
INSERT INTO `jshop_area` VALUES (520111, 520100, 3, '花溪区', 0, 100);
INSERT INTO `jshop_area` VALUES (520112, 520100, 3, '乌当区', 0, 100);
INSERT INTO `jshop_area` VALUES (520113, 520100, 3, '白云区', 0, 100);
INSERT INTO `jshop_area` VALUES (520115, 520100, 3, '观山湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (520121, 520100, 3, '开阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (520122, 520100, 3, '息烽县', 0, 100);
INSERT INTO `jshop_area` VALUES (520123, 520100, 3, '修文县', 0, 100);
INSERT INTO `jshop_area` VALUES (520181, 520100, 3, '清镇市', 0, 100);
INSERT INTO `jshop_area` VALUES (520200, 520000, 2, '六盘水市', 0, 100);
INSERT INTO `jshop_area` VALUES (520201, 520200, 3, '钟山区', 0, 100);
INSERT INTO `jshop_area` VALUES (520203, 520200, 3, '六枝特区', 0, 100);
INSERT INTO `jshop_area` VALUES (520221, 520200, 3, '水城县', 0, 100);
INSERT INTO `jshop_area` VALUES (520222, 520200, 3, '盘县', 0, 100);
INSERT INTO `jshop_area` VALUES (520300, 520000, 2, '遵义市', 0, 100);
INSERT INTO `jshop_area` VALUES (520301, 520300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (520302, 520300, 3, '红花岗区', 0, 100);
INSERT INTO `jshop_area` VALUES (520303, 520300, 3, '汇川区', 0, 100);
INSERT INTO `jshop_area` VALUES (520304, 520300, 3, '播州区', 0, 100);
INSERT INTO `jshop_area` VALUES (520322, 520300, 3, '桐梓县', 0, 100);
INSERT INTO `jshop_area` VALUES (520323, 520300, 3, '绥阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (520324, 520300, 3, '正安县', 0, 100);
INSERT INTO `jshop_area` VALUES (520325, 520300, 3, '道真仡佬族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520326, 520300, 3, '务川仡佬族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520327, 520300, 3, '凤冈县', 0, 100);
INSERT INTO `jshop_area` VALUES (520328, 520300, 3, '湄潭县', 0, 100);
INSERT INTO `jshop_area` VALUES (520329, 520300, 3, '余庆县', 0, 100);
INSERT INTO `jshop_area` VALUES (520330, 520300, 3, '习水县', 0, 100);
INSERT INTO `jshop_area` VALUES (520381, 520300, 3, '赤水市', 0, 100);
INSERT INTO `jshop_area` VALUES (520382, 520300, 3, '仁怀市', 0, 100);
INSERT INTO `jshop_area` VALUES (520400, 520000, 2, '安顺市', 0, 100);
INSERT INTO `jshop_area` VALUES (520401, 520400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (520402, 520400, 3, '西秀区', 0, 100);
INSERT INTO `jshop_area` VALUES (520403, 520400, 3, '平坝区', 0, 100);
INSERT INTO `jshop_area` VALUES (520422, 520400, 3, '普定县', 0, 100);
INSERT INTO `jshop_area` VALUES (520423, 520400, 3, '镇宁布依族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520424, 520400, 3, '关岭布依族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520425, 520400, 3, '紫云苗族布依族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520500, 520000, 2, '毕节市', 0, 100);
INSERT INTO `jshop_area` VALUES (520501, 520500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (520502, 520500, 3, '七星关区', 0, 100);
INSERT INTO `jshop_area` VALUES (520521, 520500, 3, '大方县', 0, 100);
INSERT INTO `jshop_area` VALUES (520522, 520500, 3, '黔西县', 0, 100);
INSERT INTO `jshop_area` VALUES (520523, 520500, 3, '金沙县', 0, 100);
INSERT INTO `jshop_area` VALUES (520524, 520500, 3, '织金县', 0, 100);
INSERT INTO `jshop_area` VALUES (520525, 520500, 3, '纳雍县', 0, 100);
INSERT INTO `jshop_area` VALUES (520526, 520500, 3, '威宁彝族回族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520527, 520500, 3, '赫章县', 0, 100);
INSERT INTO `jshop_area` VALUES (520600, 520000, 2, '铜仁市', 0, 100);
INSERT INTO `jshop_area` VALUES (520601, 520600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (520602, 520600, 3, '碧江区', 0, 100);
INSERT INTO `jshop_area` VALUES (520603, 520600, 3, '万山区', 0, 100);
INSERT INTO `jshop_area` VALUES (520621, 520600, 3, '江口县', 0, 100);
INSERT INTO `jshop_area` VALUES (520622, 520600, 3, '玉屏侗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520623, 520600, 3, '石阡县', 0, 100);
INSERT INTO `jshop_area` VALUES (520624, 520600, 3, '思南县', 0, 100);
INSERT INTO `jshop_area` VALUES (520625, 520600, 3, '印江土家族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520626, 520600, 3, '德江县', 0, 100);
INSERT INTO `jshop_area` VALUES (520627, 520600, 3, '沿河土家族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (520628, 520600, 3, '松桃苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (522300, 520000, 2, '黔西南布依族苗族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (522301, 522300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (522322, 522300, 3, '兴仁县', 0, 100);
INSERT INTO `jshop_area` VALUES (522323, 522300, 3, '普安县', 0, 100);
INSERT INTO `jshop_area` VALUES (522324, 522300, 3, '晴隆县', 0, 100);
INSERT INTO `jshop_area` VALUES (522325, 522300, 3, '贞丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (522326, 522300, 3, '望谟县', 0, 100);
INSERT INTO `jshop_area` VALUES (522327, 522300, 3, '册亨县', 0, 100);
INSERT INTO `jshop_area` VALUES (522328, 522300, 3, '安龙县', 0, 100);
INSERT INTO `jshop_area` VALUES (522600, 520000, 2, '黔东南苗族侗族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (522601, 522600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (522622, 522600, 3, '黄平县', 0, 100);
INSERT INTO `jshop_area` VALUES (522623, 522600, 3, '施秉县', 0, 100);
INSERT INTO `jshop_area` VALUES (522624, 522600, 3, '三穗县', 0, 100);
INSERT INTO `jshop_area` VALUES (522625, 522600, 3, '镇远县', 0, 100);
INSERT INTO `jshop_area` VALUES (522626, 522600, 3, '岑巩县', 0, 100);
INSERT INTO `jshop_area` VALUES (522627, 522600, 3, '天柱县', 0, 100);
INSERT INTO `jshop_area` VALUES (522628, 522600, 3, '锦屏县', 0, 100);
INSERT INTO `jshop_area` VALUES (522629, 522600, 3, '剑河县', 0, 100);
INSERT INTO `jshop_area` VALUES (522630, 522600, 3, '台江县', 0, 100);
INSERT INTO `jshop_area` VALUES (522631, 522600, 3, '黎平县', 0, 100);
INSERT INTO `jshop_area` VALUES (522632, 522600, 3, '榕江县', 0, 100);
INSERT INTO `jshop_area` VALUES (522633, 522600, 3, '从江县', 0, 100);
INSERT INTO `jshop_area` VALUES (522634, 522600, 3, '雷山县', 0, 100);
INSERT INTO `jshop_area` VALUES (522635, 522600, 3, '麻江县', 0, 100);
INSERT INTO `jshop_area` VALUES (522636, 522600, 3, '丹寨县', 0, 100);
INSERT INTO `jshop_area` VALUES (522700, 520000, 2, '黔南布依族苗族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (522701, 522700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (522702, 522700, 3, '福泉市', 0, 100);
INSERT INTO `jshop_area` VALUES (522722, 522700, 3, '荔波县', 0, 100);
INSERT INTO `jshop_area` VALUES (522723, 522700, 3, '贵定县', 0, 100);
INSERT INTO `jshop_area` VALUES (522725, 522700, 3, '瓮安县', 0, 100);
INSERT INTO `jshop_area` VALUES (522726, 522700, 3, '独山县', 0, 100);
INSERT INTO `jshop_area` VALUES (522727, 522700, 3, '平塘县', 0, 100);
INSERT INTO `jshop_area` VALUES (522728, 522700, 3, '罗甸县', 0, 100);
INSERT INTO `jshop_area` VALUES (522729, 522700, 3, '长顺县', 0, 100);
INSERT INTO `jshop_area` VALUES (522730, 522700, 3, '龙里县', 0, 100);
INSERT INTO `jshop_area` VALUES (522731, 522700, 3, '惠水县', 0, 100);
INSERT INTO `jshop_area` VALUES (522732, 522700, 3, '三都水族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530000, 0, 1, '云南省', 0, 100);
INSERT INTO `jshop_area` VALUES (530100, 530000, 2, '昆明市', 0, 100);
INSERT INTO `jshop_area` VALUES (530101, 530100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (530102, 530100, 3, '五华区', 0, 100);
INSERT INTO `jshop_area` VALUES (530103, 530100, 3, '盘龙区', 0, 100);
INSERT INTO `jshop_area` VALUES (530111, 530100, 3, '官渡区', 0, 100);
INSERT INTO `jshop_area` VALUES (530112, 530100, 3, '西山区', 0, 100);
INSERT INTO `jshop_area` VALUES (530113, 530100, 3, '东川区', 0, 100);
INSERT INTO `jshop_area` VALUES (530114, 530100, 3, '呈贡区', 0, 100);
INSERT INTO `jshop_area` VALUES (530122, 530100, 3, '晋宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (530124, 530100, 3, '富民县', 0, 100);
INSERT INTO `jshop_area` VALUES (530125, 530100, 3, '宜良县', 0, 100);
INSERT INTO `jshop_area` VALUES (530126, 530100, 3, '石林彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530127, 530100, 3, '嵩明县', 0, 100);
INSERT INTO `jshop_area` VALUES (530128, 530100, 3, '禄劝彝族苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530129, 530100, 3, '寻甸回族彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530181, 530100, 3, '安宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (530300, 530000, 2, '曲靖市', 0, 100);
INSERT INTO `jshop_area` VALUES (530301, 530300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (530302, 530300, 3, '麒麟区', 0, 100);
INSERT INTO `jshop_area` VALUES (530303, 530300, 3, '沾益区', 0, 100);
INSERT INTO `jshop_area` VALUES (530321, 530300, 3, '马龙县', 0, 100);
INSERT INTO `jshop_area` VALUES (530322, 530300, 3, '陆良县', 0, 100);
INSERT INTO `jshop_area` VALUES (530323, 530300, 3, '师宗县', 0, 100);
INSERT INTO `jshop_area` VALUES (530324, 530300, 3, '罗平县', 0, 100);
INSERT INTO `jshop_area` VALUES (530325, 530300, 3, '富源县', 0, 100);
INSERT INTO `jshop_area` VALUES (530326, 530300, 3, '会泽县', 0, 100);
INSERT INTO `jshop_area` VALUES (530381, 530300, 3, '宣威市', 0, 100);
INSERT INTO `jshop_area` VALUES (530400, 530000, 2, '玉溪市', 0, 100);
INSERT INTO `jshop_area` VALUES (530401, 530400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (530402, 530400, 3, '红塔区', 0, 100);
INSERT INTO `jshop_area` VALUES (530403, 530400, 3, '江川区', 0, 100);
INSERT INTO `jshop_area` VALUES (530422, 530400, 3, '澄江县', 0, 100);
INSERT INTO `jshop_area` VALUES (530423, 530400, 3, '通海县', 0, 100);
INSERT INTO `jshop_area` VALUES (530424, 530400, 3, '华宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (530425, 530400, 3, '易门县', 0, 100);
INSERT INTO `jshop_area` VALUES (530426, 530400, 3, '峨山彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530427, 530400, 3, '新平彝族傣族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530428, 530400, 3, '元江哈尼族彝族傣族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530500, 530000, 2, '保山市', 0, 100);
INSERT INTO `jshop_area` VALUES (530501, 530500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (530502, 530500, 3, '隆阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (530521, 530500, 3, '施甸县', 0, 100);
INSERT INTO `jshop_area` VALUES (530523, 530500, 3, '龙陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (530524, 530500, 3, '昌宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (530581, 530500, 3, '腾冲市', 0, 100);
INSERT INTO `jshop_area` VALUES (530600, 530000, 2, '昭通市', 0, 100);
INSERT INTO `jshop_area` VALUES (530601, 530600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (530602, 530600, 3, '昭阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (530621, 530600, 3, '鲁甸县', 0, 100);
INSERT INTO `jshop_area` VALUES (530622, 530600, 3, '巧家县', 0, 100);
INSERT INTO `jshop_area` VALUES (530623, 530600, 3, '盐津县', 0, 100);
INSERT INTO `jshop_area` VALUES (530624, 530600, 3, '大关县', 0, 100);
INSERT INTO `jshop_area` VALUES (530625, 530600, 3, '永善县', 0, 100);
INSERT INTO `jshop_area` VALUES (530626, 530600, 3, '绥江县', 0, 100);
INSERT INTO `jshop_area` VALUES (530627, 530600, 3, '镇雄县', 0, 100);
INSERT INTO `jshop_area` VALUES (530628, 530600, 3, '彝良县', 0, 100);
INSERT INTO `jshop_area` VALUES (530629, 530600, 3, '威信县', 0, 100);
INSERT INTO `jshop_area` VALUES (530630, 530600, 3, '水富县', 0, 100);
INSERT INTO `jshop_area` VALUES (530700, 530000, 2, '丽江市', 0, 100);
INSERT INTO `jshop_area` VALUES (530701, 530700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (530702, 530700, 3, '古城区', 0, 100);
INSERT INTO `jshop_area` VALUES (530721, 530700, 3, '玉龙纳西族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530722, 530700, 3, '永胜县', 0, 100);
INSERT INTO `jshop_area` VALUES (530723, 530700, 3, '华坪县', 0, 100);
INSERT INTO `jshop_area` VALUES (530724, 530700, 3, '宁蒗彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530800, 530000, 2, '普洱市', 0, 100);
INSERT INTO `jshop_area` VALUES (530801, 530800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (530802, 530800, 3, '思茅区', 0, 100);
INSERT INTO `jshop_area` VALUES (530821, 530800, 3, '宁洱哈尼族彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530822, 530800, 3, '墨江哈尼族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530823, 530800, 3, '景东彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530824, 530800, 3, '景谷傣族彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530825, 530800, 3, '镇沅彝族哈尼族拉祜族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530826, 530800, 3, '江城哈尼族彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530827, 530800, 3, '孟连傣族拉祜族佤族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530828, 530800, 3, '澜沧拉祜族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530829, 530800, 3, '西盟佤族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530900, 530000, 2, '临沧市', 0, 100);
INSERT INTO `jshop_area` VALUES (530901, 530900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (530902, 530900, 3, '临翔区', 0, 100);
INSERT INTO `jshop_area` VALUES (530921, 530900, 3, '凤庆县', 0, 100);
INSERT INTO `jshop_area` VALUES (530922, 530900, 3, '云县', 0, 100);
INSERT INTO `jshop_area` VALUES (530923, 530900, 3, '永德县', 0, 100);
INSERT INTO `jshop_area` VALUES (530924, 530900, 3, '镇康县', 0, 100);
INSERT INTO `jshop_area` VALUES (530925, 530900, 3, '双江拉祜族佤族布朗族傣族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530926, 530900, 3, '耿马傣族佤族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (530927, 530900, 3, '沧源佤族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (532300, 530000, 2, '楚雄彝族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (532301, 532300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (532322, 532300, 3, '双柏县', 0, 100);
INSERT INTO `jshop_area` VALUES (532323, 532300, 3, '牟定县', 0, 100);
INSERT INTO `jshop_area` VALUES (532324, 532300, 3, '南华县', 0, 100);
INSERT INTO `jshop_area` VALUES (532325, 532300, 3, '姚安县', 0, 100);
INSERT INTO `jshop_area` VALUES (532326, 532300, 3, '大姚县', 0, 100);
INSERT INTO `jshop_area` VALUES (532327, 532300, 3, '永仁县', 0, 100);
INSERT INTO `jshop_area` VALUES (532328, 532300, 3, '元谋县', 0, 100);
INSERT INTO `jshop_area` VALUES (532329, 532300, 3, '武定县', 0, 100);
INSERT INTO `jshop_area` VALUES (532331, 532300, 3, '禄丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (532500, 530000, 2, '红河哈尼族彝族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (532501, 532500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (532502, 532500, 3, '开远市', 0, 100);
INSERT INTO `jshop_area` VALUES (532503, 532500, 3, '蒙自市', 0, 100);
INSERT INTO `jshop_area` VALUES (532504, 532500, 3, '弥勒市', 0, 100);
INSERT INTO `jshop_area` VALUES (532523, 532500, 3, '屏边苗族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (532524, 532500, 3, '建水县', 0, 100);
INSERT INTO `jshop_area` VALUES (532525, 532500, 3, '石屏县', 0, 100);
INSERT INTO `jshop_area` VALUES (532527, 532500, 3, '泸西县', 0, 100);
INSERT INTO `jshop_area` VALUES (532528, 532500, 3, '元阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (532529, 532500, 3, '红河县', 0, 100);
INSERT INTO `jshop_area` VALUES (532530, 532500, 3, '金平苗族瑶族傣族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (532531, 532500, 3, '绿春县', 0, 100);
INSERT INTO `jshop_area` VALUES (532532, 532500, 3, '河口瑶族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (532600, 530000, 2, '文山壮族苗族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (532601, 532600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (532622, 532600, 3, '砚山县', 0, 100);
INSERT INTO `jshop_area` VALUES (532623, 532600, 3, '西畴县', 0, 100);
INSERT INTO `jshop_area` VALUES (532624, 532600, 3, '麻栗坡县', 0, 100);
INSERT INTO `jshop_area` VALUES (532625, 532600, 3, '马关县', 0, 100);
INSERT INTO `jshop_area` VALUES (532626, 532600, 3, '丘北县', 0, 100);
INSERT INTO `jshop_area` VALUES (532627, 532600, 3, '广南县', 0, 100);
INSERT INTO `jshop_area` VALUES (532628, 532600, 3, '富宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (532800, 530000, 2, '西双版纳傣族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (532801, 532800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (532822, 532800, 3, '勐海县', 0, 100);
INSERT INTO `jshop_area` VALUES (532823, 532800, 3, '勐腊县', 0, 100);
INSERT INTO `jshop_area` VALUES (532900, 530000, 2, '大理白族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (532901, 532900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (532922, 532900, 3, '漾濞彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (532923, 532900, 3, '祥云县', 0, 100);
INSERT INTO `jshop_area` VALUES (532924, 532900, 3, '宾川县', 0, 100);
INSERT INTO `jshop_area` VALUES (532925, 532900, 3, '弥渡县', 0, 100);
INSERT INTO `jshop_area` VALUES (532926, 532900, 3, '南涧彝族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (532927, 532900, 3, '巍山彝族回族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (532928, 532900, 3, '永平县', 0, 100);
INSERT INTO `jshop_area` VALUES (532929, 532900, 3, '云龙县', 0, 100);
INSERT INTO `jshop_area` VALUES (532930, 532900, 3, '洱源县', 0, 100);
INSERT INTO `jshop_area` VALUES (532931, 532900, 3, '剑川县', 0, 100);
INSERT INTO `jshop_area` VALUES (532932, 532900, 3, '鹤庆县', 0, 100);
INSERT INTO `jshop_area` VALUES (533100, 530000, 2, '德宏傣族景颇族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (533102, 533100, 3, '瑞丽市', 0, 100);
INSERT INTO `jshop_area` VALUES (533103, 533100, 3, '芒市', 0, 100);
INSERT INTO `jshop_area` VALUES (533122, 533100, 3, '梁河县', 0, 100);
INSERT INTO `jshop_area` VALUES (533123, 533100, 3, '盈江县', 0, 100);
INSERT INTO `jshop_area` VALUES (533124, 533100, 3, '陇川县', 0, 100);
INSERT INTO `jshop_area` VALUES (533300, 530000, 2, '怒江傈僳族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (533301, 533300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (533323, 533300, 3, '福贡县', 0, 100);
INSERT INTO `jshop_area` VALUES (533324, 533300, 3, '贡山独龙族怒族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (533325, 533300, 3, '兰坪白族普米族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (533400, 530000, 2, '迪庆藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (533401, 533400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (533422, 533400, 3, '德钦县', 0, 100);
INSERT INTO `jshop_area` VALUES (533423, 533400, 3, '维西傈僳族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (540000, 0, 1, '西藏自治区', 0, 100);
INSERT INTO `jshop_area` VALUES (540100, 540000, 2, '拉萨市', 0, 100);
INSERT INTO `jshop_area` VALUES (540101, 540100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (540102, 540100, 3, '城关区', 0, 100);
INSERT INTO `jshop_area` VALUES (540103, 540100, 3, '堆龙德庆区', 0, 100);
INSERT INTO `jshop_area` VALUES (540121, 540100, 3, '林周县', 0, 100);
INSERT INTO `jshop_area` VALUES (540122, 540100, 3, '当雄县', 0, 100);
INSERT INTO `jshop_area` VALUES (540123, 540100, 3, '尼木县', 0, 100);
INSERT INTO `jshop_area` VALUES (540124, 540100, 3, '曲水县', 0, 100);
INSERT INTO `jshop_area` VALUES (540126, 540100, 3, '达孜县', 0, 100);
INSERT INTO `jshop_area` VALUES (540127, 540100, 3, '墨竹工卡县', 0, 100);
INSERT INTO `jshop_area` VALUES (540200, 540000, 2, '日喀则市', 0, 100);
INSERT INTO `jshop_area` VALUES (540202, 540200, 3, '桑珠孜区', 0, 100);
INSERT INTO `jshop_area` VALUES (540221, 540200, 3, '南木林县', 0, 100);
INSERT INTO `jshop_area` VALUES (540222, 540200, 3, '江孜县', 0, 100);
INSERT INTO `jshop_area` VALUES (540223, 540200, 3, '定日县', 0, 100);
INSERT INTO `jshop_area` VALUES (540224, 540200, 3, '萨迦县', 0, 100);
INSERT INTO `jshop_area` VALUES (540225, 540200, 3, '拉孜县', 0, 100);
INSERT INTO `jshop_area` VALUES (540226, 540200, 3, '昂仁县', 0, 100);
INSERT INTO `jshop_area` VALUES (540227, 540200, 3, '谢通门县', 0, 100);
INSERT INTO `jshop_area` VALUES (540228, 540200, 3, '白朗县', 0, 100);
INSERT INTO `jshop_area` VALUES (540229, 540200, 3, '仁布县', 0, 100);
INSERT INTO `jshop_area` VALUES (540230, 540200, 3, '康马县', 0, 100);
INSERT INTO `jshop_area` VALUES (540231, 540200, 3, '定结县', 0, 100);
INSERT INTO `jshop_area` VALUES (540232, 540200, 3, '仲巴县', 0, 100);
INSERT INTO `jshop_area` VALUES (540233, 540200, 3, '亚东县', 0, 100);
INSERT INTO `jshop_area` VALUES (540234, 540200, 3, '吉隆县', 0, 100);
INSERT INTO `jshop_area` VALUES (540235, 540200, 3, '聂拉木县', 0, 100);
INSERT INTO `jshop_area` VALUES (540236, 540200, 3, '萨嘎县', 0, 100);
INSERT INTO `jshop_area` VALUES (540237, 540200, 3, '岗巴县', 0, 100);
INSERT INTO `jshop_area` VALUES (540300, 540000, 2, '昌都市', 0, 100);
INSERT INTO `jshop_area` VALUES (540302, 540300, 3, '卡若区', 0, 100);
INSERT INTO `jshop_area` VALUES (540321, 540300, 3, '江达县', 0, 100);
INSERT INTO `jshop_area` VALUES (540322, 540300, 3, '贡觉县', 0, 100);
INSERT INTO `jshop_area` VALUES (540323, 540300, 3, '类乌齐县', 0, 100);
INSERT INTO `jshop_area` VALUES (540324, 540300, 3, '丁青县', 0, 100);
INSERT INTO `jshop_area` VALUES (540325, 540300, 3, '察雅县', 0, 100);
INSERT INTO `jshop_area` VALUES (540326, 540300, 3, '八宿县', 0, 100);
INSERT INTO `jshop_area` VALUES (540327, 540300, 3, '左贡县', 0, 100);
INSERT INTO `jshop_area` VALUES (540328, 540300, 3, '芒康县', 0, 100);
INSERT INTO `jshop_area` VALUES (540329, 540300, 3, '洛隆县', 0, 100);
INSERT INTO `jshop_area` VALUES (540330, 540300, 3, '边坝县', 0, 100);
INSERT INTO `jshop_area` VALUES (540400, 540000, 2, '林芝市', 0, 100);
INSERT INTO `jshop_area` VALUES (540402, 540400, 3, '巴宜区', 0, 100);
INSERT INTO `jshop_area` VALUES (540421, 540400, 3, '工布江达县', 0, 100);
INSERT INTO `jshop_area` VALUES (540422, 540400, 3, '米林县', 0, 100);
INSERT INTO `jshop_area` VALUES (540423, 540400, 3, '墨脱县', 0, 100);
INSERT INTO `jshop_area` VALUES (540424, 540400, 3, '波密县', 0, 100);
INSERT INTO `jshop_area` VALUES (540425, 540400, 3, '察隅县', 0, 100);
INSERT INTO `jshop_area` VALUES (540426, 540400, 3, '朗县', 0, 100);
INSERT INTO `jshop_area` VALUES (540500, 540000, 2, '山南市', 0, 100);
INSERT INTO `jshop_area` VALUES (540501, 540500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (540502, 540500, 3, '乃东区', 0, 100);
INSERT INTO `jshop_area` VALUES (540521, 540500, 3, '扎囊县', 0, 100);
INSERT INTO `jshop_area` VALUES (540522, 540500, 3, '贡嘎县', 0, 100);
INSERT INTO `jshop_area` VALUES (540523, 540500, 3, '桑日县', 0, 100);
INSERT INTO `jshop_area` VALUES (540524, 540500, 3, '琼结县', 0, 100);
INSERT INTO `jshop_area` VALUES (540525, 540500, 3, '曲松县', 0, 100);
INSERT INTO `jshop_area` VALUES (540526, 540500, 3, '措美县', 0, 100);
INSERT INTO `jshop_area` VALUES (540527, 540500, 3, '洛扎县', 0, 100);
INSERT INTO `jshop_area` VALUES (540528, 540500, 3, '加查县', 0, 100);
INSERT INTO `jshop_area` VALUES (540529, 540500, 3, '隆子县', 0, 100);
INSERT INTO `jshop_area` VALUES (540530, 540500, 3, '错那县', 0, 100);
INSERT INTO `jshop_area` VALUES (540531, 540500, 3, '浪卡子县', 0, 100);
INSERT INTO `jshop_area` VALUES (542400, 540000, 2, '那曲地区', 0, 100);
INSERT INTO `jshop_area` VALUES (542421, 542400, 3, '那曲县', 0, 100);
INSERT INTO `jshop_area` VALUES (542422, 542400, 3, '嘉黎县', 0, 100);
INSERT INTO `jshop_area` VALUES (542423, 542400, 3, '比如县', 0, 100);
INSERT INTO `jshop_area` VALUES (542424, 542400, 3, '聂荣县', 0, 100);
INSERT INTO `jshop_area` VALUES (542425, 542400, 3, '安多县', 0, 100);
INSERT INTO `jshop_area` VALUES (542426, 542400, 3, '申扎县', 0, 100);
INSERT INTO `jshop_area` VALUES (542427, 542400, 3, '索县', 0, 100);
INSERT INTO `jshop_area` VALUES (542428, 542400, 3, '班戈县', 0, 100);
INSERT INTO `jshop_area` VALUES (542429, 542400, 3, '巴青县', 0, 100);
INSERT INTO `jshop_area` VALUES (542430, 542400, 3, '尼玛县', 0, 100);
INSERT INTO `jshop_area` VALUES (542431, 542400, 3, '双湖县', 0, 100);
INSERT INTO `jshop_area` VALUES (542500, 540000, 2, '阿里地区', 0, 100);
INSERT INTO `jshop_area` VALUES (542521, 542500, 3, '普兰县', 0, 100);
INSERT INTO `jshop_area` VALUES (542522, 542500, 3, '札达县', 0, 100);
INSERT INTO `jshop_area` VALUES (542523, 542500, 3, '噶尔县', 0, 100);
INSERT INTO `jshop_area` VALUES (542524, 542500, 3, '日土县', 0, 100);
INSERT INTO `jshop_area` VALUES (542525, 542500, 3, '革吉县', 0, 100);
INSERT INTO `jshop_area` VALUES (542526, 542500, 3, '改则县', 0, 100);
INSERT INTO `jshop_area` VALUES (542527, 542500, 3, '措勤县', 0, 100);
INSERT INTO `jshop_area` VALUES (610000, 0, 1, '陕西省', 0, 100);
INSERT INTO `jshop_area` VALUES (610100, 610000, 2, '西安市', 0, 100);
INSERT INTO `jshop_area` VALUES (610101, 610100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610102, 610100, 3, '新城区', 0, 100);
INSERT INTO `jshop_area` VALUES (610103, 610100, 3, '碑林区', 0, 100);
INSERT INTO `jshop_area` VALUES (610104, 610100, 3, '莲湖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610111, 610100, 3, '灞桥区', 0, 100);
INSERT INTO `jshop_area` VALUES (610112, 610100, 3, '未央区', 0, 100);
INSERT INTO `jshop_area` VALUES (610113, 610100, 3, '雁塔区', 0, 100);
INSERT INTO `jshop_area` VALUES (610114, 610100, 3, '阎良区', 0, 100);
INSERT INTO `jshop_area` VALUES (610115, 610100, 3, '临潼区', 0, 100);
INSERT INTO `jshop_area` VALUES (610116, 610100, 3, '长安区', 0, 100);
INSERT INTO `jshop_area` VALUES (610117, 610100, 3, '高陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (610122, 610100, 3, '蓝田县', 0, 100);
INSERT INTO `jshop_area` VALUES (610124, 610100, 3, '周至县', 0, 100);
INSERT INTO `jshop_area` VALUES (610125, 610100, 3, '户县', 0, 100);
INSERT INTO `jshop_area` VALUES (610200, 610000, 2, '铜川市', 0, 100);
INSERT INTO `jshop_area` VALUES (610201, 610200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610202, 610200, 3, '王益区', 0, 100);
INSERT INTO `jshop_area` VALUES (610203, 610200, 3, '印台区', 0, 100);
INSERT INTO `jshop_area` VALUES (610204, 610200, 3, '耀州区', 0, 100);
INSERT INTO `jshop_area` VALUES (610222, 610200, 3, '宜君县', 0, 100);
INSERT INTO `jshop_area` VALUES (610300, 610000, 2, '宝鸡市', 0, 100);
INSERT INTO `jshop_area` VALUES (610301, 610300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610302, 610300, 3, '渭滨区', 0, 100);
INSERT INTO `jshop_area` VALUES (610303, 610300, 3, '金台区', 0, 100);
INSERT INTO `jshop_area` VALUES (610304, 610300, 3, '陈仓区', 0, 100);
INSERT INTO `jshop_area` VALUES (610322, 610300, 3, '凤翔县', 0, 100);
INSERT INTO `jshop_area` VALUES (610323, 610300, 3, '岐山县', 0, 100);
INSERT INTO `jshop_area` VALUES (610324, 610300, 3, '扶风县', 0, 100);
INSERT INTO `jshop_area` VALUES (610326, 610300, 3, '眉县', 0, 100);
INSERT INTO `jshop_area` VALUES (610327, 610300, 3, '陇县', 0, 100);
INSERT INTO `jshop_area` VALUES (610328, 610300, 3, '千阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (610329, 610300, 3, '麟游县', 0, 100);
INSERT INTO `jshop_area` VALUES (610330, 610300, 3, '凤县', 0, 100);
INSERT INTO `jshop_area` VALUES (610331, 610300, 3, '太白县', 0, 100);
INSERT INTO `jshop_area` VALUES (610400, 610000, 2, '咸阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (610401, 610400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610402, 610400, 3, '秦都区', 0, 100);
INSERT INTO `jshop_area` VALUES (610403, 610400, 3, '杨陵区', 0, 100);
INSERT INTO `jshop_area` VALUES (610404, 610400, 3, '渭城区', 0, 100);
INSERT INTO `jshop_area` VALUES (610422, 610400, 3, '三原县', 0, 100);
INSERT INTO `jshop_area` VALUES (610423, 610400, 3, '泾阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (610424, 610400, 3, '乾县', 0, 100);
INSERT INTO `jshop_area` VALUES (610425, 610400, 3, '礼泉县', 0, 100);
INSERT INTO `jshop_area` VALUES (610426, 610400, 3, '永寿县', 0, 100);
INSERT INTO `jshop_area` VALUES (610427, 610400, 3, '彬县', 0, 100);
INSERT INTO `jshop_area` VALUES (610428, 610400, 3, '长武县', 0, 100);
INSERT INTO `jshop_area` VALUES (610429, 610400, 3, '旬邑县', 0, 100);
INSERT INTO `jshop_area` VALUES (610430, 610400, 3, '淳化县', 0, 100);
INSERT INTO `jshop_area` VALUES (610431, 610400, 3, '武功县', 0, 100);
INSERT INTO `jshop_area` VALUES (610481, 610400, 3, '兴平市', 0, 100);
INSERT INTO `jshop_area` VALUES (610500, 610000, 2, '渭南市', 0, 100);
INSERT INTO `jshop_area` VALUES (610501, 610500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610502, 610500, 3, '临渭区', 0, 100);
INSERT INTO `jshop_area` VALUES (610503, 610500, 3, '华州区', 0, 100);
INSERT INTO `jshop_area` VALUES (610522, 610500, 3, '潼关县', 0, 100);
INSERT INTO `jshop_area` VALUES (610523, 610500, 3, '大荔县', 0, 100);
INSERT INTO `jshop_area` VALUES (610524, 610500, 3, '合阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (610525, 610500, 3, '澄城县', 0, 100);
INSERT INTO `jshop_area` VALUES (610526, 610500, 3, '蒲城县', 0, 100);
INSERT INTO `jshop_area` VALUES (610527, 610500, 3, '白水县', 0, 100);
INSERT INTO `jshop_area` VALUES (610528, 610500, 3, '富平县', 0, 100);
INSERT INTO `jshop_area` VALUES (610581, 610500, 3, '韩城市', 0, 100);
INSERT INTO `jshop_area` VALUES (610582, 610500, 3, '华阴市', 0, 100);
INSERT INTO `jshop_area` VALUES (610600, 610000, 2, '延安市', 0, 100);
INSERT INTO `jshop_area` VALUES (610601, 610600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610602, 610600, 3, '宝塔区', 0, 100);
INSERT INTO `jshop_area` VALUES (610603, 610600, 3, '安塞区', 0, 100);
INSERT INTO `jshop_area` VALUES (610621, 610600, 3, '延长县', 0, 100);
INSERT INTO `jshop_area` VALUES (610622, 610600, 3, '延川县', 0, 100);
INSERT INTO `jshop_area` VALUES (610623, 610600, 3, '子长县', 0, 100);
INSERT INTO `jshop_area` VALUES (610625, 610600, 3, '志丹县', 0, 100);
INSERT INTO `jshop_area` VALUES (610626, 610600, 3, '吴起县', 0, 100);
INSERT INTO `jshop_area` VALUES (610627, 610600, 3, '甘泉县', 0, 100);
INSERT INTO `jshop_area` VALUES (610628, 610600, 3, '富县', 0, 100);
INSERT INTO `jshop_area` VALUES (610629, 610600, 3, '洛川县', 0, 100);
INSERT INTO `jshop_area` VALUES (610630, 610600, 3, '宜川县', 0, 100);
INSERT INTO `jshop_area` VALUES (610631, 610600, 3, '黄龙县', 0, 100);
INSERT INTO `jshop_area` VALUES (610632, 610600, 3, '黄陵县', 0, 100);
INSERT INTO `jshop_area` VALUES (610700, 610000, 2, '汉中市', 0, 100);
INSERT INTO `jshop_area` VALUES (610701, 610700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610702, 610700, 3, '汉台区', 0, 100);
INSERT INTO `jshop_area` VALUES (610721, 610700, 3, '南郑县', 0, 100);
INSERT INTO `jshop_area` VALUES (610722, 610700, 3, '城固县', 0, 100);
INSERT INTO `jshop_area` VALUES (610723, 610700, 3, '洋县', 0, 100);
INSERT INTO `jshop_area` VALUES (610724, 610700, 3, '西乡县', 0, 100);
INSERT INTO `jshop_area` VALUES (610725, 610700, 3, '勉县', 0, 100);
INSERT INTO `jshop_area` VALUES (610726, 610700, 3, '宁强县', 0, 100);
INSERT INTO `jshop_area` VALUES (610727, 610700, 3, '略阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (610728, 610700, 3, '镇巴县', 0, 100);
INSERT INTO `jshop_area` VALUES (610729, 610700, 3, '留坝县', 0, 100);
INSERT INTO `jshop_area` VALUES (610730, 610700, 3, '佛坪县', 0, 100);
INSERT INTO `jshop_area` VALUES (610800, 610000, 2, '榆林市', 0, 100);
INSERT INTO `jshop_area` VALUES (610801, 610800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610802, 610800, 3, '榆阳区', 0, 100);
INSERT INTO `jshop_area` VALUES (610803, 610800, 3, '横山区', 0, 100);
INSERT INTO `jshop_area` VALUES (610821, 610800, 3, '神木县', 0, 100);
INSERT INTO `jshop_area` VALUES (610822, 610800, 3, '府谷县', 0, 100);
INSERT INTO `jshop_area` VALUES (610824, 610800, 3, '靖边县', 0, 100);
INSERT INTO `jshop_area` VALUES (610825, 610800, 3, '定边县', 0, 100);
INSERT INTO `jshop_area` VALUES (610826, 610800, 3, '绥德县', 0, 100);
INSERT INTO `jshop_area` VALUES (610827, 610800, 3, '米脂县', 0, 100);
INSERT INTO `jshop_area` VALUES (610828, 610800, 3, '佳县', 0, 100);
INSERT INTO `jshop_area` VALUES (610829, 610800, 3, '吴堡县', 0, 100);
INSERT INTO `jshop_area` VALUES (610830, 610800, 3, '清涧县', 0, 100);
INSERT INTO `jshop_area` VALUES (610831, 610800, 3, '子洲县', 0, 100);
INSERT INTO `jshop_area` VALUES (610900, 610000, 2, '安康市', 0, 100);
INSERT INTO `jshop_area` VALUES (610901, 610900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (610902, 610900, 3, '汉滨区', 0, 100);
INSERT INTO `jshop_area` VALUES (610921, 610900, 3, '汉阴县', 0, 100);
INSERT INTO `jshop_area` VALUES (610922, 610900, 3, '石泉县', 0, 100);
INSERT INTO `jshop_area` VALUES (610923, 610900, 3, '宁陕县', 0, 100);
INSERT INTO `jshop_area` VALUES (610924, 610900, 3, '紫阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (610925, 610900, 3, '岚皋县', 0, 100);
INSERT INTO `jshop_area` VALUES (610926, 610900, 3, '平利县', 0, 100);
INSERT INTO `jshop_area` VALUES (610927, 610900, 3, '镇坪县', 0, 100);
INSERT INTO `jshop_area` VALUES (610928, 610900, 3, '旬阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (610929, 610900, 3, '白河县', 0, 100);
INSERT INTO `jshop_area` VALUES (611000, 610000, 2, '商洛市', 0, 100);
INSERT INTO `jshop_area` VALUES (611001, 611000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (611002, 611000, 3, '商州区', 0, 100);
INSERT INTO `jshop_area` VALUES (611021, 611000, 3, '洛南县', 0, 100);
INSERT INTO `jshop_area` VALUES (611022, 611000, 3, '丹凤县', 0, 100);
INSERT INTO `jshop_area` VALUES (611023, 611000, 3, '商南县', 0, 100);
INSERT INTO `jshop_area` VALUES (611024, 611000, 3, '山阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (611025, 611000, 3, '镇安县', 0, 100);
INSERT INTO `jshop_area` VALUES (611026, 611000, 3, '柞水县', 0, 100);
INSERT INTO `jshop_area` VALUES (620000, 0, 1, '甘肃省', 0, 100);
INSERT INTO `jshop_area` VALUES (620100, 620000, 2, '兰州市', 0, 100);
INSERT INTO `jshop_area` VALUES (620101, 620100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620102, 620100, 3, '城关区', 0, 100);
INSERT INTO `jshop_area` VALUES (620103, 620100, 3, '七里河区', 0, 100);
INSERT INTO `jshop_area` VALUES (620104, 620100, 3, '西固区', 0, 100);
INSERT INTO `jshop_area` VALUES (620105, 620100, 3, '安宁区', 0, 100);
INSERT INTO `jshop_area` VALUES (620111, 620100, 3, '红古区', 0, 100);
INSERT INTO `jshop_area` VALUES (620121, 620100, 3, '永登县', 0, 100);
INSERT INTO `jshop_area` VALUES (620122, 620100, 3, '皋兰县', 0, 100);
INSERT INTO `jshop_area` VALUES (620123, 620100, 3, '榆中县', 0, 100);
INSERT INTO `jshop_area` VALUES (620200, 620000, 2, '嘉峪关市', 0, 100);
INSERT INTO `jshop_area` VALUES (620201, 620200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620300, 620000, 2, '金昌市', 0, 100);
INSERT INTO `jshop_area` VALUES (620301, 620300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620302, 620300, 3, '金川区', 0, 100);
INSERT INTO `jshop_area` VALUES (620321, 620300, 3, '永昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (620400, 620000, 2, '白银市', 0, 100);
INSERT INTO `jshop_area` VALUES (620401, 620400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620402, 620400, 3, '白银区', 0, 100);
INSERT INTO `jshop_area` VALUES (620403, 620400, 3, '平川区', 0, 100);
INSERT INTO `jshop_area` VALUES (620421, 620400, 3, '靖远县', 0, 100);
INSERT INTO `jshop_area` VALUES (620422, 620400, 3, '会宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (620423, 620400, 3, '景泰县', 0, 100);
INSERT INTO `jshop_area` VALUES (620500, 620000, 2, '天水市', 0, 100);
INSERT INTO `jshop_area` VALUES (620501, 620500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620502, 620500, 3, '秦州区', 0, 100);
INSERT INTO `jshop_area` VALUES (620503, 620500, 3, '麦积区', 0, 100);
INSERT INTO `jshop_area` VALUES (620521, 620500, 3, '清水县', 0, 100);
INSERT INTO `jshop_area` VALUES (620522, 620500, 3, '秦安县', 0, 100);
INSERT INTO `jshop_area` VALUES (620523, 620500, 3, '甘谷县', 0, 100);
INSERT INTO `jshop_area` VALUES (620524, 620500, 3, '武山县', 0, 100);
INSERT INTO `jshop_area` VALUES (620525, 620500, 3, '张家川回族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (620600, 620000, 2, '武威市', 0, 100);
INSERT INTO `jshop_area` VALUES (620601, 620600, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620602, 620600, 3, '凉州区', 0, 100);
INSERT INTO `jshop_area` VALUES (620621, 620600, 3, '民勤县', 0, 100);
INSERT INTO `jshop_area` VALUES (620622, 620600, 3, '古浪县', 0, 100);
INSERT INTO `jshop_area` VALUES (620623, 620600, 3, '天祝藏族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (620700, 620000, 2, '张掖市', 0, 100);
INSERT INTO `jshop_area` VALUES (620701, 620700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620702, 620700, 3, '甘州区', 0, 100);
INSERT INTO `jshop_area` VALUES (620721, 620700, 3, '肃南裕固族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (620722, 620700, 3, '民乐县', 0, 100);
INSERT INTO `jshop_area` VALUES (620723, 620700, 3, '临泽县', 0, 100);
INSERT INTO `jshop_area` VALUES (620724, 620700, 3, '高台县', 0, 100);
INSERT INTO `jshop_area` VALUES (620725, 620700, 3, '山丹县', 0, 100);
INSERT INTO `jshop_area` VALUES (620800, 620000, 2, '平凉市', 0, 100);
INSERT INTO `jshop_area` VALUES (620801, 620800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620802, 620800, 3, '崆峒区', 0, 100);
INSERT INTO `jshop_area` VALUES (620821, 620800, 3, '泾川县', 0, 100);
INSERT INTO `jshop_area` VALUES (620822, 620800, 3, '灵台县', 0, 100);
INSERT INTO `jshop_area` VALUES (620823, 620800, 3, '崇信县', 0, 100);
INSERT INTO `jshop_area` VALUES (620824, 620800, 3, '华亭县', 0, 100);
INSERT INTO `jshop_area` VALUES (620825, 620800, 3, '庄浪县', 0, 100);
INSERT INTO `jshop_area` VALUES (620826, 620800, 3, '静宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (620900, 620000, 2, '酒泉市', 0, 100);
INSERT INTO `jshop_area` VALUES (620901, 620900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (620902, 620900, 3, '肃州区', 0, 100);
INSERT INTO `jshop_area` VALUES (620921, 620900, 3, '金塔县', 0, 100);
INSERT INTO `jshop_area` VALUES (620922, 620900, 3, '瓜州县', 0, 100);
INSERT INTO `jshop_area` VALUES (620923, 620900, 3, '肃北蒙古族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (620924, 620900, 3, '阿克塞哈萨克族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (620981, 620900, 3, '玉门市', 0, 100);
INSERT INTO `jshop_area` VALUES (620982, 620900, 3, '敦煌市', 0, 100);
INSERT INTO `jshop_area` VALUES (621000, 620000, 2, '庆阳市', 0, 100);
INSERT INTO `jshop_area` VALUES (621001, 621000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (621002, 621000, 3, '西峰区', 0, 100);
INSERT INTO `jshop_area` VALUES (621021, 621000, 3, '庆城县', 0, 100);
INSERT INTO `jshop_area` VALUES (621022, 621000, 3, '环县', 0, 100);
INSERT INTO `jshop_area` VALUES (621023, 621000, 3, '华池县', 0, 100);
INSERT INTO `jshop_area` VALUES (621024, 621000, 3, '合水县', 0, 100);
INSERT INTO `jshop_area` VALUES (621025, 621000, 3, '正宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (621026, 621000, 3, '宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (621027, 621000, 3, '镇原县', 0, 100);
INSERT INTO `jshop_area` VALUES (621100, 620000, 2, '定西市', 0, 100);
INSERT INTO `jshop_area` VALUES (621101, 621100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (621102, 621100, 3, '安定区', 0, 100);
INSERT INTO `jshop_area` VALUES (621121, 621100, 3, '通渭县', 0, 100);
INSERT INTO `jshop_area` VALUES (621122, 621100, 3, '陇西县', 0, 100);
INSERT INTO `jshop_area` VALUES (621123, 621100, 3, '渭源县', 0, 100);
INSERT INTO `jshop_area` VALUES (621124, 621100, 3, '临洮县', 0, 100);
INSERT INTO `jshop_area` VALUES (621125, 621100, 3, '漳县', 0, 100);
INSERT INTO `jshop_area` VALUES (621126, 621100, 3, '岷县', 0, 100);
INSERT INTO `jshop_area` VALUES (621200, 620000, 2, '陇南市', 0, 100);
INSERT INTO `jshop_area` VALUES (621201, 621200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (621202, 621200, 3, '武都区', 0, 100);
INSERT INTO `jshop_area` VALUES (621221, 621200, 3, '成县', 0, 100);
INSERT INTO `jshop_area` VALUES (621222, 621200, 3, '文县', 0, 100);
INSERT INTO `jshop_area` VALUES (621223, 621200, 3, '宕昌县', 0, 100);
INSERT INTO `jshop_area` VALUES (621224, 621200, 3, '康县', 0, 100);
INSERT INTO `jshop_area` VALUES (621225, 621200, 3, '西和县', 0, 100);
INSERT INTO `jshop_area` VALUES (621226, 621200, 3, '礼县', 0, 100);
INSERT INTO `jshop_area` VALUES (621227, 621200, 3, '徽县', 0, 100);
INSERT INTO `jshop_area` VALUES (621228, 621200, 3, '两当县', 0, 100);
INSERT INTO `jshop_area` VALUES (622900, 620000, 2, '临夏回族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (622901, 622900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (622921, 622900, 3, '临夏县', 0, 100);
INSERT INTO `jshop_area` VALUES (622922, 622900, 3, '康乐县', 0, 100);
INSERT INTO `jshop_area` VALUES (622923, 622900, 3, '永靖县', 0, 100);
INSERT INTO `jshop_area` VALUES (622924, 622900, 3, '广河县', 0, 100);
INSERT INTO `jshop_area` VALUES (622925, 622900, 3, '和政县', 0, 100);
INSERT INTO `jshop_area` VALUES (622926, 622900, 3, '东乡族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (622927, 622900, 3, '积石山保安族东乡族撒拉族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (623000, 620000, 2, '甘南藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (623001, 623000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (623021, 623000, 3, '临潭县', 0, 100);
INSERT INTO `jshop_area` VALUES (623022, 623000, 3, '卓尼县', 0, 100);
INSERT INTO `jshop_area` VALUES (623023, 623000, 3, '舟曲县', 0, 100);
INSERT INTO `jshop_area` VALUES (623024, 623000, 3, '迭部县', 0, 100);
INSERT INTO `jshop_area` VALUES (623025, 623000, 3, '玛曲县', 0, 100);
INSERT INTO `jshop_area` VALUES (623026, 623000, 3, '碌曲县', 0, 100);
INSERT INTO `jshop_area` VALUES (623027, 623000, 3, '夏河县', 0, 100);
INSERT INTO `jshop_area` VALUES (630000, 0, 1, '青海省', 0, 100);
INSERT INTO `jshop_area` VALUES (630100, 630000, 2, '西宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (630101, 630100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (630102, 630100, 3, '城东区', 0, 100);
INSERT INTO `jshop_area` VALUES (630103, 630100, 3, '城中区', 0, 100);
INSERT INTO `jshop_area` VALUES (630104, 630100, 3, '城西区', 0, 100);
INSERT INTO `jshop_area` VALUES (630105, 630100, 3, '城北区', 0, 100);
INSERT INTO `jshop_area` VALUES (630121, 630100, 3, '大通回族土族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (630122, 630100, 3, '湟中县', 0, 100);
INSERT INTO `jshop_area` VALUES (630123, 630100, 3, '湟源县', 0, 100);
INSERT INTO `jshop_area` VALUES (630200, 630000, 2, '海东市', 0, 100);
INSERT INTO `jshop_area` VALUES (630202, 630200, 3, '乐都区', 0, 100);
INSERT INTO `jshop_area` VALUES (630203, 630200, 3, '平安区', 0, 100);
INSERT INTO `jshop_area` VALUES (630222, 630200, 3, '民和回族土族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (630223, 630200, 3, '互助土族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (630224, 630200, 3, '化隆回族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (630225, 630200, 3, '循化撒拉族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (632200, 630000, 2, '海北藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (632221, 632200, 3, '门源回族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (632222, 632200, 3, '祁连县', 0, 100);
INSERT INTO `jshop_area` VALUES (632223, 632200, 3, '海晏县', 0, 100);
INSERT INTO `jshop_area` VALUES (632224, 632200, 3, '刚察县', 0, 100);
INSERT INTO `jshop_area` VALUES (632300, 630000, 2, '黄南藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (632321, 632300, 3, '同仁县', 0, 100);
INSERT INTO `jshop_area` VALUES (632322, 632300, 3, '尖扎县', 0, 100);
INSERT INTO `jshop_area` VALUES (632323, 632300, 3, '泽库县', 0, 100);
INSERT INTO `jshop_area` VALUES (632324, 632300, 3, '河南蒙古族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (632500, 630000, 2, '海南藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (632521, 632500, 3, '共和县', 0, 100);
INSERT INTO `jshop_area` VALUES (632522, 632500, 3, '同德县', 0, 100);
INSERT INTO `jshop_area` VALUES (632523, 632500, 3, '贵德县', 0, 100);
INSERT INTO `jshop_area` VALUES (632524, 632500, 3, '兴海县', 0, 100);
INSERT INTO `jshop_area` VALUES (632525, 632500, 3, '贵南县', 0, 100);
INSERT INTO `jshop_area` VALUES (632600, 630000, 2, '果洛藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (632621, 632600, 3, '玛沁县', 0, 100);
INSERT INTO `jshop_area` VALUES (632622, 632600, 3, '班玛县', 0, 100);
INSERT INTO `jshop_area` VALUES (632623, 632600, 3, '甘德县', 0, 100);
INSERT INTO `jshop_area` VALUES (632624, 632600, 3, '达日县', 0, 100);
INSERT INTO `jshop_area` VALUES (632625, 632600, 3, '久治县', 0, 100);
INSERT INTO `jshop_area` VALUES (632626, 632600, 3, '玛多县', 0, 100);
INSERT INTO `jshop_area` VALUES (632700, 630000, 2, '玉树藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (632701, 632700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (632722, 632700, 3, '杂多县', 0, 100);
INSERT INTO `jshop_area` VALUES (632723, 632700, 3, '称多县', 0, 100);
INSERT INTO `jshop_area` VALUES (632724, 632700, 3, '治多县', 0, 100);
INSERT INTO `jshop_area` VALUES (632725, 632700, 3, '囊谦县', 0, 100);
INSERT INTO `jshop_area` VALUES (632726, 632700, 3, '曲麻莱县', 0, 100);
INSERT INTO `jshop_area` VALUES (632800, 630000, 2, '海西蒙古族藏族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (632801, 632800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (632802, 632800, 3, '德令哈市', 0, 100);
INSERT INTO `jshop_area` VALUES (632821, 632800, 3, '乌兰县', 0, 100);
INSERT INTO `jshop_area` VALUES (632822, 632800, 3, '都兰县', 0, 100);
INSERT INTO `jshop_area` VALUES (632823, 632800, 3, '天峻县', 0, 100);
INSERT INTO `jshop_area` VALUES (640000, 0, 1, '宁夏回族自治区', 0, 100);
INSERT INTO `jshop_area` VALUES (640100, 640000, 2, '银川市', 0, 100);
INSERT INTO `jshop_area` VALUES (640101, 640100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (640104, 640100, 3, '兴庆区', 0, 100);
INSERT INTO `jshop_area` VALUES (640105, 640100, 3, '西夏区', 0, 100);
INSERT INTO `jshop_area` VALUES (640106, 640100, 3, '金凤区', 0, 100);
INSERT INTO `jshop_area` VALUES (640121, 640100, 3, '永宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (640122, 640100, 3, '贺兰县', 0, 100);
INSERT INTO `jshop_area` VALUES (640181, 640100, 3, '灵武市', 0, 100);
INSERT INTO `jshop_area` VALUES (640200, 640000, 2, '石嘴山市', 0, 100);
INSERT INTO `jshop_area` VALUES (640201, 640200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (640202, 640200, 3, '大武口区', 0, 100);
INSERT INTO `jshop_area` VALUES (640205, 640200, 3, '惠农区', 0, 100);
INSERT INTO `jshop_area` VALUES (640221, 640200, 3, '平罗县', 0, 100);
INSERT INTO `jshop_area` VALUES (640300, 640000, 2, '吴忠市', 0, 100);
INSERT INTO `jshop_area` VALUES (640301, 640300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (640302, 640300, 3, '利通区', 0, 100);
INSERT INTO `jshop_area` VALUES (640303, 640300, 3, '红寺堡区', 0, 100);
INSERT INTO `jshop_area` VALUES (640323, 640300, 3, '盐池县', 0, 100);
INSERT INTO `jshop_area` VALUES (640324, 640300, 3, '同心县', 0, 100);
INSERT INTO `jshop_area` VALUES (640381, 640300, 3, '青铜峡市', 0, 100);
INSERT INTO `jshop_area` VALUES (640400, 640000, 2, '固原市', 0, 100);
INSERT INTO `jshop_area` VALUES (640401, 640400, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (640402, 640400, 3, '原州区', 0, 100);
INSERT INTO `jshop_area` VALUES (640422, 640400, 3, '西吉县', 0, 100);
INSERT INTO `jshop_area` VALUES (640423, 640400, 3, '隆德县', 0, 100);
INSERT INTO `jshop_area` VALUES (640424, 640400, 3, '泾源县', 0, 100);
INSERT INTO `jshop_area` VALUES (640425, 640400, 3, '彭阳县', 0, 100);
INSERT INTO `jshop_area` VALUES (640500, 640000, 2, '中卫市', 0, 100);
INSERT INTO `jshop_area` VALUES (640501, 640500, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (640502, 640500, 3, '沙坡头区', 0, 100);
INSERT INTO `jshop_area` VALUES (640521, 640500, 3, '中宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (640522, 640500, 3, '海原县', 0, 100);
INSERT INTO `jshop_area` VALUES (650000, 0, 1, '新疆维吾尔自治区', 0, 100);
INSERT INTO `jshop_area` VALUES (650100, 650000, 2, '乌鲁木齐市', 0, 100);
INSERT INTO `jshop_area` VALUES (650101, 650100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (650102, 650100, 3, '天山区', 0, 100);
INSERT INTO `jshop_area` VALUES (650103, 650100, 3, '沙依巴克区', 0, 100);
INSERT INTO `jshop_area` VALUES (650104, 650100, 3, '新市区', 0, 100);
INSERT INTO `jshop_area` VALUES (650105, 650100, 3, '水磨沟区', 0, 100);
INSERT INTO `jshop_area` VALUES (650106, 650100, 3, '头屯河区', 0, 100);
INSERT INTO `jshop_area` VALUES (650107, 650100, 3, '达坂城区', 0, 100);
INSERT INTO `jshop_area` VALUES (650109, 650100, 3, '米东区', 0, 100);
INSERT INTO `jshop_area` VALUES (650121, 650100, 3, '乌鲁木齐县', 0, 100);
INSERT INTO `jshop_area` VALUES (650200, 650000, 2, '克拉玛依市', 0, 100);
INSERT INTO `jshop_area` VALUES (650201, 650200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (650202, 650200, 3, '独山子区', 0, 100);
INSERT INTO `jshop_area` VALUES (650203, 650200, 3, '克拉玛依区', 0, 100);
INSERT INTO `jshop_area` VALUES (650204, 650200, 3, '白碱滩区', 0, 100);
INSERT INTO `jshop_area` VALUES (650205, 650200, 3, '乌尔禾区', 0, 100);
INSERT INTO `jshop_area` VALUES (650400, 650000, 2, '吐鲁番市', 0, 100);
INSERT INTO `jshop_area` VALUES (650402, 650400, 3, '高昌区', 0, 100);
INSERT INTO `jshop_area` VALUES (650421, 650400, 3, '鄯善县', 0, 100);
INSERT INTO `jshop_area` VALUES (650422, 650400, 3, '托克逊县', 0, 100);
INSERT INTO `jshop_area` VALUES (650500, 650000, 2, '哈密市', 0, 100);
INSERT INTO `jshop_area` VALUES (650502, 650500, 3, '伊州区', 0, 100);
INSERT INTO `jshop_area` VALUES (650521, 650500, 3, '巴里坤哈萨克自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (650522, 650500, 3, '伊吾县', 0, 100);
INSERT INTO `jshop_area` VALUES (652300, 650000, 2, '昌吉回族自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (652301, 652300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (652302, 652300, 3, '阜康市', 0, 100);
INSERT INTO `jshop_area` VALUES (652323, 652300, 3, '呼图壁县', 0, 100);
INSERT INTO `jshop_area` VALUES (652324, 652300, 3, '玛纳斯县', 0, 100);
INSERT INTO `jshop_area` VALUES (652325, 652300, 3, '奇台县', 0, 100);
INSERT INTO `jshop_area` VALUES (652327, 652300, 3, '吉木萨尔县', 0, 100);
INSERT INTO `jshop_area` VALUES (652328, 652300, 3, '木垒哈萨克自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (652700, 650000, 2, '博尔塔拉蒙古自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (652701, 652700, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (652702, 652700, 3, '阿拉山口市', 0, 100);
INSERT INTO `jshop_area` VALUES (652722, 652700, 3, '精河县', 0, 100);
INSERT INTO `jshop_area` VALUES (652723, 652700, 3, '温泉县', 0, 100);
INSERT INTO `jshop_area` VALUES (652800, 650000, 2, '巴音郭楞蒙古自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (652801, 652800, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (652822, 652800, 3, '轮台县', 0, 100);
INSERT INTO `jshop_area` VALUES (652823, 652800, 3, '尉犁县', 0, 100);
INSERT INTO `jshop_area` VALUES (652824, 652800, 3, '若羌县', 0, 100);
INSERT INTO `jshop_area` VALUES (652825, 652800, 3, '且末县', 0, 100);
INSERT INTO `jshop_area` VALUES (652826, 652800, 3, '焉耆回族自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (652827, 652800, 3, '和静县', 0, 100);
INSERT INTO `jshop_area` VALUES (652828, 652800, 3, '和硕县', 0, 100);
INSERT INTO `jshop_area` VALUES (652829, 652800, 3, '博湖县', 0, 100);
INSERT INTO `jshop_area` VALUES (652900, 650000, 2, '阿克苏地区', 0, 100);
INSERT INTO `jshop_area` VALUES (652901, 652900, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (652922, 652900, 3, '温宿县', 0, 100);
INSERT INTO `jshop_area` VALUES (652923, 652900, 3, '库车县', 0, 100);
INSERT INTO `jshop_area` VALUES (652924, 652900, 3, '沙雅县', 0, 100);
INSERT INTO `jshop_area` VALUES (652925, 652900, 3, '新和县', 0, 100);
INSERT INTO `jshop_area` VALUES (652926, 652900, 3, '拜城县', 0, 100);
INSERT INTO `jshop_area` VALUES (652927, 652900, 3, '乌什县', 0, 100);
INSERT INTO `jshop_area` VALUES (652928, 652900, 3, '阿瓦提县', 0, 100);
INSERT INTO `jshop_area` VALUES (652929, 652900, 3, '柯坪县', 0, 100);
INSERT INTO `jshop_area` VALUES (653000, 650000, 2, '克孜勒苏柯尔克孜自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (653001, 653000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (653022, 653000, 3, '阿克陶县', 0, 100);
INSERT INTO `jshop_area` VALUES (653023, 653000, 3, '阿合奇县', 0, 100);
INSERT INTO `jshop_area` VALUES (653024, 653000, 3, '乌恰县', 0, 100);
INSERT INTO `jshop_area` VALUES (653100, 650000, 2, '喀什地区', 0, 100);
INSERT INTO `jshop_area` VALUES (653101, 653100, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (653121, 653100, 3, '疏附县', 0, 100);
INSERT INTO `jshop_area` VALUES (653122, 653100, 3, '疏勒县', 0, 100);
INSERT INTO `jshop_area` VALUES (653123, 653100, 3, '英吉沙县', 0, 100);
INSERT INTO `jshop_area` VALUES (653124, 653100, 3, '泽普县', 0, 100);
INSERT INTO `jshop_area` VALUES (653125, 653100, 3, '莎车县', 0, 100);
INSERT INTO `jshop_area` VALUES (653126, 653100, 3, '叶城县', 0, 100);
INSERT INTO `jshop_area` VALUES (653127, 653100, 3, '麦盖提县', 0, 100);
INSERT INTO `jshop_area` VALUES (653128, 653100, 3, '岳普湖县', 0, 100);
INSERT INTO `jshop_area` VALUES (653129, 653100, 3, '伽师县', 0, 100);
INSERT INTO `jshop_area` VALUES (653130, 653100, 3, '巴楚县', 0, 100);
INSERT INTO `jshop_area` VALUES (653131, 653100, 3, '塔什库尔干塔吉克自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (653200, 650000, 2, '和田地区', 0, 100);
INSERT INTO `jshop_area` VALUES (653201, 653200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (653221, 653200, 3, '和田县', 0, 100);
INSERT INTO `jshop_area` VALUES (653222, 653200, 3, '墨玉县', 0, 100);
INSERT INTO `jshop_area` VALUES (653223, 653200, 3, '皮山县', 0, 100);
INSERT INTO `jshop_area` VALUES (653224, 653200, 3, '洛浦县', 0, 100);
INSERT INTO `jshop_area` VALUES (653225, 653200, 3, '策勒县', 0, 100);
INSERT INTO `jshop_area` VALUES (653226, 653200, 3, '于田县', 0, 100);
INSERT INTO `jshop_area` VALUES (653227, 653200, 3, '民丰县', 0, 100);
INSERT INTO `jshop_area` VALUES (654000, 650000, 2, '伊犁哈萨克自治州', 0, 100);
INSERT INTO `jshop_area` VALUES (654002, 654000, 3, '伊宁市', 0, 100);
INSERT INTO `jshop_area` VALUES (654003, 654000, 3, '奎屯市', 0, 100);
INSERT INTO `jshop_area` VALUES (654004, 654000, 3, '霍尔果斯市', 0, 100);
INSERT INTO `jshop_area` VALUES (654021, 654000, 3, '伊宁县', 0, 100);
INSERT INTO `jshop_area` VALUES (654022, 654000, 3, '察布查尔锡伯自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (654023, 654000, 3, '霍城县', 0, 100);
INSERT INTO `jshop_area` VALUES (654024, 654000, 3, '巩留县', 0, 100);
INSERT INTO `jshop_area` VALUES (654025, 654000, 3, '新源县', 0, 100);
INSERT INTO `jshop_area` VALUES (654026, 654000, 3, '昭苏县', 0, 100);
INSERT INTO `jshop_area` VALUES (654027, 654000, 3, '特克斯县', 0, 100);
INSERT INTO `jshop_area` VALUES (654028, 654000, 3, '尼勒克县', 0, 100);
INSERT INTO `jshop_area` VALUES (654200, 650000, 2, '塔城地区', 0, 100);
INSERT INTO `jshop_area` VALUES (654201, 654200, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (654202, 654200, 3, '乌苏市', 0, 100);
INSERT INTO `jshop_area` VALUES (654221, 654200, 3, '额敏县', 0, 100);
INSERT INTO `jshop_area` VALUES (654223, 654200, 3, '沙湾县', 0, 100);
INSERT INTO `jshop_area` VALUES (654224, 654200, 3, '托里县', 0, 100);
INSERT INTO `jshop_area` VALUES (654225, 654200, 3, '裕民县', 0, 100);
INSERT INTO `jshop_area` VALUES (654226, 654200, 3, '和布克赛尔蒙古自治县', 0, 100);
INSERT INTO `jshop_area` VALUES (654300, 650000, 2, '阿勒泰地区', 0, 100);
INSERT INTO `jshop_area` VALUES (654301, 654300, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (654321, 654300, 3, '布尔津县', 0, 100);
INSERT INTO `jshop_area` VALUES (654322, 654300, 3, '富蕴县', 0, 100);
INSERT INTO `jshop_area` VALUES (654323, 654300, 3, '福海县', 0, 100);
INSERT INTO `jshop_area` VALUES (654324, 654300, 3, '哈巴河县', 0, 100);
INSERT INTO `jshop_area` VALUES (654325, 654300, 3, '青河县', 0, 100);
INSERT INTO `jshop_area` VALUES (654326, 654300, 3, '吉木乃县', 0, 100);
INSERT INTO `jshop_area` VALUES (659000, 650000, 2, '自治区直辖县级行政区划', 0, 100);
INSERT INTO `jshop_area` VALUES (659001, 659000, 3, '市辖区', 0, 100);
INSERT INTO `jshop_area` VALUES (659002, 659000, 3, '阿拉尔市', 0, 100);
INSERT INTO `jshop_area` VALUES (659003, 659000, 3, '图木舒克市', 0, 100);
INSERT INTO `jshop_area` VALUES (659004, 659000, 3, '五家渠市', 0, 100);
INSERT INTO `jshop_area` VALUES (659006, 659000, 3, '铁门关市', 0, 100);
INSERT INTO `jshop_area` VALUES (710000, 0, 1, '台湾省', 0, 100);
INSERT INTO `jshop_area` VALUES (810000, 0, 1, '香港特别行政区', 0, 100);
INSERT INTO `jshop_area` VALUES (820000, 0, 1, '澳门特别行政区', 0, 100);
INSERT INTO `jshop_area` VALUES (920000, 500200, 3, '静海县', 301600, 100);
INSERT INTO `jshop_area` VALUES (920001, 330100, 3, '富阳市', 311400, 100);
INSERT INTO `jshop_area` VALUES (920002, 410100, 3, '郑东新区', 0, 100);
INSERT INTO `jshop_area` VALUES (920003, 440100, 3, '萝岗区', 510100, 100);

-- ----------------------------
-- Table structure for jshop_article
-- ----------------------------
DROP TABLE IF EXISTS `jshop_article`;
CREATE TABLE `jshop_article`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文章标题',
  `cover` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '文章封面图',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '文章内容',
  `type_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '文章分类id',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '文章创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '文章排序  从小到大',
  `is_pub` smallint(1) UNSIGNED NULL DEFAULT NULL COMMENT '1 发布  2 不发布',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '软删除位置  有时间代表删除',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_article_type
-- ----------------------------
DROP TABLE IF EXISTS `jshop_article_type`;
CREATE TABLE `jshop_article_type`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NULL DEFAULT NULL COMMENT '父id',
  `type_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_backstage_notice
-- ----------------------------
DROP TABLE IF EXISTS `jshop_backstage_notice`;
CREATE TABLE `jshop_backstage_notice`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '公告标题',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '公告内容',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '文章排序  从小到大',
  `is_pub` smallint(1) UNSIGNED NULL DEFAULT NULL COMMENT '1 发布  2 不发布',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '软删除位置  有时间代表删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_balance
-- ----------------------------
DROP TABLE IF EXISTS `jshop_balance`;
CREATE TABLE `jshop_balance`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `type` smallint(2) UNSIGNED NOT NULL COMMENT '类型',
  `money` decimal(10, 2) NOT NULL COMMENT '金额',
  `balance` decimal(10, 2) NOT NULL COMMENT '余额',
  `source_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '资源id',
  `memo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '描述',
  `ctime` bigint(12) UNSIGNED NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户余额表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_aftersales
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_aftersales`;
CREATE TABLE `jshop_bill_aftersales`  (
  `aftersales_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '售后单id',
  `order_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订单ID 关联order.id',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID 关联user.id',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '售后类型，1=只退款，2退款退货',
  `refund` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '退款金额',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '状态 1=未审核 2=审核通过 3=审核拒绝',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '退款原因',
  `mark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '卖家备注，如果审核失败了，会显示到前端',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`aftersales_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '退货单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_aftersales_images
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_aftersales_images`;
CREATE TABLE `jshop_bill_aftersales_images`  (
  `aftersales_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '售后单id',
  `image_id` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片ID',
  INDEX `aftersales_id`(`aftersales_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品图片关联表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_aftersales_items
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_aftersales_items`;
CREATE TABLE `jshop_bill_aftersales_items`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `aftersales_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '售后单id',
  `order_items_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '订单明细ID 关联order_items.id',
  `goods_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品ID 关联goods.id',
  `product_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '货品ID 关联products.id',
  `sn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '货品编码',
  `bn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品编码',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品名称',
  `image_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片',
  `nums` smallint(5) UNSIGNED NULL DEFAULT 1 COMMENT '数量',
  `addon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '货品明细序列号存储',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '售后单明细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_delivery
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_delivery`;
CREATE TABLE `jshop_bill_delivery`  (
  `delivery_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `order_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单ID 关联order.id',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户id 关联user.id',
  `logi_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物流公司编码',
  `logi_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物流单号',
  `logi_information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '快递物流信息',
  `logi_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0快递信息可能更新  1快递信息不更新了',
  `ship_area_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '收货地区ID',
  `ship_address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货详细地址',
  `ship_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货人姓名',
  `ship_mobile` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货电话',
  `confirm_time` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '确认s收货时间',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '状态 1=准备发货 2=已发货 3=已确认 4=其他',
  `memo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`delivery_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '发货单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_delivery_items
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_delivery_items`;
CREATE TABLE `jshop_bill_delivery_items`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `delivery_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '发货单号 关联bill_delivery.id',
  `order_items_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '订单明细ID 关联order_items.id',
  `nums` smallint(5) UNSIGNED NULL DEFAULT NULL COMMENT '发货数量',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '发货单详情表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_lading
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_lading`;
CREATE TABLE `jshop_bill_lading`  (
  `id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '提货单号',
  `order_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `store_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '提货门店ID',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '提货人姓名',
  `mobile` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '提货手机号',
  `clerk_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '处理店员ID',
  `ptime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '提货时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '提货状态1=未提货 2=已提货',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '提货单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_payments
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_payments`;
CREATE TABLE `jshop_bill_payments`  (
  `payment_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '支付单号',
  `money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '支付金额',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID 关联user.id',
  `type` smallint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '资源类型1=订单,2充值单',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '支付状态 1=未支付 2=支付成功 3=其他',
  `payment_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '支付类型编码 关联payments.code',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '支付单生成IP',
  `params` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '支付的时候需要的参数，存的是json格式的一维数组',
  `payed_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '支付回调后的状态描述',
  `trade_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '第三方平台交易流水号',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`payment_id`) USING BTREE,
  INDEX `payment_id`(`payment_id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `type`(`type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '支付单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_payments_rel
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_payments_rel`;
CREATE TABLE `jshop_bill_payments_rel`  (
  `payment_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '支付单编号',
  `source_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '资源编号',
  `money` decimal(8, 2) UNSIGNED NOT NULL COMMENT '金额',
  INDEX `payment_id`(`payment_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '支付单明细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_refund
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_refund`;
CREATE TABLE `jshop_bill_refund`  (
  `refund_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `aftersales_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '售后单id',
  `money` decimal(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT '退款金额',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID 关联user.id',
  `source_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '资源id，根据type不同而关联不同的表',
  `type` smallint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '资源类型1=订单,2充值单',
  `payment_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '退款支付类型编码 默认原路返回 关联支付单表支付编码',
  `trade_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '第三方平台交易流水号',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '状态 1=未退款 2=已退款 3=退款失败，可以再次退款，4退款拒绝',
  `memo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '退款失败原因',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`refund_id`) USING BTREE,
  INDEX `refund_id`(`refund_id`) USING BTREE,
  INDEX `aftersales_id`(`aftersales_id`) USING BTREE,
  INDEX `type`(`type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '退款单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_reship
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_reship`;
CREATE TABLE `jshop_bill_reship`  (
  `reship_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `order_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单ID 关联order.id',
  `aftersales_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '售后单id',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID 关联user.id',
  `logi_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物流公司编码',
  `logi_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物流单号',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '状态 1=审核通过待发货 2=已发退货 3=已收退货',
  `memo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`reship_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '退货单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bill_reship_items
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_reship_items`;
CREATE TABLE `jshop_bill_reship_items`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reship_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '退款单单id',
  `order_items_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '订单明细ID 关联order_items.id',
  `goods_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品ID 关联goods.id',
  `product_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '货品ID 关联products.id',
  `sn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '货品编码',
  `bn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品编码',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品名称',
  `image_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片',
  `nums` smallint(5) UNSIGNED NULL DEFAULT 1 COMMENT '数量',
  `addon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '货品明细序列号存储',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `reship_id`(`reship_id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '退货单明细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_brand
-- ----------------------------
DROP TABLE IF EXISTS `jshop_brand`;
CREATE TABLE `jshop_brand`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '品牌ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '品牌名称',
  `logo` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '品牌LOGO 图片ID',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '品牌排序 越小越靠前',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标志 有数据代表删除',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '品牌表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_cart
-- ----------------------------
DROP TABLE IF EXISTS `jshop_cart`;
CREATE TABLE `jshop_cart`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID 关联user.id',
  `product_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '货品ID',
  `nums` smallint(5) UNSIGNED NULL DEFAULT 0 COMMENT '货品数量',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '购物车表（每个用户最多加100条信息）' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_clerk
-- ----------------------------
DROP TABLE IF EXISTS `jshop_clerk`;
CREATE TABLE `jshop_clerk`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '店铺ID',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '关联时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '店铺店员关联表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_coupon
-- ----------------------------
DROP TABLE IF EXISTS `jshop_coupon`;
CREATE TABLE `jshop_coupon`  (
  `coupon_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '优惠券编码',
  `promotion_id` int(10) UNSIGNED NOT NULL COMMENT '优惠券id',
  `is_used` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否使用1未使用，2已使用',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '谁领取了',
  `used_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '被谁用了',
  `ctime` bigint(12) UNSIGNED NOT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`coupon_code`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '优惠券表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_files
-- ----------------------------
DROP TABLE IF EXISTS `jshop_files`;
CREATE TABLE `jshop_files`  (
  `id` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '视频ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '视频名称',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '绝对地址',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物理地址',
  `type` enum('web','local') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'local' COMMENT '存储引擎',
  `file_type` enum('video') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'video' COMMENT '文件类型',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标志 有数据代表删除',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `id_2`(`id`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_form
-- ----------------------------
DROP TABLE IF EXISTS `jshop_form`;
CREATE TABLE `jshop_form`  (
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表单名称',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '1订单、2留言、3反馈、4登记、5调研',
  `sort` int(5) UNSIGNED NULL DEFAULT 100 COMMENT '表单排序',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表单描述',
  `head_type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '1图片2轮播3视频',
  `head_type_value` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表单头值',
  `head_type_video` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `button_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表单提交按钮名称',
  `button_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表单按钮颜色',
  `is_login` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否需要登录1需要2不需要',
  `qrcode` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '二维码图片地址',
  `return_msg` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '提交后提示语',
  `end_date` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '到期时间',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_form_item
-- ----------------------------
DROP TABLE IF EXISTS `jshop_form_item`;
CREATE TABLE `jshop_form_item`  (
  `id` bigint(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段名称',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段类型',
  `validation_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '验证类型',
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表单值',
  `default_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '默认值',
  `form_id` bigint(12) UNSIGNED NULL DEFAULT 0 COMMENT '表单id',
  `required` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否必填，1必填，2不必填',
  `sort` int(5) UNSIGNED NULL DEFAULT 100 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '表单项表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_form_submit
-- ----------------------------
DROP TABLE IF EXISTS `jshop_form_submit`;
CREATE TABLE `jshop_form_submit`  (
  `id` bigint(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id` bigint(12) NOT NULL DEFAULT 0 COMMENT '表单id',
  `form_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '表单名称',
  `user_id` bigint(20) UNSIGNED NULL DEFAULT 0 COMMENT '会员id',
  `money` decimal(20, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '总金额',
  `pay_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '2未支付，1已支付。支付状态',
  `status` tinyint(1) NULL DEFAULT 2 COMMENT '表单状态，1已处理，2未处理',
  `feedback` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表单反馈',
  `ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '提交人ip',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 137 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户对表的提交记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_form_submit_detail
-- ----------------------------
DROP TABLE IF EXISTS `jshop_form_submit_detail`;
CREATE TABLE `jshop_form_submit_detail`  (
  `id` bigint(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `submit_id` bigint(12) NULL DEFAULT 0 COMMENT '提交表单id',
  `form_id` bigint(12) UNSIGNED NULL DEFAULT 0 COMMENT '表单id',
  `form_item_id` bigint(12) NULL DEFAULT NULL COMMENT '表单项id',
  `form_item_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '表单项名称',
  `form_item_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '表单项值',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 219 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '提交表单保存大文本值表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods`;
CREATE TABLE `jshop_goods`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `bn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品编码',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品名称',
  `brief` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品简介',
  `price` decimal(10, 2) NULL DEFAULT NULL COMMENT '商品价格',
  `costprice` decimal(10, 2) NULL DEFAULT NULL COMMENT '成本价',
  `mktprice` decimal(10, 2) NULL DEFAULT NULL COMMENT '市场价',
  `image_id` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '默认图片 图片id',
  `goods_cat_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品分类ID 关联category.id',
  `goods_type_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品类别ID 关联goods_type.id',
  `brand_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '品牌ID 关联brand.id',
  `is_nomal_virtual` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '虚拟正常商品 1=正常 2=虚拟',
  `marketable` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '上架标志 1=上架 2=下架',
  `stock` int(8) NULL DEFAULT 0 COMMENT '库存',
  `freeze_stock` int(8) NULL DEFAULT 0 COMMENT '冻结库存',
  `weight` decimal(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT '重量',
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品单位',
  `intro` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '商品详情',
  `spes_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '商品规格序列号存储',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '参数序列化',
  `comments_count` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '评论次数',
  `view_count` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '浏览次数',
  `buy_count` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '购买次数',
  `uptime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '上架时间',
  `downtime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '下架时间',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '商品排序 越小越靠前',
  `is_recommend` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否推荐，1是，2不是推荐',
  `is_hot` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否热门，1是，2否',
  `label_ids` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标签id逗号分隔',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标志 有数据表示删除',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE,
  INDEX `is_hot`(`is_hot`) USING BTREE,
  INDEX `is_recommend`(`is_recommend`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE,
  INDEX `mktprice`(`mktprice`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_browsing
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_browsing`;
CREATE TABLE `jshop_goods_browsing`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `goods_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品id 关联goods.id',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `goods_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品名称',
  `ctime` bigint(12) NOT NULL COMMENT '浏览时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标志',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品浏览记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_cat
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_cat`;
CREATE TABLE `jshop_goods_cat`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级分类id',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类名称',
  `type_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '类型ID 关联 goods_type.id',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '分类排序 越小越靠前',
  `image_id` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类图片ID',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `seller_id`(`parent_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品分类' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_collection
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_collection`;
CREATE TABLE `jshop_goods_collection`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `goods_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品id 关联goods.id',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `goods_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品名称',
  `ctime` bigint(12) NOT NULL COMMENT '浏览时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `goods_id`(`goods_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品收藏表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_comment
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_comment`;
CREATE TABLE `jshop_goods_comment`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '父级评价ID',
  `score` tinyint(1) NULL DEFAULT 5 COMMENT '评价1-5星',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '评价用户ID',
  `goods_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品ID 关联goods.id',
  `order_id` bigint(20) UNSIGNED NOT NULL COMMENT '评价订单ID',
  `addon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '货品规格序列号存储',
  `images` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '评价图片逗号分隔最多五张',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '评价内容',
  `seller_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '商家回复',
  `display` tinyint(3) UNSIGNED NULL DEFAULT 1 COMMENT '是否显示 1显示 2不显示',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `display`(`display`) USING BTREE,
  INDEX `goods_id`(`goods_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品评价表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_grade
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_grade`;
CREATE TABLE `jshop_goods_grade`  (
  `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id` bigint(10) UNSIGNED NULL DEFAULT 0 COMMENT '商品id',
  `grade_id` tinyint(2) UNSIGNED NULL DEFAULT 1 COMMENT '会员等级id',
  `grade_price` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '会员价',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品会员价表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_images
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_images`;
CREATE TABLE `jshop_goods_images`  (
  `goods_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `image_id` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片ID',
  `sort` int(10) NULL DEFAULT 100 COMMENT '图片排序',
  INDEX `goods_id`(`goods_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品图片关联表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_params
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_params`;
CREATE TABLE `jshop_goods_params`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '参数名称',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '参数值',
  `type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '参数类型，text文本框，radio单选，checkbox复选框',
  `ctime` bigint(12) NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品参数表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_type
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_type`;
CREATE TABLE `jshop_goods_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类型名称',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '参数序列号存储 array(参数组名=>array(\'参数1\',\'参数二\'))',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品类型' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_type_params
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_type_params`;
CREATE TABLE `jshop_goods_type_params`  (
  `params_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '商品参数id',
  `type_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '商品类型id'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_type_spec
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_type_spec`;
CREATE TABLE `jshop_goods_type_spec`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品类型属性名称',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '商品类型属性排序 越小越靠前',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品类型属性表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_type_spec_rel
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_type_spec_rel`;
CREATE TABLE `jshop_goods_type_spec_rel`  (
  `spec_id` int(10) UNSIGNED NOT NULL COMMENT '属性ID',
  `type_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '类型ID'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品类型和属性关联表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_goods_type_spec_value
-- ----------------------------
DROP TABLE IF EXISTS `jshop_goods_type_spec_value`;
CREATE TABLE `jshop_goods_type_spec_value`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `spec_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '属性ID 关联goods_type_spec.id',
  `value` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '属性值',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '排序 越小越靠前',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `spec_id`(`spec_id`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品类型属性值表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_hooks
-- ----------------------------
DROP TABLE IF EXISTS `jshop_hooks`;
CREATE TABLE `jshop_hooks`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '钩子名称',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '钩子描述',
  `type` tinyint(1) NULL DEFAULT 1 COMMENT '钩子类型,1是控制器，2是视图',
  `addons` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '钩子挂载的插件，逗号分隔',
  `ctime` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `utime` int(11) NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10005 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_hooks
-- ----------------------------
INSERT INTO `jshop_hooks` VALUES (1, 'testhook', '测试钩子', 1, 'appletmessage,test', 1538029316, 1540173219);
INSERT INTO `jshop_hooks` VALUES (2, 'sendwxmessage', '发送微信消息', 1, 'appletmessage', 1538029328, 1538029328);
INSERT INTO `jshop_hooks` VALUES (3, 'kefu', '在线客服', 1, 'hecong', 1538029339, 1538029339);
INSERT INTO `jshop_hooks` VALUES (4, 'sendsms', '发送短信', 1, 'mms1086,aliyunsms', 1540891064, 1540891064);
INSERT INTO `jshop_hooks` VALUES (5, 'goodscommon', '商品编辑、添加时', 1, '', 1541072382, 1541072415);
INSERT INTO `jshop_hooks` VALUES (6, 'deletegoodsafter', '删除商品后', 1, '', 1541072393, 1541072393);
INSERT INTO `jshop_hooks` VALUES (7, 'editgoodsafter', '编辑商品后', 1, '', 1541072405, 1541072405);
INSERT INTO `jshop_hooks` VALUES (8, 'addgoodsafter', '添加商品后', 1, '', 1541072425, 1541072425);
INSERT INTO `jshop_hooks` VALUES (9, 'addgoodsview', '添加商品页面', 2, '', 1541072442, 1541072451);
INSERT INTO `jshop_hooks` VALUES (10, 'editgoodsview', '编辑商品页面', 2, '', 1541072464, 1541072464);
INSERT INTO `jshop_hooks` VALUES (11, 'trustlogin', '信任登录', 1, 'trustlogin', 1541771962, 1541771962);
INSERT INTO `jshop_hooks` VALUES (12, 'trustcallback', '信任登录返回', 1, 'trustlogin', 1541771974, 1541771974);
INSERT INTO `jshop_hooks` VALUES (14, 'orderpayed', '订单支付', 1, 'test', 1542349293, 1542349507);
INSERT INTO `jshop_hooks` VALUES (10000, 'printOrder', '打印订单埋点', 1, 'PrintExpress,KdniaoExpress', 1552448691, 1552448691);
INSERT INTO `jshop_hooks` VALUES (10001, 'getPrintExpressInfo', '获取打印信息', 1, 'KdniaoExpress', 1552557472, 1552557472);
INSERT INTO `jshop_hooks` VALUES (10002, 'orderExtJs', '订单扩展js', 2, 'KdniaoExpress', 1552558689, 1552559645);
INSERT INTO `jshop_hooks` VALUES (10003, 'orderExtBtn', '订单扩展按钮', 2, 'KdniaoExpress', 1552558705, 1552559662);
INSERT INTO `jshop_hooks` VALUES (10004, 'logisticsQuery', '快递查询', 1, 'KdniaoExpress', 1552704811, 1552704811);

-- ----------------------------
-- Table structure for jshop_ietask
-- ----------------------------
DROP TABLE IF EXISTS `jshop_ietask`;
CREATE TABLE `jshop_ietask`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '任务名称',
  `message` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '任务消息',
  `file_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'csv' COMMENT '文件类型',
  `ctime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `type` tinyint(1) NULL DEFAULT NULL COMMENT '任务类型，1为导出，2为导入',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '任务状态，0为等待执行，1正在导出，2导出成功，3导出失败，4正在导入，5导入成功，6导入失败，7中断，8部分导入',
  `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '相关参数',
  `file_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '文件名称',
  `file_size` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '文件大小',
  `file_path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '文件路径',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '导入导出任务表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_images
-- ----------------------------
DROP TABLE IF EXISTS `jshop_images`;
CREATE TABLE `jshop_images`  (
  `id` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片名称',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '绝对地址',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物理地址',
  `type` enum('web','local') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'local' COMMENT '存储引擎',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标志 有数据代表删除',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `id_2`(`id`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '图片表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_invoice_record
-- ----------------------------
DROP TABLE IF EXISTS `jshop_invoice_record`;
CREATE TABLE `jshop_invoice_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '发票抬头',
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '发票税号',
  `frequency` mediumint(6) UNSIGNED NULL DEFAULT 1 COMMENT '被使用次数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '发票信息记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_jobs
-- ----------------------------
DROP TABLE IF EXISTS `jshop_jobs`;
CREATE TABLE `jshop_jobs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED NULL DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '队列表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_label
-- ----------------------------
DROP TABLE IF EXISTS `jshop_label`;
CREATE TABLE `jshop_label`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标签名称',
  `style` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'hot' COMMENT '标签样式',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '标签表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_login_log
-- ----------------------------
DROP TABLE IF EXISTS `jshop_login_log`;
CREATE TABLE `jshop_login_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `state` tinyint(1) NULL DEFAULT NULL COMMENT '登录 1  退出2,3注册',
  `log_time` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '时间',
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '地点城市',
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ip地址',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_logistics
-- ----------------------------
DROP TABLE IF EXISTS `jshop_logistics`;
CREATE TABLE `jshop_logistics`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `logi_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物流公司名称',
  `logi_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物流公司编码',
  `sort` tinyint(3) UNSIGNED NULL DEFAULT 100 COMMENT '排序 越小越靠前',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `logi_code`(`logi_code`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1100 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物流公司表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_logistics
-- ----------------------------
INSERT INTO `jshop_logistics` VALUES (1, '海带宝', 'haidaibao', 100);
INSERT INTO `jshop_logistics` VALUES (2, '澳通华人物流', 'cllexpress', 100);
INSERT INTO `jshop_logistics` VALUES (3, '斑马物流', 'banma', 100);
INSERT INTO `jshop_logistics` VALUES (4, '信丰物流', 'xinfengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (5, '德国(Deutsche Post)', 'deutschepost', 100);
INSERT INTO `jshop_logistics` VALUES (6, '苏宁订单', 'suningorder', 100);
INSERT INTO `jshop_logistics` VALUES (7, '宜送物流', 'yiex', 100);
INSERT INTO `jshop_logistics` VALUES (8, 'AOL澳通速递', 'aolau', 100);
INSERT INTO `jshop_logistics` VALUES (9, 'TRAKPAK', 'trakpak', 100);
INSERT INTO `jshop_logistics` VALUES (10, 'GTS快递', 'gts', 100);
INSERT INTO `jshop_logistics` VALUES (11, '通达兴物流', 'tongdaxing', 100);
INSERT INTO `jshop_logistics` VALUES (12, '中国香港(HongKong Post)英文', 'hkposten', 100);
INSERT INTO `jshop_logistics` VALUES (13, '骏丰国际速递', 'junfengguoji', 100);
INSERT INTO `jshop_logistics` VALUES (14, '俄罗斯邮政(Russian Post)', 'pochta', 100);
INSERT INTO `jshop_logistics` VALUES (15, '云达通', 'ydglobe', 100);
INSERT INTO `jshop_logistics` VALUES (16, 'EU-EXPRESS', 'euexpress', 100);
INSERT INTO `jshop_logistics` VALUES (17, '广州海关', 'gzcustoms', 100);
INSERT INTO `jshop_logistics` VALUES (18, '杭州海关', 'hzcustoms', 100);
INSERT INTO `jshop_logistics` VALUES (19, '南京海关', 'njcustoms', 100);
INSERT INTO `jshop_logistics` VALUES (20, '北京海关', 'bjcustoms', 100);
INSERT INTO `jshop_logistics` VALUES (21, '美西快递', 'meixi', 100);
INSERT INTO `jshop_logistics` VALUES (22, '一站通快递', 'zgyzt', 100);
INSERT INTO `jshop_logistics` VALUES (23, '易联通达', 'el56', 100);
INSERT INTO `jshop_logistics` VALUES (24, '驿扬国际速运', 'iyoungspeed', 100);
INSERT INTO `jshop_logistics` VALUES (25, '途鲜物流', 'ibenben', 100);
INSERT INTO `jshop_logistics` VALUES (26, '豌豆物流', 'wandougongzhu', 100);
INSERT INTO `jshop_logistics` VALUES (27, '哥士传奇速递', 'gscq365', 100);
INSERT INTO `jshop_logistics` VALUES (28, '心怡物流', 'alog', 100);
INSERT INTO `jshop_logistics` VALUES (29, 'ME物流', 'macroexpressco', 100);
INSERT INTO `jshop_logistics` VALUES (30, '疯狂快递', 'crazyexpress', 100);
INSERT INTO `jshop_logistics` VALUES (31, '韩国邮政韩文', 'koreapostkr', 100);
INSERT INTO `jshop_logistics` VALUES (32, '全速物流', 'quansu', 100);
INSERT INTO `jshop_logistics` VALUES (33, '新杰物流', 'sunjex', 100);
INSERT INTO `jshop_logistics` VALUES (34, '鲁通快运', 'lutong', 100);
INSERT INTO `jshop_logistics` VALUES (35, '安的快递', 'gda', 100);
INSERT INTO `jshop_logistics` VALUES (36, '美国申通', 'stoexpress', 100);
INSERT INTO `jshop_logistics` VALUES (37, '法国小包（colissimo）', 'colissimo', 100);
INSERT INTO `jshop_logistics` VALUES (38, '泛捷国际速递', 'epanex', 100);
INSERT INTO `jshop_logistics` VALUES (39, '中远e环球', 'cosco', 100);
INSERT INTO `jshop_logistics` VALUES (40, '顺达快递', 'sundarexpress', 100);
INSERT INTO `jshop_logistics` VALUES (41, '捷记方舟', 'ajexpress', 100);
INSERT INTO `jshop_logistics` VALUES (42, '方舟速递', 'arkexpress', 100);
INSERT INTO `jshop_logistics` VALUES (43, '明大快递', 'adaexpress', 100);
INSERT INTO `jshop_logistics` VALUES (44, '长江国际速递', 'changjiang', 100);
INSERT INTO `jshop_logistics` VALUES (45, 'PCA Express', 'pcaexpress', 100);
INSERT INTO `jshop_logistics` VALUES (46, '洋包裹', 'yangbaoguo', 100);
INSERT INTO `jshop_logistics` VALUES (47, '优联吉运', 'uluckex', 100);
INSERT INTO `jshop_logistics` VALUES (48, '德豪驿', 'dehaoyi', 100);
INSERT INTO `jshop_logistics` VALUES (49, '堡昕德速递', 'bosind', 100);
INSERT INTO `jshop_logistics` VALUES (50, '阿根廷(Correo Argentina)', 'correoargentino', 100);
INSERT INTO `jshop_logistics` VALUES (51, '秘鲁(SERPOST)', 'peru', 100);
INSERT INTO `jshop_logistics` VALUES (52, '哈萨克斯坦(Kazpost)', 'kazpost', 100);
INSERT INTO `jshop_logistics` VALUES (53, '广通速递', 'gtongsudi', 100);
INSERT INTO `jshop_logistics` VALUES (54, '东瀚物流', 'donghanwl', 100);
INSERT INTO `jshop_logistics` VALUES (55, 'rpx', 'rpx', 100);
INSERT INTO `jshop_logistics` VALUES (56, '黑猫雅玛多', 'yamato', 100);
INSERT INTO `jshop_logistics` VALUES (57, '华通快运', 'htongexpress', 100);
INSERT INTO `jshop_logistics` VALUES (58, '吉尔吉斯斯坦(Kyrgyz Post)', 'kyrgyzpost', 100);
INSERT INTO `jshop_logistics` VALUES (59, '拉脱维亚(Latvijas Pasts)', 'latvia', 100);
INSERT INTO `jshop_logistics` VALUES (60, '黎巴嫩(Liban Post)', 'libanpost', 100);
INSERT INTO `jshop_logistics` VALUES (61, '立陶宛（Lietuvos pa?tas）', 'lithuania', 100);
INSERT INTO `jshop_logistics` VALUES (62, '马尔代夫(Maldives Post)', 'maldives', 100);
INSERT INTO `jshop_logistics` VALUES (63, '马耳他（Malta Post）', 'malta', 100);
INSERT INTO `jshop_logistics` VALUES (64, '马其顿(Macedonian Post)', 'macedonia', 100);
INSERT INTO `jshop_logistics` VALUES (65, '新西兰（New Zealand Post）', 'newzealand', 100);
INSERT INTO `jshop_logistics` VALUES (66, '摩尔多瓦(Posta Moldovei)', 'moldova', 100);
INSERT INTO `jshop_logistics` VALUES (67, '塞尔维亚(PE Post of Serbia)', 'serbia', 100);
INSERT INTO `jshop_logistics` VALUES (68, '塞浦路斯(Cyprus Post)', 'cypruspost', 100);
INSERT INTO `jshop_logistics` VALUES (69, '突尼斯EMS(Rapid-Poste)', 'tunisia', 100);
INSERT INTO `jshop_logistics` VALUES (70, '乌兹别克斯坦(Post of Uzbekistan)', 'uzbekistan', 100);
INSERT INTO `jshop_logistics` VALUES (71, '新喀里多尼亚[法国](New Caledonia)', 'caledonia', 100);
INSERT INTO `jshop_logistics` VALUES (72, '叙利亚(Syrian Post)', 'republic', 100);
INSERT INTO `jshop_logistics` VALUES (73, '亚美尼亚(Haypost-Armenian Postal)', 'haypost', 100);
INSERT INTO `jshop_logistics` VALUES (74, '也门(Yemen Post)', 'yemen', 100);
INSERT INTO `jshop_logistics` VALUES (75, '印度(India Post)', 'india', 100);
INSERT INTO `jshop_logistics` VALUES (76, '英国(大包,EMS)', 'england', 100);
INSERT INTO `jshop_logistics` VALUES (77, '约旦(Jordan Post)', 'jordan', 100);
INSERT INTO `jshop_logistics` VALUES (78, '越南小包(Vietnam Posts)', 'vietnam', 100);
INSERT INTO `jshop_logistics` VALUES (79, '黑山(Po?ta Crne Gore)', 'montenegro', 100);
INSERT INTO `jshop_logistics` VALUES (80, '哥斯达黎加(Correos de Costa Rica)', 'correos', 100);
INSERT INTO `jshop_logistics` VALUES (81, 'EFS Post（平安快递）', 'efs', 100);
INSERT INTO `jshop_logistics` VALUES (82, 'TNT Post', 'tntpostcn', 100);
INSERT INTO `jshop_logistics` VALUES (83, '立白宝凯物流', 'lbbk', 100);
INSERT INTO `jshop_logistics` VALUES (84, '匈牙利（Magyar Posta）', 'hungary', 100);
INSERT INTO `jshop_logistics` VALUES (85, '中国澳门(Macau Post)', 'macao', 100);
INSERT INTO `jshop_logistics` VALUES (86, '西安喜来快递', 'xilaikd', 100);
INSERT INTO `jshop_logistics` VALUES (87, '韩润', 'hanrun', 100);
INSERT INTO `jshop_logistics` VALUES (88, '格陵兰[丹麦]（TELE Greenland A/S）', 'greenland', 100);
INSERT INTO `jshop_logistics` VALUES (89, '菲律宾（Philippine Postal）', 'phlpost', 100);
INSERT INTO `jshop_logistics` VALUES (90, '厄瓜多尔(Correos del Ecuador)', 'ecuador', 100);
INSERT INTO `jshop_logistics` VALUES (91, '冰岛(Iceland Post)', 'iceland', 100);
INSERT INTO `jshop_logistics` VALUES (92, '波兰小包(Poczta Polska)', 'emonitoring', 100);
INSERT INTO `jshop_logistics` VALUES (93, '阿尔巴尼亚(Posta shqipatre)', 'albania', 100);
INSERT INTO `jshop_logistics` VALUES (94, '埃及（Egypt Post）', 'egypt', 100);
INSERT INTO `jshop_logistics` VALUES (95, '爱沙尼亚(Eesti Post)', 'omniva', 100);
INSERT INTO `jshop_logistics` VALUES (96, '云豹国际货运', 'leopard', 100);
INSERT INTO `jshop_logistics` VALUES (97, '中外运空运', 'sinoairinex', 100);
INSERT INTO `jshop_logistics` VALUES (98, '上海昊宏国际货物', 'hyk', 100);
INSERT INTO `jshop_logistics` VALUES (99, '城晓国际快递', 'ckeex', 100);
INSERT INTO `jshop_logistics` VALUES (100, '中铁快运', 'ztky', 100);
INSERT INTO `jshop_logistics` VALUES (101, '出口易', 'chukou1', 100);
INSERT INTO `jshop_logistics` VALUES (102, '跨畅（直邮易）', 'kuachangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (103, 'WTD海外通', 'wtdex', 100);
INSERT INTO `jshop_logistics` VALUES (104, 'CHS中环国际快递', 'chszhonghuanguoji', 100);
INSERT INTO `jshop_logistics` VALUES (105, '汉邦国际速递', 'handboy', 100);
INSERT INTO `jshop_logistics` VALUES (106, '银河物流', 'milkyway', 100);
INSERT INTO `jshop_logistics` VALUES (107, '荷兰速递(Nederland Post)', 'nederlandpost', 100);
INSERT INTO `jshop_logistics` VALUES (108, '澳州顺风快递', 'emms', 100);
INSERT INTO `jshop_logistics` VALUES (109, '环东物流', 'huandonglg', 100);
INSERT INTO `jshop_logistics` VALUES (110, '中邮速递', 'wondersyd', 100);
INSERT INTO `jshop_logistics` VALUES (111, '布谷鸟速递', 'cuckooexpess', 100);
INSERT INTO `jshop_logistics` VALUES (112, '万庚国际速递', 'vangenexpress', 100);
INSERT INTO `jshop_logistics` VALUES (113, 'FedRoad 联邦转运', 'fedroad', 100);
INSERT INTO `jshop_logistics` VALUES (114, 'Landmark Global', 'landmarkglobal', 100);
INSERT INTO `jshop_logistics` VALUES (115, '佳成快递', 'jiacheng', 100);
INSERT INTO `jshop_logistics` VALUES (116, '诺尔国际物流', 'nuoer', 100);
INSERT INTO `jshop_logistics` VALUES (117, '加运美速递', 'jym56', 100);
INSERT INTO `jshop_logistics` VALUES (118, '新时速物流', 'csxss', 100);
INSERT INTO `jshop_logistics` VALUES (119, '中宇天地', 'zytdscm', 100);
INSERT INTO `jshop_logistics` VALUES (120, '翔腾物流', 'xiangteng', 100);
INSERT INTO `jshop_logistics` VALUES (121, '恒瑞物流', 'hengrui56', 100);
INSERT INTO `jshop_logistics` VALUES (122, '中国翼', 'cnws', 100);
INSERT INTO `jshop_logistics` VALUES (123, '邦工快运', 'bgky100', 100);
INSERT INTO `jshop_logistics` VALUES (124, '上海无疆for买卖宝', 'shanghaiwujiangmmb', 100);
INSERT INTO `jshop_logistics` VALUES (125, '新加坡小包(Singapore Post)', 'singpost', 100);
INSERT INTO `jshop_logistics` VALUES (126, '中俄速通（淼信）', 'mxe56', 100);
INSERT INTO `jshop_logistics` VALUES (127, '海派通', 'hipito', 100);
INSERT INTO `jshop_logistics` VALUES (128, '源安达', 'yuananda', 100);
INSERT INTO `jshop_logistics` VALUES (129, '赛澳递for买卖宝', 'saiaodimmb', 100);
INSERT INTO `jshop_logistics` VALUES (130, 'ECMS Express', 'ecmsglobal', 100);
INSERT INTO `jshop_logistics` VALUES (131, '英脉物流', 'gml', 100);
INSERT INTO `jshop_logistics` VALUES (132, '佳家通货运', 'jiajiatong56', 100);
INSERT INTO `jshop_logistics` VALUES (133, '吉日优派', 'jrypex', 100);
INSERT INTO `jshop_logistics` VALUES (134, '西安胜峰', 'xaetc', 100);
INSERT INTO `jshop_logistics` VALUES (135, 'logen路坚', 'ilogen', 100);
INSERT INTO `jshop_logistics` VALUES (136, 'amazon-国际订单', 'amusorder', 100);
INSERT INTO `jshop_logistics` VALUES (137, 'CJ物流', 'doortodoor', 100);
INSERT INTO `jshop_logistics` VALUES (138, '转运四方', 'zhuanyunsifang', 100);
INSERT INTO `jshop_logistics` VALUES (139, '成都东骏物流', 'dongjun', 100);
INSERT INTO `jshop_logistics` VALUES (140, '日本郵便', 'japanpost', 100);
INSERT INTO `jshop_logistics` VALUES (141, '猴急送', 'hjs', 100);
INSERT INTO `jshop_logistics` VALUES (142, '全信通快递', 'quanxintong', 100);
INSERT INTO `jshop_logistics` VALUES (143, '信天捷快递', 'xintianjie', 100);
INSERT INTO `jshop_logistics` VALUES (144, '泰国138国际物流', 'sd138', 100);
INSERT INTO `jshop_logistics` VALUES (145, '荷兰包裹(PostNL International Parc', 'postnlpacle', 100);
INSERT INTO `jshop_logistics` VALUES (146, '乐天速递', 'ltexp', 100);
INSERT INTO `jshop_logistics` VALUES (147, '智通物流', 'ztong', 100);
INSERT INTO `jshop_logistics` VALUES (148, '全速通', 'quansutong', 100);
INSERT INTO `jshop_logistics` VALUES (149, '中技物流', 'zhongjiwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (150, '九曳供应链', 'jiuyescm', 100);
INSERT INTO `jshop_logistics` VALUES (151, '当当', 'dangdang', 100);
INSERT INTO `jshop_logistics` VALUES (152, '美龙快递', 'mjexp', 100);
INSERT INTO `jshop_logistics` VALUES (153, '唯品会(vip)', 'vipshop', 100);
INSERT INTO `jshop_logistics` VALUES (154, '1号店', 'yhdshop', 100);
INSERT INTO `jshop_logistics` VALUES (155, '皇家物流', 'pfcexpress', 100);
INSERT INTO `jshop_logistics` VALUES (156, '百千诚物流', 'bqcwl', 100);
INSERT INTO `jshop_logistics` VALUES (157, '法国(La Poste)', 'csuivi', 100);
INSERT INTO `jshop_logistics` VALUES (158, 'DHL-全球件', 'dhlen', 100);
INSERT INTO `jshop_logistics` VALUES (159, '运通中港', 'yuntongkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (160, '苏宁物流', 'suning', 100);
INSERT INTO `jshop_logistics` VALUES (161, '荷兰Sky Post', 'skypost', 100);
INSERT INTO `jshop_logistics` VALUES (162, '瑞达国际速递', 'ruidaex', 100);
INSERT INTO `jshop_logistics` VALUES (163, '丰程物流', 'sccod', 100);
INSERT INTO `jshop_logistics` VALUES (164, '德中快递', 'decnlh', 100);
INSERT INTO `jshop_logistics` VALUES (165, '全时速运', 'runhengfeng', 100);
INSERT INTO `jshop_logistics` VALUES (166, '云邮跨境快递', 'hkems', 100);
INSERT INTO `jshop_logistics` VALUES (167, '亚风速递', 'yafengsudi', 100);
INSERT INTO `jshop_logistics` VALUES (168, '快淘快递', 'kuaitao', 100);
INSERT INTO `jshop_logistics` VALUES (169, '鑫通宝物流', 'xtb', 100);
INSERT INTO `jshop_logistics` VALUES (170, 'USPS', 'usps', 100);
INSERT INTO `jshop_logistics` VALUES (171, '加拿大邮政', 'canpostfr', 100);
INSERT INTO `jshop_logistics` VALUES (172, '汇通天下物流', 'httx56', 100);
INSERT INTO `jshop_logistics` VALUES (173, '台湾（中华邮政）', 'postserv', 100);
INSERT INTO `jshop_logistics` VALUES (174, '好又快物流', 'haoyoukuai', 100);
INSERT INTO `jshop_logistics` VALUES (175, '永旺达快递', 'yongwangda', 100);
INSERT INTO `jshop_logistics` VALUES (176, '木春货运', 'mchy', 100);
INSERT INTO `jshop_logistics` VALUES (177, '程光快递', 'flyway', 100);
INSERT INTO `jshop_logistics` VALUES (178, '百事亨通', 'bsht', 100);
INSERT INTO `jshop_logistics` VALUES (179, '万家通快递', 'timedg', 100);
INSERT INTO `jshop_logistics` VALUES (180, '全之鑫物流', 'qzx56', 100);
INSERT INTO `jshop_logistics` VALUES (181, '美快国际物流', 'meiquick', 100);
INSERT INTO `jshop_logistics` VALUES (182, 'ILYANG', 'ilyang', 100);
INSERT INTO `jshop_logistics` VALUES (183, '先锋快递', 'xianfeng', 100);
INSERT INTO `jshop_logistics` VALUES (184, '亿顺航', 'yishunhang', 100);
INSERT INTO `jshop_logistics` VALUES (185, '尚橙物流', 'shangcheng', 100);
INSERT INTO `jshop_logistics` VALUES (186, 'OnTrac', 'ontrac', 100);
INSERT INTO `jshop_logistics` VALUES (187, 'TNT-全球件', 'tnten', 100);
INSERT INTO `jshop_logistics` VALUES (188, '顺丰-美国件', 'shunfengen', 100);
INSERT INTO `jshop_logistics` VALUES (189, '共速达', 'gongsuda', 100);
INSERT INTO `jshop_logistics` VALUES (190, '源伟丰', 'yuanweifeng', 100);
INSERT INTO `jshop_logistics` VALUES (191, '祥龙运通物流', 'xianglongyuntong', 100);
INSERT INTO `jshop_logistics` VALUES (192, '偌亚奥国际快递', 'nuoyaao', 100);
INSERT INTO `jshop_logistics` VALUES (193, '陪行物流', 'peixingwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (194, '天天快递', 'tiantian', 100);
INSERT INTO `jshop_logistics` VALUES (195, 'CCES/国通快递', 'cces', 100);
INSERT INTO `jshop_logistics` VALUES (196, '彪记快递', 'biaojikuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (197, '安信达', 'anxindakuaixi', 100);
INSERT INTO `jshop_logistics` VALUES (198, '配思货运', 'peisihuoyunkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (199, '大田物流', 'datianwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (200, '邮政快递包裹', 'youzhengguonei', 100);
INSERT INTO `jshop_logistics` VALUES (201, '文捷航空', 'wenjiesudi', 100);
INSERT INTO `jshop_logistics` VALUES (202, 'BHT', 'bht', 100);
INSERT INTO `jshop_logistics` VALUES (203, '北青小红帽', 'xiaohongmao', 100);
INSERT INTO `jshop_logistics` VALUES (204, 'GSM', 'gsm', 100);
INSERT INTO `jshop_logistics` VALUES (205, '汇强快递', 'huiqiangkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (206, '昊盛物流', 'haoshengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (207, '联邦快递-英文', 'lianbangkuaidien', 100);
INSERT INTO `jshop_logistics` VALUES (208, '伍圆速递', 'wuyuansudi', 100);
INSERT INTO `jshop_logistics` VALUES (209, '南京100', 'nanjing', 100);
INSERT INTO `jshop_logistics` VALUES (210, '全通快运', 'quantwl', 100);
INSERT INTO `jshop_logistics` VALUES (211, '宅急便', 'zhaijibian', 100);
INSERT INTO `jshop_logistics` VALUES (212, '加拿大(Canada Post)', 'canpost', 100);
INSERT INTO `jshop_logistics` VALUES (213, 'COE', 'coe', 100);
INSERT INTO `jshop_logistics` VALUES (214, '百通物流', 'buytong', 100);
INSERT INTO `jshop_logistics` VALUES (215, '友家速递', 'youjia', 100);
INSERT INTO `jshop_logistics` VALUES (216, '新元快递', 'xingyuankuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (217, '中澳速递', 'cnausu', 100);
INSERT INTO `jshop_logistics` VALUES (218, '联合快递', 'gslhkd', 100);
INSERT INTO `jshop_logistics` VALUES (219, '河南次晨达', 'ccd', 100);
INSERT INTO `jshop_logistics` VALUES (220, '奔腾物流', 'benteng', 100);
INSERT INTO `jshop_logistics` VALUES (221, '今枫国际快运', 'mapleexpress', 100);
INSERT INTO `jshop_logistics` VALUES (222, '中运全速', 'topspeedex', 100);
INSERT INTO `jshop_logistics` VALUES (223, '中欧快运', 'otobv', 100);
INSERT INTO `jshop_logistics` VALUES (224, '宜家行', 'yjxlm', 100);
INSERT INTO `jshop_logistics` VALUES (225, '金马甲', 'jmjss', 100);
INSERT INTO `jshop_logistics` VALUES (226, '一号仓', 'onehcang', 100);
INSERT INTO `jshop_logistics` VALUES (227, '论道国际物流', 'lundao', 100);
INSERT INTO `jshop_logistics` VALUES (228, '顺通快递', 'stkd', 100);
INSERT INTO `jshop_logistics` VALUES (229, 'globaltracktrace', 'globaltracktrace', 100);
INSERT INTO `jshop_logistics` VALUES (230, '德方物流', 'ahdf', 100);
INSERT INTO `jshop_logistics` VALUES (231, '速递中国', 'sendtochina', 100);
INSERT INTO `jshop_logistics` VALUES (232, 'NLE', 'nle', 100);
INSERT INTO `jshop_logistics` VALUES (233, '亚欧专线', 'nlebv', 100);
INSERT INTO `jshop_logistics` VALUES (234, '信联通', 'sinatone', 100);
INSERT INTO `jshop_logistics` VALUES (235, '澳德物流', 'auod', 100);
INSERT INTO `jshop_logistics` VALUES (236, '微转运', 'wzhaunyun', 100);
INSERT INTO `jshop_logistics` VALUES (237, 'iExpress', 'iexpress', 100);
INSERT INTO `jshop_logistics` VALUES (238, '远成快运', 'ycgky', 100);
INSERT INTO `jshop_logistics` VALUES (239, '高考通知书', 'emsluqu', 100);
INSERT INTO `jshop_logistics` VALUES (240, '安鲜达', 'exfresh', 100);
INSERT INTO `jshop_logistics` VALUES (241, 'BCWELT', 'bcwelt', 100);
INSERT INTO `jshop_logistics` VALUES (242, '欧亚专线', 'euasia', 100);
INSERT INTO `jshop_logistics` VALUES (243, '乐递供应链', 'ledii', 100);
INSERT INTO `jshop_logistics` VALUES (244, '万通快递', 'gswtkd', 100);
INSERT INTO `jshop_logistics` VALUES (245, '特急送', 'lntjs', 100);
INSERT INTO `jshop_logistics` VALUES (246, '金大物流', 'jindawuliu', 100);
INSERT INTO `jshop_logistics` VALUES (247, '民航快递', 'minghangkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (248, '红马甲物流', 'sxhongmajia', 100);
INSERT INTO `jshop_logistics` VALUES (249, 'amazon-国内订单', 'amcnorder', 100);
INSERT INTO `jshop_logistics` VALUES (250, 'ABF', 'abf', 100);
INSERT INTO `jshop_logistics` VALUES (251, '小米', 'xiaomi', 100);
INSERT INTO `jshop_logistics` VALUES (252, '新元国际', 'xynyc', 100);
INSERT INTO `jshop_logistics` VALUES (253, '小C海淘', 'xiaocex', 100);
INSERT INTO `jshop_logistics` VALUES (254, '航空快递', 'airgtc', 100);
INSERT INTO `jshop_logistics` VALUES (255, '叮咚澳洲转运', 'dindon', 100);
INSERT INTO `jshop_logistics` VALUES (256, '环球通达', 'hqtd', 100);
INSERT INTO `jshop_logistics` VALUES (257, '新西兰中通', 'nzzto', 100);
INSERT INTO `jshop_logistics` VALUES (258, '良藤国际速递', 'lmfex', 100);
INSERT INTO `jshop_logistics` VALUES (259, '速品快递', 'supinexpress', 100);
INSERT INTO `jshop_logistics` VALUES (260, '海龟国际快递', 'turtle', 100);
INSERT INTO `jshop_logistics` VALUES (261, '韩国邮政', 'koreapostcn', 100);
INSERT INTO `jshop_logistics` VALUES (262, '韵丰物流', 'yunfeng56', 100);
INSERT INTO `jshop_logistics` VALUES (263, '易达通快递', 'qexpress', 100);
INSERT INTO `jshop_logistics` VALUES (264, '一运全成物流', 'yyqc56', 100);
INSERT INTO `jshop_logistics` VALUES (265, '泛远国际物流', 'farlogistis', 100);
INSERT INTO `jshop_logistics` VALUES (266, '达速物流', 'dasu', 100);
INSERT INTO `jshop_logistics` VALUES (267, '恒通快递', 'lqht', 100);
INSERT INTO `jshop_logistics` VALUES (268, '壹品速递', 'ypsd', 100);
INSERT INTO `jshop_logistics` VALUES (269, '鹰运国际速递', 'vipexpress', 100);
INSERT INTO `jshop_logistics` VALUES (270, '南方传媒物流', 'ndwl', 100);
INSERT INTO `jshop_logistics` VALUES (271, '速呈宅配', 'sucheng', 100);
INSERT INTO `jshop_logistics` VALUES (272, '云南滇驿物流', 'dianyi', 100);
INSERT INTO `jshop_logistics` VALUES (273, '四川星程快递', 'scxingcheng', 100);
INSERT INTO `jshop_logistics` VALUES (274, '运通中港快递', 'ytkd', 100);
INSERT INTO `jshop_logistics` VALUES (275, 'Gati-英文', 'gatien', 100);
INSERT INTO `jshop_logistics` VALUES (276, 'jcex', 'jcex', 100);
INSERT INTO `jshop_logistics` VALUES (277, '凯信达', 'kxda', 100);
INSERT INTO `jshop_logistics` VALUES (278, '安达信', 'advancing', 100);
INSERT INTO `jshop_logistics` VALUES (279, '亿翔', 'yxexpress', 100);
INSERT INTO `jshop_logistics` VALUES (280, '加运美', 'jiayunmeiwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (281, '赛澳递', 'saiaodi', 100);
INSERT INTO `jshop_logistics` VALUES (282, '康力物流', 'kangliwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (283, '鑫飞鸿', 'xinhongyukuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (284, '全一快递', 'quanyikuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (285, '华企快运', 'huaqikuaiyun', 100);
INSERT INTO `jshop_logistics` VALUES (286, '青岛安捷快递', 'anjiekuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (287, '递四方', 'disifang', 100);
INSERT INTO `jshop_logistics` VALUES (288, '三态速递', 'santaisudi', 100);
INSERT INTO `jshop_logistics` VALUES (289, '成都立即送', 'lijisong', 100);
INSERT INTO `jshop_logistics` VALUES (290, '河北建华', 'hebeijianhua', 100);
INSERT INTO `jshop_logistics` VALUES (291, '风行天下', 'fengxingtianxia', 100);
INSERT INTO `jshop_logistics` VALUES (292, '一统飞鸿', 'yitongfeihong', 100);
INSERT INTO `jshop_logistics` VALUES (293, '海外环球', 'haiwaihuanqiu', 100);
INSERT INTO `jshop_logistics` VALUES (294, 'DHL-中国件', 'dhl', 100);
INSERT INTO `jshop_logistics` VALUES (295, '西安城联速递', 'xianchengliansudi', 100);
INSERT INTO `jshop_logistics` VALUES (296, '一柒国际物流', 'yiqiguojiwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (297, '广东通路', 'guangdongtonglu', 100);
INSERT INTO `jshop_logistics` VALUES (298, '中国香港骏辉物流', 'chunfai', 100);
INSERT INTO `jshop_logistics` VALUES (299, '三三国际物流', 'zenzen', 100);
INSERT INTO `jshop_logistics` VALUES (300, '比利时国际(Bpost international)', 'bpostinter', 100);
INSERT INTO `jshop_logistics` VALUES (301, '海红for买卖宝', 'haihongmmb', 100);
INSERT INTO `jshop_logistics` VALUES (302, 'FedEx-英国件（FedEx UK)', 'fedexuk', 100);
INSERT INTO `jshop_logistics` VALUES (303, 'FedEx-英国件', 'fedexukcn', 100);
INSERT INTO `jshop_logistics` VALUES (304, '叮咚快递', 'dingdong', 100);
INSERT INTO `jshop_logistics` VALUES (305, 'MRW', 'mrw', 100);
INSERT INTO `jshop_logistics` VALUES (306, 'Chronopost Portugal', 'chronopostport', 100);
INSERT INTO `jshop_logistics` VALUES (307, '西班牙(Correos de Espa?a)', 'correosdees', 100);
INSERT INTO `jshop_logistics` VALUES (308, '丹麦(Post Denmark)', 'postdanmarken', 100);
INSERT INTO `jshop_logistics` VALUES (309, 'Purolator', 'purolator', 100);
INSERT INTO `jshop_logistics` VALUES (310, '法国大包、EMS-法文（Chronopost France）', 'chronopostfra', 100);
INSERT INTO `jshop_logistics` VALUES (311, 'Selektvracht', 'selektvracht', 100);
INSERT INTO `jshop_logistics` VALUES (312, '蓝弧快递', 'lanhukuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (313, '比利时(Belgium Post)', 'belgiumpost', 100);
INSERT INTO `jshop_logistics` VALUES (314, '晟邦物流', 'nanjingshengbang', 100);
INSERT INTO `jshop_logistics` VALUES (315, 'UPS Mail Innovations', 'upsmailinno', 100);
INSERT INTO `jshop_logistics` VALUES (316, '挪威（Posten Norge）', 'postennorge', 100);
INSERT INTO `jshop_logistics` VALUES (317, '瑞士(Swiss Post)', 'swisspost', 100);
INSERT INTO `jshop_logistics` VALUES (318, '英国邮政小包', 'royalmailcn', 100);
INSERT INTO `jshop_logistics` VALUES (319, '英国小包（Royal Mail）', 'royalmail', 100);
INSERT INTO `jshop_logistics` VALUES (320, 'DHL Benelux', 'dhlbenelux', 100);
INSERT INTO `jshop_logistics` VALUES (321, 'DHL-荷兰（DHL Netherlands）', 'dhlnetherlands', 100);
INSERT INTO `jshop_logistics` VALUES (322, 'OPEK', 'opek', 100);
INSERT INTO `jshop_logistics` VALUES (323, 'Italy SDA', 'italysad', 100);
INSERT INTO `jshop_logistics` VALUES (324, 'Fastway Ireland', 'fastway', 100);
INSERT INTO `jshop_logistics` VALUES (325, 'DHL-波兰（DHL Poland）', 'dhlpoland', 100);
INSERT INTO `jshop_logistics` VALUES (326, 'DPD', 'dpd', 100);
INSERT INTO `jshop_logistics` VALUES (327, '速通物流', 'sutongwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (328, '荷兰邮政-中文(PostNL international r', 'postnlcn', 100);
INSERT INTO `jshop_logistics` VALUES (329, '荷兰邮政(PostNL international regi', 'postnl', 100);
INSERT INTO `jshop_logistics` VALUES (330, '乌克兰EMS(EMS Ukraine)', 'emsukraine', 100);
INSERT INTO `jshop_logistics` VALUES (331, '乌克兰邮政包裹', 'ukrpostcn', 100);
INSERT INTO `jshop_logistics` VALUES (332, '英国大包、EMS（Parcel Force）', 'parcelforce', 100);
INSERT INTO `jshop_logistics` VALUES (333, 'YODEL', 'yodel', 100);
INSERT INTO `jshop_logistics` VALUES (334, 'UBI Australia', 'gotoubi', 100);
INSERT INTO `jshop_logistics` VALUES (335, '红马速递', 'nedahm', 100);
INSERT INTO `jshop_logistics` VALUES (336, '云南诚中物流', 'czwlyn', 100);
INSERT INTO `jshop_logistics` VALUES (337, '万博快递', 'wanboex', 100);
INSERT INTO `jshop_logistics` VALUES (338, '腾达速递', 'nntengda', 100);
INSERT INTO `jshop_logistics` VALUES (339, '郑州速捷', 'sujievip', 100);
INSERT INTO `jshop_logistics` VALUES (340, '中睿速递', 'zhongruisudi', 100);
INSERT INTO `jshop_logistics` VALUES (341, '中天万运', 'zhongtianwanyun', 100);
INSERT INTO `jshop_logistics` VALUES (342, '新蛋奥硕', 'neweggozzo', 100);
INSERT INTO `jshop_logistics` VALUES (343, '七天连锁', 'sevendays', 100);
INSERT INTO `jshop_logistics` VALUES (344, 'UPS-全球件', 'upsen', 100);
INSERT INTO `jshop_logistics` VALUES (345, '跨越速运', 'kuayue', 100);
INSERT INTO `jshop_logistics` VALUES (346, '全际通', 'quanjitong', 100);
INSERT INTO `jshop_logistics` VALUES (347, 'UPS', 'ups', 100);
INSERT INTO `jshop_logistics` VALUES (348, '一邦速递', 'yibangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (349, '上海快通', 'shanghaikuaitong', 100);
INSERT INTO `jshop_logistics` VALUES (350, '品速心达快递', 'pinsuxinda', 100);
INSERT INTO `jshop_logistics` VALUES (351, 'PostNord(Posten AB)', 'postenab', 100);
INSERT INTO `jshop_logistics` VALUES (352, '城际速递', 'chengjisudi', 100);
INSERT INTO `jshop_logistics` VALUES (353, '户通物流', 'hutongwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (354, '飞康达', 'feikangda', 100);
INSERT INTO `jshop_logistics` VALUES (355, '星晨急便', 'xingchengjibian', 100);
INSERT INTO `jshop_logistics` VALUES (356, '全日通', 'quanritongkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (357, '凤凰快递', 'fenghuangkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (358, '广东邮政', 'guangdongyouzhengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (359, '长宇物流', 'changyuwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (360, '万家物流', 'wanjiawuliu', 100);
INSERT INTO `jshop_logistics` VALUES (361, 'EMS-国际件-英文', 'emsinten', 100);
INSERT INTO `jshop_logistics` VALUES (362, '飞远配送', 'feiyuanvipshop', 100);
INSERT INTO `jshop_logistics` VALUES (363, '国美', 'gome', 100);
INSERT INTO `jshop_logistics` VALUES (364, '能达速递', 'ganzhongnengda', 100);
INSERT INTO `jshop_logistics` VALUES (365, '急先达', 'jixianda', 100);
INSERT INTO `jshop_logistics` VALUES (366, '凡宇快递', 'fanyukuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (367, '希优特', 'xiyoutekuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (368, '中通（带电话）', 'zhongtongphone', 100);
INSERT INTO `jshop_logistics` VALUES (369, '蓝镖快递', 'lanbiaokuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (370, '佳吉快运', 'jiajiwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (371, '宏品物流', 'hongpinwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (372, 'GLS', 'gls', 100);
INSERT INTO `jshop_logistics` VALUES (373, '原飞航', 'yuanfeihangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (374, '海红网送', 'haihongwangsong', 100);
INSERT INTO `jshop_logistics` VALUES (375, 'TNT', 'tnt', 100);
INSERT INTO `jshop_logistics` VALUES (376, '元智捷诚', 'yuanzhijiecheng', 100);
INSERT INTO `jshop_logistics` VALUES (377, '国际包裹', 'youzhengguoji', 100);
INSERT INTO `jshop_logistics` VALUES (378, '城市100', 'city100', 100);
INSERT INTO `jshop_logistics` VALUES (379, 'DPEX', 'dpex', 100);
INSERT INTO `jshop_logistics` VALUES (380, '芝麻开门', 'zhimakaimen', 100);
INSERT INTO `jshop_logistics` VALUES (381, 'EMS-国际件', 'emsguoji', 100);
INSERT INTO `jshop_logistics` VALUES (382, '晋越快递', 'jinyuekuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (383, '乐捷递', 'lejiedi', 100);
INSERT INTO `jshop_logistics` VALUES (384, '飞力士物流', 'flysman', 100);
INSERT INTO `jshop_logistics` VALUES (385, '百腾物流', 'baitengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (386, '品骏快递', 'pjbest', 100);
INSERT INTO `jshop_logistics` VALUES (387, '瓦努阿图(Vanuatu Post)', 'vanuatu', 100);
INSERT INTO `jshop_logistics` VALUES (388, '巴巴多斯(Barbados Post)', 'barbados', 100);
INSERT INTO `jshop_logistics` VALUES (389, '萨摩亚(Samoa Post)', 'samoa', 100);
INSERT INTO `jshop_logistics` VALUES (390, '斐济(Fiji Post)', 'fiji', 100);
INSERT INTO `jshop_logistics` VALUES (391, '英超物流', 'yingchao', 100);
INSERT INTO `jshop_logistics` VALUES (392, 'TNY物流', 'tny', 100);
INSERT INTO `jshop_logistics` VALUES (393, '美通', 'valueway', 100);
INSERT INTO `jshop_logistics` VALUES (394, '新速航', 'sunspeedy', 100);
INSERT INTO `jshop_logistics` VALUES (395, '速方(Sufast)', 'bphchina', 100);
INSERT INTO `jshop_logistics` VALUES (396, '华航快递', 'hzpl', 100);
INSERT INTO `jshop_logistics` VALUES (397, 'Gati-KWE', 'gatikwe', 100);
INSERT INTO `jshop_logistics` VALUES (398, 'Red Express', 'redexpress', 100);
INSERT INTO `jshop_logistics` VALUES (399, 'Toll Priority(Toll Online)', 'tollpriority', 100);
INSERT INTO `jshop_logistics` VALUES (400, 'Estafeta', 'estafeta', 100);
INSERT INTO `jshop_logistics` VALUES (401, '港快速递', 'gdkd', 100);
INSERT INTO `jshop_logistics` VALUES (402, '墨西哥（Correos de Mexico）', 'mexico', 100);
INSERT INTO `jshop_logistics` VALUES (403, '罗马尼亚（Posta Romanian）', 'romanian', 100);
INSERT INTO `jshop_logistics` VALUES (404, 'DPD Poland', 'dpdpoland', 100);
INSERT INTO `jshop_logistics` VALUES (405, '阿联酋(Emirates Post)', 'emirates', 100);
INSERT INTO `jshop_logistics` VALUES (406, '新顺丰（NSF）', 'nsf', 100);
INSERT INTO `jshop_logistics` VALUES (407, '巴基斯坦(Pakistan Post)', 'pakistan', 100);
INSERT INTO `jshop_logistics` VALUES (408, 'Asendia USA', 'asendiausa', 100);
INSERT INTO `jshop_logistics` VALUES (409, '法国大包、EMS-英文(Chronopost France)', 'chronopostfren', 100);
INSERT INTO `jshop_logistics` VALUES (410, '意大利(Poste Italiane)', 'italiane', 100);
INSERT INTO `jshop_logistics` VALUES (411, '世运快递', 'shiyunkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (412, '新干线快递', 'anlexpress', 100);
INSERT INTO `jshop_logistics` VALUES (413, '飞洋快递', 'shipgce', 100);
INSERT INTO `jshop_logistics` VALUES (414, '贝海国际速递', 'xlobo', 100);
INSERT INTO `jshop_logistics` VALUES (415, '黄马甲', 'huangmajia', 100);
INSERT INTO `jshop_logistics` VALUES (416, 'Toll', 'dpexen', 100);
INSERT INTO `jshop_logistics` VALUES (417, '如风达', 'rufengda', 100);
INSERT INTO `jshop_logistics` VALUES (418, 'EC-Firstclass', 'ecfirstclass', 100);
INSERT INTO `jshop_logistics` VALUES (419, 'DTDC India', 'dtdcindia', 100);
INSERT INTO `jshop_logistics` VALUES (420, 'Safexpress', 'safexpress', 100);
INSERT INTO `jshop_logistics` VALUES (421, '泰国（Thailand Thai Post）', 'thailand', 100);
INSERT INTO `jshop_logistics` VALUES (422, 'SkyNet Malaysia', 'skynetmalaysia', 100);
INSERT INTO `jshop_logistics` VALUES (423, 'TNT Australia', 'tntau', 100);
INSERT INTO `jshop_logistics` VALUES (424, '马来西亚小包（Malaysia Post(Registere', 'malaysiapost', 100);
INSERT INTO `jshop_logistics` VALUES (425, '马来西亚大包、EMS（Malaysia Post(parce', 'malaysiaems', 100);
INSERT INTO `jshop_logistics` VALUES (426, '沙特阿拉伯(Saudi Post)', 'saudipost', 100);
INSERT INTO `jshop_logistics` VALUES (427, '南非（South African Post Office）', 'southafrican', 100);
INSERT INTO `jshop_logistics` VALUES (428, 'Mexico Senda Express', 'mexicodenda', 100);
INSERT INTO `jshop_logistics` VALUES (429, 'MyHermes', 'myhermes', 100);
INSERT INTO `jshop_logistics` VALUES (430, 'DPD Germany', 'dpdgermany', 100);
INSERT INTO `jshop_logistics` VALUES (431, 'Nova Poshta', 'novaposhta', 100);
INSERT INTO `jshop_logistics` VALUES (432, 'Estes', 'estes', 100);
INSERT INTO `jshop_logistics` VALUES (433, 'TNT UK', 'tntuk', 100);
INSERT INTO `jshop_logistics` VALUES (434, 'Deltec Courier', 'deltec', 100);
INSERT INTO `jshop_logistics` VALUES (435, 'UPS Freight', 'upsfreight', 100);
INSERT INTO `jshop_logistics` VALUES (436, 'TNT Italy', 'tntitaly', 100);
INSERT INTO `jshop_logistics` VALUES (437, 'Mexico Multipack', 'multipack', 100);
INSERT INTO `jshop_logistics` VALUES (438, '葡萄牙（Portugal CTT）', 'portugalctt', 100);
INSERT INTO `jshop_logistics` VALUES (439, 'Interlink Express', 'interlink', 100);
INSERT INTO `jshop_logistics` VALUES (440, 'DPD UK', 'dpduk', 100);
INSERT INTO `jshop_logistics` VALUES (441, '乌克兰EMS-中文(EMS Ukraine)', 'emsukrainecn', 100);
INSERT INTO `jshop_logistics` VALUES (442, '乌克兰小包、大包(UkrPost)', 'ukrpost', 100);
INSERT INTO `jshop_logistics` VALUES (443, 'TCI XPS', 'tcixps', 100);
INSERT INTO `jshop_logistics` VALUES (444, '高铁速递', 'hre', 100);
INSERT INTO `jshop_logistics` VALUES (445, '新加坡EMS、大包(Singapore Speedpost)', 'speedpost', 100);
INSERT INTO `jshop_logistics` VALUES (446, 'LaserShip', 'lasership', 100);
INSERT INTO `jshop_logistics` VALUES (447, '英国邮政大包EMS', 'parcelforcecn', 100);
INSERT INTO `jshop_logistics` VALUES (448, '同舟行物流', 'chinatzx', 100);
INSERT INTO `jshop_logistics` VALUES (449, '秦邦快运', 'qbexpress', 100);
INSERT INTO `jshop_logistics` VALUES (450, 'skynet', 'skynet', 100);
INSERT INTO `jshop_logistics` VALUES (451, '忠信达', 'zhongxinda', 100);
INSERT INTO `jshop_logistics` VALUES (452, '门对门', 'menduimen', 100);
INSERT INTO `jshop_logistics` VALUES (453, '微特派', 'weitepai', 100);
INSERT INTO `jshop_logistics` VALUES (454, '海盟速递', 'haimengsudi', 100);
INSERT INTO `jshop_logistics` VALUES (455, '圣安物流', 'shenganwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (456, '联邦快递', 'lianbangkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (457, '飞快达', 'feikuaida', 100);
INSERT INTO `jshop_logistics` VALUES (458, 'EMS', 'ems', 100);
INSERT INTO `jshop_logistics` VALUES (459, '天地华宇', 'tiandihuayu', 100);
INSERT INTO `jshop_logistics` VALUES (460, '煜嘉物流', 'yujiawuliu', 100);
INSERT INTO `jshop_logistics` VALUES (461, '郑州建华', 'zhengzhoujianhua', 100);
INSERT INTO `jshop_logistics` VALUES (462, '大洋物流', 'dayangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (463, '递达速运', 'didasuyun', 100);
INSERT INTO `jshop_logistics` VALUES (464, '易通达', 'yitongda', 100);
INSERT INTO `jshop_logistics` VALUES (465, '邮必佳', 'youbijia', 100);
INSERT INTO `jshop_logistics` VALUES (466, 'EMS-英文', 'emsen', 100);
INSERT INTO `jshop_logistics` VALUES (467, '闽盛快递', 'minshengkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (468, '佳惠尔', 'syjiahuier', 100);
INSERT INTO `jshop_logistics` VALUES (469, 'KCS', 'kcs', 100);
INSERT INTO `jshop_logistics` VALUES (470, 'ADP国际快递', 'adp', 100);
INSERT INTO `jshop_logistics` VALUES (471, '颿达国际快递', 'fardarww', 100);
INSERT INTO `jshop_logistics` VALUES (472, '颿达国际快递-英文', 'fandaguoji', 100);
INSERT INTO `jshop_logistics` VALUES (473, '林道国际快递', 'shlindao', 100);
INSERT INTO `jshop_logistics` VALUES (474, '中外运速递-中文', 'sinoex', 100);
INSERT INTO `jshop_logistics` VALUES (475, '中外运速递', 'zhongwaiyun', 100);
INSERT INTO `jshop_logistics` VALUES (476, '深圳德创物流', 'dechuangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (477, '林道国际快递-英文', 'ldxpres', 100);
INSERT INTO `jshop_logistics` VALUES (478, '中国香港(HongKong Post)', 'hkpost', 100);
INSERT INTO `jshop_logistics` VALUES (479, '邦送物流', 'bangsongwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (480, '华赫物流', 'nmhuahe', 100);
INSERT INTO `jshop_logistics` VALUES (481, '顺捷丰达', 'shunjiefengda', 100);
INSERT INTO `jshop_logistics` VALUES (482, '天马迅达', 'tianma', 100);
INSERT INTO `jshop_logistics` VALUES (483, '恒宇运通', 'hyytes', 100);
INSERT INTO `jshop_logistics` VALUES (484, '考拉国际速递', 'kaolaexpress', 100);
INSERT INTO `jshop_logistics` VALUES (485, 'BlueDart', 'bluedart', 100);
INSERT INTO `jshop_logistics` VALUES (486, '日日顺快线', 'rrskx', 100);
INSERT INTO `jshop_logistics` VALUES (487, '运东西', 'yundx', 100);
INSERT INTO `jshop_logistics` VALUES (488, '黑狗物流', 'higo', 100);
INSERT INTO `jshop_logistics` VALUES (489, '鹏远国际速递', 'pengyuanexpress', 100);
INSERT INTO `jshop_logistics` VALUES (490, '安捷物流', 'anjie88', 100);
INSERT INTO `jshop_logistics` VALUES (491, '骏达快递', 'jdexpressusa', 100);
INSERT INTO `jshop_logistics` VALUES (492, 'C&C国际速递', 'cncexp', 100);
INSERT INTO `jshop_logistics` VALUES (493, '北京EMS', 'bjemstckj', 100);
INSERT INTO `jshop_logistics` VALUES (494, 'airpak expresss', 'airpak', 100);
INSERT INTO `jshop_logistics` VALUES (495, '荷兰邮政-中国件', 'postnlchina', 100);
INSERT INTO `jshop_logistics` VALUES (496, '大达物流', 'idada', 100);
INSERT INTO `jshop_logistics` VALUES (497, '益递物流', 'edlogistics', 100);
INSERT INTO `jshop_logistics` VALUES (498, '中外运', 'esinotrans', 100);
INSERT INTO `jshop_logistics` VALUES (499, '速派快递(FastGo)', 'fastgo', 100);
INSERT INTO `jshop_logistics` VALUES (500, '易客满', 'ecmscn', 100);
INSERT INTO `jshop_logistics` VALUES (501, '美国云达', 'yundaexus', 100);
INSERT INTO `jshop_logistics` VALUES (502, 'Toll', 'toll', 100);
INSERT INTO `jshop_logistics` VALUES (503, '深圳DPEX', 'szdpex', 100);
INSERT INTO `jshop_logistics` VALUES (504, '俄顺达', 'eshunda', 100);
INSERT INTO `jshop_logistics` VALUES (505, '广东速腾物流', 'suteng', 100);
INSERT INTO `jshop_logistics` VALUES (506, '新鹏快递', 'gdxp', 100);
INSERT INTO `jshop_logistics` VALUES (507, '平安达腾飞', 'pingandatengfei', 100);
INSERT INTO `jshop_logistics` VALUES (508, '穗佳物流', 'suijiawuliu', 100);
INSERT INTO `jshop_logistics` VALUES (509, '传喜物流', 'chuanxiwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (510, '捷特快递', 'jietekuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (511, '隆浪快递', 'longlangkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (512, '佳吉快递', 'jiajikuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (513, '快达物流', 'kuaidawuliu', 100);
INSERT INTO `jshop_logistics` VALUES (514, '飞狐快递', 'feihukuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (515, '潇湘晨报', 'xiaoxiangchenbao', 100);
INSERT INTO `jshop_logistics` VALUES (516, '巴伦支', 'balunzhi', 100);
INSERT INTO `jshop_logistics` VALUES (517, '安能物流', 'annengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (518, '申通快递', 'shentong', 100);
INSERT INTO `jshop_logistics` VALUES (519, '亿领速运', 'yilingsuyun', 100);
INSERT INTO `jshop_logistics` VALUES (520, '店通快递', 'diantongkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (521, 'OCA Argentina', 'ocaargen', 100);
INSERT INTO `jshop_logistics` VALUES (522, '尼日利亚(Nigerian Postal)', 'nigerianpost', 100);
INSERT INTO `jshop_logistics` VALUES (523, '智利(Correos Chile)', 'chile', 100);
INSERT INTO `jshop_logistics` VALUES (524, '以色列(Israel Post)', 'israelpost', 100);
INSERT INTO `jshop_logistics` VALUES (525, '京东物流', 'jd', 100);
INSERT INTO `jshop_logistics` VALUES (526, '奥地利(Austrian Post)', 'austria', 100);
INSERT INTO `jshop_logistics` VALUES (527, '乌克兰小包、大包(UkrPoshta)', 'ukraine', 100);
INSERT INTO `jshop_logistics` VALUES (528, '乌干达(Posta Uganda)', 'uganda', 100);
INSERT INTO `jshop_logistics` VALUES (529, '阿塞拜疆EMS(EMS AzerExpressPost)', 'azerbaijan', 100);
INSERT INTO `jshop_logistics` VALUES (530, '芬兰(Itella Posti Oy)', 'finland', 100);
INSERT INTO `jshop_logistics` VALUES (531, '斯洛伐克(Slovenská Posta)', 'slovak', 100);
INSERT INTO `jshop_logistics` VALUES (532, '阿鲁巴[荷兰]（Post Aruba）', 'aruba', 100);
INSERT INTO `jshop_logistics` VALUES (533, '爱尔兰(An Post)', 'ireland', 100);
INSERT INTO `jshop_logistics` VALUES (534, '印度尼西亚EMS(Pos Indonesia-EMS)', 'indonesia', 100);
INSERT INTO `jshop_logistics` VALUES (535, '易优包裹', 'eupackage', 100);
INSERT INTO `jshop_logistics` VALUES (536, '威时沛运货运', 'wtdchina', 100);
INSERT INTO `jshop_logistics` VALUES (537, '行必达', 'speeda', 100);
INSERT INTO `jshop_logistics` VALUES (538, '中通国际', 'zhongtongguoji', 100);
INSERT INTO `jshop_logistics` VALUES (539, '千顺快递', 'qskdyxgs', 100);
INSERT INTO `jshop_logistics` VALUES (540, '西邮寄', 'xipost', 100);
INSERT INTO `jshop_logistics` VALUES (541, '顺捷达', 'shunjieda', 100);
INSERT INTO `jshop_logistics` VALUES (542, 'CE易欧通国际速递', 'cloudexpress', 100);
INSERT INTO `jshop_logistics` VALUES (543, '和丰同城', 'hfwuxi', 100);
INSERT INTO `jshop_logistics` VALUES (544, '天联快运', 'tlky', 100);
INSERT INTO `jshop_logistics` VALUES (545, '优速物流', 'youshuwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (546, '埃塞俄比亚(Ethiopian postal)', 'ethiopia', 100);
INSERT INTO `jshop_logistics` VALUES (547, '卢森堡(Luxembourg Post)', 'luxembourg', 100);
INSERT INTO `jshop_logistics` VALUES (548, '毛里求斯(Mauritius Post)', 'mauritius', 100);
INSERT INTO `jshop_logistics` VALUES (549, '文莱(Brunei Postal)', 'brunei', 100);
INSERT INTO `jshop_logistics` VALUES (550, 'Quantium', 'quantium', 100);
INSERT INTO `jshop_logistics` VALUES (551, '中铁物流', 'zhongtiewuliu', 100);
INSERT INTO `jshop_logistics` VALUES (552, '宇鑫物流', 'yuxinwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (553, '巴林(Bahrain Post)', 'bahrain', 100);
INSERT INTO `jshop_logistics` VALUES (554, '纳米比亚(NamPost)', 'namibia', 100);
INSERT INTO `jshop_logistics` VALUES (555, '卢旺达(Rwanda i-posita)', 'rwanda', 100);
INSERT INTO `jshop_logistics` VALUES (556, '莱索托(Lesotho Post)', 'lesotho', 100);
INSERT INTO `jshop_logistics` VALUES (557, '肯尼亚(POSTA KENYA)', 'kenya', 100);
INSERT INTO `jshop_logistics` VALUES (558, '喀麦隆(CAMPOST)', 'cameroon', 100);
INSERT INTO `jshop_logistics` VALUES (559, '伯利兹(Belize Postal)', 'belize', 100);
INSERT INTO `jshop_logistics` VALUES (560, '巴拉圭(Correo Paraguayo)', 'paraguay', 100);
INSERT INTO `jshop_logistics` VALUES (561, '波黑(JP BH Posta)', 'bohei', 100);
INSERT INTO `jshop_logistics` VALUES (562, '玻利维亚', 'bolivia', 100);
INSERT INTO `jshop_logistics` VALUES (563, '柬埔寨(Cambodia Post)', 'cambodia', 100);
INSERT INTO `jshop_logistics` VALUES (564, '兰州伙伴物流', 'huoban', 100);
INSERT INTO `jshop_logistics` VALUES (565, '天纵物流', 'tianzong', 100);
INSERT INTO `jshop_logistics` VALUES (566, '坦桑尼亚(Tanzania Posts)', 'tanzania', 100);
INSERT INTO `jshop_logistics` VALUES (567, '阿曼(Oman Post)', 'oman', 100);
INSERT INTO `jshop_logistics` VALUES (568, '直布罗陀[英国]( Royal Gibraltar Post', 'gibraltar', 100);
INSERT INTO `jshop_logistics` VALUES (569, '展勤快递', 'byht', 100);
INSERT INTO `jshop_logistics` VALUES (570, '越南EMS(VNPost Express)', 'vnpost', 100);
INSERT INTO `jshop_logistics` VALUES (571, '安迅物流', 'anxl', 100);
INSERT INTO `jshop_logistics` VALUES (572, '达方物流', 'dfpost', 100);
INSERT INTO `jshop_logistics` VALUES (573, '十方通物流', 'sfift', 100);
INSERT INTO `jshop_logistics` VALUES (574, '飞鹰物流', 'hnfy', 100);
INSERT INTO `jshop_logistics` VALUES (575, 'UPS i-parcel', 'iparcel', 100);
INSERT INTO `jshop_logistics` VALUES (576, '鑫锐达', 'bjxsrd', 100);
INSERT INTO `jshop_logistics` VALUES (577, '孟加拉国(EMS)', 'bangladesh', 100);
INSERT INTO `jshop_logistics` VALUES (578, '快捷速递', 'kuaijiesudi', 100);
INSERT INTO `jshop_logistics` VALUES (579, '日本（Japan Post）', 'japanposten', 100);
INSERT INTO `jshop_logistics` VALUES (580, '众辉达物流', 'zhdwl', 100);
INSERT INTO `jshop_logistics` VALUES (581, '秦远物流', 'qinyuan', 100);
INSERT INTO `jshop_logistics` VALUES (582, '澳邮中国快运', 'auexpress', 100);
INSERT INTO `jshop_logistics` VALUES (583, '日益通速递', 'rytsd', 100);
INSERT INTO `jshop_logistics` VALUES (584, '航宇快递', 'hangyu', 100);
INSERT INTO `jshop_logistics` VALUES (585, '急顺通', 'pzhjst', 100);
INSERT INTO `jshop_logistics` VALUES (586, '优速通达', 'yousutongda', 100);
INSERT INTO `jshop_logistics` VALUES (587, '飞邦快递', 'fbkd', 100);
INSERT INTO `jshop_logistics` VALUES (588, '华达快运', 'huada', 100);
INSERT INTO `jshop_logistics` VALUES (589, 'FOX国际快递', 'fox', 100);
INSERT INTO `jshop_logistics` VALUES (590, '佳怡物流', 'jiayiwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (591, '鹏程快递', 'pengcheng', 100);
INSERT INTO `jshop_logistics` VALUES (592, '冠庭国际物流', 'guanting', 100);
INSERT INTO `jshop_logistics` VALUES (593, '美国快递', 'meiguokuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (594, '通和天下', 'tonghetianxia', 100);
INSERT INTO `jshop_logistics` VALUES (595, '音素快运', 'yinsu', 100);
INSERT INTO `jshop_logistics` VALUES (596, '创一快递', 'chuangyi', 100);
INSERT INTO `jshop_logistics` VALUES (597, '重庆星程快递', 'cqxingcheng', 100);
INSERT INTO `jshop_logistics` VALUES (598, '贵州星程快递', 'gzxingcheng', 100);
INSERT INTO `jshop_logistics` VALUES (599, '河南全速通', 'hnqst', 100);
INSERT INTO `jshop_logistics` VALUES (600, '快速递', 'ksudi', 100);
INSERT INTO `jshop_logistics` VALUES (601, '北极星快运', 'polarisexpress', 100);
INSERT INTO `jshop_logistics` VALUES (602, '6LS EXPRESS', 'lsexpress', 100);
INSERT INTO `jshop_logistics` VALUES (603, 'ANTS EXPRESS', 'qdants', 100);
INSERT INTO `jshop_logistics` VALUES (604, 'S2C', 's2c', 100);
INSERT INTO `jshop_logistics` VALUES (605, 'Hi淘易快递', 'hitaoe', 100);
INSERT INTO `jshop_logistics` VALUES (606, 'CNAIR', 'cnair', 100);
INSERT INTO `jshop_logistics` VALUES (607, '易欧洲国际物流', 'yiouzhou', 100);
INSERT INTO `jshop_logistics` VALUES (608, '阳光快递', 'shiningexpress', 100);
INSERT INTO `jshop_logistics` VALUES (609, '北京丰越供应链', 'beijingfengyue', 100);
INSERT INTO `jshop_logistics` VALUES (610, '华中快递', 'cpsair', 100);
INSERT INTO `jshop_logistics` VALUES (611, '青旅物流', 'zqlwl', 100);
INSERT INTO `jshop_logistics` VALUES (612, '易航物流', 'yihangmall', 100);
INSERT INTO `jshop_logistics` VALUES (613, '城铁速递', 'cex', 100);
INSERT INTO `jshop_logistics` VALUES (614, '千里速递', 'qianli', 100);
INSERT INTO `jshop_logistics` VALUES (615, '急递', 'jdpplus', 100);
INSERT INTO `jshop_logistics` VALUES (616, '佳捷翔物流', 'jjx888', 100);
INSERT INTO `jshop_logistics` VALUES (617, '洋口岸', 'ykouan', 100);
INSERT INTO `jshop_logistics` VALUES (618, '考拉速递', 'koalaexp', 100);
INSERT INTO `jshop_logistics` VALUES (619, '天越物流', 'surpassgo', 100);
INSERT INTO `jshop_logistics` VALUES (620, '邮政标准快递', 'youzhengbk', 100);
INSERT INTO `jshop_logistics` VALUES (621, '运通快运', 'ytky168', 100);
INSERT INTO `jshop_logistics` VALUES (622, '卢森堡航空', 'cargolux', 100);
INSERT INTO `jshop_logistics` VALUES (623, '优优速递', 'youyou', 100);
INSERT INTO `jshop_logistics` VALUES (624, '全川物流', 'quanchuan56', 100);
INSERT INTO `jshop_logistics` VALUES (625, 'SYNSHIP快递', 'synship', 100);
INSERT INTO `jshop_logistics` VALUES (626, '仓鼠快递', 'cangspeed', 100);
INSERT INTO `jshop_logistics` VALUES (627, '递五方云仓', 'di5pll', 100);
INSERT INTO `jshop_logistics` VALUES (628, '卓志速运', 'chinaicip', 100);
INSERT INTO `jshop_logistics` VALUES (629, '闪电兔', 'shandiantu', 100);
INSERT INTO `jshop_logistics` VALUES (630, '新宁物流', 'xinning', 100);
INSERT INTO `jshop_logistics` VALUES (631, '春风物流', 'spring56', 100);
INSERT INTO `jshop_logistics` VALUES (632, '首达速运', 'sdsy888', 100);
INSERT INTO `jshop_logistics` VALUES (633, '丽狮物流', 'lishi', 100);
INSERT INTO `jshop_logistics` VALUES (634, '雅澳物流', 'yourscm', 100);
INSERT INTO `jshop_logistics` VALUES (635, '直德邮', 'zdepost', 100);
INSERT INTO `jshop_logistics` VALUES (636, '日昱物流', 'riyuwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (637, 'Gati-中文', 'gaticn', 100);
INSERT INTO `jshop_logistics` VALUES (638, '派尔快递', 'peex', 100);
INSERT INTO `jshop_logistics` VALUES (639, '汇文', 'huiwen', 100);
INSERT INTO `jshop_logistics` VALUES (640, '东红物流', 'donghong', 100);
INSERT INTO `jshop_logistics` VALUES (641, '增益速递', 'zengyisudi', 100);
INSERT INTO `jshop_logistics` VALUES (642, '好运来', 'hlyex', 100);
INSERT INTO `jshop_logistics` VALUES (643, '顺丰速运', 'shunfeng', 100);
INSERT INTO `jshop_logistics` VALUES (644, '城际快递', 'chengji', 100);
INSERT INTO `jshop_logistics` VALUES (645, '程光快递', 'chengguangkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (646, '天翼快递', 'tykd', 100);
INSERT INTO `jshop_logistics` VALUES (647, '京东订单', 'jdorder', 100);
INSERT INTO `jshop_logistics` VALUES (648, '蓝天快递', 'lantiankuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (649, '永昌物流', 'yongchangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (650, '笨鸟海淘', 'birdex', 100);
INSERT INTO `jshop_logistics` VALUES (651, '一正达速运', 'yizhengdasuyun', 100);
INSERT INTO `jshop_logistics` VALUES (652, '德意思', 'dabei', 100);
INSERT INTO `jshop_logistics` VALUES (653, '佐川急便', 'sagawa', 100);
INSERT INTO `jshop_logistics` VALUES (654, '优配速运', 'sdyoupei', 100);
INSERT INTO `jshop_logistics` VALUES (655, '速必达', 'subida', 100);
INSERT INTO `jshop_logistics` VALUES (656, '景光物流', 'jgwl', 100);
INSERT INTO `jshop_logistics` VALUES (657, '御风速运', 'yufeng', 100);
INSERT INTO `jshop_logistics` VALUES (658, '至诚通达快递', 'zhichengtongda', 100);
INSERT INTO `jshop_logistics` VALUES (659, '特急便物流', 'sucmj', 100);
INSERT INTO `jshop_logistics` VALUES (660, '亚马逊中国', 'yamaxunwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (661, '货运皇', 'kingfreight', 100);
INSERT INTO `jshop_logistics` VALUES (662, '锦程物流', 'jinchengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (663, '澳货通', 'auex', 100);
INSERT INTO `jshop_logistics` VALUES (664, '澳速物流', 'aosu', 100);
INSERT INTO `jshop_logistics` VALUES (665, '澳世速递', 'aus', 100);
INSERT INTO `jshop_logistics` VALUES (666, '环球速运', 'huanqiu', 100);
INSERT INTO `jshop_logistics` VALUES (667, '麦力快递', 'mailikuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (668, '瑞丰速递', 'rfsd', 100);
INSERT INTO `jshop_logistics` VALUES (669, '美联快递', 'letseml', 100);
INSERT INTO `jshop_logistics` VALUES (670, 'CNPEX中邮快递', 'cnpex', 100);
INSERT INTO `jshop_logistics` VALUES (671, '鑫世锐达', 'xsrd', 100);
INSERT INTO `jshop_logistics` VALUES (672, '顺丰优选', 'sfbest', 100);
INSERT INTO `jshop_logistics` VALUES (673, '全峰快递', 'quanfengkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (674, '克罗地亚（Hrvatska Posta）', 'hrvatska', 100);
INSERT INTO `jshop_logistics` VALUES (675, '保加利亚（Bulgarian Posts）', 'bulgarian', 100);
INSERT INTO `jshop_logistics` VALUES (676, 'Portugal Seur', 'portugalseur', 100);
INSERT INTO `jshop_logistics` VALUES (677, 'International Seur', 'seur', 100);
INSERT INTO `jshop_logistics` VALUES (678, '久易快递', 'jiuyicn', 100);
INSERT INTO `jshop_logistics` VALUES (679, 'Direct Link', 'directlink', 100);
INSERT INTO `jshop_logistics` VALUES (680, '希腊EMS（ELTA Courier）', 'eltahell', 100);
INSERT INTO `jshop_logistics` VALUES (681, '捷克（?eská po?ta）', 'ceskaposta', 100);
INSERT INTO `jshop_logistics` VALUES (682, 'Siodemka', 'siodemka', 100);
INSERT INTO `jshop_logistics` VALUES (683, '爱尔兰(An Post)', 'anposten', 100);
INSERT INTO `jshop_logistics` VALUES (684, '渥途国际速运', 'wotu', 100);
INSERT INTO `jshop_logistics` VALUES (685, '一号线', 'lineone', 100);
INSERT INTO `jshop_logistics` VALUES (686, '四海快递', 'sihaiet', 100);
INSERT INTO `jshop_logistics` VALUES (687, '德坤物流', 'dekuncn', 100);
INSERT INTO `jshop_logistics` VALUES (688, '准实快运', 'zsky123', 100);
INSERT INTO `jshop_logistics` VALUES (689, '宏捷国际物流', 'hongjie', 100);
INSERT INTO `jshop_logistics` VALUES (690, '鸿讯物流', 'hongxun', 100);
INSERT INTO `jshop_logistics` VALUES (691, '卡邦配送', 'ahkbps', 100);
INSERT INTO `jshop_logistics` VALUES (692, '凡客配送（作废）', 'vancl', 100);
INSERT INTO `jshop_logistics` VALUES (693, '瑞士邮政', 'swisspostcn', 100);
INSERT INTO `jshop_logistics` VALUES (694, '辉联物流', 'huilian', 100);
INSERT INTO `jshop_logistics` VALUES (695, 'A2U速递', 'a2u', 100);
INSERT INTO `jshop_logistics` VALUES (696, 'UEQ快递', 'ueq', 100);
INSERT INTO `jshop_logistics` VALUES (697, '中加国际快递', 'scic', 100);
INSERT INTO `jshop_logistics` VALUES (698, '易达通', 'yidatong', 100);
INSERT INTO `jshop_logistics` VALUES (699, '宜送', 'yisong', 100);
INSERT INTO `jshop_logistics` VALUES (700, '全球快运', 'abcglobal', 100);
INSERT INTO `jshop_logistics` VALUES (701, '芒果速递', 'mangguo', 100);
INSERT INTO `jshop_logistics` VALUES (702, '金海淘', 'goldhaitao', 100);
INSERT INTO `jshop_logistics` VALUES (703, '极光转运', 'jiguang', 100);
INSERT INTO `jshop_logistics` VALUES (704, '富腾达国际货运', 'ftd', 100);
INSERT INTO `jshop_logistics` VALUES (705, 'DCS', 'dcs', 100);
INSERT INTO `jshop_logistics` VALUES (706, '捷网俄全通', 'ruexp', 100);
INSERT INTO `jshop_logistics` VALUES (707, '华通务达物流', 'htwd', 100);
INSERT INTO `jshop_logistics` VALUES (708, '申必达', 'speedoex', 100);
INSERT INTO `jshop_logistics` VALUES (709, '联运快递', 'lianyun', 100);
INSERT INTO `jshop_logistics` VALUES (710, '捷安达', 'jieanda', 100);
INSERT INTO `jshop_logistics` VALUES (711, 'SHL畅灵国际物流', 'shlexp', 100);
INSERT INTO `jshop_logistics` VALUES (712, 'EWE全球快递', 'ewe', 100);
INSERT INTO `jshop_logistics` VALUES (713, '顺邦国际物流', 'shunbang', 100);
INSERT INTO `jshop_logistics` VALUES (714, '成达国际速递', 'chengda', 100);
INSERT INTO `jshop_logistics` VALUES (715, '启辰国际速递', 'qichen', 100);
INSERT INTO `jshop_logistics` VALUES (716, '合众速递(UCS）', 'ucs', 100);
INSERT INTO `jshop_logistics` VALUES (717, '阿富汗(Afghan Post)', 'afghan', 100);
INSERT INTO `jshop_logistics` VALUES (718, '白俄罗斯(Belpochta)', 'belpost', 100);
INSERT INTO `jshop_logistics` VALUES (719, '冠捷物流', 'gjwl', 100);
INSERT INTO `jshop_logistics` VALUES (720, '钏博物流', 'cbo56', 100);
INSERT INTO `jshop_logistics` VALUES (721, '西翼物流', 'westwing', 100);
INSERT INTO `jshop_logistics` VALUES (722, '优邦速运', 'ubonex', 100);
INSERT INTO `jshop_logistics` VALUES (723, '首通快运', 'staky', 100);
INSERT INTO `jshop_logistics` VALUES (724, '马珂博逻', 'cnmcpl', 100);
INSERT INTO `jshop_logistics` VALUES (725, '小熊物流', 'littlebearbear', 100);
INSERT INTO `jshop_logistics` VALUES (726, '玥玛速运', 'yue777', 100);
INSERT INTO `jshop_logistics` VALUES (727, '上海航瑞货运', 'hangrui', 100);
INSERT INTO `jshop_logistics` VALUES (728, '星云速递', 'nebuex', 100);
INSERT INTO `jshop_logistics` VALUES (729, '环创物流', 'ghl', 100);
INSERT INTO `jshop_logistics` VALUES (730, '林安物流', 'lasy56', 100);
INSERT INTO `jshop_logistics` VALUES (731, '笨鸟国际', 'benniao', 100);
INSERT INTO `jshop_logistics` VALUES (732, '全速快递', 'fsexp', 100);
INSERT INTO `jshop_logistics` VALUES (733, '法翔速运', 'ftlexpress', 100);
INSERT INTO `jshop_logistics` VALUES (734, '易转运', 'ezhuanyuan', 100);
INSERT INTO `jshop_logistics` VALUES (735, 'Superb Grace', 'superb', 100);
INSERT INTO `jshop_logistics` VALUES (736, '蓝天国际快递', 'ltx', 100);
INSERT INTO `jshop_logistics` VALUES (737, '圣飞捷快递', 'sfjhd', 100);
INSERT INTO `jshop_logistics` VALUES (738, '淘韩国际快递', 'krtao', 100);
INSERT INTO `jshop_logistics` VALUES (739, '容智快运', 'gdrz58', 100);
INSERT INTO `jshop_logistics` VALUES (740, '锦程快递', 'hrex', 100);
INSERT INTO `jshop_logistics` VALUES (741, '顺时达物流', 'hnssd56', 100);
INSERT INTO `jshop_logistics` VALUES (742, '骏绅物流', 'jsexpress', 100);
INSERT INTO `jshop_logistics` VALUES (743, '德国雄鹰速递', 'adlerlogi', 100);
INSERT INTO `jshop_logistics` VALUES (744, '远为快递', 'ywexpress', 100);
INSERT INTO `jshop_logistics` VALUES (745, '嗖一下同城快递', 'sofast56', 100);
INSERT INTO `jshop_logistics` VALUES (746, '开心快递', 'happylink', 100);
INSERT INTO `jshop_logistics` VALUES (747, '五六快运', 'wuliuky', 100);
INSERT INTO `jshop_logistics` VALUES (748, '卓烨快递', 'hrbzykd', 100);
INSERT INTO `jshop_logistics` VALUES (749, 'ZTE中兴物流', 'zteexpress', 100);
INSERT INTO `jshop_logistics` VALUES (750, '尼尔快递', 'nell', 100);
INSERT INTO `jshop_logistics` VALUES (751, '高铁快运', 'gaotieex', 100);
INSERT INTO `jshop_logistics` VALUES (752, '万家康物流', 'wjkwl', 100);
INSERT INTO `jshop_logistics` VALUES (753, '国晶物流', 'xdshipping', 100);
INSERT INTO `jshop_logistics` VALUES (754, '德国云快递', 'yunexpress', 100);
INSERT INTO `jshop_logistics` VALUES (755, '宏递快运', 'hd', 100);
INSERT INTO `jshop_logistics` VALUES (756, '一起送', 'yiqisong', 100);
INSERT INTO `jshop_logistics` VALUES (757, '迈隆递运', 'mailongdy', 100);
INSERT INTO `jshop_logistics` VALUES (758, '新亚物流', 'nalexpress', 100);
INSERT INTO `jshop_logistics` VALUES (759, '艾瑞斯远', 'ariesfar', 100);
INSERT INTO `jshop_logistics` VALUES (760, '澳多多国际速递', 'adodoxm', 100);
INSERT INTO `jshop_logistics` VALUES (761, 'CNUP 中联邮', 'cnup', 100);
INSERT INTO `jshop_logistics` VALUES (762, 'UEX国际物流', 'uex', 100);
INSERT INTO `jshop_logistics` VALUES (763, 'Hermes', 'hermes', 100);
INSERT INTO `jshop_logistics` VALUES (764, 'PostElbe', 'postelbe', 100);
INSERT INTO `jshop_logistics` VALUES (765, '维普恩物流', 'vps', 100);
INSERT INTO `jshop_logistics` VALUES (766, '明辉物流', 'zsmhwl', 100);
INSERT INTO `jshop_logistics` VALUES (767, '联运通物流', 'szuem', 100);
INSERT INTO `jshop_logistics` VALUES (768, '龙象国际物流', 'edragon', 100);
INSERT INTO `jshop_logistics` VALUES (769, '永邦国际物流', 'yongbangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (770, '51跨境通', 'wykjt', 100);
INSERT INTO `jshop_logistics` VALUES (771, '速配欧翼', 'superoz', 100);
INSERT INTO `jshop_logistics` VALUES (772, '嘉里大荣物流', 'kerrytj', 100);
INSERT INTO `jshop_logistics` VALUES (773, '中国香港环球快运', 'huanqiuabc', 100);
INSERT INTO `jshop_logistics` VALUES (774, 'CL日中速运', 'clsp', 100);
INSERT INTO `jshop_logistics` VALUES (775, 'SQK国际速递', 'chinasqk', 100);
INSERT INTO `jshop_logistics` VALUES (776, '家家通快递', 'newsway', 100);
INSERT INTO `jshop_logistics` VALUES (777, '邮客全球速递', 'yyox', 100);
INSERT INTO `jshop_logistics` VALUES (778, '华瀚快递', 'hhair56', 100);
INSERT INTO `jshop_logistics` VALUES (779, '顺士达速运', 'shunshid', 100);
INSERT INTO `jshop_logistics` VALUES (780, '天翔东捷运', 'djy56', 100);
INSERT INTO `jshop_logistics` VALUES (781, '卓实快运', 'zhuoshikuaiyun', 100);
INSERT INTO `jshop_logistics` VALUES (782, '吉祥邮（澳洲）', 'jixiangyouau', 100);
INSERT INTO `jshop_logistics` VALUES (783, '蓝天快递', 'blueskyexpress', 100);
INSERT INTO `jshop_logistics` VALUES (784, '天天快物流', 'guoeryue', 100);
INSERT INTO `jshop_logistics` VALUES (785, '纵通速运', 'ynztsy', 100);
INSERT INTO `jshop_logistics` VALUES (786, '中通快运', 'zhongtongkuaiyun', 100);
INSERT INTO `jshop_logistics` VALUES (787, 'CNE', 'cnexps', 100);
INSERT INTO `jshop_logistics` VALUES (788, '希腊包裹（ELTA Hellenic Post）', 'elta', 100);
INSERT INTO `jshop_logistics` VALUES (789, '星速递', 'starex', 100);
INSERT INTO `jshop_logistics` VALUES (790, '土耳其', 'ptt', 100);
INSERT INTO `jshop_logistics` VALUES (791, '哥伦比亚(4-72 La Red Postal de Col', 'colombia', 100);
INSERT INTO `jshop_logistics` VALUES (792, '加州猫速递', 'jiazhoumao', 100);
INSERT INTO `jshop_logistics` VALUES (793, '捷邦物流', 'jieborne', 100);
INSERT INTO `jshop_logistics` VALUES (794, '邮政国内', 'yzguonei', 100);
INSERT INTO `jshop_logistics` VALUES (795, 'Canpar', 'canpar', 100);
INSERT INTO `jshop_logistics` VALUES (796, '海硕高铁速递', 'hsgtsd', 100);
INSERT INTO `jshop_logistics` VALUES (797, '日日通国际', 'rrthk', 100);
INSERT INTO `jshop_logistics` VALUES (798, '天翼物流', 'tywl99', 100);
INSERT INTO `jshop_logistics` VALUES (799, '啪啪供应链', 'papascm', 100);
INSERT INTO `jshop_logistics` VALUES (800, '万达美', 'wdm', 100);
INSERT INTO `jshop_logistics` VALUES (801, '安得物流', 'annto', 100);
INSERT INTO `jshop_logistics` VALUES (802, '广东诚通物流', 'gdct56', 100);
INSERT INTO `jshop_logistics` VALUES (803, '安达速递', 'adapost', 100);
INSERT INTO `jshop_logistics` VALUES (804, '易达国际速递', 'eta100', 100);
INSERT INTO `jshop_logistics` VALUES (805, '西游寄', 'xiyoug', 100);
INSERT INTO `jshop_logistics` VALUES (806, '光线速递', 'gxwl', 100);
INSERT INTO `jshop_logistics` VALUES (807, '易邮国际', 'euguoji', 100);
INSERT INTO `jshop_logistics` VALUES (808, '深圳邮政', 'szyouzheng', 100);
INSERT INTO `jshop_logistics` VALUES (809, '粤中国际货运代理（上海）有限公司', 'yuezhongsh', 100);
INSERT INTO `jshop_logistics` VALUES (810, '城通物流', 'chengtong', 100);
INSERT INTO `jshop_logistics` VALUES (811, 'GE2D跨境物流', 'ge2d', 100);
INSERT INTO `jshop_logistics` VALUES (812, '败欧洲', 'europe8', 100);
INSERT INTO `jshop_logistics` VALUES (813, '飛斯特', 'bester', 100);
INSERT INTO `jshop_logistics` VALUES (814, '蒙古国(Mongol Post)', 'mongolpost', 100);
INSERT INTO `jshop_logistics` VALUES (815, '乌拉圭（Correo Uruguayo）', 'correo', 100);
INSERT INTO `jshop_logistics` VALUES (816, '牙买加（Jamaica Post）', 'jamaicapost', 100);
INSERT INTO `jshop_logistics` VALUES (817, '格鲁吉亚(Georgian Pos）', 'georgianpost', 100);
INSERT INTO `jshop_logistics` VALUES (818, '美达快递', 'meidaexpress', 100);
INSERT INTO `jshop_logistics` VALUES (819, '驭丰速运', 'yfsuyun', 100);
INSERT INTO `jshop_logistics` VALUES (820, '无忧物流', 'aliexpress', 100);
INSERT INTO `jshop_logistics` VALUES (821, '邮鸽速运', 'ugoexpress', 100);
INSERT INTO `jshop_logistics` VALUES (822, '澳洲新干线快递', 'expressplus', 100);
INSERT INTO `jshop_logistics` VALUES (823, '标杆物流', 'bmlchina', 100);
INSERT INTO `jshop_logistics` VALUES (824, '长风物流', 'longvast', 100);
INSERT INTO `jshop_logistics` VALUES (825, '邮来速递', 'youlai', 100);
INSERT INTO `jshop_logistics` VALUES (826, '魔速达', 'mosuda', 100);
INSERT INTO `jshop_logistics` VALUES (827, '商桥物流', 'shangqiao56', 100);
INSERT INTO `jshop_logistics` VALUES (828, 'AUV国际快递', 'auvexpress', 100);
INSERT INTO `jshop_logistics` VALUES (829, 'Newgistics', 'newgistics', 100);
INSERT INTO `jshop_logistics` VALUES (830, 'FQ狂派速递', 'freakyquick', 100);
INSERT INTO `jshop_logistics` VALUES (831, '泽西岛', 'jerseypost', 100);
INSERT INTO `jshop_logistics` VALUES (832, '威盛快递', 'wherexpess', 100);
INSERT INTO `jshop_logistics` VALUES (833, '运通速运', 'yuntong', 100);
INSERT INTO `jshop_logistics` VALUES (834, '老挝(Lao Express)', 'lao', 100);
INSERT INTO `jshop_logistics` VALUES (835, '巴布亚新几内亚(PNG Post)', 'postpng', 100);
INSERT INTO `jshop_logistics` VALUES (836, 'EASY EXPRESS', 'easyexpress', 100);
INSERT INTO `jshop_logistics` VALUES (837, '壹米滴答', 'yimidida', 100);
INSERT INTO `jshop_logistics` VALUES (838, '飞云快递系统', 'fyex', 100);
INSERT INTO `jshop_logistics` VALUES (839, '跨跃国际', 'kyue', 100);
INSERT INTO `jshop_logistics` VALUES (840, 'EMS包裹', 'emsbg', 100);
INSERT INTO `jshop_logistics` VALUES (841, '珠峰速运', 'zf365', 100);
INSERT INTO `jshop_logistics` VALUES (842, '甘肃安的快递', 'gansuandi', 100);
INSERT INTO `jshop_logistics` VALUES (843, '一辉物流', 'yatfai', 100);
INSERT INTO `jshop_logistics` VALUES (844, 'e直运', 'edtexpress', 100);
INSERT INTO `jshop_logistics` VALUES (845, 'wish邮', 'shpostwish', 100);
INSERT INTO `jshop_logistics` VALUES (846, '顶世国际物流', 'topshey', 100);
INSERT INTO `jshop_logistics` VALUES (847, '龙枫国际快递', 'lfexpress', 100);
INSERT INTO `jshop_logistics` VALUES (848, '安能快递', 'ane66', 100);
INSERT INTO `jshop_logistics` VALUES (849, '圆通快运', 'yuantongkuaiyun', 100);
INSERT INTO `jshop_logistics` VALUES (850, '宝通快递', 'baotongkd', 100);
INSERT INTO `jshop_logistics` VALUES (851, '美国汉邦快递', 'aplus100', 100);
INSERT INTO `jshop_logistics` VALUES (852, '易普递', 'sixroad', 100);
INSERT INTO `jshop_logistics` VALUES (853, '速呈', 'sczpds', 100);
INSERT INTO `jshop_logistics` VALUES (854, '海淘物流', 'ht22', 100);
INSERT INTO `jshop_logistics` VALUES (855, '海米派物流', 'haimibuy', 100);
INSERT INTO `jshop_logistics` VALUES (856, '天翔快递', 'tianxiang', 100);
INSERT INTO `jshop_logistics` VALUES (857, '易境达国际物流', 'uscbexpress', 100);
INSERT INTO `jshop_logistics` VALUES (858, '大韩通运', 'cjkoreaexpress', 100);
INSERT INTO `jshop_logistics` VALUES (859, '澳世速递', 'ausexpress', 100);
INSERT INTO `jshop_logistics` VALUES (860, '未来明天快递', 'weilaimingtian', 100);
INSERT INTO `jshop_logistics` VALUES (861, '科捷物流', 'kejie', 100);
INSERT INTO `jshop_logistics` VALUES (862, '大道物流', 'dadaoex', 100);
INSERT INTO `jshop_logistics` VALUES (863, '全联速运', 'guexp', 100);
INSERT INTO `jshop_logistics` VALUES (864, '可可树美中速运', 'excocotree', 100);
INSERT INTO `jshop_logistics` VALUES (865, '邮邦国际', 'youban', 100);
INSERT INTO `jshop_logistics` VALUES (866, '西安运逸快递', 'yyexp', 100);
INSERT INTO `jshop_logistics` VALUES (867, 'Aplus物流', 'aplusex', 100);
INSERT INTO `jshop_logistics` VALUES (868, '锋鸟物流', 'beebird', 100);
INSERT INTO `jshop_logistics` VALUES (869, '青云物流', 'bjqywl', 100);
INSERT INTO `jshop_logistics` VALUES (870, '万邑通', 'winit', 100);
INSERT INTO `jshop_logistics` VALUES (871, '中翼国际物流', 'chnexp', 100);
INSERT INTO `jshop_logistics` VALUES (872, '亚洲顺物流', 'yzswuliu', 100);
INSERT INTO `jshop_logistics` VALUES (873, 'E跨通', 'ecallturn', 100);
INSERT INTO `jshop_logistics` VALUES (874, '递四方美国', 'disifangus', 100);
INSERT INTO `jshop_logistics` VALUES (875, '星空国际', 'wlwex', 100);
INSERT INTO `jshop_logistics` VALUES (876, '极地快递', 'polarexpress', 100);
INSERT INTO `jshop_logistics` VALUES (877, '到了港', 'camekong', 100);
INSERT INTO `jshop_logistics` VALUES (878, '斯里兰卡(Sri Lanka Post)', 'slpost', 100);
INSERT INTO `jshop_logistics` VALUES (879, '斯洛文尼亚(Slovenia Post)', 'slovenia', 100);
INSERT INTO `jshop_logistics` VALUES (880, '多米尼加（INPOSDOM – Instituto Post', 'inposdom', 100);
INSERT INTO `jshop_logistics` VALUES (881, '星运快递', 'staryvr', 100);
INSERT INTO `jshop_logistics` VALUES (882, '狮爱高铁物流', 'sycawl', 100);
INSERT INTO `jshop_logistics` VALUES (883, '爱拜物流', 'ibuy8', 100);
INSERT INTO `jshop_logistics` VALUES (884, '商海德物流', 'shd56', 100);
INSERT INTO `jshop_logistics` VALUES (885, '九宫物流', 'jiugong', 100);
INSERT INTO `jshop_logistics` VALUES (886, '缔惠盛合', 'twkd56', 100);
INSERT INTO `jshop_logistics` VALUES (887, '快服务', 'kfwnet', 100);
INSERT INTO `jshop_logistics` VALUES (888, 'dhl小包', 'dhlecommerce', 100);
INSERT INTO `jshop_logistics` VALUES (889, '宇佳物流', 'yujiawl', 100);
INSERT INTO `jshop_logistics` VALUES (890, '湘达物流', 'xiangdawuliu', 100);
INSERT INTO `jshop_logistics` VALUES (891, '远盾物流', 'yuandun', 100);
INSERT INTO `jshop_logistics` VALUES (892, '黑猫宅急便', 'tcat', 100);
INSERT INTO `jshop_logistics` VALUES (893, '韵达快运', 'yundakuaiyun', 100);
INSERT INTO `jshop_logistics` VALUES (894, '速派快递', 'fastgoexpress', 100);
INSERT INTO `jshop_logistics` VALUES (895, '中集冷云', 'cccc58', 100);
INSERT INTO `jshop_logistics` VALUES (896, '久久物流', 'jiujiuwl', 100);
INSERT INTO `jshop_logistics` VALUES (897, '德国八易转运', 'deguo8elog', 100);
INSERT INTO `jshop_logistics` VALUES (898, 'UTAO优到', 'utaoscm', 100);
INSERT INTO `jshop_logistics` VALUES (899, '乾坤物流', 'yatexpress', 100);
INSERT INTO `jshop_logistics` VALUES (900, '摩洛哥 ( Morocco Post )', 'morocco', 100);
INSERT INTO `jshop_logistics` VALUES (901, '尼泊尔（Nepal Postal Services）', 'nepalpost', 100);
INSERT INTO `jshop_logistics` VALUES (902, '伊朗（Iran Post）', 'iran', 100);
INSERT INTO `jshop_logistics` VALUES (903, '坦桑尼亚（Tanzania Posts Corporatio', 'posta', 100);
INSERT INTO `jshop_logistics` VALUES (904, '莫桑比克（Correios de Mo?ambique）', 'correios', 100);
INSERT INTO `jshop_logistics` VALUES (905, '聚中大', 'juzhongda', 100);
INSERT INTO `jshop_logistics` VALUES (906, '中邮电商', 'chinapostcb', 100);
INSERT INTO `jshop_logistics` VALUES (907, '鸿泰物流', 'hnht56', 100);
INSERT INTO `jshop_logistics` VALUES (908, '南非EMS', 'emssouthafrica', 100);
INSERT INTO `jshop_logistics` VALUES (909, '申通国际', 'stosolution', 100);
INSERT INTO `jshop_logistics` VALUES (910, '皮牙子快递', 'bazirim', 100);
INSERT INTO `jshop_logistics` VALUES (911, '联众国际', 'epspost', 100);
INSERT INTO `jshop_logistics` VALUES (912, '丰通快运', 'ftky365', 100);
INSERT INTO `jshop_logistics` VALUES (913, 'BorderGuru', 'borderguru', 100);
INSERT INTO `jshop_logistics` VALUES (914, '艾姆勒', 'imlb2c', 100);
INSERT INTO `jshop_logistics` VALUES (915, '中欧国际物流', 'eucnrail', 100);
INSERT INTO `jshop_logistics` VALUES (916, '递四方澳洲', 'disifangau', 100);
INSERT INTO `jshop_logistics` VALUES (917, '艺凡快递', 'yifankd', 100);
INSERT INTO `jshop_logistics` VALUES (918, '宏观国际快递', 'gvpexpress', 100);
INSERT INTO `jshop_logistics` VALUES (919, '博茨瓦纳', 'botspost', 100);
INSERT INTO `jshop_logistics` VALUES (920, '塞内加尔', 'laposte', 100);
INSERT INTO `jshop_logistics` VALUES (921, '卡塔尔（Qatar Post）', 'qpost', 100);
INSERT INTO `jshop_logistics` VALUES (922, '苏丹（Sudapost）', 'sudapost', 100);
INSERT INTO `jshop_logistics` VALUES (923, 'Sureline冠泰', 'sureline', 100);
INSERT INTO `jshop_logistics` VALUES (924, '海沧无忧', 'hivewms', 100);
INSERT INTO `jshop_logistics` VALUES (925, '安世通快递', 'astexpress', 100);
INSERT INTO `jshop_logistics` VALUES (926, '集先锋快递', 'jxfex', 100);
INSERT INTO `jshop_logistics` VALUES (927, '丰客物流', 'fecobv', 100);
INSERT INTO `jshop_logistics` VALUES (928, '同城快寄', 'shpost', 100);
INSERT INTO `jshop_logistics` VALUES (929, '海联快递', 'hltop', 100);
INSERT INTO `jshop_logistics` VALUES (930, '中联速递', 'auvanda', 100);
INSERT INTO `jshop_logistics` VALUES (931, '三象速递', 'sxexpress', 100);
INSERT INTO `jshop_logistics` VALUES (932, '神马快递', 'shenma', 100);
INSERT INTO `jshop_logistics` VALUES (933, '互联快运', 'hlkytj', 100);
INSERT INTO `jshop_logistics` VALUES (934, '温通物流', 'wto56kj', 100);
INSERT INTO `jshop_logistics` VALUES (935, '四海捷运', 'sihiexpress', 100);
INSERT INTO `jshop_logistics` VALUES (936, '苏通快运', 'zjstky', 100);
INSERT INTO `jshop_logistics` VALUES (937, '邦通国际', 'comexpress', 100);
INSERT INTO `jshop_logistics` VALUES (938, '劲通快递', 'jintongkd', 100);
INSERT INTO `jshop_logistics` VALUES (939, '凡仕特物流', 'wlfast', 100);
INSERT INTO `jshop_logistics` VALUES (940, '红背心', 'hongbeixin', 100);
INSERT INTO `jshop_logistics` VALUES (941, '居家通', 'homexpress', 100);
INSERT INTO `jshop_logistics` VALUES (942, '上大物流', 'shangda', 100);
INSERT INTO `jshop_logistics` VALUES (943, '中邮物流', 'zhongyouwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (944, 'Fedex-国际件-中文', 'fedexcn', 100);
INSERT INTO `jshop_logistics` VALUES (945, '韩国（Korea Post）', 'koreapost', 100);
INSERT INTO `jshop_logistics` VALUES (946, '中通快递', 'zhongtong', 100);
INSERT INTO `jshop_logistics` VALUES (947, '京广速递', 'jinguangsudikuaijian', 100);
INSERT INTO `jshop_logistics` VALUES (948, 'FedEx-国际件', 'fedex', 100);
INSERT INTO `jshop_logistics` VALUES (949, '日日顺物流', 'rrs', 100);
INSERT INTO `jshop_logistics` VALUES (950, '微店', 'weidianorder', 100);
INSERT INTO `jshop_logistics` VALUES (951, '当当', 'dangdangorder', 100);
INSERT INTO `jshop_logistics` VALUES (952, '国送快运', 'guosong', 100);
INSERT INTO `jshop_logistics` VALUES (953, '考拉订单', 'kaolaorder', 100);
INSERT INTO `jshop_logistics` VALUES (954, 'AAE-中国件', 'aae', 100);
INSERT INTO `jshop_logistics` VALUES (955, '四川快优达速递', 'kuaiyouda', 100);
INSERT INTO `jshop_logistics` VALUES (956, '百福东方', 'baifudongfang', 100);
INSERT INTO `jshop_logistics` VALUES (957, 'TST速运通', 'tstexp', 100);
INSERT INTO `jshop_logistics` VALUES (958, 'YUN TRACK', 'yuntrack', 100);
INSERT INTO `jshop_logistics` VALUES (959, '招金精炼', 'zhaojin', 100);
INSERT INTO `jshop_logistics` VALUES (960, '全程快递', 'agopost', 100);
INSERT INTO `jshop_logistics` VALUES (961, 'CDEK', 'cdek', 100);
INSERT INTO `jshop_logistics` VALUES (962, '签收快递', 'signedexpress', 100);
INSERT INTO `jshop_logistics` VALUES (963, '佰麒快递', 'beckygo', 100);
INSERT INTO `jshop_logistics` VALUES (964, '增速跨境', 'zyzoom', 100);
INSERT INTO `jshop_logistics` VALUES (965, 'Aramex', 'aramex', 100);
INSERT INTO `jshop_logistics` VALUES (966, '越中国际物流', 'vctrans', 100);
INSERT INTO `jshop_logistics` VALUES (967, '德国优拜物流', 'ubuy', 100);
INSERT INTO `jshop_logistics` VALUES (968, '德尚国际速递', 'gslexpress', 100);
INSERT INTO `jshop_logistics` VALUES (969, '德国 EUC POST', 'eucpost', 100);
INSERT INTO `jshop_logistics` VALUES (970, '泰国中通ZTO', 'thaizto', 100);
INSERT INTO `jshop_logistics` VALUES (971, '泰国中通CTO', 'ctoexp', 100);
INSERT INTO `jshop_logistics` VALUES (972, '顺丰-繁体', 'shunfenghk', 100);
INSERT INTO `jshop_logistics` VALUES (973, '嘉诚速达', 'jcsuda', 100);
INSERT INTO `jshop_logistics` VALUES (974, 'AFL', 'afl', 100);
INSERT INTO `jshop_logistics` VALUES (975, '众派速递', 'zhpex', 100);
INSERT INTO `jshop_logistics` VALUES (976, '海星桥快递', 'haixingqiao', 100);
INSERT INTO `jshop_logistics` VALUES (977, '蘑菇街', 'mogujieorder', 100);
INSERT INTO `jshop_logistics` VALUES (978, '嘉里大通', 'jialidatong', 100);
INSERT INTO `jshop_logistics` VALUES (979, '万象物流', 'wanxiangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (980, '澳大利亚(Australia Post)', 'auspost', 100);
INSERT INTO `jshop_logistics` VALUES (981, '国通快递', 'guotongkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (982, '全晨快递', 'quanchenkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (983, '飞豹快递', 'feibaokuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (984, '中速快递', 'zhongsukuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (985, '优能物流', 'mantoo', 100);
INSERT INTO `jshop_logistics` VALUES (986, '国美', 'gomeorder', 100);
INSERT INTO `jshop_logistics` VALUES (987, '亚马逊中国订单', 'amazoncnorder', 100);
INSERT INTO `jshop_logistics` VALUES (988, '蜜芽订单', 'miaorder', 100);
INSERT INTO `jshop_logistics` VALUES (989, '顺丰订单', 'sfexpressorder', 100);
INSERT INTO `jshop_logistics` VALUES (990, '申通快运', 'stoe56', 100);
INSERT INTO `jshop_logistics` VALUES (991, 'City-Link', 'citylink', 100);
INSERT INTO `jshop_logistics` VALUES (992, '德邦快递', 'debangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (993, '银捷速递', 'yinjiesudi', 100);
INSERT INTO `jshop_logistics` VALUES (994, 'D速快递', 'dsukuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (995, '民邦速递', 'minbangsudi', 100);
INSERT INTO `jshop_logistics` VALUES (996, '百世快运', 'baishiwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (997, 'DHL-德国件（DHL Deutschland）', 'dhlde', 100);
INSERT INTO `jshop_logistics` VALUES (998, '能装能送', 'canhold', 100);
INSERT INTO `jshop_logistics` VALUES (999, '聚美优品', 'jumeiyoupinorder', 100);
INSERT INTO `jshop_logistics` VALUES (1000, '诚一物流', 'parcelchina', 100);
INSERT INTO `jshop_logistics` VALUES (1001, '网易严选', 'wangyiyxorder', 100);
INSERT INTO `jshop_logistics` VALUES (1002, '龙邦速递', 'longbanwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1003, '明亮物流', 'mingliangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1004, '速尔快递', 'suer', 100);
INSERT INTO `jshop_logistics` VALUES (1005, '盛辉物流', 'shenghuiwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1006, '越丰物流', 'yuefengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1007, '比利时（Bpost）', 'bpost', 100);
INSERT INTO `jshop_logistics` VALUES (1008, '韵达快递', 'yunda', 100);
INSERT INTO `jshop_logistics` VALUES (1009, '唯品会', 'vipshoporder', 100);
INSERT INTO `jshop_logistics` VALUES (1010, '美丽说', 'meilishuoorder', 100);
INSERT INTO `jshop_logistics` VALUES (1011, '顺丰优选', 'sfbestorder', 100);
INSERT INTO `jshop_logistics` VALUES (1012, '驼峰国际', 'humpline', 100);
INSERT INTO `jshop_logistics` VALUES (1013, '小米订单', 'xiaomiorder', 100);
INSERT INTO `jshop_logistics` VALUES (1014, '一智通', '1ziton', 100);
INSERT INTO `jshop_logistics` VALUES (1015, 'TransRush', 'transrush', 100);
INSERT INTO `jshop_logistics` VALUES (1016, '百世快递', 'huitongkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (1017, '联昊通', 'lianhaowuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1018, '远成物流', 'yuanchengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1019, 'FedEx-美国件', 'fedexus', 100);
INSERT INTO `jshop_logistics` VALUES (1020, 'OCS', 'ocs', 100);
INSERT INTO `jshop_logistics` VALUES (1021, '巴西(Brazil Post/Correios)', 'brazilposten', 100);
INSERT INTO `jshop_logistics` VALUES (1022, '孔夫子', 'kongfzorder', 100);
INSERT INTO `jshop_logistics` VALUES (1023, '一号店', 'yhdshoporder', 100);
INSERT INTO `jshop_logistics` VALUES (1024, '卷皮', 'juanpiorder', 100);
INSERT INTO `jshop_logistics` VALUES (1025, '淘宝订单', 'taobaoorder', 100);
INSERT INTO `jshop_logistics` VALUES (1026, '盛丰物流', 'shengfengwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1027, '瑞典（Sweden Post）', 'ruidianyouzheng', 100);
INSERT INTO `jshop_logistics` VALUES (1028, '圆通速递', 'yuantong', 100);
INSERT INTO `jshop_logistics` VALUES (1029, '宅急送', 'zhaijisong', 100);
INSERT INTO `jshop_logistics` VALUES (1030, '新邦物流', 'xinbangwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1031, '恒路物流', 'hengluwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1032, '华夏龙', 'huaxialongwuliu', 100);
INSERT INTO `jshop_logistics` VALUES (1033, '龙飞祥快递', 'longfx', 100);
INSERT INTO `jshop_logistics` VALUES (1034, '城市映急', 'city56', 100);
INSERT INTO `jshop_logistics` VALUES (1035, '顺心捷达', 'sxjdfreight', 100);
INSERT INTO `jshop_logistics` VALUES (1036, '圆通承诺达', 'ytchengnuoda', 100);
INSERT INTO `jshop_logistics` VALUES (1037, '宁夏万家通', 'wanjiatong', 100);
INSERT INTO `jshop_logistics` VALUES (1038, '通道', 'intercomv2', 100);
INSERT INTO `jshop_logistics` VALUES (1039, '曹操到', 'sfpost', 100);
INSERT INTO `jshop_logistics` VALUES (1040, 'USPSCN', 'uspscn', 100);
INSERT INTO `jshop_logistics` VALUES (1041, '五六快运', '56kuaiyun', 100);
INSERT INTO `jshop_logistics` VALUES (1042, '速通物流', 'sut56', 100);
INSERT INTO `jshop_logistics` VALUES (1043, '迅达速递', 'xdexpress', 100);
INSERT INTO `jshop_logistics` VALUES (1044, '澳行快递', 'desworks', 100);
INSERT INTO `jshop_logistics` VALUES (1045, '易邮速运', 'yiyou', 100);
INSERT INTO `jshop_logistics` VALUES (1046, 'JDIEX', 'jdiex', 100);
INSERT INTO `jshop_logistics` VALUES (1047, '泰捷达国际物流', 'ztjieda', 100);
INSERT INTO `jshop_logistics` VALUES (1048, '捎客物流', 'shaoke', 100);
INSERT INTO `jshop_logistics` VALUES (1049, '全球速递', 'pdstow', 100);
INSERT INTO `jshop_logistics` VALUES (1050, '安达易国际速递', 'adiexpress', 100);
INSERT INTO `jshop_logistics` VALUES (1051, '番薯国际货运', 'koali', 100);
INSERT INTO `jshop_logistics` VALUES (1052, '贝贝', 'beibeiorder', 100);
INSERT INTO `jshop_logistics` VALUES (1053, '德邦快递', 'debangkuaidi', 100);
INSERT INTO `jshop_logistics` VALUES (1054, '联合速运', 'unitedex', 100);
INSERT INTO `jshop_logistics` VALUES (1055, '龙邦物流', 'lbex', 100);
INSERT INTO `jshop_logistics` VALUES (1056, '东方航空物流', 'ealceair', 100);
INSERT INTO `jshop_logistics` VALUES (1057, 'GHT物流', 'ghtexpress', 100);
INSERT INTO `jshop_logistics` VALUES (1058, '香港伟豪国际物流', 'whgjkd', 100);
INSERT INTO `jshop_logistics` VALUES (1059, '澳洲迈速快递', 'maxeedexpress', 100);
INSERT INTO `jshop_logistics` VALUES (1060, 'TCXB国际物流', 'tcxbthai', 100);
INSERT INTO `jshop_logistics` VALUES (1061, '贝业物流', 'boyol', 100);
INSERT INTO `jshop_logistics` VALUES (1062, '如家国际快递', 'homecourier', 100);
INSERT INTO `jshop_logistics` VALUES (1063, '东方汇', 'est365', 100);
INSERT INTO `jshop_logistics` VALUES (1064, '八达通', 'bdatong', 100);
INSERT INTO `jshop_logistics` VALUES (1065, '波音速递', 'overseaex', 100);
INSERT INTO `jshop_logistics` VALUES (1066, '鑫远东速运', 'xyd666', 100);
INSERT INTO `jshop_logistics` VALUES (1067, '中环快递', 'zhonghuan', 100);
INSERT INTO `jshop_logistics` VALUES (1068, '沃埃家', 'wowvip', 100);
INSERT INTO `jshop_logistics` VALUES (1069, 'OBOR Express', 'oborexpress', 100);
INSERT INTO `jshop_logistics` VALUES (1070, '盛丰物流', 'sfwl', 100);
INSERT INTO `jshop_logistics` VALUES (1071, '转运中国', 'uszcn', 100);
INSERT INTO `jshop_logistics` VALUES (1072, '远航国际快运', 'yuanhhk', 100);
INSERT INTO `jshop_logistics` VALUES (1073, '鼎润物流', 'la911', 100);
INSERT INTO `jshop_logistics` VALUES (1074, '速达通', 'sdto', 100);
INSERT INTO `jshop_logistics` VALUES (1075, '苏豪快递', 'shipsoho', 100);
INSERT INTO `jshop_logistics` VALUES (1076, '三盛快递', 'sanshengco', 100);
INSERT INTO `jshop_logistics` VALUES (1077, '迅速快递', 'xunsuexpress', 100);
INSERT INTO `jshop_logistics` VALUES (1078, '众川国际', 'zhongchuan', 100);
INSERT INTO `jshop_logistics` VALUES (1079, '陆本速递 LUBEN EXPRESS', 'luben', 100);
INSERT INTO `jshop_logistics` VALUES (1080, '西濃運輸', 'seino', 100);
INSERT INTO `jshop_logistics` VALUES (1081, '加拿大联通快运', 'fastontime', 100);
INSERT INTO `jshop_logistics` VALUES (1082, '花瓣转运', 'flowerkd', 100);
INSERT INTO `jshop_logistics` VALUES (1083, '联通快递', 'ltparcel', 100);
INSERT INTO `jshop_logistics` VALUES (1084, '鸿远物流', 'hyeship', 100);
INSERT INTO `jshop_logistics` VALUES (1085, '合心速递', 'hexinexpress', 100);
INSERT INTO `jshop_logistics` VALUES (1086, 'Highsince', 'highsince', 100);
INSERT INTO `jshop_logistics` VALUES (1087, '蜜蜂速递', 'bee001', 100);
INSERT INTO `jshop_logistics` VALUES (1088, '天使物流云', 'tswlcloud', 100);
INSERT INTO `jshop_logistics` VALUES (1089, '王牌快递', 'shipbyace', 100);
INSERT INTO `jshop_logistics` VALUES (1090, '华美快递', 'hmus', 100);
INSERT INTO `jshop_logistics` VALUES (1091, '折800', 'zhe800order', 100);
INSERT INTO `jshop_logistics` VALUES (1092, '小红书', 'xiaohongshuorder', 100);
INSERT INTO `jshop_logistics` VALUES (1093, '铁中快运', 'tzky', 100);
INSERT INTO `jshop_logistics` VALUES (1094, '景顺物流', 'jingshun', 100);
INSERT INTO `jshop_logistics` VALUES (1095, '中环转运', 'zhonghuanus', 100);
INSERT INTO `jshop_logistics` VALUES (1096, 'YCG物流', 'ycgglobal', 100);
INSERT INTO `jshop_logistics` VALUES (1097, '驿递汇速递', 'yidihui', 100);
INSERT INTO `jshop_logistics` VALUES (1098, 'track-parcel', 'trackparcel', 100);
INSERT INTO `jshop_logistics` VALUES (1099, '金岸物流', 'jinan', 100);

-- ----------------------------
-- Table structure for jshop_manage
-- ----------------------------
DROP TABLE IF EXISTS `jshop_manage`;
CREATE TABLE `jshop_manage`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '密码 md5(md5()+创建时间)',
  `mobile` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '头像',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '1 = 正常 2 = 停用',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_manage_role
-- ----------------------------
DROP TABLE IF EXISTS `jshop_manage_role`;
CREATE TABLE `jshop_manage_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '角色名称',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '总后台角色表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_manage_role_operation_rel
-- ----------------------------
DROP TABLE IF EXISTS `jshop_manage_role_operation_rel`;
CREATE TABLE `jshop_manage_role_operation_rel`  (
  `manage_role_id` int(10) NOT NULL,
  `operation_id` int(10) NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '店铺角色操作权限关联表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_manage_role_rel
-- ----------------------------
DROP TABLE IF EXISTS `jshop_manage_role_rel`;
CREATE TABLE `jshop_manage_role_rel`  (
  `manage_id` int(10) UNSIGNED NOT NULL COMMENT '管理员ID 关联manage.id',
  `role_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '角色ID 关联role.id'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员和角色关系表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_message
-- ----------------------------
DROP TABLE IF EXISTS `jshop_message`;
CREATE TABLE `jshop_message`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `code` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '消息编码',
  `params` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '参数',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `ctime` bigint(12) UNSIGNED NOT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NOT NULL COMMENT '查看时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1未查看，2已查看',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '站内信' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_message_center
-- ----------------------------
DROP TABLE IF EXISTS `jshop_message_center`;
CREATE TABLE `jshop_message_center`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '编码',
  `sms` tinyint(1) UNSIGNED NOT NULL COMMENT '1 启用  2禁用',
  `message` tinyint(1) UNSIGNED NOT NULL COMMENT '站内消息',
  `wx_tpl_message` tinyint(1) UNSIGNED NOT NULL COMMENT '微信模板消息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '消息中心表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_notice
-- ----------------------------
DROP TABLE IF EXISTS `jshop_notice`;
CREATE TABLE `jshop_notice`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '公告标题',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '公告内容',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '公告类型',
  `ctime` bigint(12) NULL DEFAULT NULL COMMENT '创建时间 毫秒',
  `sort` tinyint(3) UNSIGNED NULL DEFAULT 100 COMMENT '排序 从小到大',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '软删除位  有时间代表已删除',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '公告表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_operation
-- ----------------------------
DROP TABLE IF EXISTS `jshop_operation`;
CREATE TABLE `jshop_operation`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) UNSIGNED NOT NULL COMMENT '父ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作名称',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作编码',
  `type` enum('m','c','a') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'a' COMMENT '类型',
  `parent_menu_id` int(10) UNSIGNED NOT NULL COMMENT '菜单id',
  `perm_type` int(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '权限许可类型，如果为1就是主体权限，， 如果为2就是半主体权限，在左侧菜单不显示，但是在权限菜单上有体现，如果为3就是关联权限',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '操作排序 越小越靠前',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `parent_menu_id`(`parent_menu_id`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 559 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_operation
-- ----------------------------
INSERT INTO `jshop_operation` VALUES (2, 1, '管理后台', 'manage', 'm', 1, 1, 200);
INSERT INTO `jshop_operation` VALUES (238, 2, '会员管理', 'User', 'c', 2, 1, 100);
INSERT INTO `jshop_operation` VALUES (239, 238, '用户列表', 'index', 'a', 238, 1, 100);
INSERT INTO `jshop_operation` VALUES (240, 238, '商品评价', 'comment', 'a', 305, 1, 101);
INSERT INTO `jshop_operation` VALUES (242, 238, '积分记录查询', 'pointlog', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (243, 238, '积分编辑', 'editpoint', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (244, 2, '控制面板', 'Setting', 'c', 2, 1, 200);
INSERT INTO `jshop_operation` VALUES (245, 244, '平台设置', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (246, 2, '支付方式', 'Payments', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (247, 246, '支付方式列表', 'index', 'a', 399, 1, 100);
INSERT INTO `jshop_operation` VALUES (249, 246, '支付方式修改', 'edit', 'a', 247, 2, 100);
INSERT INTO `jshop_operation` VALUES (251, 246, '支付方式启/禁用', 'changestatus', 'a', 249, 2, 100);
INSERT INTO `jshop_operation` VALUES (252, 2, '文章分类', 'ArticleType', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (253, 252, '文章分类', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (254, 252, '文章分类添加', 'add', 'a', 253, 2, 100);
INSERT INTO `jshop_operation` VALUES (255, 252, '文章分类修改', 'edit', 'a', 253, 2, 100);
INSERT INTO `jshop_operation` VALUES (256, 252, '文章分类删除', 'del', 'a', 253, 2, 100);
INSERT INTO `jshop_operation` VALUES (257, 252, '文章添加子分类', 'addson', 'a', 253, 3, 100);
INSERT INTO `jshop_operation` VALUES (258, 2, '文章管理', 'Article', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (259, 258, '文章列表', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (260, 258, '文章添加', 'add', 'a', 259, 2, 100);
INSERT INTO `jshop_operation` VALUES (261, 258, '文章修改', 'edit', 'a', 259, 2, 100);
INSERT INTO `jshop_operation` VALUES (262, 258, '文章删除', 'del', 'a', 259, 2, 100);
INSERT INTO `jshop_operation` VALUES (263, 2, '优惠券管理', 'Coupon', 'c', 2, 3, 140);
INSERT INTO `jshop_operation` VALUES (264, 263, '优惠券领取列表', 'index', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (265, 263, '优惠券删除', 'del', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (266, 2, '运营管理', 'Notice', 'c', 2, 1, 130);
INSERT INTO `jshop_operation` VALUES (267, 266, '公告列表', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (268, 266, '公告添加', 'add', 'a', 267, 2, 100);
INSERT INTO `jshop_operation` VALUES (269, 266, '公告修改', 'edit', 'a', 267, 2, 100);
INSERT INTO `jshop_operation` VALUES (270, 266, '公告删除', 'del', 'a', 267, 2, 100);
INSERT INTO `jshop_operation` VALUES (271, 2, '广告位管理', 'CarouselSeat', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (272, 271, '广告位列表', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (273, 271, '广告位添加', 'add', 'a', 272, 2, 100);
INSERT INTO `jshop_operation` VALUES (274, 271, '广告位修改', 'edit', 'a', 272, 2, 100);
INSERT INTO `jshop_operation` VALUES (275, 271, '广告位删除', 'del', 'a', 272, 2, 100);
INSERT INTO `jshop_operation` VALUES (277, 2, '广告管理', 'Carousel', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (278, 277, '广告列表', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (279, 277, '广告添加', 'add', 'a', 278, 2, 100);
INSERT INTO `jshop_operation` VALUES (280, 277, '广告修改', 'edit', 'a', 278, 2, 100);
INSERT INTO `jshop_operation` VALUES (281, 277, '广告删除', 'del', 'a', 278, 2, 100);
INSERT INTO `jshop_operation` VALUES (282, 277, '获取商品', 'getgoods', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (283, 277, '获取文章', 'getarticle', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (284, 277, '获取商品名称', 'goodsinfo', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (285, 277, '获取文章名称', 'articleinfo', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (286, 2, '品牌管理', 'Brand', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (287, 286, '品牌列表', 'index', 'a', 305, 1, 30);
INSERT INTO `jshop_operation` VALUES (288, 286, '品牌添加', 'add', 'a', 287, 2, 100);
INSERT INTO `jshop_operation` VALUES (289, 286, '品牌修改', 'edit', 'a', 287, 2, 100);
INSERT INTO `jshop_operation` VALUES (290, 286, '品牌删除', 'del', 'a', 287, 2, 100);
INSERT INTO `jshop_operation` VALUES (291, 2, '商品分类', 'Categories', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (292, 291, '商品分类', 'index', 'a', 305, 1, 20);
INSERT INTO `jshop_operation` VALUES (293, 291, '添加商品分类', 'add', 'a', 292, 2, 100);
INSERT INTO `jshop_operation` VALUES (294, 291, '编辑商品分类', 'edit', 'a', 292, 2, 100);
INSERT INTO `jshop_operation` VALUES (295, 291, '删除商品分类', 'del', 'a', 292, 2, 100);
INSERT INTO `jshop_operation` VALUES (296, 2, '订单管理', 'Order', 'c', 2, 1, 120);
INSERT INTO `jshop_operation` VALUES (297, 296, '订单列表', 'index', 'a', 296, 1, 90);
INSERT INTO `jshop_operation` VALUES (298, 296, '查看订单详情', 'view', 'a', 297, 3, 100);
INSERT INTO `jshop_operation` VALUES (299, 296, '编辑订单', 'edit', 'a', 297, 2, 100);
INSERT INTO `jshop_operation` VALUES (300, 296, '订单发货', 'ship', 'a', 297, 2, 100);
INSERT INTO `jshop_operation` VALUES (301, 296, '取消订单', 'cancel', 'a', 299, 3, 100);
INSERT INTO `jshop_operation` VALUES (302, 296, '完成订单', 'complete', 'a', 299, 3, 100);
INSERT INTO `jshop_operation` VALUES (303, 296, '删除订单', 'del', 'a', 297, 2, 100);
INSERT INTO `jshop_operation` VALUES (304, 296, '物流查询', 'logistics', 'a', 297, 3, 100);
INSERT INTO `jshop_operation` VALUES (305, 2, '商品管理', 'Goods', 'c', 2, 1, 110);
INSERT INTO `jshop_operation` VALUES (306, 305, '商品列表', 'index', 'a', 305, 1, 10);
INSERT INTO `jshop_operation` VALUES (307, 305, '添加商品', 'add', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (308, 305, '保存商品', 'doAdd', 'a', 307, 3, 100);
INSERT INTO `jshop_operation` VALUES (309, 305, '商品评价列表', 'commentlist', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (310, 305, '商品评价详情', 'getcommentinfo', 'a', 309, 3, 100);
INSERT INTO `jshop_operation` VALUES (311, 305, '商家回复', 'sellercontent', 'a', 309, 3, 100);
INSERT INTO `jshop_operation` VALUES (312, 305, '前台展示状态', 'setdisplay', 'a', 164, 3, 100);
INSERT INTO `jshop_operation` VALUES (313, 305, '获取子分类', 'getcat', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (314, 305, '获取规格信息', 'getspec', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (315, 305, '生成多规格', 'getspechtml', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (316, 305, '编辑商品', 'edit', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (317, 305, '保存编辑商品信息', 'doedit', 'a', 316, 3, 100);
INSERT INTO `jshop_operation` VALUES (318, 305, '批量上下架', 'batchmarketable', 'a', 316, 3, 100);
INSERT INTO `jshop_operation` VALUES (319, 305, '批量删除', 'batchdel', 'a', 321, 3, 100);
INSERT INTO `jshop_operation` VALUES (320, 305, '商品高级筛选', 'goodssearch', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (321, 305, '删除商品', 'del', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (322, 305, '设置热门或推荐', 'changestate', 'a', 316, 3, 100);
INSERT INTO `jshop_operation` VALUES (323, 2, '商品类型管理', 'GoodsType', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (324, 323, '商品类型', 'index', 'a', 305, 1, 40);
INSERT INTO `jshop_operation` VALUES (325, 323, '添加类型', 'add', 'a', 324, 2, 100);
INSERT INTO `jshop_operation` VALUES (326, 323, '关联属性', 'addrel', 'a', 324, 3, 100);
INSERT INTO `jshop_operation` VALUES (327, 323, '编辑属性', 'edit', 'a', 324, 2, 100);
INSERT INTO `jshop_operation` VALUES (328, 323, '删除属性', 'del', 'a', 324, 2, 100);
INSERT INTO `jshop_operation` VALUES (329, 323, '关联参数', 'addparams', 'a', 324, 3, 100);
INSERT INTO `jshop_operation` VALUES (330, 2, '商品属性', 'GoodsTypeSpec', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (331, 330, '商品属性', 'index', 'a', 305, 1, 50);
INSERT INTO `jshop_operation` VALUES (332, 330, '添加属性', 'add', 'a', 331, 3, 100);
INSERT INTO `jshop_operation` VALUES (333, 330, '编辑属性', 'edit', 'a', 331, 3, 100);
INSERT INTO `jshop_operation` VALUES (334, 330, '删除属性', 'del', 'a', 331, 3, 100);
INSERT INTO `jshop_operation` VALUES (335, 2, '配送方式', 'Ship', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (336, 335, '配送方式列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (337, 335, '添加配送方式', 'add', 'a', 336, 2, 100);
INSERT INTO `jshop_operation` VALUES (338, 335, '编辑配送方式', 'edit', 'a', 336, 2, 100);
INSERT INTO `jshop_operation` VALUES (339, 335, '删除配送方式', 'del', 'a', 336, 2, 100);
INSERT INTO `jshop_operation` VALUES (340, 335, '选择地区', 'choosearea', 'a', 336, 3, 100);
INSERT INTO `jshop_operation` VALUES (343, 2, '售后单管理', 'BillAftersales', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (344, 343, '售后单列表', 'index', 'a', 296, 1, 140);
INSERT INTO `jshop_operation` VALUES (345, 343, '售后单审核', 'audit', 'a', 344, 2, 100);
INSERT INTO `jshop_operation` VALUES (346, 343, '售后单查看', 'view', 'a', 344, 3, 100);
INSERT INTO `jshop_operation` VALUES (347, 2, '退货单管理', 'BillReship', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (348, 347, '退货单列表', 'index', 'a', 296, 1, 160);
INSERT INTO `jshop_operation` VALUES (349, 347, '退货单查看', 'view', 'a', 348, 3, 100);
INSERT INTO `jshop_operation` VALUES (350, 347, '退货单确认收货', 'confirmreship', 'a', 348, 2, 100);
INSERT INTO `jshop_operation` VALUES (351, 2, '退款单管理', 'BillRefund', 'c', 2, 3, 150);
INSERT INTO `jshop_operation` VALUES (352, 351, '退款单列表', 'index', 'a', 399, 1, 110);
INSERT INTO `jshop_operation` VALUES (353, 351, '退款单查看', 'view', 'a', 352, 3, 100);
INSERT INTO `jshop_operation` VALUES (354, 351, '操作退款', 'refund', 'a', 352, 2, 100);
INSERT INTO `jshop_operation` VALUES (355, 351, '再次退款', 'reaudit', 'a', 354, 3, 100);
INSERT INTO `jshop_operation` VALUES (356, 2, '促销管理', 'Promotion', 'c', 2, 1, 150);
INSERT INTO `jshop_operation` VALUES (357, 356, '促销列表', 'index', 'a', 356, 1, 100);
INSERT INTO `jshop_operation` VALUES (358, 356, '添加促销', 'add', 'a', 357, 2, 100);
INSERT INTO `jshop_operation` VALUES (359, 356, '促销编辑', 'edit', 'a', 357, 2, 100);
INSERT INTO `jshop_operation` VALUES (360, 356, '促销删除', 'del', 'a', 357, 2, 100);
INSERT INTO `jshop_operation` VALUES (361, 356, '优惠券列表', 'coupon', 'a', 356, 1, 100);
INSERT INTO `jshop_operation` VALUES (362, 356, '添加优惠券规则', 'couponadd', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (363, 356, '优惠券规则编辑', 'couponedit', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (364, 356, '优惠券规则删除', 'coupondel', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (365, 2, '门店管理', 'Store', 'c', 2, 3, 220);
INSERT INTO `jshop_operation` VALUES (366, 365, '门店列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (367, 365, '门店添加', 'add', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (368, 365, '门店修改', 'edit', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (369, 365, '门店删除', 'del', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (370, 365, '门店地图展现', 'showmap', 'a', 366, 3, 100);
INSERT INTO `jshop_operation` VALUES (390, 2, '图片列表', 'images', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (391, 390, '图片列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (392, 2, '导入导出管理', 'ietask', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (393, 392, '导入导出', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (394, 392, '导出', 'export', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (395, 392, '导入', 'import', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (396, 392, '下载导入模板', 'importtemplete', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (397, 392, '下载文件', 'down', 'a', 393, 2, 100);
INSERT INTO `jshop_operation` VALUES (398, 392, '执行下载文件', 'dodown', 'a', 397, 3, 100);
INSERT INTO `jshop_operation` VALUES (399, 2, '财务管理', 'BillPayments', 'c', 2, 1, 160);
INSERT INTO `jshop_operation` VALUES (400, 399, '支付单列表', 'index', 'a', 399, 1, 100);
INSERT INTO `jshop_operation` VALUES (401, 399, '后台支付', 'pay', 'a', 400, 2, 100);
INSERT INTO `jshop_operation` VALUES (402, 399, '后台支付实际操作', 'topay', 'a', 401, 3, 100);
INSERT INTO `jshop_operation` VALUES (403, 399, '支付单查看', 'index', 'a', 400, 3, 100);
INSERT INTO `jshop_operation` VALUES (404, 2, '发货单管理', 'BillDelivery', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (405, 404, '发货单列表', 'index', 'a', 296, 1, 100);
INSERT INTO `jshop_operation` VALUES (406, 404, '发货单详情', 'view', 'a', 405, 3, 100);
INSERT INTO `jshop_operation` VALUES (407, 404, '发货单明细', 'items', 'a', 405, 3, 100);
INSERT INTO `jshop_operation` VALUES (408, 2, '消息中心', 'MessageCenter', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (409, 408, '消息配置', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (410, 2, '角色管理', 'Role', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (411, 410, '角色管理', 'index', 'a', 238, 1, 210);
INSERT INTO `jshop_operation` VALUES (412, 410, '角色添加', 'add', 'a', 411, 2, 100);
INSERT INTO `jshop_operation` VALUES (413, 410, '角色删除', 'del', 'a', 411, 2, 100);
INSERT INTO `jshop_operation` VALUES (414, 410, '编辑权限', 'getoperation', 'a', 411, 2, 100);
INSERT INTO `jshop_operation` VALUES (415, 410, '角色保存权限', 'saveperm', 'a', 411, 3, 100);
INSERT INTO `jshop_operation` VALUES (416, 2, '管理员管理', 'Administrator', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (417, 416, '管理员管理', 'index', 'a', 238, 1, 200);
INSERT INTO `jshop_operation` VALUES (418, 416, '管理员添加', 'add', 'a', 417, 2, 100);
INSERT INTO `jshop_operation` VALUES (419, 416, '管理员修改', 'edit', 'a', 417, 2, 100);
INSERT INTO `jshop_operation` VALUES (420, 416, '管理员删除', 'del', 'a', 417, 2, 100);
INSERT INTO `jshop_operation` VALUES (421, 2, '商品参数', 'GoodsParams', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (422, 421, '参数列表', 'index', 'a', 305, 1, 100);
INSERT INTO `jshop_operation` VALUES (423, 421, '添加参数', 'add', 'a', 422, 2, 100);
INSERT INTO `jshop_operation` VALUES (424, 421, '编辑参数', 'edit', 'a', 422, 2, 100);
INSERT INTO `jshop_operation` VALUES (425, 421, '删除参数', 'del', 'a', 422, 2, 100);
INSERT INTO `jshop_operation` VALUES (426, 2, '标签管理', 'Label', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (427, 426, '设置标签', 'setlabel', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (428, 426, '设置标签', 'dosetlabel', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (429, 426, '删除标签', 'dellabel', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (430, 426, '删除标签', 'dodellabel', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (431, 2, '账户余额模块', 'Balance', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (432, 431, '账户资金管理', 'index', 'a', 399, 1, 150);
INSERT INTO `jshop_operation` VALUES (434, 2, '节点控制器', 'Operation', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (435, 434, '节点管理', 'index', 'a', 244, 1, 255);
INSERT INTO `jshop_operation` VALUES (436, 434, '节点删除', 'del', 'a', 435, 2, 100);
INSERT INTO `jshop_operation` VALUES (437, 434, '节点编辑', 'add', 'a', 435, 2, 100);
INSERT INTO `jshop_operation` VALUES (438, 2, '消息控制器', 'Message', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (439, 438, '消息管理', 'index', 'a', 239, 3, 300);
INSERT INTO `jshop_operation` VALUES (440, 438, '消息删除', 'del', 'a', 439, 3, 100);
INSERT INTO `jshop_operation` VALUES (441, 2, '短信控制器', 'Sms', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (442, 441, '短信管理', 'index', 'a', 238, 1, 310);
INSERT INTO `jshop_operation` VALUES (443, 441, '短信删除', 'del', 'a', 442, 2, 100);
INSERT INTO `jshop_operation` VALUES (460, 431, '提现列表', 'tocash', 'a', 399, 1, 100);
INSERT INTO `jshop_operation` VALUES (461, 431, '提现审核', 'tocashexamine', 'a', 460, 2, 100);
INSERT INTO `jshop_operation` VALUES (462, 2, '钩子列表', 'Hooks', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (463, 462, '钩子列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (464, 2, '插件列表', 'Addons', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (465, 464, '插件列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (466, 2, '地区管理', 'Area', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (467, 466, '地区管理', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (468, 466, '添加地区', 'add', 'a', 467, 2, 100);
INSERT INTO `jshop_operation` VALUES (469, 466, '编辑地区', 'edit', 'a', 467, 2, 100);
INSERT INTO `jshop_operation` VALUES (470, 466, '删除地区', 'del', 'a', 467, 2, 100);
INSERT INTO `jshop_operation` VALUES (471, 462, '添加钩子', 'add', 'a', 463, 2, 100);
INSERT INTO `jshop_operation` VALUES (472, 462, '编辑钩子', 'edit', 'a', 463, 2, 100);
INSERT INTO `jshop_operation` VALUES (473, 462, '删除钩子', 'del', 'a', 463, 2, 100);
INSERT INTO `jshop_operation` VALUES (474, 464, '安装插件', 'install', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (475, 464, '卸载插件', 'uninstall', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (476, 464, '插件配置', 'setting', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (477, 464, '保存配置', 'dosetting', 'a', 476, 3, 100);
INSERT INTO `jshop_operation` VALUES (478, 464, '插件启用/停用', 'changestatus', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (479, 2, '微信管理', 'Wechat', 'c', 2, 1, 220);
INSERT INTO `jshop_operation` VALUES (480, 479, '小程序配置', 'edit', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (481, 479, '模板列表', 'template', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (482, 479, '保存配置', 'doEdit', 'a', 480, 3, 100);
INSERT INTO `jshop_operation` VALUES (483, 479, '公众号配置', 'official', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (484, 365, '店员列表', 'clerklist', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (485, 365, '添加店员', 'addclerk', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (486, 365, '删除店员', 'delclerk', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (487, 2, '提货单列表', 'BillLading', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (488, 487, '提货单列表', 'index', 'a', 296, 1, 100);
INSERT INTO `jshop_operation` VALUES (489, 487, '提货单详情', 'info', 'a', 488, 3, 100);
INSERT INTO `jshop_operation` VALUES (490, 487, '删除提货单', 'dellading', 'a', 488, 2, 100);
INSERT INTO `jshop_operation` VALUES (491, 356, '团购秒杀列表', 'group', 'a', 356, 1, 100);
INSERT INTO `jshop_operation` VALUES (492, 356, '添加团购', 'groupadd', 'a', 491, 2, 100);
INSERT INTO `jshop_operation` VALUES (493, 356, '编辑团购', 'groupedit', 'a', 491, 2, 100);
INSERT INTO `jshop_operation` VALUES (494, 356, '删除团购', 'groupdel', 'a', 491, 2, 100);
INSERT INTO `jshop_operation` VALUES (495, 2, '物流公司', 'Logistics', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (496, 495, '物流公司列表', 'index', 'a', 244, 1, 200);
INSERT INTO `jshop_operation` VALUES (497, 495, '添加', 'add', 'a', 496, 2, 100);
INSERT INTO `jshop_operation` VALUES (498, 495, '编辑', 'edit', 'a', 496, 2, 100);
INSERT INTO `jshop_operation` VALUES (499, 495, '删除', 'del', 'a', 496, 2, 100);
INSERT INTO `jshop_operation` VALUES (500, 277, '获取文章分类', 'getarticletype', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (501, 277, '获取文章分类名称', 'articletypeinfo', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (502, 466, '地区列表', 'getlist', 'a', 467, 3, 100);
INSERT INTO `jshop_operation` VALUES (503, 487, '提货单核销', 'write', 'a', 488, 2, 100);
INSERT INTO `jshop_operation` VALUES (504, 286, '获取所有品牌', 'getAll', 'a', 287, 3, 100);
INSERT INTO `jshop_operation` VALUES (505, 291, '全部分类', 'getAll', 'a', 292, 3, 100);
INSERT INTO `jshop_operation` VALUES (506, 305, '更新排序', 'updatesort', 'a', 316, 3, 100);
INSERT INTO `jshop_operation` VALUES (507, 421, '弹窗参数', 'getlist', 'a', 422, 3, 100);
INSERT INTO `jshop_operation` VALUES (508, 323, '获取所有', 'getAll', 'a', 324, 3, 100);
INSERT INTO `jshop_operation` VALUES (509, 330, '弹窗属性列表', 'getlist', 'a', 331, 3, 100);
INSERT INTO `jshop_operation` VALUES (510, 408, '消息编辑', 'edit', 'a', 409, 2, 100);
INSERT INTO `jshop_operation` VALUES (511, 2, '操作日志', 'OperationLog', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (512, 511, '操作日志', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (517, 479, '公众号菜单', 'officialmenu', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (518, 479, '公众号菜单-编辑', 'officialmenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (519, 479, '公众号菜单-编辑保存', 'doeditmenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (520, 479, '公众号菜单-删除', 'deletemenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (521, 479, '公众号菜单-同步', 'updatemenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (522, 479, '微信消息管理', 'message', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (523, 479, '微信消息-添加', 'addMessage', 'a', 522, 2, 100);
INSERT INTO `jshop_operation` VALUES (524, 479, '微信消息-删除', 'delMessage', 'a', 522, 2, 100);
INSERT INTO `jshop_operation` VALUES (525, 479, '微信消息-编辑', 'editMessage', 'a', 522, 2, 100);
INSERT INTO `jshop_operation` VALUES (526, 479, '微信消息-互动', 'autoreply', 'a', 522, 2, 100);
INSERT INTO `jshop_operation` VALUES (527, 479, '编辑图文', 'editmediamessage', 'a', 522, 3, 100);
INSERT INTO `jshop_operation` VALUES (528, 479, '保存图文消息', 'doeditmediamessage', 'a', 522, 3, 100);
INSERT INTO `jshop_operation` VALUES (529, 238, '邀请人修改', 'editinvite', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (530, 238, '会员编辑', 'edituser', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (531, 238, '余额修改', 'editmoney', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (532, 238, '余额明细', 'moneylog', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (533, 238, '添加会员', 'adduser', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (534, 335, '选择地区', 'getarea', 'a', 336, 3, 100);
INSERT INTO `jshop_operation` VALUES (535, 2, '报表统计', 'Report', 'c', 2, 1, 210);
INSERT INTO `jshop_operation` VALUES (536, 535, '订单销量', 'order', 'a', 535, 1, 100);
INSERT INTO `jshop_operation` VALUES (537, 535, '财务收款', 'payments', 'a', 535, 1, 100);
INSERT INTO `jshop_operation` VALUES (538, 535, '商品销量', 'goods', 'a', 535, 1, 100);
INSERT INTO `jshop_operation` VALUES (539, 390, '删除图片', 'del', 'a', 391, 2, 100);
INSERT INTO `jshop_operation` VALUES (540, 392, '删除任务', 'del', 'a', 393, 1, 100);
INSERT INTO `jshop_operation` VALUES (541, 2, '智能表单', 'Form', 'c', 2, 1, 300);
INSERT INTO `jshop_operation` VALUES (542, 541, '表单列表', 'index', 'a', 541, 1, 100);
INSERT INTO `jshop_operation` VALUES (543, 541, '添加表单', 'add', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (544, 541, '编辑表单', 'edit', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (545, 541, '删除表单', 'del', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (546, 541, '小程序码', 'generate', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (547, 541, '提交列表', 'formsubmit', 'a', 541, 1, 100);
INSERT INTO `jshop_operation` VALUES (548, 541, '表单报表', 'report', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (549, 541, '表单统计', 'statistics', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (550, 541, '删除提交', 'delsubmit', 'a', 547, 2, 100);
INSERT INTO `jshop_operation` VALUES (551, 541, '提交明细', 'formsubmitdetail', 'a', 547, 2, 100);
INSERT INTO `jshop_operation` VALUES (552, 541, '反馈', 'editformsubmit', 'a', 547, 2, 100);
INSERT INTO `jshop_operation` VALUES (553, 238, '用户等级', 'grade', 'a', 238, 1, 150);
INSERT INTO `jshop_operation` VALUES (554, 238, '添加&修改', 'gradeadd', 'a', 553, 2, 100);
INSERT INTO `jshop_operation` VALUES (555, 238, '删除', 'gradedel', 'a', 553, 3, 100);
INSERT INTO `jshop_operation` VALUES (556, 296, '订单打印', 'print_tpl', 'a', 297, 3, 100);
INSERT INTO `jshop_operation` VALUES (557, 296, '打印选择快递', 'print_form', 'a', 297, 3, 100);
INSERT INTO `jshop_operation` VALUES (558, 535, '用户收藏统计', 'goodscollection', 'a', 535, 1, 100);

-- ----------------------------
-- Table structure for jshop_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `jshop_operation_log`;
CREATE TABLE `jshop_operation_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `manage_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '管理员ID',
  `controller` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作的控制器名',
  `method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作方法名',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作描述',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '操作数据序列号存储',
  `ip` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作IP',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台操作记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_order
-- ----------------------------
DROP TABLE IF EXISTS `jshop_order`;
CREATE TABLE `jshop_order`  (
  `order_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `goods_amount` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '商品总价',
  `payed` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '已支付的金额',
  `order_amount` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '订单实际销售总额',
  `pay_status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '支付状态 1=未付款 2=已付款 3=部分付款 4=部分退款 5=已退款',
  `ship_status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '发货状态 1=未发货 2=部分发货 3=已发货 4=部分退货 5=已退货',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '订单状态 1=正常 2=完成 3=取消',
  `payment_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '支付方式代码',
  `payment_time` bigint(12) NULL DEFAULT NULL COMMENT '支付时间',
  `logistics_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配送方式ID 关联ship.id',
  `logistics_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配送方式名称',
  `cost_freight` decimal(6, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '配送费用',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID 关联user.id',
  `seller_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '店铺ID 关联seller.id',
  `confirm` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '售后状态 1=未确认收货 2=已确认收货',
  `confirm_time` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '确认收货时间',
  `store_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '自提门店ID，0就是不门店自提',
  `ship_area_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '收货地区ID',
  `ship_address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货详细地址',
  `ship_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货人姓名',
  `ship_mobile` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货电话',
  `weight` double(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '商品总重量',
  `tax_type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否开发票 1=不发票 2=个人发票 3=公司发票',
  `tax_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '商品详情' COMMENT '发票内容',
  `tax_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '税号',
  `tax_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '发票抬头',
  `point` int(10) UNSIGNED NULL DEFAULT NULL,
  `point_money` decimal(10, 2) NULL DEFAULT NULL,
  `order_pmt` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '订单优惠金额',
  `goods_pmt` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '商品优惠金额',
  `coupon_pmt` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '优惠券优惠额度',
  `coupon` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '优惠券信息',
  `promotion_list` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '优惠信息',
  `memo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '买家备注',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '下单IP',
  `mark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '卖家备注',
  `source` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '订单来源 1=PC页面 2=H5页面 3=微信小程序',
  `is_comment` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否评论，1未评论，2已评论',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标志 有数据表示删除',
  PRIMARY KEY (`order_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_order_items
-- ----------------------------
DROP TABLE IF EXISTS `jshop_order_items`;
CREATE TABLE `jshop_order_items`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单ID 关联order.id',
  `goods_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品ID 关联goods.id',
  `product_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '货品ID 关联products.id',
  `sn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '货品编码',
  `bn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品编码',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品名称',
  `price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '货品价格单价',
  `costprice` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '货品成本价单价',
  `mktprice` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '市场价',
  `image_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片',
  `nums` smallint(5) UNSIGNED NULL DEFAULT NULL COMMENT '数量',
  `amount` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '总价',
  `promotion_amount` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '商品优惠总金额',
  `promotion_list` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '促销信息',
  `weight` decimal(10, 2) NULL DEFAULT NULL COMMENT '总重量',
  `sendnums` smallint(5) UNSIGNED NULL DEFAULT NULL COMMENT '发货数量',
  `addon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '货品明细序列号存储',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单明细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_order_log
-- ----------------------------
DROP TABLE IF EXISTS `jshop_order_log`;
CREATE TABLE `jshop_order_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订单ID',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',
  `type` tinyint(2) UNSIGNED NULL DEFAULT 1 COMMENT '类型',
  `msg` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '描述介绍',
  `data` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求的数据json',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_payments
-- ----------------------------
DROP TABLE IF EXISTS `jshop_payments`;
CREATE TABLE `jshop_payments`  (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '支付类型编码',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '支付类型名称',
  `is_online` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否线上支付 1=线上支付 2=线下支付',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '参数',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `memo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '支付方式描述',
  `status` smallint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '启用状态 1=启用 2=停用',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '支付方式表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_payments
-- ----------------------------
INSERT INTO `jshop_payments` VALUES (1, 'wechatpay', '微信支付', 1, '{\"appid\":\"\",\"mch_id\":\"\",\"key\":\"\",\"sslcert\":\"\",\"sslkey\":\"\"}', 100, '点击去微信支付', 2);
INSERT INTO `jshop_payments` VALUES (2, 'alipay', '支付宝支付', 1, '{\"appid\":\"\",\"rsa_private_key\":\"\",\"alipay_public_key\":\"\"}', 100, '点击去支付宝支付', 2);
INSERT INTO `jshop_payments` VALUES (3, 'offline', '线下支付', 2, '', 100, '联系客服进行线下付款', 1);
INSERT INTO `jshop_payments` VALUES (4, 'balancepay', '余额支付', 1, '', 100, '账户余额支付', 1);

-- ----------------------------
-- Table structure for jshop_products
-- ----------------------------
DROP TABLE IF EXISTS `jshop_products`;
CREATE TABLE `jshop_products`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '货品ID',
  `goods_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品id 关联goods.id',
  `barcode` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '货品条码',
  `sn` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品编码',
  `price` decimal(10, 2) NULL DEFAULT NULL COMMENT '货品价格',
  `costprice` decimal(10, 2) NULL DEFAULT NULL COMMENT '货品成本价',
  `mktprice` decimal(10, 2) NULL DEFAULT NULL COMMENT '货品市场价',
  `marketable` tinyint(1) UNSIGNED NULL DEFAULT NULL COMMENT '上架标志 1=上架 2=下架',
  `stock` int(11) NULL DEFAULT 0 COMMENT '库存',
  `freeze_stock` int(11) NULL DEFAULT 0 COMMENT '冻结库存',
  `spes_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '规格值逗号分隔存储',
  `is_defalut` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否默认货品 1=是 2=否',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标志',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE,
  INDEX `goods_id`(`goods_id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '货品表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_promotion
-- ----------------------------
DROP TABLE IF EXISTS `jshop_promotion`;
CREATE TABLE `jshop_promotion`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '促销名称',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '启用状态，1开启，2关闭',
  `type` smallint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1促销，2优惠券，3团购，4秒杀',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `exclusive` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '排他，1不排他，2排他',
  `auto_receive` smallint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '当时优惠券的时候，自动是否自动领取，1自动领取，2不自动领取',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '其它参数',
  `stime` bigint(12) UNSIGNED NOT NULL COMMENT '开始时间',
  `etime` bigint(12) UNSIGNED NOT NULL COMMENT '结束时间',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `isdel`(`isdel`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '促销表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_promotion_condition
-- ----------------------------
DROP TABLE IF EXISTS `jshop_promotion_condition`;
CREATE TABLE `jshop_promotion_condition`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promotion_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '促销ID',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '促销条件编码',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '支付配置参数序列号存储',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `promotion_id`(`promotion_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '促销条件表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_promotion_result
-- ----------------------------
DROP TABLE IF EXISTS `jshop_promotion_result`;
CREATE TABLE `jshop_promotion_result`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promotion_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '促销ID',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '促销条件编码',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '支付配置参数序列号存储',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `promotion_id`(`promotion_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '促销结果表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_setting
-- ----------------------------
DROP TABLE IF EXISTS `jshop_setting`;
CREATE TABLE `jshop_setting`  (
  `skey` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '键',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '值',
  PRIMARY KEY (`skey`) USING BTREE,
  INDEX `skey`(`skey`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '店铺设置表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_ship
-- ----------------------------
DROP TABLE IF EXISTS `jshop_ship`;
CREATE TABLE `jshop_ship`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配送方式名称',
  `has_cod` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否货到付款 1=不是货到付款 2=是货到付款',
  `firstunit` mediumint(8) UNSIGNED NULL DEFAULT NULL COMMENT '首重',
  `continueunit` mediumint(8) UNSIGNED NULL DEFAULT NULL COMMENT '续重',
  `def_area_fee` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '按地区设置配送费用是否启用默认配送费用 1=启用 2=不启用',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '地区类型 1=全部地区 2=指定地区',
  `firstunit_price` decimal(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT '首重费用',
  `continueunit_price` decimal(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT '续重费用',
  `exp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配送费用计算表达式',
  `logi_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物流公司名称',
  `logi_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '物流公司编码',
  `is_def` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否默认 1=默认 2=不默认',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '配送方式排序 越小越靠前',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '状态 1=正常 2=停用',
  `free_postage` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否包邮，1包邮，2不包邮',
  `area_fee` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '地区配送费用',
  `goodsmoney` decimal(20, 2) NULL DEFAULT 0.00 COMMENT '商品总额满多少免运费',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE,
  INDEX `sort_2`(`sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10002 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配送方式表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_sms
-- ----------------------------
DROP TABLE IF EXISTS `jshop_sms`;
CREATE TABLE `jshop_sms`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '手机号码',
  `code` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `params` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '参数',
  `content` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `ctime` bigint(12) UNSIGNED NOT NULL COMMENT '创建时间',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ip',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1未使用，2已使用',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mobile`(`mobile`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_store
-- ----------------------------
DROP TABLE IF EXISTS `jshop_store`;
CREATE TABLE `jshop_store`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `store_name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '门店名称',
  `mobile` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '门店电话/手机号',
  `linkman` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '门店联系人',
  `logo` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '门店logo',
  `area_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '门店地区id',
  `address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '门店详细地址',
  `coordinate` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_template
-- ----------------------------
DROP TABLE IF EXISTS `jshop_template`;
CREATE TABLE `jshop_template`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板名称',
  `th_template_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '模板ID，第三方平台id',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `user_desc` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板描述',
  `source_appid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板APPID',
  `developer` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板开发者',
  `version` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板版本，总共3位，第一位表示重大版本升级，第二位表示小版本升级，第三位表示补丁或更新',
  `image_id` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板主图',
  `desc` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '模板需要注意事项',
  `qr_demo` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '二维码预览图片',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '模板类型，1为小程序',
  `ext_json` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '模板增加模板配置字段',
  `ctime` int(10) NULL DEFAULT NULL COMMENT '数据创建时间',
  `utime` int(10) NULL DEFAULT NULL COMMENT '数据更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '模板列表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_template_message
-- ----------------------------
DROP TABLE IF EXISTS `jshop_template_message`;
CREATE TABLE `jshop_template_message`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '消息类型',
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '单号',
  `form_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '要发生给的用户',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '发送状态 1=未发送 2=已发送',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标识',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_template_order
-- ----------------------------
DROP TABLE IF EXISTS `jshop_template_order`;
CREATE TABLE `jshop_template_order`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `template_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '模板id',
  `appid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商户appid',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '模板订购记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_user
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user`;
CREATE TABLE `jshop_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '密码 md5(md5()+创建时间)',
  `mobile` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `sex` tinyint(1) UNSIGNED NULL DEFAULT 3 COMMENT '1=男 2=女 3=未知',
  `birthday` date NULL DEFAULT NULL COMMENT '生日',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '头像',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `balance` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '余额',
  `point` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分',
  `grade` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户等级',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '1 = 正常 2 = 停用',
  `pid` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '推荐人',
  `isdel` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '删除标志 有数据就是删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_user_bankcards
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user_bankcards`;
CREATE TABLE `jshop_user_bankcards`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID号',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',
  `bank_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '银行名称',
  `bank_code` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '银行缩写',
  `bank_area_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '账号地区ID',
  `account_bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '开户行',
  `account_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '账户名',
  `card_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '卡号',
  `card_type` tinyint(1) NULL DEFAULT 1 COMMENT '银行卡类型: 1=储蓄卡 2=信用卡',
  `is_default` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '默认卡 1=默认 2=不默认',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_user_grade
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user_grade`;
CREATE TABLE `jshop_user_grade`  (
  `id` tinyint(2) UNSIGNED NOT NULL COMMENT 'id',
  `name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `is_def` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1默认，2不默认',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '用户等级表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_user_grade
-- ----------------------------
INSERT INTO `jshop_user_grade` VALUES (1, '普通等级', 1);

-- ----------------------------
-- Table structure for jshop_user_log
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user_log`;
CREATE TABLE `jshop_user_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `state` tinyint(1) NULL DEFAULT NULL COMMENT '登录 1  退出2,3注册',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '时间',
  `params` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '参数',
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ip地址',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_user_point_log
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user_point_log`;
CREATE TABLE `jshop_user_point_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '类型 1=签到 2=购物返积分 3=购物使用积分',
  `num` int(10) NULL DEFAULT 0 COMMENT '积分数量',
  `balance` bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '积分余额',
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户积分记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_user_ship
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user_ship`;
CREATE TABLE `jshop_user_ship`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户id 关联user.id',
  `area_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '收货地区ID',
  `address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货详细地址',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货人姓名',
  `mobile` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '收货电话',
  `utime` bigint(12) NOT NULL COMMENT '更新时间',
  `is_def` tinyint(1) UNSIGNED NULL DEFAULT NULL COMMENT '是否默认 1=默认 2=不默认',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_user_tocash
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user_tocash`;
CREATE TABLE `jshop_user_tocash`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID号',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',
  `money` decimal(8, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '提现金额',
  `bank_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '银行名称',
  `bank_code` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '银行缩写',
  `bank_area_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '账号地区ID',
  `account_bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '开户行',
  `account_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '账户名',
  `card_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '卡号',
  `withdrawals` decimal(8, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '提现服务费',
  `type` smallint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1默认，2提现成功，3提现失败',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_user_token
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user_token`;
CREATE TABLE `jshop_user_token`  (
  `token` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `platform` smallint(2) NOT NULL DEFAULT 1 COMMENT '平台类型，1就是默认，2就是微信小程序',
  `ctime` bigint(12) UNSIGNED NOT NULL,
  PRIMARY KEY (`token`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户token' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_user_wx
-- ----------------------------
DROP TABLE IF EXISTS `jshop_user_wx`;
CREATE TABLE `jshop_user_wx`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '第三方登录类型，1微信小程序，2微信公众号',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联用户表',
  `openid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `session_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `unionid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '头像',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `gender` smallint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别 0：未知、1：男、2：女',
  `language` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '语言',
  `city` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '城市',
  `province` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '省',
  `country` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '国家',
  `country_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号码国家编码',
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号码',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_weixin_author
-- ----------------------------
DROP TABLE IF EXISTS `jshop_weixin_author`;
CREATE TABLE `jshop_weixin_author`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nick_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '授权方昵称',
  `head_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '授权方头像',
  `service_type_info` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '默认为0',
  `verify_type_info` tinyint(1) NULL DEFAULT -1 COMMENT '授权方认证类型，-1代表未认证，0代表微信认证',
  `user_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '小程序的原始ID',
  `signature` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '帐号介绍',
  `principal_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '小程序的主体名称',
  `business_info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '用以了解以下功能的开通状况（0代表未开通，1代表已开通）： open_store:是否开通微信门店功能 open_scan:是否开通微信扫商品功能 open_pay:是否开通微信支付功能 open_card:是否开通微信卡券功能 open_shake:是否开通微信摇一摇功能',
  `qrcode_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '二维码图片的URL',
  `authorization_info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '授权信息',
  `appid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '授权方appid',
  `appsecret` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '授权方AppSecret',
  `miniprograminfo` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '可根据这个字段判断是否为小程序类型授权,有值为小程序',
  `func_info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '小程序授权给开发者的权限集列表，ID为17到19时分别代表： 17.帐号管理权限 18.开发管理权限 19.客服消息管理权限 请注意： 1）该字段的返回不会考虑小程序是否具备该权限集的权限（因为可能部分具备）',
  `authorizer_refresh_token` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '刷新token',
  `authorizer_access_token` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'token',
  `bind_type` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '绑定类型，1为第三方授权绑定，2为自助绑定',
  `author_type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'b2c' COMMENT '授权类型，默认b2c',
  `expires_in` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '绑定授权到期时间',
  `ctime` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '小程序授权时间',
  `utime` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '小程序更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '获取授权方的帐号基本信息表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_weixin_media_message
-- ----------------------------
DROP TABLE IF EXISTS `jshop_weixin_media_message`;
CREATE TABLE `jshop_weixin_media_message`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '作者',
  `brief` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '摘要',
  `image` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '封面',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '文章详情',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '原文地址',
  `ctime` bigint(12) NULL DEFAULT 0 COMMENT '创建时间',
  `utime` bigint(12) NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信图文消息表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_weixin_menu
-- ----------------------------
DROP TABLE IF EXISTS `jshop_weixin_menu`;
CREATE TABLE `jshop_weixin_menu`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) NOT NULL COMMENT '菜单id',
  `pid` int(10) NOT NULL DEFAULT 0 COMMENT '父级菜单',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单名称',
  `type` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单类型',
  `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单参数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信公众号菜单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_weixin_message
-- ----------------------------
DROP TABLE IF EXISTS `jshop_weixin_message`;
CREATE TABLE `jshop_weixin_message`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '消息名称',
  `type` tinyint(1) NULL DEFAULT 1 COMMENT '消息类型1:文本消息，2:图文消息',
  `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '消息参数',
  `ctime` bigint(12) NULL DEFAULT 0 COMMENT '创建时间',
  `utime` bigint(12) NULL DEFAULT 0 COMMENT '更新时间',
  `is_attention` tinyint(1) NULL DEFAULT 2 COMMENT '关注回复，1是关注回复，2不是关注回复',
  `is_default` tinyint(1) NULL DEFAULT 2 COMMENT '是否默认回复，1是，2不是',
  `enable` tinyint(1) NULL DEFAULT 1 COMMENT '1启用，2禁用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信消息表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_weixin_publish
-- ----------------------------
DROP TABLE IF EXISTS `jshop_weixin_publish`;
CREATE TABLE `jshop_weixin_publish`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '模板id，不是第三方平台模板id',
  `ctime` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '购买模板时间',
  `audit_status` tinyint(1) NULL DEFAULT NULL COMMENT '审核状态，其中0为审核成功，1为审核失败，2为审核中,-1为未提交审核',
  `reason` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '审核反馈',
  `publish_status` tinyint(1) NULL DEFAULT 0 COMMENT '发布状态，0未发布，1已发布，2发布成功，3发布失败',
  `publish_msg` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '发布反馈',
  `auditid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '审核id',
  `ext_json` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '第三方自定义的json',
  `user_version` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '代码版本号',
  `user_desc` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '代码描述',
  `appid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '授权appid',
  `qrcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '预览二维码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '小程序发布审核表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_worksheet
-- ----------------------------
DROP TABLE IF EXISTS `jshop_worksheet`;
CREATE TABLE `jshop_worksheet`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` char(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '未处理',
  `job_num` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '工单号',
  `create_time` int(11) NOT NULL COMMENT '提交时间',
  `type` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_wsdetail
-- ----------------------------
DROP TABLE IF EXISTS `jshop_wsdetail`;
CREATE TABLE `jshop_wsdetail`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_num` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `event` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(11) NOT NULL,
  `pic_path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
