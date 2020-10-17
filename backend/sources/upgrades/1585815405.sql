ALTER TABLE `category` ADD COLUMN `url` VARCHAR (255) NOT NULL after `id`;
ALTER TABLE `category` ADD COLUMN `upload_id` BIGINT(20) NOT NULL AFTER `parent_id`;