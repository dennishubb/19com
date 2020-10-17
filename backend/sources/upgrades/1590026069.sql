CREATE TABLE IF NOT EXISTS `message_like` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`user_id` bigint NOT NULL,
	`message_id` bigint NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

ALTER TABLE `user` ADD COLUMN `upload_id` bigint NOT NULL AFTER `role_id`;

CREATE TABLE IF NOT EXISTS `prediction_top_ten` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`user_id` bigint NOT NULL,
	`event_id` bigint NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `prediction_top_ten_unlock` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`user_id` bigint NOT NULL,
	`prediction_top_ten_id` bigint NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `prediction_user_favourite` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`user_id` bigint NOT NULL,
	`prediction_id` bigint NOT NULL,
	`prediction_type` varchar(255) NOT NULL comment 'handicap, over_under, single',
	`prediction_bet` varchar(255) NOT NULL comment 'home, away, tie',
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;