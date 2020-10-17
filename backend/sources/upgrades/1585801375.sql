CREATE TABLE IF NOT EXISTS `category` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `display` VARCHAR(255) NOT NULL,
    `description` text NOT NULL,
    `parent_id` bigint(20) NOT NULL,
    `sort` int NOT NULL,
	`disabled` tinyint(1) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;