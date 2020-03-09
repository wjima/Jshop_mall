
TRUNCATE TABLE `jshop_goods_cat`;

--
-- Dumping data for table `jshop_goods_cat`
--

INSERT INTO `jshop_goods_cat` (`id`, `parent_id`, `name`, `type_id`, `sort`, `image_id`, `utime`) VALUES
  (3, 0, '精选上衣', 0, 100, '', NULL),
  (4, 0, '浪漫裙装', 0, 100, '', NULL),
  (5, 0, '女装下装', 0, 100, '', NULL),
  (6, 9, '家居服', 1, 100, 'a496465d657cd5fbeb90f10e25e16086', 1539941157),
  (8, 0, '女鞋', 0, 100, '', NULL),
  (9, 0, '女包', 0, 100, '', NULL),
  (10, 3, '毛呢外套', 0, 100, 'a422f452969f04e4493af4728eec3ab8', 1540172964),
  (11, 3, '羽绒服', 0, 100, '9ca906f23d05dc9db1de552bfefa36fc', 1539940964),
  (12, 3, '棉服', 0, 100, 'f4f98826b4db681ab21433c3fecc8326', 1539940902),
  (13, 3, '卫衣', 0, 100, 'b9857035fa36ca4fbbeadc72e3ead0bb', 1539940850),
  (14, 3, '毛针织衫', 0, 100, 'de21cc610a10fada67ef1974bdacf9fb', 1539940683),
  (15, 9, '皮草', 1, 100, 'b68a51db26eb5c35e4f9b16f0c41d455', 1540459969),
  (16, 3, '衬衫', 0, 100, 'db1b8c0257b08a5542e10125fcef8fc1', 1539940635),
  (17, 3, 'T恤', 0, 100, 'd74ea8dc93080b9e4ae686f4a14dbe96', 1539940585),
  (18, 0, '风衣', 1, 100, 'd47c3377663dfbb925ea9a40a1488155', 1540186606),
  (19, 9, '单肩包', 0, 100, '443c2d9ddb8b769ceddcd6f2bee5908b', 1539941054),
  (20, 9, '斜挎包', 0, 100, 'dea52ed56c999be5583ba568aff9029a', 1539941097),
  (21, 9, '手提包', 0, 100, '8fec591f6b83a0edaa973e3ea35ae8ee', 1539941013),
  (22, 8, '高跟鞋', 0, 100, 'e51a16d2996d29939e7031434fbc3c29', 1540172563),
  (23, 8, '运动鞋', 0, 100, 'fe91f59b549fab49ca2db1ea9883f8d3', 1540172488),
  (24, 8, '帆布鞋', 0, 100, 'cd2c104a7d9100e12f7529d6b2c3f209', 1540172536),
  (26, 5, '牛仔裤', 0, 100, '2776b0176aa478be959d762326c4e58f', 1540459686),
  (27, 5, '九分裤', 0, 100, '160b5dfcb243deffb794ce3d0ac48431', 1540172257),
  (28, 5, '直筒裤', 0, 100, '7e9d7a60a7d40c9e908c45ed092de558', 1540172331),
  (29, 4, '连衣裙', 0, 100, 'aea2675adb10467d6dfbd92ab9321a33', 1540172373),
  (30, 4, '碎花裙', 0, 100, 'f8b31c241ad346fceb55f83fcf2874bb', 1540172412),
  (31, 4, '短裙', 0, 100, 'af547e9f2513965d6eaf56b4dcc276ec', 1540172457);

--
-- Truncate table before insert `jshop_brand`
--

TRUNCATE TABLE `jshop_brand`;
--
-- Dumping data for table `jshop_brand`
--

INSERT  INTO `jshop_brand` (`id`, `name`, `logo`, `sort`, `utime`, `isdel`) VALUES
  (1, '依嬅莎', '56020c21a0e3b07e9906c1c6f06bee6a', 100, 1540008147, NULL),
  (2, '玖姿', '14b44959327363e274c8a2ba9dba3ace', 100, 1537860157, NULL),
  (3, '裂帛', '03ca799895a0b094bca75116e322539c', 100, 1537860168, NULL),
  (4, '恩裳', 'dcc56740b60745aac85a1b0433767a8d', 100, 1537860181, NULL),
  (5, '水墨青华', 'b9d70b1b457f77b3796cd4229c9b0aa2', 100, 1537860201, NULL),
  (6, 'UR', 'def45acaee091fb6ab66634b76a5ce48', 100, 1537860212, NULL),
  (7, '拉夏贝尔', 'e066155b201c578f5cd467616b0483b2', 100, 1537860243, NULL),
  (8, '茵曼', '38b53dadcfeb35a77678a0a9cfdb7a7c', 100, 1537860259, NULL),
  (9, '罗衣', 'fb594a67a5bd4034879e10b39fc27be9', 100, 1537860270, NULL),
  (10, '两三事', '2660dd8bb5ef408b75d3ac78d7a45c72', 100, 1537860285, NULL),
  (11, '千仞岗', '9422a9632cae4f6c45a171c58a9f776c', 100, 1537860296, NULL),
  (12, '艾尚臣', 'd80e32cf4665fb60ef9b632466c835e2', 100, 1537860317, NULL),
  (13, '波司登', '78881593fcf3d651b5c0e69e6e8eba97', 100, 1537860332, NULL),
  (14, 'DKCHENPIN', 'e75777b3c40755d4d0febd7696452150', 100, 1537860350, NULL),
  (15, '薇咔拉', 'fcf3bc0c3842963e0ccd606312263f9c', 100, 1537860372, NULL);

--
-- Truncate table before insert `jshop_goods`
--

TRUNCATE TABLE `jshop_goods`;
--
-- Dumping data for table `jshop_goods`
--

