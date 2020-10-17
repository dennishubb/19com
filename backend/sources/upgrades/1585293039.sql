ALTER TABLE `user` 
ADD COLUMN `comment_count` INT NOT NULL AFTER `disabled`, 
ADD COLUMN `article_count` INT NOT NULL AFTER `comment_count`;