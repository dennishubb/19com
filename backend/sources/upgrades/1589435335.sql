ALTER TABLE `prediction_rate` ADD COLUMN total_count int NOT NULL AFTER lose_count;
ALTER TABLE `prediction_rate` ADD COLUMN total_points decimal(10,4) NOT NULL AFTER total_count;