INSERT INTO `jshop_goods`(`id`, `bn`, `name`, `brief`, `price`, `costprice`, `mktprice`, `image_id`, `goods_cat_id`, `goods_type_id`, `brand_id`, `is_nomal_virtual`, `marketable`, `stock`, `freeze_stock`, `weight`, `unit`, `intro`, `spes_desc`, `params`, `comments_count`, `view_count`, `buy_count`, `uptime`, `downtime`, `sort`, `is_recommend`, `is_hot`, `label_ids`, `new_spec`, `ctime`, `utime`, `isdel`) VALUES (1, '118336505', 'DKCHENPIN2018秋新款天丝中长款修身风衣外套女', '可调节袖 系带收腰设', 450.00, 0.00, 600.00, 'b419e4164d5726d057b2ae195f9a96df', 18, 1, 14, 1, 1, 198, 0, 400.00, '件', '<p><img class=\"desc_anchor img-ks-lazyload\" id=\"desc-module-1\" src=\"https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif\"/></p><p><a style=\"margin: 0px; padding: 0px; color: rgb(51, 85, 170); outline: 0px;\"></a></p><p style=\"margin-top: 0px; margin-bottom: 0px; padding: 0px; line-height: 1.4; color: rgb(64, 64, 64); font-family: tahoma, arial, 宋体, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\"><a href=\"https://meidian.play.m.jaeapp.com/?iid=991593&cpp=0\" target=\"_blank\" style=\"margin: 0px; padding: 0px; text-decoration-line: none; color: rgb(51, 85, 170); outline: 0px;\"><img alt=\"\" src=\"https://img.alicdn.com/imgextra/i1/356060330/O1CN011EJBA7NKQe5fuXI_!!356060330.jpg\" class=\"img-ks-lazyload\"/></a></p><p><a style=\"margin: 0px; padding: 0px; color: rgb(51, 85, 170); outline: 0px;\"></a></p><p><a style=\"margin: 0px; padding: 0px; color: rgb(51, 85, 170); outline: 0px;\"></a></p><p><img src=\"https://gdp.alicdn.com/imgextra/i2/356060330/O1CN011EJBA7UknUQjGCt_!!356060330.jpg\" class=\"img-ks-lazyload\"/></p><ul style=\"list-style-type: none;\" class=\" list-paddingleft-2\"><li><p><br/></p></li></ul><p><img class=\"desc_anchor img-ks-lazyload\" id=\"desc-module-2\" src=\"https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i4/356060330/TB2L0NmwhSYBuNjSsphXXbGvVXa-356060330.jpg\" class=\"img-ks-lazyload\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i2/356060330/TB26FUOnLuSBuNkHFqDXXXfhVXa-356060330.jpg\" class=\"img-ks-lazyload\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i4/356060330/TB2CinchHZnBKNjSZFhXXc.oXXa-356060330.jpg\" width=\"730\" height=\"1046\" class=\"img-ks-lazyload\"/></p><p><img class=\"desc_anchor img-ks-lazyload\" id=\"desc-module-3\" src=\"https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i3/356060330/TB2XcAnnIyYBuNkSnfoXXcWgVXa-356060330.jpg\" width=\"249\" height=\"47\" class=\"img-ks-lazyload\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i4/356060330/TB2eItzhP7nBKNjSZLeXXbxCFXa-356060330.jpg\" width=\"730\" height=\"1168\" class=\"img-ks-lazyload\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i4/356060330/TB20a1OwmBYBeNjy0FeXXbnmFXa-356060330.jpg\" width=\"608\" class=\"img-ks-lazyload\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i2/356060330/TB24SvFhHArBKNjSZFLXXc_dVXa-356060330.jpg\" width=\"730\" height=\"1168\" class=\"img-ks-lazyload\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i1/356060330/TB2QndxwbGYBuNjy0FoXXciBFXa-356060330.jpg\" width=\"608\" class=\"img-ks-lazyload\"/></p><p><img src=\"https://img.alicdn.com/imgextra/i4/356060330/TB2EntxwbGYBuNjy0FoXXciBFXa-356060330.jpg\" width=\"730\" height=\"1168\" class=\"img-ks-lazyload\"/></p><p>翻领垂性风衣</p><p>柔软亲肤面料 可调节袖口 饰有侧边口袋 配有同色腰带可调节腰身</p><p><img src=\"https://img.alicdn.com/imgextra/i4/356060330/TB2jlFQwk9WBuNjSspeXXaz5VXa-356060330.jpg\" width=\"249\" height=\"40\" class=\"img-ks-lazyload\"/></p><table width=\"730\"><tbody style=\"margin: 0px; padding: 0px;\"><tr style=\"margin: 0px; padding: 0px;\" class=\"firstRow\"><td width=\"363\" style=\"margin: 0px; padding: 0px; border-color: rgb(0, 0, 0);\"><img src=\"https://img.alicdn.com/imgextra/i1/356060330/TB2TVtZwh1YBuNjy1zcXXbNcXXa-356060330.jpg\" width=\"363\" height=\"581\" class=\"img-ks-lazyload\"/></td><td width=\"4\" style=\"margin: 0px; padding: 0px; border-color: rgb(0, 0, 0);\"><br/></td><td width=\"363\" style=\"margin: 0px; padding: 0px; border-color: rgb(0, 0, 0);\"><img src=\"https://img.alicdn.com/imgextra/i2/356060330/TB2BFJ2weuSBuNjy1XcXXcYjFXa-356060330.jpg\" width=\"363\" height=\"581\" class=\"img-ks-lazyload\"/></td></tr><tr style=\"margin: 0px; padding: 0px; font-size: 20px; text-align: center; line-height: 20px;\"><td style=\"margin: 0px; padding: 20px 0px 40px; border-color: rgb(0, 0, 0);\">里约红</td><td style=\"margin: 0px; padding: 20px 0px 40px; border-color: rgb(0, 0, 0);\"><br/></td><td style=\"margin: 0px; padding: 20px 0px 40px; border-color: rgb(0, 0, 0);\">蒸汽灰</td></tr></tbody></table><table width=\"730\"><tbody style=\"margin: 0px; padding: 0px;\"><tr style=\"margin: 0px; padding: 0px;\" class=\"firstRow\"><td colspan=\"3\" style=\"margin: 0px; padding: 0px; border-color: rgb(0, 0, 0);\"><img src=\"https://img.alicdn.com/imgextra/i3/356060330/TB2W1snnIyYBuNkSnfoXXcWgVXa-356060330.jpg\" width=\"730\" class=\"img-ks-lazyload\"/></td></tr></tbody></table><p><br/></p>', '', '', 0, 0, 0, NULL, NULL, 100, 1, 1, NULL, NULL, NULL, 1583739667, NULL);
INSERT INTO `jshop_goods`(`id`, `bn`, `name`, `brief`, `price`, `costprice`, `mktprice`, `image_id`, `goods_cat_id`, `goods_type_id`, `brand_id`, `is_nomal_virtual`, `marketable`, `stock`, `freeze_stock`, `weight`, `unit`, `intro`, `spes_desc`, `params`, `comments_count`, `view_count`, `buy_count`, `uptime`, `downtime`, `sort`, `is_recommend`, `is_hot`, `label_ids`, `new_spec`, `ctime`, `utime`, `isdel`) VALUES (2, '5446546546', '七匹狼羽绒服男短款2018冬季新款男士中年休闲爸爸装男装潮流外套', '8周年店庆，22号10点到11点 全场商品3件6折', 600.00, 590.00, 590.00, '6ee0f5a5ac6c53418852ee6cc78aeb48', 10, 1, 11, 1, 1, 98, 0, 0.00, '件', '<p><span style=\"margin: 0px; padding: 0px;\"></span></p><table width=\"790\"><tbody style=\"margin: 0px; padding: 0px;\"><tr style=\"margin: 0px; padding: 0px;\" class=\"firstRow\"><td style=\"margin: 0px; padding: 0px;\"><img src=\"https://gdp.alicdn.com/imgextra/i2/673765387/O1CN011pfI4eEVQ0tsN0d_!!673765387.jpg\" alt=\" 关联_01.jpg\"/></td></tr><tr style=\"margin: 0px; padding: 0px;\"><td style=\"margin: 0px; padding: 0px;\"><img src=\"https://gdp.alicdn.com/imgextra/i4/673765387/O1CN011pfI4d2HD5YEdXz_!!673765387.jpg\" alt=\"日常关联_02.jpg\"/></td></tr><tr style=\"margin: 0px; padding: 0px;\"><td style=\"margin: 0px; padding: 0px;\"><img src=\"https://gdp.alicdn.com/imgextra/i2/673765387/O1CN011pfI4dbubdTN6I3_!!673765387.jpg\" alt=\" 关联_02.jpg\"/></td></tr><tr style=\"margin: 0px; padding: 0px;\"><td style=\"margin: 0px; padding: 0px; word-break: break-all;\"><img src=\"https://gdp.alicdn.com/imgextra/i4/673765387/O1CN011pfI4cntvLq6gYj_!!673765387.jpg\" alt=\" 关联_03.jpg\"/><img src=\"https://img.alicdn.com/imgextra/i2/673765387/O1CN011pfI4SjnNEgZQh0_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/673765387/O1CN011pfI4RO6TCEQIvw_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/673765387/O1CN011pfI4QHbG78TFxQ_!!673765387.jpg\" class=\"img-ks-lazyload\" width=\"655\" height=\"920\"/><img src=\"https://img.alicdn.com/imgextra/i4/673765387/O1CN011pfI4RtZ8DLTMDY_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/673765387/O1CN011pfI4QkfUSd7rW9_!!673765387.jpg\" class=\"img-ks-lazyload\" width=\"768\" height=\"1090\"/><img src=\"https://img.alicdn.com/imgextra/i2/673765387/O1CN011pfI4QkfYdJGYEt_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/673765387/O1CN011pfI4RuPpXFhCoV_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/673765387/O1CN011pfI4PT18Z29Y2d_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/673765387/O1CN011pfI4L7zrmbNYMe_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/673765387/O1CN011pfI4Svq7HOnDG7_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/673765387/O1CN011pfI4SjltuQdcqx_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/673765387/O1CN011pfI4RuQMql2UKL_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/673765387/O1CN011pfI4UeVxXNXGAH_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/673765387/O1CN011pfI4SYAgWQx8nC_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/673765387/O1CN011pfI4RuQu5aBV68_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/673765387/O1CN011pfI4PT3PqFBsZ1_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/673765387/O1CN011pfI4RtYWvyEIyC_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/673765387/O1CN011pfI4QkhdXpsyzq_!!673765387.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/673765387/O1CN011pfI4PT2XqSiuGV_!!673765387.jpg\" class=\"img-ks-lazyload\"/></td></tr></tbody></table><p><br/><br/></p>', '', '', 0, 0, 0, NULL, NULL, 100, 1, 1, NULL, NULL, NULL, 1583739660, NULL);
INSERT INTO `jshop_goods`(`id`, `bn`, `name`, `brief`, `price`, `costprice`, `mktprice`, `image_id`, `goods_cat_id`, `goods_type_id`, `brand_id`, `is_nomal_virtual`, `marketable`, `stock`, `freeze_stock`, `weight`, `unit`, `intro`, `spes_desc`, `params`, `comments_count`, `view_count`, `buy_count`, `uptime`, `downtime`, `sort`, `is_recommend`, `is_hot`, `label_ids`, `new_spec`, `ctime`, `utime`, `isdel`) VALUES (4, '78678657865', '芙娜妮2018秋冬吊带连衣裙女两件套条纹针织打底衫背带PU皮裙套装', '品牌: 芙娜妮适用年龄: 25-29周岁尺码: S M L XL图案: 条纹风格: 街头街头: 欧美领型: 圆领腰型: 中腰衣门襟: 套头颜色分类: 红色袖型: 其他组合形式: 两件套', 200.00, 200.00, 200.00, 'fce63f9c69c6c4ec606b544fcc7610a2', 4, 1, 15, 1, 1, 18, 0, 0.00, '件', '<p><span style=\"margin: 0px; padding: 0px;\"></span></p><p>&nbsp;</p><p><br/></p><p><span style=\"text-decoration:line-through;\"><strong style=\"margin: 0px; padding: 0px;\"></strong></span></p><p><span style=\"text-decoration:line-through;\"><strong style=\"margin: 0px; padding: 0px;\"></strong></span></p><p><span style=\"margin: 0px; padding: 0px;\"></span></p><p>&nbsp;</p><p><br/></p><p><span style=\"text-decoration:line-through;\"><strong style=\"margin: 0px; padding: 0px;\"></strong></span></p><p><span style=\"text-decoration:line-through;\"><strong style=\"margin: 0px; padding: 0px;\"></strong></span></p><p><span style=\"margin: 0px; padding: 0px;\"></span></p><p>&nbsp;</p><p><br/></p><p><span style=\"text-decoration:line-through;\"><strong style=\"margin: 0px; padding: 0px;\"></strong></span></p><p><img class=\"desc_anchor img-ks-lazyload\" id=\"desc-module-1\" src=\"https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif\"/><img src=\"https://img.alicdn.com/imgextra/i2/736758035/O1CN0129E4r0DXqQB56xr_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4r0DXdxMNWfc_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/736758035/O1CN0129E4qrPklkfSb1Q_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/736758035/O1CN0129E4qugWFnVdi9z_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img class=\"desc_anchor img-ks-lazyload\" id=\"desc-module-2\" src=\"https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4r0cEVb02gru_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img class=\"desc_anchor img-ks-lazyload\" id=\"desc-module-3\" src=\"https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif\"/><img src=\"https://img.alicdn.com/imgextra/i3/736758035/O1CN0129E4r27gqRgoy5s_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/736758035/O1CN0129E4r14jhXApfi7_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4r0XTf29jB3f_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4r14jZFNpwke_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4r0DXVhNJ4y3_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4qrPku4ykal1_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/736758035/O1CN0129E4qs1U57QX8jF_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4qrPkyH4d8zf_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/736758035/O1CN0129E4qpdKv3Jadpn_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4qq0IAjL7qin_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/736758035/O1CN0129E4qs1V1KFIdzp_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/736758035/O1CN0129E4qrmHpEdYuys_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img class=\"desc_anchor img-ks-lazyload\" id=\"desc-module-4\" src=\"https://assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif\"/><img src=\"https://img.alicdn.com/imgextra/i4/736758035/O1CN0129E4qquo7wgQ2T0_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/736758035/O1CN0129E4qsGrStbpeLC_!!736758035.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/736758035/O1CN0129E4r1VkIrPvXuk_!!736758035.jpg\" class=\"img-ks-lazyload\"/></p><p><br/></p>', 'a:1:{s:6:\"颜色\";a:4:{i:1;s:6:\"红色\";i:2;s:6:\"白色\";i:3;s:6:\"绿色\";i:8;s:6:\"灰色\";}}', '', 0, 0, 0, NULL, NULL, 100, 1, 1, NULL, 'a:20:{i:1;a:1:{i:0;s:6:\"红色\";}i:2;a:1:{i:0;s:6:\"白色\";}i:3;a:1:{i:0;s:6:\"绿色\";}i:4;a:1:{i:0;s:6:\"蓝色\";}i:5;a:1:{i:0;s:6:\"紫色\";}i:6;a:1:{i:0;s:6:\"黑色\";}i:7;a:1:{i:0;s:6:\"棕色\";}i:8;a:1:{i:0;s:6:\"灰色\";}i:20;a:1:{i:0;s:7:\"规格1\";}i:21;a:1:{i:0;s:7:\"规格2\";}i:22;a:1:{i:0;s:7:\"规格3\";}i:23;a:1:{i:0;s:7:\"规格4\";}i:24;a:1:{i:0;s:7:\"规格5\";}i:25;a:1:{i:0;s:7:\"规格6\";}i:26;a:1:{i:0;s:7:\"规格7\";}i:27;a:1:{i:0;s:7:\"规格8\";}i:28;a:1:{i:0;s:7:\"规格9\";}i:29;a:1:{i:0;s:8:\"规格10\";}i:30;a:1:{i:0;s:3:\"111\";}i:31;a:1:{i:0;s:3:\"111\";}}', NULL, 1583739643, NULL);
INSERT INTO `jshop_goods`(`id`, `bn`, `name`, `brief`, `price`, `costprice`, `mktprice`, `image_id`, `goods_cat_id`, `goods_type_id`, `brand_id`, `is_nomal_virtual`, `marketable`, `stock`, `freeze_stock`, `weight`, `unit`, `intro`, `spes_desc`, `params`, `comments_count`, `view_count`, `buy_count`, `uptime`, `downtime`, `sort`, `is_recommend`, `is_hot`, `label_ids`, `new_spec`, `ctime`, `utime`, `isdel`) VALUES (5, '5465', '正品代购PINKO女包2018新款燕子包单肩斜挎压花徽章小香风链条包', 'PINKO质地: 牛皮皮革材质: 头层牛皮风格: 欧美时尚成色: 全新颜色分类: 热带雨林 黑色勋章 白色勋章 编织小香风 黑色压花 红色压花 黑色镶钻眼 黑色单排 红色单排 白色涂鸦 黑色涂鸦 粉黑涂鸦 黄黑涂鸦 黑色字母可乐 白色字母鳄鱼纹 红色字母 黑色枪排 红色love 白色珍珠 黑色珍珠 黑色镶钻双排 粉色镶钻双排 其他颜色拍下备注颜色有无夹层: 有背包方式: 单肩斜挎手提适用场景: 休闲', 200.00, 190.00, 190.00, '3e1c30dd1125de24e5395d3cd39ef5d8', 9, 1, 0, 1, 1, 200, 0, 0.00, '件', '<p><img src=\"https://img.alicdn.com/imgextra/i1/277648218/O1CN012AZtKHWohuSFUTy_!!277648218.jpg\"/><img src=\"https://img.alicdn.com/imgextra/i3/277648218/O1CN012AZtKITihkwYwMA_!!277648218.jpg\"/><img src=\"https://img.alicdn.com/imgextra/i2/277648218/O1CN012AZtKHWgaHav22O_!!277648218.jpg\"/><img src=\"https://img.alicdn.com/imgextra/i1/277648218/O1CN012AZtKIJ0juQHiIV_!!277648218.jpg\"/><img src=\"https://img.alicdn.com/imgextra/i4/277648218/O1CN012AZtKHYqNSSV8jJ_!!277648218.jpg\" class=\"\"/><img src=\"https://img.alicdn.com/imgextra/i4/277648218/O1CN012AZtKITiq2RVbr1_!!277648218.jpg\" class=\"\"/><img src=\"https://img.alicdn.com/imgextra/i4/277648218/O1CN012AZtKH5DAwyBhmw_!!277648218.jpg\" class=\"\"/><img src=\"https://img.alicdn.com/imgextra/i4/277648218/O1CN012AZtKH5De26pOtC_!!277648218.jpg\" class=\"\"/></p>', '', '', 0, 0, 0, NULL, NULL, 100, 1, 1, NULL, NULL, NULL, 1583739626, NULL);
INSERT INTO `jshop_goods`(`id`, `bn`, `name`, `brief`, `price`, `costprice`, `mktprice`, `image_id`, `goods_cat_id`, `goods_type_id`, `brand_id`, `is_nomal_virtual`, `marketable`, `stock`, `freeze_stock`, `weight`, `unit`, `intro`, `spes_desc`, `params`, `comments_count`, `view_count`, `buy_count`, `uptime`, `downtime`, `sort`, `is_recommend`, `is_hot`, `label_ids`, `new_spec`, `ctime`, `utime`, `isdel`) VALUES (7, 'G5837396190542', '太平鸟男装 夏季新品纯棉T恤男士黑色短袖潮字母印花韩版修身T恤', '品牌: PEACEBIRD/太平鸟货号: BWDA82170上市年份季节: 2018年夏季销售渠道类型: 纯电商(只在线上销售)厚薄: 常规面料分类: 棉毛布', 99.00, 99.00, 99.00, '4f86bd749dddd098403fc44a1e473643', 3, 1, 12, 1, 1, 307, 0, 0.00, '', '<p><img src=\"https://img.alicdn.com/imgextra/i1/173275708/TB21QRnabSYBuNjSspiXXXNzpXa_!!173275708.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/173275708/TB2jdGramtYBeNjSspkXXbU8VXa_!!173275708.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/173275708/TB2BUCGXUD.BuNjt_ioXXcKEFXa_!!173275708.jpg\" class=\"img-ks-lazyload\"/><br/><br/><img src=\"https://img.alicdn.com/imgextra/i2/173275708/TB2sbX_of9TBuNjy1zbXXXpepXa_!!173275708.jpg\" class=\"img-ks-lazyload\"/></p>', 'a:2:{s:6:\"颜色\";a:2:{i:1;s:6:\"红色\";i:2;s:6:\"白色\";}s:6:\"规格\";a:1:{i:21;s:7:\"规格2\";}}', '', 0, 0, 0, NULL, NULL, 100, 1, 1, NULL, 'a:20:{i:1;a:1:{i:0;s:6:\"红色\";}i:2;a:1:{i:0;s:6:\"白色\";}i:3;a:1:{i:0;s:6:\"绿色\";}i:4;a:1:{i:0;s:6:\"蓝色\";}i:5;a:1:{i:0;s:6:\"紫色\";}i:6;a:1:{i:0;s:6:\"黑色\";}i:7;a:1:{i:0;s:6:\"棕色\";}i:8;a:1:{i:0;s:6:\"灰色\";}i:20;a:1:{i:0;s:7:\"规格1\";}i:21;a:1:{i:0;s:7:\"规格2\";}i:22;a:1:{i:0;s:7:\"规格3\";}i:23;a:1:{i:0;s:7:\"规格4\";}i:24;a:1:{i:0;s:7:\"规格5\";}i:25;a:1:{i:0;s:7:\"规格6\";}i:26;a:1:{i:0;s:7:\"规格7\";}i:27;a:1:{i:0;s:7:\"规格8\";}i:28;a:1:{i:0;s:7:\"规格9\";}i:29;a:1:{i:0;s:8:\"规格10\";}i:30;a:1:{i:0;s:3:\"111\";}i:31;a:1:{i:0;s:3:\"111\";}}', NULL, 1583739619, NULL);
INSERT INTO `jshop_goods`(`id`, `bn`, `name`, `brief`, `price`, `costprice`, `mktprice`, `image_id`, `goods_cat_id`, `goods_type_id`, `brand_id`, `is_nomal_virtual`, `marketable`, `stock`, `freeze_stock`, `weight`, `unit`, `intro`, `spes_desc`, `params`, `comments_count`, `view_count`, `buy_count`, `uptime`, `downtime`, `sort`, `is_recommend`, `is_hot`, `label_ids`, `new_spec`, `ctime`, `utime`, `isdel`) VALUES (8, 'G5837396054543', '秋装连衣裙女2018新款七分袖职业名媛气质中长女装正式场合裙子', '组合形式: 单件裙长: 中长裙款式: 其他/other袖长: 七分袖领型: V领袖型: 其他腰型: 高腰衣门襟: 套头', 220.00, 220.00, 220.00, '1575deb7e275d200f105ba941d815ab5', 4, 1, 1, 1, 1, 198, 0, 0.00, '件', '<p><img src=\"https://img.alicdn.com/imgextra/i3/2856524584/TB2tATPXPfguuRjSszcXXbb7FXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2856524584/TB2cRqDG7CWBuNjy0FaXXXUlXXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/2856524584/TB2anvQXPfguuRjSspaXXaXVXXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/2856524584/O1CN011jjW1bbvbZB3eXb_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2856524584/TB26YJVG1OSBuNjy0FdXXbDnVXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2856524584/TB2aL6QXO6guuRkSmLyXXculFXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/2856524584/TB2UxkVyviSBuNkSnhJXXbDcpXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2856524584/TB29_8vyRmWBuNkSndVXXcsApXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2856524584/TB22SexG4SYBuNjSspjXXX73VXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2856524584/TB2z7dAlYZnBKNjSZFGXXbt3FXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2856524584/TB2mOajG1SSBuNjy0FlXXbBpVXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/2856524584/TB2MuQTysyYBuNkSnfoXXcWgVXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/2856524584/TB28b3EGWmWBuNjy1XaXXXCbXXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2856524584/TB2ahhOyMKTBuNkSne1XXaJoXXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/2856524584/TB25v9UG1ySBuNjy1zdXXXPxFXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2856524584/TB2cOJwG3mTBuNjy1XbXXaMrVXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/2856524584/TB2Mx.VyviSBuNkSnhJXXbDcpXa_!!2856524584.jpg\" class=\"img-ks-lazyload\"/></p>', '', '', 0, 0, 0, NULL, NULL, 100, 1, 1, NULL, NULL, NULL, 1583739605, NULL);
INSERT INTO `jshop_goods`(`id`, `bn`, `name`, `brief`, `price`, `costprice`, `mktprice`, `image_id`, `goods_cat_id`, `goods_type_id`, `brand_id`, `is_nomal_virtual`, `marketable`, `stock`, `freeze_stock`, `weight`, `unit`, `intro`, `spes_desc`, `params`, `comments_count`, `view_count`, `buy_count`, `uptime`, `downtime`, `sort`, `is_recommend`, `is_hot`, `label_ids`, `new_spec`, `ctime`, `utime`, `isdel`) VALUES (9, 'G5837395994964', '2018冷冬新款棉衣男士加肥加大码中长款棉服韩版潮流加厚棉袄外套', '品牌: 奢旺填充物: 仿丝棉货号: 之江A09--MY001基础风格: 青春流行上市年份季节: 2018年秋季厚薄: 常规材质成分: 聚酯纤维100%', 300.00, 320.00, 320.00, '052d4af90ec88851c10982bf7fdfa763', 3, 1, 0, 1, 1, 14, 0, 0.00, '', '<p style=\"margin-top: 1.12em; margin-bottom: 1.12em; padding: 0px; line-height: 1.4; color: rgb(64, 64, 64); font-family: tahoma, arial, 宋体, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\"><img src=\"https://img.alicdn.com/imgextra/i1/3241519092/O1CN012H2BUiq8IQ4o0CK_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/3241519092/O1CN012H2BUlGsJ4Rv2Ki_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/3241519092/O1CN012H2BUkwOjZozDmz_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/3241519092/O1CN012H2BUkQ3PVIfadP_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/3241519092/O1CN012H2BUlvRiGJDWNq_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/3241519092/O1CN012H2BUeU4i7DOzGT_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/3241519092/O1CN012H2BUk7cNM4cdYp_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/3241519092/O1CN012H2BUlGru3cnqdx_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/3241519092/O1CN012H2BUlWO9KMnnEy_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/3241519092/O1CN012H2BUkQ3HDPdPCa_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/3241519092/O1CN012H2BUm71zLUjqSm_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/3241519092/O1CN012H2BUlHHLecsvTr_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/3241519092/O1CN012H2BUkePx8ZFBU9_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/3241519092/O1CN012H2BUlWLfZe5Vkh_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/3241519092/O1CN012H2BUm72KBVirDk_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/3241519092/O1CN012H2BUlWMXcPhNtF_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/3241519092/O1CN012H2BUkkqjgWOeQm_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/3241519092/O1CN012H2BUlGsuNb8cRJ_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/3241519092/O1CN012H2BUkydbnFeP2J_!!3241519092.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/3241519092/O1CN012H2BUmJr3uZSfHG_!!3241519092.jpg\" class=\"img-ks-lazyload\"/></p><p><br/></p>', 'a:2:{s:6:\"颜色\";a:2:{i:1;s:6:\"红色\";i:8;s:6:\"灰色\";}s:6:\"规格\";a:2:{i:20;s:7:\"规格1\";i:21;s:7:\"规格2\";}}', '', 0, 0, 0, NULL, NULL, 100, 1, 1, NULL, 'a:20:{i:1;a:1:{i:0;s:6:\"红色\";}i:2;a:1:{i:0;s:6:\"白色\";}i:3;a:1:{i:0;s:6:\"绿色\";}i:4;a:1:{i:0;s:6:\"蓝色\";}i:5;a:1:{i:0;s:6:\"紫色\";}i:6;a:1:{i:0;s:6:\"黑色\";}i:7;a:1:{i:0;s:6:\"棕色\";}i:8;a:1:{i:0;s:6:\"灰色\";}i:20;a:1:{i:0;s:7:\"规格1\";}i:21;a:1:{i:0;s:7:\"规格2\";}i:22;a:1:{i:0;s:7:\"规格3\";}i:23;a:1:{i:0;s:7:\"规格4\";}i:24;a:1:{i:0;s:7:\"规格5\";}i:25;a:1:{i:0;s:7:\"规格6\";}i:26;a:1:{i:0;s:7:\"规格7\";}i:27;a:1:{i:0;s:7:\"规格8\";}i:28;a:1:{i:0;s:7:\"规格9\";}i:29;a:1:{i:0;s:8:\"规格10\";}i:30;a:1:{i:0;s:3:\"111\";}i:31;a:1:{i:0;s:3:\"111\";}}', NULL, 1583739599, NULL);
INSERT INTO `jshop_goods`(`id`, `bn`, `name`, `brief`, `price`, `costprice`, `mktprice`, `image_id`, `goods_cat_id`, `goods_type_id`, `brand_id`, `is_nomal_virtual`, `marketable`, `stock`, `freeze_stock`, `weight`, `unit`, `intro`, `spes_desc`, `params`, `comments_count`, `view_count`, `buy_count`, `uptime`, `downtime`, `sort`, `is_recommend`, `is_hot`, `label_ids`, `new_spec`, `ctime`, `utime`, `isdel`) VALUES (10, '456546456', '衬衫男士秋季韩版修身潮流百搭帅气休闲外套白色衬衣加绒长袖寸衫', '品牌: 简旗货号: 22TC144基础风格: 青春流行上市年份季节: 2018年夏季厚薄: 常规材质成分: 棉100%', 69.00, 69.00, 69.00, 'deed2e4f1c54f87eb672ad8c444ba229', 3, 1, 14, 1, 1, 5, 0, 0.00, '件', '<p><img src=\"https://img.alicdn.com/imgextra/i1/2946030476/O1CN011FO2zd40aYmt5wY_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2946030476/O1CN011FO2zbiY8W2jjEa_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2946030476/O1CN011FO2zd1oGpkgRMC_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/2946030476/O1CN011FO2zeQBnPbix4s_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2946030476/O1CN011FO2zcii03syGye_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2946030476/O1CN011FO2zcih82dl0xw_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2946030476/O1CN011FO2zbI4A4mJGsM_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2946030476/O1CN011FO2zauXrW7nP0Z_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2946030476/O1CN011FO2ze0qAnphiMZ_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2946030476/O1CN011FO2zcCrR94O4WN_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/2946030476/O1CN011FO2zdMnGCHeHBv_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i1/2946030476/O1CN011FO2zWYcbSJs8sj_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/2946030476/O1CN011FO2zd42KhfQwNO_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/2946030476/O1CN011FO2zd1q9EKSye8_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/2946030476/O1CN011FO2zcCsi8o8z02_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2946030476/O1CN011FO2zcoqSOk4nUJ_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/2946030476/O1CN011FO2zWYdXbCDH0U_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i4/2946030476/O1CN011FO2zcCtFPzBqGG_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i3/2946030476/O1CN011FO2zdbOufCSxEP_!!2946030476.jpg\" class=\"img-ks-lazyload\"/><img src=\"https://img.alicdn.com/imgextra/i2/2946030476/O1CN011FO2zd1p8tutFFE_!!2946030476.jpg\" class=\"img-ks-lazyload\"/></p>', 'a:2:{s:6:\"颜色\";a:2:{i:1;s:6:\"红色\";i:2;s:6:\"白色\";}s:6:\"规格\";a:1:{i:20;s:7:\"规格1\";}}', '', 0, 0, 0, NULL, NULL, 100, 1, 1, NULL, 'a:20:{i:1;a:1:{i:0;s:6:\"红色\";}i:2;a:1:{i:0;s:6:\"白色\";}i:3;a:1:{i:0;s:6:\"绿色\";}i:4;a:1:{i:0;s:6:\"蓝色\";}i:5;a:1:{i:0;s:6:\"紫色\";}i:6;a:1:{i:0;s:6:\"黑色\";}i:7;a:1:{i:0;s:6:\"棕色\";}i:8;a:1:{i:0;s:6:\"灰色\";}i:20;a:1:{i:0;s:7:\"规格1\";}i:21;a:1:{i:0;s:7:\"规格2\";}i:22;a:1:{i:0;s:7:\"规格3\";}i:23;a:1:{i:0;s:7:\"规格4\";}i:24;a:1:{i:0;s:7:\"规格5\";}i:25;a:1:{i:0;s:7:\"规格6\";}i:26;a:1:{i:0;s:7:\"规格7\";}i:27;a:1:{i:0;s:7:\"规格8\";}i:28;a:1:{i:0;s:7:\"规格9\";}i:29;a:1:{i:0;s:8:\"规格10\";}i:30;a:1:{i:0;s:3:\"111\";}i:31;a:1:{i:0;s:3:\"111\";}}', NULL, 1583739585, NULL);


