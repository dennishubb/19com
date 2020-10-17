ALTER TABLE `prediction` ADD COLUMN `selected_team_id` bigint NOT NULL AFTER `amount`;
ALTER TABLE `prediction` ADD COLUMN `handicap` tinyint NOT NULL AFTER `selected_team_id`;
ALTER TABLE `prediction` ADD COLUMN `over_under` tinyint NOT NULL AFTER `handicap`;
ALTER TABLE `prediction` ADD COLUMN `single` tinyint NOT NULL AFTER `over_under`;
ALTER TABLE `event` ADD COLUMN `handicap_result` tinyint NOT NULL AFTER `single_away`;
ALTER TABLE `event` ADD COLUMN `over_under_result` tinyint NOT NULL AFTER `handicap_result`;
ALTER TABLE `event` ADD COLUMN `single_result` tinyint NOT NULL AFTER `over_under_result`;
ALTER TABLE `event` ADD COLUMN `ended` tinyint NOT NULL AFTER `single_result`;