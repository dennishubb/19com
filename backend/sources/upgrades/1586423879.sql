ALTER TABLE `prediction` ADD COLUMN `handicap_win` tinyint NOT NULL AFTER `single`;
ALTER TABLE `prediction` ADD COLUMN `over_under_win` tinyint NOT NULL AFTER `handicap_win`;
ALTER TABLE `prediction` ADD COLUMN `single_win` tinyint NOT NULL AFTER `over_under_win`;