--
-- Truncate table before insert `jshop_goods_images`
--

TRUNCATE TABLE `jshop_goods_images`;
--
-- Dumping data for table `jshop_goods_images`
--

INSERT  INTO `jshop_goods_images` (`goods_id`, `image_id`) VALUES
  (1, '8ef876168670ddb5c37941a867172f28'),
  (1, '1e6a5117d2a6082b565ad4d1c9e58c0d'),
  (1, '2606549d84e2ded23935ddcc2d562c43'),
  (1, '708ef31a7ba3d766a9a0424aeb8b8481'),
  (4, '6554e73ccd5a4b81b196b5ec3f412d0c'),
  (4, '9f906ea7c6d4d785946f3f54e3717c59'),
  (5, '3b509fa01f8b7fbc6163fd7c591314fd'),
  (5, '51496b49fdad4fc6f359cb6a29e6596a'),
  (5, '070fe78d0712b1ceaa3cdb7240f57246'),
  (7, '9b7051cf017685b0c3a48c4658d48272'),
  (7, '127fe7340aec7641c981c73a3ccff801'),
  (8, '26717fd8e2314719ec5b1f9f0dfccce3'),
  (8, 'ba2be1b85728a546038f1c783e28582c'),
  (8, 'fcbc476de3f2feb0c1eca55406b6a64c'),
  (9, 'f669696078688c02bbe721ac9d5f0a54'),
  (9, '897fad09b83c0f847f91843fdb2282a2'),
  (9, '54a8b2f0465e01d9e6f13104181728a5'),
  (10, '265cc34a43e319d39a454666a2592bf2'),
  (10, 'd6164aafe1d26463fb077b28e919c861'),
  (10, '9207c7644b6a6a3b7b29195a6a9d7a3b'),
  (11, 'd04e0aba7ce7aaeee6364aae8821797b');

