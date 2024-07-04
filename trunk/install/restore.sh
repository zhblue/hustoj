#/bin/bash
TARBZNAME=`find -name "hustoj_*.tar.bz2"`
if [ $# != 1 ] ; then
  echo "USAGE: sudo $0 $TARBZNAME"
  echo " e.g.: sudo $0 hustoj_xxxxxxx.tar.bz2"
  echo " tar.bz2 should be created by bak.sh, default location : /var/backups/ "
  exit 1;
fi

DATE=`date +%Y%m%d%H%M%S`
BAKDATE=`echo $1 |awk -F\. '{print $1}'|awk -F_ '{print $2}'`
config="/home/judge/etc/judge.conf"
SERVER=`cat $config|grep 'OJ_HOST_NAME' |awk -F= '{print $2}'`
USER=`cat $config|grep 'OJ_USER_NAME' |awk -F= '{print $2}'`
PASSWORD=`cat $config|grep 'OJ_PASSWORD' |awk -F= '{print $2}'`
DATABASE=`cat $config|grep 'OJ_DB_NAME' |awk -F= '{print $2}'`
mkdir hustoj-restore
cd hustoj-restore
MAIN="../$1"
/home/judge/src/install/bak.sh
tar xjf $MAIN
mv /home/judge/data /home/judge/data.del.$DATE
mv home/judge/data /home/judge/
chown www-data -R /home/judge/data
mv /home/judge/src/web/upload /home/judge/src/web/upload.del.$DATE
mv home/judge/src/web/upload /home/judge/src/web/
chown www-data -R /home/judge/src/web/
bzip2 -d var/backups/db_${BAKDATE}.sql.bz2
sed -i 's/COLLATE=utf8mb4_0900_ai_ci//g' var/backups/db_${BAKDATE}.sql
sed -i 's/COLLATE utf8mb4_0900_ai_ci//g' var/backups/db_${BAKDATE}.sql
sed -i 's/utf8mb4_0900_ai_ci/utf8mb4_unicode_ci/g' var/backups/db_${BAKDATE}.sql
if ! mysql -h $SERVER -u$USER -p$PASSWORD $DATABASE < var/backups/db_${BAKDATE}.sql ; then
   mysql $DATABASE < var/backups/db_${BAKDATE}.sql
fi
if ! mysql -h $SERVER -u$USER -p$PASSWORD $DATABASE < /home/judge/src/install/update.sql ; then
   mysql $DATABASE < /home/judge/src/install/update.sql
fi

