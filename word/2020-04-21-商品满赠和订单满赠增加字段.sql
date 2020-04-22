ALTER TABLE `order`
ADD COLUMN `giveaway` varchar(5000) NULL COMMENT '订单赠送商品信息' AFTER `coupon_pmt`;

ALTER TABLE `order_items`
ADD COLUMN `giveaway` varchar(5000) NULL COMMENT '商品赠品信息' AFTER `combination_goods_ids`;