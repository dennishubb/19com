ALTER TABLE `event` ADD COLUMN `handicap_home_odds` decimal(3,2) NOT NULL AFTER `handicap_home`;
ALTER TABLE `event` ADD COLUMN `over_under_home_odds` decimal(3,2) NOT NULL AFTER `over_under_home`;
ALTER TABLE `event` ADD COLUMN `handicap_away_odds` decimal(3,2) NOT NULL AFTER `handicap_away`;
ALTER TABLE `event` ADD COLUMN `over_under_away_odds` int NOT NULL AFTER `over_under_away`;
ALTER TABLE `event` ADD COLUMN `winning_team_id` bigint NOT NULL AFTER `single_away`;