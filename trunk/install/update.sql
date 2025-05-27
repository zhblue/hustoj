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
alter table problem modify description mediumtext not null, modify input  mediumtext not null, modify output mediumtext not null, modify hint mediumtext not null;
alter table users add column activecode varchar(16) not null default '' after school;
alter table users add column group_name varchar(16) not null default '' after school;
alter table loginlog add column log_id int not null auto_increment primary key first;
alter table problem add index key_p_def(defunct);
alter table contest add index key_c_def(defunct);
alter table contest add index key_c_end(end_time);
alter table contest add index key_c_dend(defunct,end_time);
alter table users add column starred int default 0 after activecode ;
alter table users add column expiry_date date not null default '2099-01-01' after reg_time;
alter table contest add column contest_type tinyint UNSIGNED default 0 after `password`;
alter table contest add column subnet varchar(255) not null default '' after contest_type;
alter table online modify refer varchar(4096) DEFAULT NULL;
alter table solution add column first_time tinyint(1) default 0 after pass_rate ;
alter table solution add index fst(first_time);
delimiter //

drop trigger if exists firstAC//
UPDATE solution s JOIN (SELECT user_id,problem_id, MIN(solution_id) AS first_solution_id FROM solution WHERE result = 4 GROUP BY user_id, problem_id ) t ON s.solution_id = t.first_solution_id SET s.first_time = 1 //
create trigger firstAC
before update on solution
for each row
begin
 declare acTimes int;
 if new.result=4 then
    select count(1) from solution where problem_id=new.problem_id and result=4 and first_time=1 and  user_id=new.user_id into acTimes;
    if acTimes=0 then
        set new.first_time=1;
    end if;
end if;
end;//
delimiter ;
#create fulltext index problem_title_source_index on problem(title,source);

                                                                                                         
