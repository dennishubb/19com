CREATE TABLE IF NOT EXISTS `fan_zone` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`upload_id` bigint NOT NULL,
	`url` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4