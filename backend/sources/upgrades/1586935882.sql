ALTER TABLE `prediction` ADD COLUMN `win` tinyint(1) NOT NULL AFTER `single_win`;
ALTER TABLE `article` ADD COLUMN `sub_category_id` bigint NOT NULL AFTER `category_id`;