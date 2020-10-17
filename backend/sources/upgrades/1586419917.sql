CREATE TABLE IF NOT EXISTS `result` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
	`event_id` bigint(20) NOT NULL,
	`team_id` bigint(20) NOT NULL,
	`handicap` tinyint NOT NULL COMMENT '1=home, 2=away, 3=tie',
	`over_under` tinyint NOT NULL COMMENT '1=home, 2=away, 3=tie',
	`single` tinyint NOT NULL COMMENT '1=home, 2=away, 3=tie',
	`handicap_odds` decimal(3,2) NOT NULL,
	`handicap_bet` decimal(3,2) NOT NULL,
	`over_under_odds` decimal(3,2) NOT NULL,
	`over_under_bet` decimal(3,2) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8