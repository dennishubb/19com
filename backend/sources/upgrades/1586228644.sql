ALTER TABLE `user` ADD COLUMN `address` text NOT NULL AFTER `email`;
ALTER TABLE `user` ADD COLUMN `birth_at` DATETIME NOT NULL AFTER `address`;
ALTER TABLE `user` ADD COLUMN `gender` varchar(255) NOT NULL AFTER `birth_at`;
ALTER TABLE `user` ADD COLUMN `weibo` varchar(255) NOT NULL AFTER `gender`;