#!/bin/bash
DATE=`date +%Y%m%d%H%M`
USER="hustoj"
PASSWORD=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1`
WWW=`grep www /etc/passwd|awk -F: '{print $1}'`
echo "DROP USER $USER;" | mysql
echo "CREATE USER $USER identified by '$PASSWORD';" | mysql
echo "grant all privileges on jol.* to $USER ;flush privileges;" | mysql
if [ `whoami` = "root" ];then
        cd /home/judge/
        if test -e src/web/include/db_info.inc.php ;then
                   sed -i "s/DB_USER[[:space:]]*=[[:space:]]*\".*\"/DB_USER=\"$USER\"/g" src/web/include/db_info.inc.php|grep DB_USER
                   sed -i "s/DB_PASS[[:space:]]*=[[:space:]]*\".*\"/DB_PASS=\"$PASSWORD\"/g" src/web/include/db_info.inc.php|grep DB_PASS
        fi
        chown $WWW:$WWW -R src
        if test -e etc/judge.conf ;then
                sed -i "s/OJ_USER_NAME[[:space:]]*=[[:space:]]*.*/OJ_USER_NAME=$USER/g" etc/judge.conf|grep OJ_USER_NAME
                sed -i "s/OJ_PASSWORD[[:space:]]*=[[:space:]]*.*/OJ_PASSWORD=$PASSWORD/g" etc/judge.conf|grep OJ_PASSWORD
                pkill -9 judged
                judged
        fi
else
        echo "usage: sudo $0"
fi
