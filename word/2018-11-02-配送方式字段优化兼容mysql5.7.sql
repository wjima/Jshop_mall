ALTER TABLE `jshop_ship`
MODIFY COLUMN `area_fee`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '地区配送费用' AFTER `free_postage`,
MODIFY COLUMN `goodsmoney`  decimal(20,2) NULL DEFAULT 0.00 COMMENT '商品总额满多少免运费' AFTER `area_fee`;