ALTER TABLE `prediction` CHANGE `amount` `amount` DECIMAL(10,2) NOT NULL;
ALTER TABLE `prediction` CHANGE `win_amount` `win_amount` DECIMAL(10,2) NOT NULL;
ALTER TABLE `transaction` CHANGE `amount` `amount` DECIMAL(10,2) NOT NULL;
ALTER TABLE `balance` CHANGE `balance` `balance` DECIMAL(10,2) NOT NULL;
ALTER TABLE `accounting` CHANGE `credit` `credit` DECIMAL(10,2) NOT NULL;
ALTER TABLE `accounting` CHANGE `debit` `debit` DECIMAL(10,2) NOT NULL;