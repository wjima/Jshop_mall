ALTER TABLE `jshop_images`
MODIFY COLUMN `type` enum('web','Local','Aliyun') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'local' COMMENT '存储引擎' AFTER `path`;