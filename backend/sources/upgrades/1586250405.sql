CREATE TABLE IF NOT EXISTS `area` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`category_id` bigint(20) NOT NULL,
    `name_en` varchar(255) NOT NULL,
    `name_zh` varchar(255) NOT NULL,
    `name_zht` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `country` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`category_id` bigint(20) NOT NULL,
	`area_id` bigint(20) NOT NULL,
    `name_en` varchar(255) NOT NULL,
    `name_zh` varchar(255) NOT NULL,
    `name_zht` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `league` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`category_id` bigint(20) NOT NULL,
	`area_id` bigint(20) NOT NULL,
	`country_id` bigint(20) NOT NULL,
    `name_en` varchar(255) NOT NULL,
    `name_zh` varchar(255) NOT NULL,
    `name_zht` varchar(255) NOT NULL,
	`shortname_en` varchar(255) NOT NULL,
    `shortname_zh` varchar(255) NOT NULL,
    `shortname_zht` varchar(255) NOT NULL,
    `logo` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `team` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`league_id` bigint(20) NOT NULL,
	`category_id` bigint(20) NOT NULL,
    `name_en` varchar(255) NOT NULL,
    `name_zh` varchar(255) NOT NULL,
    `name_zht` varchar(255) NOT NULL,
	`shortname_en` varchar(255) NOT NULL,
    `shortname_zh` varchar(255) NOT NULL,
    `shortname_zht` varchar(255) NOT NULL,
    `logo` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `event` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`category_id` bigint(20) NOT NULL,
	`league_id` bigint(20) NOT NULL,
	`home_team_id` bigint(20) NOT NULL,
    `away_team_id` bigint(20) NOT NULL,
    `match_at` datetime NOT NULL,
	`prediction_end_at` datetime NOT NULL,
    `round` int NOT NULL,
    `handicap_home` decimal(3,2) NOT NULL,
	`handicap_away` decimal(3,2) NOT NULL,
	`over_under_home` decimal(3,2) NOT NULL,
    `over_under_away` decimal(3,2) NOT NULL,
	`single_home` decimal(3,2) NOT NULL,
    `single_tie` decimal(3,2) NOT NULL,
	`single_away` decimal(3,2) NOT NULL,
	`editor_note` text NOT NULL,
	`chatroom_id` bigint(20) NOT NULL,
	`disabled` tinyint(1) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prediction` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`user_id` bigint(20) NOT NULL,
	`event_id` bigint(20) NOT NULL,
	`amount` decimal(20,2) NOT NULL,
	`status` varchar(255) NOT NULL,
	`disabled` tinyint(1) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `credit` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`display` bigint(20) NOT NULL,
	`disabled` tinyint(1) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `transaction` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`user_id` bigint(20) NOT NULL,
	`amount` decimal(20,2) NOT NULL,
	`reference_id` bigint(20) NOT NULL,
	`credit_id` bigint(20) NOT NULL,
	`subject` varchar(255) NOT NULL,
	`remark` text NOT NULL,
	`from_id` bigint(20) NOT NULL,
	`to_id` bigint(20) NOT NULL,
	`deleted` tinyint(1) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `accounting` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`credit` bigint(20) NOT NULL,
	`debit` decimal(20,2) NOT NULL,
	`user_id` bigint(20) NOT NULL,
	`from_id` bigint(20) NOT NULL,
	`to_id` bigint(20) NOT NULL,
	`reference_id` bigint(20) NOT NULL,
	`credit_id` bigint(20) NOT NULL,
	`deleted` tinyint(1) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `balance` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`user_id` bigint(20) NOT NULL,
	`credit_id` bigint(20) NOT NULL,
	`date` date NOT NULL,
	`balance` decimal(20,2) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;