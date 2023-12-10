#!/bin/bash
DATE=`date +%Y%m%d%H%M`
USER=`cat /etc/mysql/debian.cnf |grep user|head -1|awk  '{print $3}'`
PASSWORD=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'`
WWW=`grep www /etc/passwd|awk -F: '{print $1}'`

if [ `whoami` = "root" ];then
	cd /home/judge/
        mkdir new
        cd new
	wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
	tar xzf hustoj.tar.gz
	mv src/* ./
	cd ..
	if test -e /home/judge/src/web/include/db_info.inc.php ;then
		    echo 'db_info.inc.php exists !';
		    cp src/web/include/db_info.inc.php new/web/include/
		else
		   echo 'db_info.inc.php not found';
		   sed -i "s/DB_USER[[:space:]]*=[[:space:]]*\"root\"/DB_USER=\"$USER\"/g" new/web/include/db_info.inc.php
		   sed -i "s/DB_PASS[[:space:]]*=[[:space:]]*\"root\"/DB_PASS=\"$PASSWORD\"/g" new/web/include/db_info.inc.php
	fi
	cp -a src/web/upload/* new/web/upload/
	mv src "old.$DATE"
 	echo "Your old files are moved to old.$DATE , find them if you need ."
	mv new src
	chown $WWW:$WWW -R src
else
	echo "usage: sudo $0"
fi
