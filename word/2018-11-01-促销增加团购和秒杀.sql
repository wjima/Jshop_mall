ALTER TABLE `jshop_promotion` CHANGE `type` `type` SMALLINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '类型：1促销，2优惠券，3团购，4秒杀';


ALTER TABLE `jshop_promotion` ADD `params` TEXT NULL COMMENT '其它参数' AFTER `auto_receive`;