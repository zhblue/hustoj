#!/bin/bash

DIRECTORY="/data/data/"
if [ ! -d $DIRECTORY ]; then
	mv  /home/judge/data/ /data/
else
	rm -R /home/judge/data/
fi
ln -s $DIRECTORY /home/judge/data
	
DIRECTORY="/data/judge.conf"
if [ ! -f $DIRECTORY ]; then
	mv /home/judge/etc/judge.conf /data/
else
	rm /home/judge/etc/judge.conf
fi
ln -s $DIRECTORY /home/judge/etc/judge.conf

DIRECTORY="/data/db_info.inc.php"
if [ ! -f $DIRECTORY ]; then
	mv /home/judge/src/web/include/db_info.inc.php /data/
else
	rm /home/judge/src/web/include/db_info.inc.php
fi
#ln -s $DIRECTORY /home/judge/src/web/include/db_info.inc.php
cp $DIRECTORY /home/judge/src/web/include/db_info.inc.php

DIRECTORY="/data/mysql"
if [ ! -d $DIRECTORY ]; then
	mv  /var/lib/mysql /data
else
	rm -R /var/lib/mysql
fi
ln -s $DIRECTORY /var/lib/mysql

chmod 775 -R /data/data 
chgrp -R www-data /data/data
chmod 770 -R /data/upload 
chgrp -R www-data /data/upload
chmod 770 -R /data/judge.conf 
chgrp -R www-data /data/judge.conf
chmod 770 -R /data/db_info.inc.php
chgrp -R www-data /data/db_info.inc.php

# 1. 创建www-data组
groupadd -r www-data
# 2. 创建www-data用户（并加入组）
useradd -r -g www-data -s /sbin/nologin www-data

chmod o+x /home/ /home/judge/ /home/judge/src/
chown -R www-data:www-data /home/judge/src/web/
chmod -R 755 /home/judge/src/web/

#chown -R mysql:mysql /var/lib/mysql 
chown -R mysql:mysql /data/mysql/
usermod -d /var/lib/mysql -m mysql

service mariadb start
/usr/bin/judged
service php8.3-fpm start
service nginx start

/bin/bash  
exit 0 


