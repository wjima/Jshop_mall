ALTER TABLE `jshop_order`
ADD COLUMN `point`  int(10) UNSIGNED NULL AFTER `tax_title`,
ADD COLUMN `point_money`  decimal(10,2) NULL AFTER `point`;