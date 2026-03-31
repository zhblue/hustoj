
ALTER TABLE `source_code` ADD COLUMN `tsource` TEXT  AFTER `source`;
update source_code set tsource=cast(uncompress(source) as char);
ALTER TABLE `source_code` DROP COLUMN `source`;

ALTER TABLE `source_code` 
 CHANGE COLUMN `tsource` `source` TEXT  CHARACTER SET utf8mb4  DEFAULT NULL;

--
ALTER TABLE `users` ADD COLUMN `password2` VARCHAR(32)  NOT NULL AFTER `password`;
update users set password2=md5(convert(decode(password,'PWDforJO2005'),char)) ;
ALTER TABLE `users` CHANGE COLUMN `password` `password0` BLOB  DEFAULT NULL,
  CHANGE COLUMN `password2` `password` VARCHAR(32)  NOT NULL ;
ALTER TABLE `users` DROP COLUMN `password0`;
ALTER TABLE `problem` ADD COLUMN `spj` tinyint  NOT NULL DEFAULT 0 AFTER `case_time_limit`;
ALTER TABLE `problem` MODIFY COLUMN `problem_id` INTEGER  NOT NULL AUTO_INCREMENT;

ALTER TABLE `solution` ADD COLUMN `judgetime` DATETIME  AFTER `code_length`;
ALTER TABLE `solution` MODIFY COLUMN `solution_id` INTEGER  NOT NULL AUTO_INCREMENT;
update problem set spj=0;
CREATE TABLE `online` (

  `hash` varchar(32) collate utf8mb4_unicode_ci NOT NULL,

  `ip` varchar(20) character set utf8mb4 NOT NULL default '',

  `ua` varchar(255) character set utf8mb4 NOT NULL default '',

  `refer` varchar(255) collate utf8mb4_unicode_ci default NULL,

  `lastmove` int(10) NOT NULL,

  `firsttime` int(10) default NULL,

  `uri` varchar(255) collate utf8mb4_unicode_ci default NULL,

  PRIMARY KEY  (`hash`),

  UNIQUE KEY `hash` (`hash`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `contest` ADD COLUMN `langmask` TINYINT  NOT NULL DEFAULT 0 COMMENT 'bits for LANG to mask' AFTER `private`;
ALTER TABLE `contest` MODIFY COLUMN `contest_id` INTEGER NOT NULL AUTO_INCREMENT;

update solution set language=4 where language=0;
update solution set language=0 where language=1;
update solution set language=1 where language=4;

update solution set result=9 where result=6;
update solution set result=6 where result=4;
update solution set result=4 where result=0;
update solution set result=10 where result=5;
update solution set result=5 where result=1;
update solution set result=11 where result=7;
update solution set result=7 where result=2;
update solution set result=8 where result=3;

update problem set time_limit=time_limit/1000,memory_limit=memory_limit/1024;

alter table mail modify column `mail_id` int(11) auto_increment;
