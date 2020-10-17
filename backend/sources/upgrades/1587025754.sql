CREATE TABLE IF NOT EXISTS `seo_module` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `site` ADD COLUMN `module_id` bigint NOT NULL AFTER `description`;