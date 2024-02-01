update contest set start_time='2000-01-01 00:00:00' where start_time<'1000-01-01 00:00:00';
update contest set end_time='2099-01-01 00:00:00' where end_time<'1000-01-01 00:00:00';
alter TABLE `contest` ADD `user_id` CHAR( 48 ) NOT NULL DEFAULT 'admin' AFTER `password` ;
update contest c inner JOIN (SELECT * FROM privilege WHERE rightstr LIKE 'm%') p ON concat('m',contest_id)=rightstr set c.user_id=p.user_id;
alter TABLE `contest_problem` ADD `c_accepted` INT NOT NULL DEFAULT '0' AFTER `num` ,ADD `c_submit` INT NOT NULL DEFAULT '0' AFTER `c_accepted` ;
update contest_problem cp inner join (select count(1) submit,contest_id cid,num from solution where contest_id>0 group by contest_id,num) sb on cp.contest_id=sb.cid and cp.num=sb.num set cp.c_submit=sb.submit;update contest_problem cp inner join (select count(1) ac,contest_id cid,num from solution where contest_id>0 and result=4 group by contest_id,num) sb on cp.contest_id=sb.cid and cp.num=sb.num set cp.c_accepted =sb.ac;
alter table solution add column nick char(20) not null default '' after user_id ;
update solution s inner join users u on s.user_id=u.user_id set s.nick=u.nick;
alter table privilege add index user_id_index(user_id);
ALTER TABLE `problem` CHANGE `time_limit` `time_limit` DECIMAL(10,3) NOT NULL DEFAULT '0';
alter table privilege add column valuestr char(11) not null default 'true' after rightstr; 
alter table news modify column `time` datetime NOT NULL DEFAULT '2016-05-13 19:24:00';
ALTER TABLE `news` ADD COLUMN `menu` int(11) NOT NULL DEFAULT 0 AFTER `importance`;
alter table solution modify column pass_rate decimal(4,3) not null default 0.0;
alter table problem add column remote_oj varchar(16) default NULL after solved;
alter table problem add column remote_id varchar(32) default NULL after remote_oj;
alter table solution add column remote_oj char(16) not null default '' after judger;
alter table solution add column remote_id char(32) not null default '' after remote_oj;
alter table news modify content mediumtext not null;
alter table problem modify description mediumtext not null, modify input  mediumtext not null, modify output mediumtext not null;
alter table users add column activecode varchar(16) not null default '' after school;
#create fulltext index problem_title_source_index on problem(title,source);

                                                                                                         
