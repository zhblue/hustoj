#!/bin/bash
DATE=$(date +%Y%m%d)
BACKUP_DIR="/var/backups"
CONFIG="/home/judge/etc/judge.conf"

# 1. 提取数据库配置
SERVER=$(grep 'OJ_HOST_NAME' $CONFIG | cut -d= -f2)
USER=$(grep 'OJ_USER_NAME' $CONFIG | cut -d= -f2)
PASSWORD=$(grep 'OJ_PASSWORD' $CONFIG | cut -d= -f2)
DATABASE=$(grep 'OJ_DB_NAME' $CONFIG | cut -d= -f2)
PORT=$(grep 'OJ_PORT_NUMBER' $CONFIG | cut -d= -f2)

# 定义清理函数
cleanup_old_backups() {
    echo "警告：空间不足，正在强制清理 3 天前的旧备份..."
    find $BACKUP_DIR -name "hustoj_*.tar.bz2" -mtime +3 -delete
    find $BACKUP_DIR -name "db_*.sql.bz2" -mtime +3 -delete
}

# 2. 动态计算空间阈值
# 查找最新的备份文件
LATEST_BACKUP=$(ls -t $BACKUP_DIR/hustoj_*.tar.bz2 2>/dev/null | head -n1)

if [ -f "$LATEST_BACKUP" ]; then
    # 获取文件大小 (单位: MB)，并预留 20% 的增长空间作为阈值
    LAST_SIZE=$(du -m "$LATEST_BACKUP" | cut -f1)
    THRESHOLD=$(( LAST_SIZE * 120 / 100 ))
    echo "参考上次备份大小 (${LAST_SIZE}MB)，设定预警阈值为: ${THRESHOLD}MB"
else
    # 如果没有历史备份，默认预留 2048MB
    THRESHOLD=2048
    echo "未找到历史备份，使用默认阈值: ${THRESHOLD}MB"
fi

# 3. 空间检查
FREE_SPACE=$(df -m $BACKUP_DIR | awk 'NR==2 {print $4}')
if [ "$FREE_SPACE" -lt "$THRESHOLD" ]; then
    cleanup_old_backups
fi

# 4. 执行数据库清理与优化
echo "正在清理并优化数据库..."
mysql -h$SERVER -P$PORT -u$USER -p$PASSWORD $DATABASE <<EOF
delete from source_code where solution_id in (select solution_id from solution where problem_id=0 and result>4);
delete from source_code_user where solution_id in (select solution_id from solution where problem_id=0 and result>4);
delete from runtimeinfo where solution_id in (select solution_id from solution where problem_id=0 and result>4);
delete from compileinfo where solution_id in (select solution_id from solution where problem_id=0 and result>4);
update solution set result=5 where result<4 and in_date<curdate()-interval 3 day;
delete from solution where problem_id=0 and result>4;
delete from loginlog where time<curdate()-interval 6 month;
repair table compileinfo,contest,contest_problem,loginlog,news,privilege,problem,solution,source_code,users,topic,reply,online,sim,mail;
optimize table compileinfo,contest,contest_problem,loginlog,news,privilege,problem,solution,source_code,users,topic,reply,online,sim,mail;
EOF

# 5. 执行备份
echo "开始备份流程..."
mkdir -p $BACKUP_DIR
SQL_FILE="$BACKUP_DIR/db_${DATE}.sql.bz2"
TAR_FILE="$BACKUP_DIR/hustoj_${DATE}.tar.bz2"

# 导出数据库快照
mysqldump --default-character-set=utf8mb4 -h$SERVER -P$PORT $DATABASE -u$USER -p$PASSWORD | bzip2 > $SQL_FILE

# 压缩打包
if tar cjf $TAR_FILE /home/judge/data /home/judge/src/web /home/judge/src/core /home/judge/etc $SQL_FILE; then
    echo "备份完成: $TAR_FILE"
    # 成功后删除 1 天前的临时数据库文件和 3 天前的旧包
    find $BACKUP_DIR -name "db_*.sql.bz2" -mtime +1 -delete
    find $BACKUP_DIR -name "hustoj_*.tar.bz2" -mtime +3 -delete
else
    echo "错误：备份失败，磁盘空间可能已耗尽。"
    cleanup_old_backups
    exit 1
fi
