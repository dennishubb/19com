CREATE TABLE IF NOT EXISTS `level` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`description` text NOT NULL,
	`points` decimal(10,2) NOT NULL,
	`top_ten` int NOT NULL,
	`upload_id` bigint NOT NULL,
	`system` tinyint NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `top_ten` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`user_id` bigint NOT NULL,
	`category_id` bigint NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `prediction_rate` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`user_id` bigint NOT NULL,
	`category_id` bigint NOT NULL,
	`win_count` int NOT NULL,
	`lose_count` int NOT NULL,
	`rate` decimal(10,2) NOT NULL,
	`disabled` tinyint NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `gift` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`category_id` bigint NOT NULL,
	`sub_category_id` bigint NOT NULL,
	`name` varchar(255) NOT NULL,
	`url` varchar(255) NOT NULL,
	`upload_id` bigint NOT NULL,
	`points` decimal(10,2) NOT NULL,
	`size` text NOT NULL,
	`color` text NOT NULL,
	`amount` decimal(10,2) NOT NULL,
	`start_at` datetime NOT NULL,
	`end_at` datetime NOT NULL,
	`disabled` tinyint NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `gift_redeem` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`gift_id` bigint NOT NULL,
	`user_id` bigint NOT NULL,
	`admin_id` bigint NOT NULL,
	`name` varchar(255) NOT NULL,
	`phone` varchar(255) NOT NULL,
	`address` text NOT NULL,
	`remark` text NOT NULL,
	`tracking_no` varchar(255) NOT NULL,
	`status` varchar(255) NOT NULL comment 'pending, reject, approve, history',
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `promotion` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`name` text NOT NULL,
	`upload_id_big` bigint NOT NULL,
	`upload_id_medium` bigint NOT NULL,
	`upload_id_small` bigint NOT NULL,
	`start_at` datetime NOT NULL,
	`end_at` datetime NOT NULL,
	`settle_at` datetime NOT NULL,
	`level_id` bigint NOT NULL,
	`limitation` varchar(255) NOT NULL comment 'daily, monthly, once',
	`introduction` text NOT NULL,
	`display_method` varchar(255) NOT NULL comment 'url, popup',
	`sign_up` tinyint NOT NULL,
	`disabled` tinyint NOT NULL,
	`status` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `promotion_redeem` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`promotion_id` bigint NOT NULL,
	`user_id` bigint NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `adjustment` (
    `id` bigint NOT NULL AUTO_INCREMENT,
	`user_id` bigint NOT NULL,
	`reference_id` bigint NOT NULL,
	`points_before` decimal(10,2) NOT NULL,
	`points` decimal(10,2) NOT NULL,
	`points_after` decimal(10,2) NOT NULL,
	`points_id` bigint NOT NULL,
	`voucher_before` decimal(10,2) NOT NULL,
	`voucher` decimal(10,2) NOT NULL,
	`voucher_after` decimal(10,2) NOT NULL,
	`voucher_id` bigint NOT NULL,
	`admin_id` bigint NOT NULL,
	`adjustment_count` int NOT NULL,
	`remark` text NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `user` ADD COLUMN `gift_redeem_count` int NOT NULL after `article_count`;
ALTER TABLE `user` ADD COLUMN `level_id` bigint NOT NULL after `role_id`;
ALTER TABLE `user` ADD COLUMN `system` tinyint NOT NULL after `disabled`;
ALTER TABLE `category` ADD COLUMN `type` varchar(255) NOT NULL AFTER `disabled`;
ALTER TABLE `level` ADD COLUMN `voucher` decimal(10,2) NOT NULL AFTER upload_id;
ALTER TABLE `level` ADD COLUMN `ticket` decimal(10,2) NOT NULL AFTER voucher;
ALTER TABLE `level` CHANGE COLUMN `description` `reward_description` text NOT NULL;
ALTER TABLE `promotion` ADD `type` varchar(255) NOT NULL AFTER `name`;
ALTER TABLE `adjustment` ADD `latest` tinyint NOT NULL AFTER `remark`;
ALTER TABLE `gift` ADD COLUMN `size_type` varchar(255) NOT NULL AFTER `size`;