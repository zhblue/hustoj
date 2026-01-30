alter table users add column expiry_date date not null default '2099-01-01' after reg_time;
alter table contest add column contest_type smallint UNSIGNED default 0 after `password`;
alter table contest modify column contest_type smallint UNSIGNED default 0;
alter table contest add column subnet varchar(255) not null default '' after contest_type;
alter table online modify refer varchar(4096) DEFAULT NULL;
alter table solution add column first_time tinyint(1) default 0 after pass_rate ;
alter table solution add index fst(first_time);
CREATE TABLE IF NOT EXISTS solution_ai_answer ( solution_id int not null default 0, answer mediumtext ,primary key (solution_id)) charset utf8mb4;
CREATE INDEX idx_solution_in_date ON solution(in_date);
CREATE TABLE `openai_task_queue` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` varchar(40) NOT NULL DEFAULT '',
  `task_type` varchar(24) NOT NULL DEFAULT '',
  `solution_id` bigint DEFAULT '0',
  `problem_id` bigint not null default 0,
  `request_body` mediumtext NOT NULL COMMENT '请求参数(JSON格式字符串)',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '状态：0:待处理, 1:处理中, 2:已完成, 3:失败',
  `response_body` mediumtext COMMENT '返回结果',
  `error_message` text COMMENT '错误信息',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `update_date` datetime NOT NULL COMMENT '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_status_create` (`status`,`create_date`),
  KEY `idx_user_status` (`user_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='异步任务队列-MyISAM版';
alter table openai_task_queue add column problem_id bigint not null default 0  after solution_id;

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

                                                                                                         
