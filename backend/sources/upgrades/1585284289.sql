ALTER TABLE article 
ADD COLUMN `seo_title` VARCHAR(255) NOT NULL AFTER `title`, 
ADD COLUMN `hot` tinyint(1) NOT NULL AFTER `keywords`,
ADD COLUMN `popular` tinyint(1) NOT NULL AFTER `hot`;