--
-- Truncate table before insert `jshop_goods_params`
--

TRUNCATE TABLE `jshop_goods_params`;

INSERT INTO `jshop_goods_params`(`id`, `name`, `value`, `type`, `ctime`, `utime`) VALUES (1, '产地', '', 'text', 1582454016, 1582454016);
INSERT INTO `jshop_goods_params`(`id`, `name`, `value`, `type`, `ctime`, `utime`) VALUES (2, '材质', '', 'text', 1582454017, 1582454017);
INSERT INTO `jshop_goods_params`(`id`, `name`, `value`, `type`, `ctime`, `utime`) VALUES (3, '型号', '', 'text', 1582454017, 1582454017);

--
-- Truncate table before insert `jshop_goods_type`
--

TRUNCATE TABLE `jshop_goods_type`;
--
-- Dumping data for table `jshop_goods_type`
--


INSERT INTO `jshop_goods_type`(`id`, `name`, `params`) VALUES (1, '上衣', NULL);
INSERT INTO `jshop_goods_type`(`id`, `name`, `params`) VALUES (3, '通用类型', NULL);


--
-- Truncate table before insert `jshop_goods_type_params`
--

TRUNCATE TABLE `jshop_goods_type_params`;

INSERT INTO `jshop_goods_type_params`(`params_id`, `type_id`) VALUES (1, 3);
INSERT INTO `jshop_goods_type_params`(`params_id`, `type_id`) VALUES (2, 3);
INSERT INTO `jshop_goods_type_params`(`params_id`, `type_id`) VALUES (3, 3);


