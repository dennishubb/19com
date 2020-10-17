ALTER TABLE `article` ADD COLUMN `type` varchar(255) NOT NULL AFTER `sorting`;
ALTER TABLE `message` ADD COLUMN `like_count` int NOT NULL AFTER type;