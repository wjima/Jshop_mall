ALTER TABLE `jshop_pintuan_rule`
ADD COLUMN `max_nums` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '每人限购数量' AFTER `discount_amount`,
ADD COLUMN `max_goods_nums` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '每个商品活动数量' AFTER `max_nums`;