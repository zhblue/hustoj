-- 消课系统家长查询 — users 表加列
-- 用法：mysql -u root -p jol < xiaoke_parent_phone.sql

-- 1. 加 parent_phone 列（家长手机号）
ALTER TABLE `users`
  ADD COLUMN `parent_phone` VARCHAR(20) NOT NULL DEFAULT ''
  COMMENT '家长手机号，家长查询入口使用'
  AFTER `school`;

-- 2. 加索引（按手机号查学员时用）
ALTER TABLE `users`
  ADD INDEX `idx_parent_phone` (`parent_phone`);

-- 3. 可选：批量填充示例（仅参考，根据你的数据调整）
-- UPDATE users SET parent_phone='13800138000' WHERE user_id='test_student_1';
