#!/bin/bash
config="/home/judge/etc/judge.conf"
VIRTUAL="/var/www/virtual/"
SERVER=`cat $config|grep 'OJ_HOST_NAME' |awk -F= '{print $2}'`
USER=`cat $config|grep 'OJ_USER_NAME' |awk -F= '{print $2}'`
PASSWORD=`cat $config|grep 'OJ_PASSWORD' |awk -F= '{print $2}'`
DATABASE=`cat $config|grep 'OJ_DB_NAME' |awk -F= '{print $2}'`
PORT=`cat $config|grep 'OJ_PORT_NUMBER' |awk -F= '{print $2}'`

echo "mysql -u${USER} -p{PASSWORD_HIDDEN} ${DBNAME} "

mysql -h $SERVER -P $PORT -u$USER -p$PASSWORD $DATABASE --force
