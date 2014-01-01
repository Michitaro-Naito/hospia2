ALTER TABLE  `users` ADD  `email` VARCHAR( 255 ) NOT NULL AFTER  `role` ;
ALTER TABLE  `users` ADD  `displayname` VARCHAR( 50 ) NOT NULL AFTER  `email` ;
ALTER TABLE  `users` ADD  `deleted` DATETIME NULL ;