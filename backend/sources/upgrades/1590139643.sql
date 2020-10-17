CREATE TABLE IF NOT EXISTS `message_report` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`user_id` bigint NOT NULL,
	`message_id` bigint NOT NULL,
	`report` text NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4