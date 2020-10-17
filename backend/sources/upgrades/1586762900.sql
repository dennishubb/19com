ALTER TABLE `result` MODIFY `handicap_away` tinyint NOT NULL;
ALTER TABLE `result` ADD COLUMN `single_odds` varchar(255) NOT NULL AFTER `over_under_bet`;