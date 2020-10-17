CREATE TABLE IF NOT EXISTS `tutorial` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`content` text NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4