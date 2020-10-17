CREATE TABLE IF NOT EXISTS `season_list` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `season_id` bigint NOT NULL,
	`category_id` bigint NOT NULL,
	`league_api_id` bigint NOT NULL,
	`season` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `season_matches` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `match_id` bigint NOT NULL,
    `season_id` bigint NOT NULL,
	`category_id` bigint NOT NULL,
	`league_api_id` bigint NOT NULL,
    `match_type` int NOT NULL,
	`status` int NOT NULL,
    `match_time` int NOT NULL,
    `home_team_id` bigint NOT NULL,
    `away_team_id` bigint NOT NULL,
	`home_score` int NOT NULL,
	`away_score` int NOT NULL,
    `venue_id` bigint NOT NULL,
	`round_stage_id` bigint NOT NULL,
	`round_num` int NOT NULL,
	`group_num` int NOT NULL,
	`home_position` int NOT NULL,
	`away_position` int NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `prediction_points` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `handicap_win` tinyint NOT NULL,
	`over_under_win` tinyint NOT NULL,
	`single_win` tinyint NOT NULL,
	`points` decimal(10,4) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `forget_password` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` bigint NOT NULL,
	`phone` varchar(255) NOT NULL,
	`verification_code` varchar(255) NOT NULL,
	`status` tinyint NOT NULL,
    `created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reset_password` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` bigint NOT NULL,
	`verification_code` varchar(255) NOT NULL,
	`old_password` varchar(255) NOT NULL,
	`new_password` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reset_password` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` bigint NOT NULL,
	`verification_code` varchar(255) NOT NULL,
	`old_password` varchar(255) NOT NULL,
	`new_password` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `prediction_stats` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` bigint NOT NULL,
	`category_id` bigint NOT NULL,
    `prediction_count` int NOT NULL,
    `prediction_total_count` int NOT NULL,
    `prediction_participation_rate` decimal(10,2) NOT NULL,
    `win_rate` decimal(10,2) NOT NULL,
    `top_ten_count` int NOT NULL,
    `top_ten_total_count` int NOT NULL,
    `month` int NOT NULL,
    `year` int NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_level_up` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` bigint NOT NULL,
	`level_id` bigint NOT NULL,
    `claimed` tinyint NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `top_ten_rate` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `category_id` bigint NOT NULL,
	`league_id` bigint NOT NULL,
    `min_rate` decimal(10,2) NOT NULL,
    `prediction_count` int NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `feedback` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `type` varchar(255) NOT NULL,
	`message` text NOT NULL,
	`email` varchar(255) NOT NULL,
	`status` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `season_player_stats_basketball` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `category_id` bigint NOT NULL,
	`season_id` bigint NOT NULL,
    `player_id` bigint NOT NULL,
    `team_id` bigint NOT NULL,
    `league_id` bigint NOT NULL,
	`player_name` varchar(255) NOT NULL,
	`team_name` varchar(255) NOT NULL,
    `scope` tinyint NOT NULL,
    `matches` SMALLINT NOT NULL,
    `first` tinyint NOT NULL,
    `court` tinyint NOT NULL,
    `minutes_played` smallint NOT NULL,
    `points` int NOT NULL,
    `two_points_scored` int NOT NULL,
    `two_points_total` int NOT NULL,
    `two_points_accuracy` decimal(5,2) NOT NULL,
    `three_points_scored` int NOT NULL,
    `three_points_total` int NOT NULL,
    `three_points_accuracy` decimal(5,2) NOT NULL,
    `field_goals_scored` int NOT NULL,
    `field_goals_total` int NOT NULL,
    `field_goals_accuracy` decimal(5,2) NOT NULL,
    `free_throw_scored` int NOT NULL,
    `free_throw_total` int NOT NULL,
    `free_throw_accuracy` decimal(5,2) NOT NULL,
    `personal_fouls` SMALLINT NOT NULL,
    `rebounds` SMALLINT NOT NULL,
    `defensive_rebounds` SMALLINT NOT NULL,
    `offensive_rebounds` SMALLINT NOT NULL,
    `assists` SMALLINT NOT NULL,
    `turnovers` SMALLINT NOT NULL,
    `steals` SMALLINT NOT NULL,
    `blocks` SMALLINT NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `season_ranking_basketball` (
	`id` bigint NOT NULL AUTO_INCREMENT,
	`season_id` bigint NOT NULL,
    `team_id` bigint NOT NULL,
    `league_id` bigint NOT NULL,
    `scope` tinyint NOT NULL,
	`name` varchar(255) NOT NULL,
	`team_name` varchar(255) NOT NULL,
    `position` int NOT NULL,
	`diff_avg` decimal(5,2) NOT NULL,
	`streaks` int NOT NULL,
	`won` int NOT NULL,
	`lost` int NOT NULL,
	`home` varchar(255) NOT NULL,
	`away` varchar(255) NOT NULL,
	`points_avg` decimal(5,2) NOT NULL,
	`points_against_avg` decimal(5,2) NOT NULL,
	`last_ten` varchar(255) NOT NULL,
	`division` varchar(255) NOT NULL,
	`game_back` varchar(255) NOT NULL,
	`conference` varchar(255) NOT NULL,
	`win_rate` decimal(5,2) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `season_player_stats_soccer` (
	`id` bigint NOT NULL AUTO_INCREMENT,
    `category_id` bigint NOT NULL,
	`season_id` bigint NOT NULL,
    `player_id` bigint NOT NULL,
    `team_id` bigint NOT NULL,
    `league_id` bigint NOT NULL,
	`player_name` varchar(255) NOT NULL,
	`team_name` varchar(255) NOT NULL,
    `rating` int NOT NULL,
    `matches` int NOT NULL,
    `first` tinyint NOT NULL,
    `goals` int NOT NULL,
    `minutes_played` smallint NOT NULL,
    `red_cards` int NOT NULL,
	`yellow_cards` int NOT NULL,
	`shots` int NOT NULL,
	`shots_on_target` int NOT NULL,
	`dribble` int NOT NULL,
	`dribble_success` int NOT NULL,
	`clearances` int NOT NULL,
	`blocked_shots` int NOT NULL,
	`interceptions` int NOT NULL,
	`tackles` int NOT NULL,
	`passes` int NOT NULL,
	`passes_accuracy` int NOT NULL,
	`key_passes` int NOT NULL,
	`crosses` int NOT NULL,
	`crosses_accuracy` int NOT NULL,
	`long_balls` int NOT NULL,
	`long_balls_accuracy` int NOT NULL,
	`duels` int NOT NULL,
	`duels_won` int NOT NULL,
	`dispossessed` int NOT NULL,
	`fouls` int NOT NULL,
	`was_fouled` int NOT NULL,
	`saves` int NOT NULL,
	`punches` int NOT NULL,
	`runs_out` int NOT NULL,
	`runs_out_success` int NOT NULL,
	`good_high_claim` int NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `season_ranking_soccer` (
	`id` bigint NOT NULL AUTO_INCREMENT,
	`season_id` bigint NOT NULL,
    `team_id` bigint NOT NULL,
    `league_id` bigint NOT NULL,
	`name` varchar(255) NOT NULL,
	`team_name` varchar(255) NOT NULL,
    `position` int NOT NULL,
	`conference` varchar(255) NOT NULL,
	`points` int NOT NULL,
	`deduct_points` int NOT NULL,
	`note` varchar(255) NOT NULL,
	`won` int NOT NULL,
	`draw` int NOT NULL,
	`lost` int NOT NULL,
	`total` int NOT NULL,
	`goals` int NOT NULL,
	`goals_against` int NOT NULL,
	`goals_diff` int NOT NULL,
	`home_points` int NOT NULL,
	`home_position` int NOT NULL,
	`home_total` int NOT NULL,
	`home_won` int NOT NULL,
	`home_draw` int NOT NULL,
	`home_loss` int NOT NULL,
	`home_goals` int NOT NULL,
	`home_goals_against` int NOT NULL,
	`home_goals_diff` int NOT NULL,
	`away_points` int NOT NULL,
	`away_position` int NOT NULL,
	`away_total` int NOT NULL,
	`away_won` int NOT NULL,
	`away_draw` int NOT NULL,
	`away_loss` int NOT NULL,
	`away_goals` int NOT NULL,
	`away_goals_against` int NOT NULL,
	`away_goals_diff` int NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `upload` ADD COLUMN `md5` varchar(255) NOT NULL AFTER `url`;
ALTER TABLE `user` ADD COLUMN `points` decimal(10,4) NOT NULL AFTER adjustment_count;
ALTER TABLE `user` ADD COLUMN `voucher` decimal(10,4) NOT NULL AFTER points;
ALTER TABLE `user` ADD COLUMN `total_points` decimal(10,4) NOT NULL AFTER voucher;
ALTER TABLE `user` ADD COLUMN `win_rate` decimal(10,2) NOT NULL AFTER total_points;
ALTER TABLE `site` ADD COLUMN `category_id` bigint NOT NULL after description;
ALTER TABLE `site` ADD COLUMN `sub_category_id` bigint NOT NULL after category_id;
ALTER TABLE `event` CHANGE `handicap_away_bet` `handicap_away_bet` VARCHAR(255) NOT NULL;
ALTER TABLE `prediction_rate`ADD COLUMN `rank` int NOT NULL after rate;
ALTER TABLE `prediction_rate`ADD COLUMN `type` varchar(255) NOT NULL after category_id;
ALTER TABLE `top_ten`ADD COLUMN `rank` int NOT NULL after prediction_count;
ALTER TABLE `article` ADD COLUMN `comment_count` int NOT NULL after view_count;
ALTER TABLE `promotion` ADD COLUMN points decimal(10,4) NOT NULL AFTER `type`;
ALTER TABLE `promotion` ADD COLUMN voucher decimal(10,4) NOT NULL AFTER `points`;
ALTER TABLE `promotion` ADD COLUMN `system` tinyint NOT NULL AFTER `status`;
ALTER TABLE `message_like` ADD COLUMN `updated_at` datetime NOT NULL AFTER `created_at`;
ALTER TABLE `prediction_stats` ADD COLUMN `league_id` bigint NOT NULL AFTER category_id;
ALTER TABLE `gift` ADD COLUMN `hot_category` text NOT NULL AFTER color;
ALTER TABLE `top_ten` ADD COLUMN `month` int NOT NULL AFTER `rank`;
ALTER TABLE `top_ten` ADD COLUMN `year` int NOT NULL AFTER `month`;
ALTER TABLE `top_ten` ADD COLUMN `league_id` bigint NOT NULL AFTER category_id;
ALTER TABLE `prediction_stats` ADD COLUMN total_win_rate decimal(10,2) NOT NULL AFTER win_rate;
ALTER TABLE `prediction_stats` DROP COLUMN prediction_participation_rate;
ALTER TABLE `illegal_words` ADD COLUMN `regex` tinyint NOT NULL AFTER word;
ALTER TABLE `user` ADD COLUMN `total_voucher`decimal(10,4) NOT NULL AFTER voucher;
ALTER TABLE `promotion` CHANGE `points` `points` int NOT NULL;
ALTER TABLE `promotion` CHANGE `voucher` `voucher` int NOT NULL;
ALTER TABLE `forget_password` ADD COLUMN `type` varchar(255) NOT NULL AFTER verification_code;
ALTER TABLE `season_list` CHANGE `league_api_id` `league_id` bigint(20) NOT NULL;
ALTER TABLE `season_matches` CHANGE `league_api_id` `league_id` bigint(20) NOT NULL;
ALTER TABLE season_matches ADD COLUMN home_team_name varchar(255) NOT NULL AFTER home_team_id;
ALTER TABLE season_matches ADD COLUMN away_team_name varchar(255) NOT NULL AFTER away_team_id;
ALTER TABLE `season_player_stats_soccer` ADD COLUMN `penalty` int NOT NULL AFTER goals;