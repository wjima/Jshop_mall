alter table jshop_user_tocash add column withdrawals decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '提现服务费' after card_number;


UPDATE `jshop_operation` SET `code` = 'CarouselSeat' WHERE code = 'AdvertPosition' and type = 'c' and parent_id = 2;
UPDATE `jshop_operation` SET `code` = 'Carousel' WHERE code = 'Advertisement' and type = 'c' and parent_id = 2;

INSERT INTO `jshop_operation`
(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES
('535', '2', '报表统计', 'Report', 'c', '2', '1', '210'),
('536', '535', '订单销量', 'order', 'a', '535', '1', '100'),
('537', '535', '财务收款', 'payments', 'a', '535', '1', '100'),
('538', '535', '商品销量', 'goods', 'a', '535', '1', '100');