--
-- Truncate table before insert `jshop_goods_type_spec`
--

TRUNCATE TABLE `jshop_goods_type_spec`;
--
-- Dumping data for table `jshop_goods_type_spec`
--
INSERT INTO `jshop_goods_type_spec`(`id`, `name`, `sort`) VALUES (1, '颜色', 100);
INSERT INTO `jshop_goods_type_spec`(`id`, `name`, `sort`) VALUES (2, '规格', 100);
INSERT INTO `jshop_goods_type_spec`(`id`, `name`, `sort`) VALUES (3, '规格', 100);


--
-- Truncate table before insert `jshop_goods_type_spec_rel`
--

TRUNCATE TABLE `jshop_goods_type_spec_rel`;
--
-- Dumping data for table `jshop_goods_type_spec_rel`
--

INSERT INTO `jshop_goods_type_spec_rel`(`spec_id`, `type_id`) VALUES (1, 1);
INSERT INTO `jshop_goods_type_spec_rel`(`spec_id`, `type_id`) VALUES (2, 1);
INSERT INTO `jshop_goods_type_spec_rel`(`spec_id`, `type_id`) VALUES (3, 3);


--
-- Truncate table before insert `jshop_goods_type_spec_value`
--

TRUNCATE TABLE `jshop_goods_type_spec_value`;
--
-- Dumping data for table `jshop_goods_type_spec_value`
--
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (1, 1, '红色', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (2, 1, '白色', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (3, 1, '绿色', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (4, 1, '蓝色', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (5, 1, '紫色', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (6, 1, '黑色', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (7, 1, '棕色', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (8, 1, '灰色', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (20, 2, '规格1', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (21, 2, '规格2', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (22, 2, '规格3', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (23, 2, '规格4', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (24, 2, '规格5', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (25, 2, '规格6', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (26, 2, '规格7', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (27, 2, '规格8', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (28, 2, '规格9', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (29, 2, '规格10', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (30, 2, '111', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (31, 2, '111', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (32, 3, '规格1', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (33, 3, '规格2', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (34, 3, '规格3', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (35, 3, '规格4', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (36, 3, '规格5', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (37, 3, '规格6', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (38, 3, '规格7', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (39, 3, '规格8', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (40, 3, '规格9', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (41, 3, '规格10', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (42, 3, '规格11', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (43, 3, '规格12', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (44, 3, '规格13', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (45, 3, '规格14', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (46, 3, '规格15', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (47, 3, '规格16', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (48, 3, '规格17', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (49, 3, '规格18', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (50, 3, '规格19', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (51, 3, '规格20', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (52, 3, '规格21', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (53, 3, '规格22', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (54, 3, '规格23', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (55, 3, '规格24', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (56, 3, '规格25', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (57, 3, '规格26', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (58, 3, '规格27', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (59, 3, '规格28', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (60, 3, '规格29', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (61, 3, '规格30', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (62, 3, '规格31', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (63, 3, '规格32', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (64, 3, '规格33', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (65, 3, '规格34', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (66, 3, '规格35', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (67, 3, '规格36', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (68, 3, '规格37', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (69, 3, '规格38', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (70, 3, '规格39', 100);
INSERT INTO `jshop_goods_type_spec_value`(`id`, `spec_id`, `value`, `sort`) VALUES (71, 3, '规格40', 100);

--
-- Truncate table before insert `jshop_images`
--

TRUNCATE TABLE `jshop_images`;
--
-- Dumping data for table `jshop_images`
--

INSERT  INTO `jshop_images` (`id`, `name`, `url`, `path`, `type`, `ctime`, `isdel`) VALUES
  ('022bbf0657d3b9ed38a97dd746b2e84c', '0.jpg', 'https://b2c.jihainet.com/static/uploads/e8/87/87/5bca86083380f.jpg', '', 'local', 1539999240, NULL),
  ('0385f8221a24f7dd3ac09db1b78fd8d0', 'b3.jpg', 'https://b2c.jihainet.com/static/uploads/97/06/7d/5bd0271e42170.jpg', '', 'local', 1540368158, NULL),
  ('03ca799895a0b094bca75116e322539c', '裂帛.png', 'http://b2c.jihainet.com/static/uploads/2d/6f/d5/5ba9e21e12188.png', '', 'local', 1537860126, NULL),
  ('052d4af90ec88851c10982bf7fdfa763', 'O1CN012H2BUpZVbIA3OwR_!!0-item_pic.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/a4/cd/ec/5bc9a989cb351.jpg', '', 'local', 1539942793, NULL),
  ('070fe78d0712b1ceaa3cdb7240f57246', 'O1CN012AZtKI6Iz1SeIEH_!!277648218.jpg', 'https://b2c.jihainet.com/static/uploads/76/7e/81/5bc9a5c7bbc8e.jpg', '', 'local', 1539941831, NULL),
  ('127fe7340aec7641c981c73a3ccff801', 'TB2RYMOX4WYBuNjy1zkXXXGGpXa_!!173275708.jpg_430x43', 'https://b2c.jihainet.com/static/uploads/d1/d7/cb/5bc9a6dee7ebb.jpg', '', 'local', 1539942110, NULL),
  ('12c60848aba2425c7a79e18a7ee1b8f8', '翠岭77平米户型.jpg', 'https://b2c.jihainet.com/static/uploads/45/82/06/5bcc1fe98fed0.jpg', '', 'local', 1540104169, NULL),
  ('14b44959327363e274c8a2ba9dba3ace', '玖姿.png', 'http://b2c.jihainet.com/static/uploads/0e/f2/b1/5ba9e21f0dd1a.png', '', 'local', 1537860127, NULL),
  ('1575deb7e275d200f105ba941d815ab5', 'O1CN011jjW1HVm2eoMd1M_!!2856524584.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/ce/ae/8d/5bc9a81609585.jpg', '', 'local', 1539942422, NULL),
  ('160b5dfcb243deffb794ce3d0ac48431', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/3f/a6/7f/5bcd29de34a05.jpg', '', 'local', 1540172254, NULL),
  ('1ce148f164e099cfa15017eae9c0324d', '773245_limingsky.png', 'https://b2c.jihainet.com/static/uploads/7c/0a/fd/5bc9a3125560c.png', '', 'local', 1539941138, NULL),
  ('1e6a5117d2a6082b565ad4d1c9e58c0d', 'goods1_3.jpg', 'http://b2c.jihainet.com/static/uploads/9f/81/10/5ba9e786c45eb.jpg', '', 'local', 1537861510, NULL),
  ('24eea7da4b715ed23ed2e403a376223f', '未命名表单 (4).png', 'https://b2c.jihainet.com/static/uploads/84/38/75/5bc98bc9a619d.png', '', 'local', 1539935177, NULL),
  ('2606549d84e2ded23935ddcc2d562c43', 'goods1_1.jpg', 'http://b2c.jihainet.com/static/uploads/df/6d/df/5ba9e78ac77d0.jpg', '', 'local', 1537861514, NULL),
  ('265cc34a43e319d39a454666a2592bf2', 'O1CN011FO2zOzv9iYed2a_!!2946030476.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/08/d0/3a/5bc9ab837a41a.jpg', '', 'local', 1539943299, NULL),
  ('2660dd8bb5ef408b75d3ac78d7a45c72', '两三事.png', 'http://b2c.jihainet.com/static/uploads/a5/fc/e3/5ba9e21b64271.png', '', 'local', 1537860123, NULL),
  ('26717fd8e2314719ec5b1f9f0dfccce3', 'O1CN011jjW1JidazOFH2I_!!2856524584.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/e9/ac/a3/5bc9a8167a827.jpg', '', 'local', 1539942422, NULL),
  ('2776b0176aa478be959d762326c4e58f', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/ba/6b/4b/5bcd29715fa78.jpg', '', 'local', 1540172145, NULL),
  ('38b53dadcfeb35a77678a0a9cfdb7a7c', '茵曼.png', 'http://b2c.jihainet.com/static/uploads/a2/3d/6e/5ba9e21ccf87e.png', '', 'local', 1537860124, NULL),
  ('3b509fa01f8b7fbc6163fd7c591314fd', 'O1CN012AZtKFGQ1bonLYH_!!277648218.jpg', 'https://b2c.jihainet.com/static/uploads/b7/9f/72/5bc9a5c440801.jpg', '', 'local', 1539941828, NULL),
  ('3e1c30dd1125de24e5395d3cd39ef5d8', 'O1CN012AZtKGxGsMBKldW_!!277648218.jpg', 'https://b2c.jihainet.com/static/uploads/af/00/b7/5bc9a5b89bd9a.jpg', '', 'local', 1539941816, NULL),
  ('443c2d9ddb8b769ceddcd6f2bee5908b', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/92/70/83/5bc9a2bc7a634.jpg', '', 'local', 1539941052, NULL),
  ('4a6f179672c7f1ec02582eb104607dd2', 'TB1CYIFi9zqK1RjSZFjSuvlCFXa.jpg', 'https://b2c.jihainet.com/static/uploads/9f/c9/54/5bcd2b69d8e2d.jpg', '', 'local', 1540172649, NULL),
  ('4f86bd749dddd098403fc44a1e473643', 'TB1Qz.HX3mTBuNjy1XbXXaMrVXa_!!0-item_pic.jpg_430x4', 'https://b2c.jihainet.com/static/uploads/0b/b2/ff/5bc9a6de5361c.jpg', '', 'local', 1539942110, NULL),
  ('50f61b1f46866f1f7664cd4ab8ae62ec', 'TB1SOQOdr2pK1RjSZFsSuuNlXXa.jpg', 'https://b2c.jihainet.com/static/uploads/f5/f1/c2/5bcd2c57a660f.jpg', '', 'local', 1540172887, NULL),
  ('51496b49fdad4fc6f359cb6a29e6596a', 'O1CN012AZtKHCituSHjfW_!!277648218.jpg', 'https://b2c.jihainet.com/static/uploads/4c/28/57/5bc9a5c53aa6e.jpg', '', 'local', 1539941829, NULL),
  ('54a8b2f0465e01d9e6f13104181728a5', 'O1CN012H2BUk821QpMLUv_!!3241519092.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/99/99/1d/5bc9a98960531.jpg', '', 'local', 1539942793, NULL),
  ('55cbc9c9642052f18e989578ae89f4e0', 'b1.jpg', 'https://b2c.jihainet.com/static/uploads/cd/5a/00/5bd0271e324f1.jpg', '', 'local', 1540368158, NULL),
  ('56020c21a0e3b07e9906c1c6f06bee6a', '依嬅莎.jpg', 'http://b2c.jihainet.com/static/uploads/d6/a3/95/5ba9e21a032e0.jpg', '', 'local', 1537860122, NULL),
  ('6554e73ccd5a4b81b196b5ec3f412d0c', 'O1CN0129E4qsGtc2HGKtz_!!736758035.jpg_430x430q90.j', 'https://b2c.jihainet.com/static/uploads/b3/16/38/5bc9a50662303.jpg', '', 'local', 1539941638, NULL),
  ('6ee0f5a5ac6c53418852ee6cc78aeb48', '430x430q90.jpg', 'https://b2c.jihainet.com/static/uploads/8c/f8/53/5bc9a3b2916a4.jpg', '', 'local', 1539941298, NULL),
  ('708ef31a7ba3d766a9a0424aeb8b8481', 'goods1_5.jpg', 'http://b2c.jihainet.com/static/uploads/e9/bc/30/5ba9e786e2f66.jpg', '', 'local', 1537861510, NULL),
  ('78881593fcf3d651b5c0e69e6e8eba97', '波司登.png', 'http://b2c.jihainet.com/static/uploads/eb/72/fe/5ba9e21a61aa1.png', '', 'local', 1537860122, NULL),
  ('7e9d7a60a7d40c9e908c45ed092de558', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/f5/71/ea/5bcd2a29965d7.jpg', '', 'local', 1540172329, NULL),
  ('8809c039c5afd2af6610a3111bb7d4b7', 'u=3112331775,1705654587&fm=27&gp=0.jpg', '/static/uploads/b4/10/f7/5bab3d8e8847e.jpg', '', 'local', 1537949070, NULL),
  ('897fad09b83c0f847f91843fdb2282a2', 'O1CN012H2BUlWm7Rnugw5_!!3241519092.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/51/3a/27/5bc9a98957a8c.jpg', '', 'local', 1539942793, NULL),
  ('8ef876168670ddb5c37941a867172f28', 'goods1_4.jpg', 'http://b2c.jihainet.com/static/uploads/5b/ff/67/5ba9e78294c5a.jpg', '', 'local', 1537861506, NULL),
  ('8fec591f6b83a0edaa973e3ea35ae8ee', '430x430q90.jpg', 'https://b2c.jihainet.com/static/uploads/72/3e/1e/5bc9a293a44ee.jpg', '', 'local', 1539941011, NULL),
  ('9207c7644b6a6a3b7b29195a6a9d7a3b', 'O1CN011FO2zThCdx2pftI_!!2946030476.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/83/37/8a/5bc9ab85d0414.jpg', '', 'local', 1539943301, NULL),
  ('9422a9632cae4f6c45a171c58a9f776c', '千仞岗.png', 'http://b2c.jihainet.com/static/uploads/21/10/c9/5ba9e21b1c076.png', '', 'local', 1537860123, NULL),
  ('9b7051cf017685b0c3a48c4658d48272', 'TB2_LVcev9TBuNjy0FcXXbeiFXa_!!173275708.jpg_430x43', 'https://b2c.jihainet.com/static/uploads/58/57/c8/5bc9a6de50a60.jpg', '', 'local', 1539942110, NULL),
  ('9ca906f23d05dc9db1de552bfefa36fc', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/19/92/5a/5bc9a262427d4.jpg', '', 'local', 1539940962, NULL),
  ('9f906ea7c6d4d785946f3f54e3717c59', 'O1CN0129E4qt3dH4bv5Fl_!!736758035.png_430x430q90.j', 'https://b2c.jihainet.com/static/uploads/2e/1b/3b/5bc9a50900998.jpg', '', 'local', 1539941641, NULL),
  ('a422f452969f04e4493af4728eec3ab8', 'O1CN011EoK6MJ0F08K4mr_!!0-saturn_solar.jpg_270x270', 'https://b2c.jihainet.com/static/uploads/04/f9/ec/5bcd2ca278a34.jpg', '', 'local', 1540172962, NULL),
  ('a496465d657cd5fbeb90f10e25e16086', '123.jpg', 'https://b2c.jihainet.com/static/uploads/11/e9/e7/5bc9a31259dda.jpg', '', 'local', 1539941138, NULL),
  ('a5fd9ad6780b56a9ecece1d1ae38b18e', '9252150_174018365301_2.png', 'https://b2c.jihainet.com/static/uploads/8e/44/1c/5bc9a31283f70.png', '', 'local', 1539941138, NULL),
  ('aea2675adb10467d6dfbd92ab9321a33', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/7a/20/9c/5bcd2a537bf1f.jpg', '', 'local', 1540172371, NULL),
  ('af547e9f2513965d6eaf56b4dcc276ec', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/f2/1d/d0/5bcd2aa788869.jpg', '', 'local', 1540172455, NULL),
  ('b419e4164d5726d057b2ae195f9a96df', 'goods1_2.jpg', 'http://b2c.jihainet.com/static/uploads/b7/03/ed/5ba9e78922087.jpg', '', 'local', 1537861513, NULL),
  ('b55ea82d8c05abdc06da42331ac3820b', 'actAug_sharePic.jpg', 'https://b2c.jihainet.com/static/uploads/cd/57/0d/5bcdaa110ed6f.jpg', '', 'local', 1540205073, NULL),
  ('b68a51db26eb5c35e4f9b16f0c41d455', 'O1CN011DHW7IubkRgdFiS_!!0-saturn_solar.jpg_270x270', 'https://b2c.jihainet.com/static/uploads/fa/6c/24/5bcd2cd0edf0d.jpg', '', 'local', 1540173008, NULL),
  ('b9857035fa36ca4fbbeadc72e3ead0bb', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/34/13/4a/5bc9a1f05e5c6.jpg', '', 'local', 1539940848, NULL),
  ('b9d70b1b457f77b3796cd4229c9b0aa2', '水墨清华.jpg', 'http://b2c.jihainet.com/static/uploads/43/cb/eb/5ba9e21d41d70.jpg', '', 'local', 1537860125, NULL),
  ('ba2be1b85728a546038f1c783e28582c', 'O1CN011jjW1JBSBRDj7YA_!!2856524584.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/7e/18/48/5bc9a816ce21b.jpg', '', 'local', 1539942422, NULL),
  ('c667372acee834c7e4bc36fb51c3bee9', '110425022312-0.jpg', 'https://b2c.jihainet.com/static/uploads/b8/73/29/5bce8cb00be24.jpg', '', 'local', 1540263088, NULL),
  ('cd2c104a7d9100e12f7529d6b2c3f209', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/67/24/48/5bcd2af7116a3.jpg', '', 'local', 1540172535, NULL),
  ('d47c3377663dfbb925ea9a40a1488155', '250x250.jpg', 'https://b2c.jihainet.com/static/uploads/cc/15/4b/5bc9a02015a35.jpg', '', 'local', 1539940384, NULL),
  ('d6164aafe1d26463fb077b28e919c861', 'O1CN011FO2zV7NDZHC7HV_!!2946030476.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/46/4e/5e/5bc9ab85b6541.jpg', '', 'local', 1539943301, NULL),
  ('d621f6c85afaaf113fdbfd630bfefb65', 'b2.jpg', 'https://b2c.jihainet.com/static/uploads/46/1e/1c/5bd0271e3c8fa.jpg', '', 'local', 1540368158, NULL),
  ('d74ea8dc93080b9e4ae686f4a14dbe96', '160x160q90.jpg', 'https://b2c.jihainet.com/static/uploads/15/2d/d3/5bc9a0e70ac7f.jpg', '', 'local', 1539940583, NULL),
  ('d80e32cf4665fb60ef9b632466c835e2', '艾尚臣.png', 'http://b2c.jihainet.com/static/uploads/4f/4d/86/5ba9e21b16403.png', '', 'local', 1537860123, NULL),
  ('d8f07a89b3f8d35b74ed6b21c21a180e', 'u=1134602731,3644147114&fm=27&gp=0.jpg', 'https://b2c.jihainet.com/static/uploads/eb/96/5b/5bcac64e3986e.jpg', '', 'local', 1540015694, NULL),
  ('d9543b910358d02d1c60b859dd05c8aa', '定坊嘉园77户型.jpg', 'https://b2c.jihainet.com/static/uploads/7f/93/09/5bcc1fe997d8e.jpg', '', 'local', 1540104169, NULL),
  ('db1b8c0257b08a5542e10125fcef8fc1', '160x160q90.jpg', 'https://b2c.jihainet.com/static/uploads/17/22/8b/5bc9a118068ec.jpg', '', 'local', 1539940632, NULL),
  ('dcc56740b60745aac85a1b0433767a8d', '恩裳.png', 'http://b2c.jihainet.com/static/uploads/a7/fb/46/5ba9e21e33314.png', '', 'local', 1537860126, NULL),
  ('de21cc610a10fada67ef1974bdacf9fb', '160x160q90.jpg', 'https://b2c.jihainet.com/static/uploads/dd/da/cb/5bc9a14a0d106.jpg', '', 'local', 1539940682, NULL),
  ('dea52ed56c999be5583ba568aff9029a', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/e4/95/26/5bc9a2e8277ce.jpg', '', 'local', 1539941096, NULL),
  ('deed2e4f1c54f87eb672ad8c444ba229', 'O1CN011FO2zWdwMIOhIb0_!!0-item_pic.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/0c/4b/01/5bc9ab85cc335.jpg', '', 'local', 1539943301, NULL),
  ('def45acaee091fb6ab66634b76a5ce48', 'UR.jpeg', 'http://b2c.jihainet.com/static/uploads/d5/02/ee/5ba9e21d4ff8b.jpeg', '', 'local', 1537860125, NULL),
  ('e066155b201c578f5cd467616b0483b2', '拉夏贝尔.jpg', 'http://b2c.jihainet.com/static/uploads/54/82/d1/5ba9e21dcbd68.jpg', '', 'local', 1537860125, NULL),
  ('e214d5d3a8412df8f9c771a895425916', 'LMZ1537428876.png', 'https://b2c.jihainet.com/static/uploads/75/91/20/5bc9a3124dd0b.png', '', 'local', 1539941138, NULL),
  ('e51a16d2996d29939e7031434fbc3c29', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/05/3b/e8/5bcd2b11a2990.jpg', '', 'local', 1540172561, NULL),
  ('e75777b3c40755d4d0febd7696452150', 'DKCHENPIN.jpg', 'http://b2c.jihainet.com/static/uploads/9e/17/a8/5ba9e21a4d831.jpg', '', 'local', 1537860122, NULL),
  ('edc14b67f169e0512acf6f20a253d263', 'b4.jpg', 'https://b2c.jihainet.com/static/uploads/09/e1/ce/5bd0271e5fa2d.jpg', '', 'local', 1540368158, NULL),
  ('f0007202b0603ea8263da17b53ff0b7a', '0751ef339ada6598c11199991ddd14a9.jpg', '/static/uploads/e5/f9/a4/5bac52e9d3c2e.jpg', '', 'local', 1538020073, NULL),
  ('f4f98826b4db681ab21433c3fecc8326', '430x430q90.jpg', 'https://b2c.jihainet.com/static/uploads/8f/30/40/5bc9a224b81f4.jpg', '', 'local', 1539940900, NULL),
  ('f669696078688c02bbe721ac9d5f0a54', 'O1CN012H2BUk81kovjH1J_!!3241519092.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/76/6d/d5/5bc9a988051b3.jpg', '', 'local', 1539942792, NULL),
  ('f8b31c241ad346fceb55f83fcf2874bb', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/a8/9e/44/5bcd2a79f1ea0.jpg', '', 'local', 1540172409, NULL),
  ('fb594a67a5bd4034879e10b39fc27be9', '罗衣.png', 'http://b2c.jihainet.com/static/uploads/24/db/73/5ba9e21ce529b.png', '', 'local', 1537860124, NULL),
  ('fcbc476de3f2feb0c1eca55406b6a64c', 'O1CN011jjW1JieT3iWJDU_!!2856524584.jpg_430x430q90.', 'https://b2c.jihainet.com/static/uploads/99/3c/3e/5bc9a817e6b9d.jpg', '', 'local', 1539942423, NULL),
  ('fce63f9c69c6c4ec606b544fcc7610a2', 'O1CN0129E4qkPG80nYrJl_!!736758035.jpg_430x430q90.j', 'https://b2c.jihainet.com/static/uploads/dd/bd/35/5bc9a50154075.jpg', '', 'local', 1539941633, NULL),
  ('fcf3bc0c3842963e0ccd606312263f9c', '维卡拉.jpg', 'http://b2c.jihainet.com/static/uploads/8f/d0/58/5ba9e21a038aa.jpg', '', 'local', 1537860122, NULL),
  ('fe91f59b549fab49ca2db1ea9883f8d3', '270x270.jpg', 'https://b2c.jihainet.com/static/uploads/46/92/b7/5bcd2ac6d1d2c.jpg', '', 'local', 1540172486, NULL);

--
-- Truncate table before insert `jshop_products`
--

TRUNCATE TABLE `jshop_products`;
--
-- Dumping data for table `jshop_products`
--

INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (1, 1, NULL, 'P5378659179491', 450.00, 0.00, 600.00, 1, 198, 2, '颜色:红色,规格:规格1', 1, 'b419e4164d5726d057b2ae195f9a96df', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (23, 2, NULL, 'GP3232323', 600.00, 590.00, 590.00, 1, 98, 0, '颜色:白色,规格:规格1', 1, '6ee0f5a5ac6c53418852ee6cc78aeb48', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (33, 5, NULL, '564456', 200.00, 190.00, 190.00, 1, 200, 0, NULL, 1, '3e1c30dd1125de24e5395d3cd39ef5d8', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (35, 7, NULL, '222', 98.00, 98.00, 98.00, 1, 304, 0, '颜色:白色,规格:规格2', 2, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (40, 8, NULL, '21111', 220.00, 220.00, 220.00, 1, 198, 10, '颜色:红色,规格:规格1', 1, '1575deb7e275d200f105ba941d815ab5', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (59, 10, NULL, '61111-1', 69.00, 69.00, 69.00, 1, 2, 0, '颜色:红色,规格:规格1', 1, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (60, 10, NULL, '61111-2', 69.00, 69.00, 69.00, 1, 3, 0, '颜色:白色,规格:规格1', 2, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (62, 9, NULL, '23045242-1', 300.00, 320.00, 320.00, 1, 2, 0, '颜色:红色,规格:规格1', 1, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (63, 9, NULL, '23045242-2', 300.00, 320.00, 320.00, 1, 3, 0, '颜色:灰色,规格:规格1', 2, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (64, 9, NULL, '23045242-3', 300.00, 320.00, 320.00, 1, 4, 0, '颜色:红色,规格:规格2', 2, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (65, 9, NULL, '23045242-4', 300.00, 320.00, 320.00, 1, 5, 0, '颜色:灰色,规格:规格2', 2, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (67, 7, NULL, '1111-1', 99.00, 99.00, 99.00, 1, 3, 0, '颜色:红色,规格:规格2', 1, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (69, 4, NULL, '51111-2', 200.00, 200.00, 200.00, 1, 3, 0, '颜色:红色', 1, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (70, 4, NULL, '51111-3', 200.00, 200.00, 200.00, 1, 4, 0, '颜色:白色', 2, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (71, 4, NULL, '51111-4', 200.00, 200.00, 200.00, 1, 5, 0, '颜色:绿色', 2, '', NULL);
INSERT INTO `jshop_products`(`id`, `goods_id`, `barcode`, `sn`, `price`, `costprice`, `mktprice`, `marketable`, `stock`, `freeze_stock`, `spes_desc`, `is_defalut`, `image_id`, `isdel`) VALUES (72, 4, NULL, '51111-9', 200.00, 200.00, 200.00, 1, 6, 0, '颜色:灰色', 2, '', NULL);
