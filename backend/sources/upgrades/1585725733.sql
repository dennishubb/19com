ALTER TABLE `message` ADD COLUMN `status` VARCHAR(255) NOT NULL AFTER `attachment_upload_id`;
ALTER TABLE `message` ADD COLUMN `updated_at` datetime NOT NULL AFTER `created_at`;