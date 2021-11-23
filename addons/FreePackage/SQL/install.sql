ALTER TABLE `jshop_goods`
ADD COLUMN `is_combo` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否是套餐商品 1=是 2=不是';

ALTER TABLE `jshop_order_items`
ADD COLUMN `is_free` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否免单商品 0=不是 1=是';