alter table users add column expiry_date date not null default '2099-01-01' after reg_time;
alter table contest add column contest_type smallint UNSIGNED default 0 after `password`;
alter table contest modify column contest_type smallint UNSIGNED default 0;
alter table contest add column subnet varchar(255) not null default '' after contest_type;
alter table online modify refer varchar(4096) DEFAULT NULL;
alter table solution add column first_time tinyint(1) default 0 after pass_rate ;
alter table solution add index fst(first_time);
CREATE TABLE IF NOT EXISTS solution_ai_answer ( solution_id int not null default 0, answer mediumtext ,primary key (solution_id)) charset utf8mb4;
CREATE INDEX idx_solution_in_date ON solution(in_date);
CREATE INDEX idx_contest_user_id ON solution(contest_id,user_id,solution_id);
CREATE INDEX idx_cid_result_num_sid ON solution(contest_id,result,num,solution_id);
CREATE INDEX idx_uid_pid ON solution(user_id,problem_id);
CREATE INDEX idx_uid_pid_res ON solution(user_id,problem_id,result);
CREATE INDEX idx_contest_result ON solution(contest_id,result);
CREATE INDEX idx_contest_num ON solution(contest_id,num,result);
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

ALTER TABLE `users` 
ADD COLUMN `coin_earned` INT NOT NULL DEFAULT 0 COMMENT '做题获得的积分',
ADD COLUMN `coin_bonus`  INT NOT NULL DEFAULT 0 COMMENT '老师额外奖励的积分',
ADD COLUMN `coin_spent`  INT NOT NULL DEFAULT 0 COMMENT '已经消耗的积分';

ALTER TABLE `problem` 
ADD COLUMN `coin` INT NOT NULL DEFAULT 1 COMMENT 'AC此题可得的金币数';



delimiter //

-- ============================================================
-- firstAC 触发器修复版
--
-- 目标：NOIP 类比赛（contest_type & 20 > 0）尚未结束时（end_time > now()），
--       不发放积分，避免学生通过积分变化猜出自己是否 AC。
--
-- 策略：比赛期间产生的 AC 保持 first_time = 0（不标记、不发积分）；
--       比赛结束后运行已有的批量 UPDATE 脚本，触发器会自然补发积分并标记。
-- ============================================================

DROP TRIGGER IF EXISTS firstAC//

CREATE TRIGGER firstAC
BEFORE UPDATE ON solution
FOR EACH ROW
BEGIN
    DECLARE acTimes INT DEFAULT 0;
    DECLARE acCoin INT DEFAULT 1;
    DECLARE inOngoingNoip INT DEFAULT 0;

    IF NEW.result = 4 THEN

        -- 1. 本次提交是否属于「尚未结束的 NOIP 类比赛」
        --    contest_id = 0 / NULL 的普通提交不会匹配，inOngoingNoip 保持 0
        SELECT COUNT(1)
          INTO inOngoingNoip
          FROM contest
         WHERE contest.contest_id = NEW.contest_id
           AND contest.end_time > NOW()
           AND (contest.contest_type & 20) > 0;

        -- 2. 该用户该题是否已有「首次 AC」标记
        SELECT COUNT(1)
          INTO acTimes
          FROM solution
         WHERE problem_id = NEW.problem_id
           AND result = 4
           AND first_time = 1
           AND user_id = NEW.user_id;

        -- 3. 题目积分
        SELECT IFNULL(coin, 1)
          INTO acCoin
          FROM problem
         WHERE problem_id = NEW.problem_id;

        -- 4. 只有在「未领取过」且「不在未结束的 NOIP 比赛中」时才标记并发积分
        IF acTimes = 0 AND inOngoingNoip = 0 THEN
            SET NEW.first_time = 1;

            IF OLD.first_time = 0 THEN
                UPDATE users
                   SET coin_earned = coin_earned + acCoin
                 WHERE user_id = NEW.user_id;
            END IF;
        END IF;

    END IF;
END;//

delimiter ;

# 家长手机号字段（xiaoke.php 家长查询使用）
alter table users add column parent_phone varchar(20) not null default '' after school;
alter table users add index idx_parent_phone(parent_phone);

#create fulltext index problem_title_source_index on problem(title,source);

                                                                                                         


