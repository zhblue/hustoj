#!/bin/bash
DATE=`date +%Y%m%d`
OLD=`date -d"1 day ago" +"%Y%m%d"`
OLD3=`date -d"3 day ago" +"%Y%m%d"`
config="/home/judge/etc/judge.conf"
SERVER=`cat $config|grep 'OJ_HOST_NAME' |awk -F= '{print $2}'`
USER=`cat $config|grep 'OJ_USER_NAME' |awk -F= '{print $2}'`
PASSWORD=`cat $config|grep 'OJ_PASSWORD' |awk -F= '{print $2}'`
DATABASE=`cat $config|grep 'OJ_DB_NAME' |awk -F= '{print $2}'`
PORT=`cat $config|grep 'OJ_PORT_NUMBER' |awk -F= '{print $2}'`

echo "delete from source_code where solution_id in (select solution_id from solution where problem_id=0 and result>4);"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 
echo "delete from source_code_user where solution_id in (select solution_id from solution where problem_id=0 and result>4);"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 
echo "delete from runtimeinfo where solution_id in (select solution_id from solution where problem_id=0 and result>4);"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 
echo "delete from compileinfo where solution_id in (select solution_id from solution where problem_id=0 and result>4);"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 
echo "update solution set result=5 where result<4 and in_date<curdate()-interval 3 day ;"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 
echo "delete from solution where problem_id=0 and result>4 "|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 

# cleanup trash from 6 month ago
echo "delete from loginlog where time<curdate()-interval 6 month;"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 
echo "delete from compileinfo where solution_id < (select solution_id from solution where result=11 and in_date< curdate()-interval 6 month order by solution_id desc limit 1);"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 
echo "delete from runtimeinfo where solution_id < (select solution_id from solution where result=11 and in_date< curdate()-interval 6 month order by solution_id desc limit 1);"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 


echo "repair table compileinfo,contest,contest_problem,loginlog,news,privilege,problem,solution,source_code,users,topic,reply,online,sim,mail;"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 
echo "optimize table compileinfo,contest,contest_problem,loginlog,news,privilege,problem,solution,source_code,users,topic,reply,online,sim,mail;"|mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE 

echo "这里有警告是正常现象，请勿担心，下面的打包压缩耗时较长，请耐心等待备份结束，重新回到命令行提示符。"
echo "The warning here is normal, don't worry, the following packaging and compression takes a long time, please wait patiently for the backup to end and return to the command line prompt."
mkdir /var/backups 
mysqldump -h $SERVER -P $PORT $DATABASE -u$USER -p$PASSWORD | bzip2 >/var/backups/db_${DATE}.sql.bz2
if tar cjf /var/backups/hustoj_${DATE}.tar.bz2 /home/judge/data /home/judge/src/web /home/judge/src/core /home/judge/etc /var/backups/db_${DATE}.sql.bz2; then
	rm /var/backups/hustoj_${OLD3}.tar.bz2  2> /dev/null
	rm /var/backups/db_${OLD}.sql.bz2  2> /dev/null
	# 如果经常遇到磁盘空间不足，可以尝试启用下面的内容
	# find /var/backups/ -maxdepth 2 -ctime 5 -name "*.bz2" -exec rm -f {} \;
fi
echo "备份完成，请检查并用FileZilla通过sftp下载备份文件：/var/backups/hustoj_${DATE}.tar.bz2"
echo "After the backup is complete, please check and download the backup file via sftp with FileZilla: /var/backups/hustoj_${DATE}.tar.bz2"

