DELETE FROM `oxconfig` WHERE `OXMODULE` = 'module:barzahlen';
ALTER TABLE `oxorder` CHANGE `BZSTATE` `BZSTATE` ENUM( 'pending', 'paid', 'expired', 'canceled') NOT NULL;