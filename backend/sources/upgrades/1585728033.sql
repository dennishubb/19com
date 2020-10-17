ALTER TABLE `role` ADD COLUMN `disabled` TINYINT(1) NOT NULL AFTER `description`;
ALTER TABLE `role` ADD COLUMN `created_at` datetime NOT NULL AFTER `disabled`;
ALTER TABLE `role` ADD COLUMN `updated_at` datetime NOT NULL AFTER `created_at`;