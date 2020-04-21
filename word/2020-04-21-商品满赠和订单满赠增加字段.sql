ALTER TABLE `jshop_order`
ADD COLUMN `giveaway` varchar(5000) NULL COMMENT '订单赠送商品信息' AFTER `coupon_pmt`;

ALTER TABLE `jshop_order_items`
  ADD COLUMN `giveaway` varchar(5000) NULL DEFAULT '' COMMENT '赠品信息' AFTER `addon`;