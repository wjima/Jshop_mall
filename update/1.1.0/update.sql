ALTER TABLE `jshop_bill_payments`
DROP COLUMN `generate_params`;


-- 更新权限菜单
UPDATE `jshop_operation` SET `code` = 'Administrator' WHERE `id` = 416
