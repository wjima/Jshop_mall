ALTER TABLE `user` ADD `point` INT(11) UNSIGNED NOT NULL COMMENT '积分' AFTER `balance`;
DROP TABLE seller_user;