ALTER TABLE `chatroom` ADD COLUMN `type` varchar(255) NOT NULL AFTER `status`;
ALTER TABLE `message` ADD COLUMN `type` varchar(255) NOT NULL AFTER `status`;