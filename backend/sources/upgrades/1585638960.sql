ALTER TABLE `article` ADD COLUMN `chatroom_id` bigint(20) NOT NULL AFTER `category_id`;
ALTER TABLE `message` ADD COLUMN `chatroom_id` bigint(20) NOT NULL AFTER `user_id`;