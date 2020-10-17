ALTER TABLE promotion ADD COLUMN limitation_count int NOT NULL AFTER limitation;
ALTER TABLE `promotion` MODIFY `level_id` VARCHAR(255) NOT NULL;