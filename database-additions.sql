ALTER TABLE  `users` ADD  `email` VARCHAR( 255 ) NOT NULL AFTER  `role` ;
ALTER TABLE  `users` ADD  `displayname` VARCHAR( 50 ) NOT NULL AFTER  `email` ;
ALTER TABLE  `users` ADD  `deleted` DATETIME NULL ;
ALTER TABLE  `users` ADD  `active` BOOLEAN NOT NULL AFTER  `deleted` ;

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL auto_increment,
  `hash` varchar(255) default NULL,
  `caller` varchar(255) default NULL,
  `data` varchar(255) default NULL,
  `deadline` datetime default NULL,
  `usecount` int(11) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `hashs` (`hash`)
) 