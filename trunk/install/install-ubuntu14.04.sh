#!/bin/bash
apt-get update
apt-get install -y subversion
/usr/sbin/useradd -m -u 1536 judge
cd /home/judge/
svn co https://github.com/shiningrise/hustoj/trunk/trunk/ src

echo 'mysql-server-5.5 mysql-server/root_password password ""' | sudo debconf-set-selections
echo 'mysql-server-5.5 mysql-server/root_password_again password ""' | sudo debconf-set-selections
apt-get install -y make flex g++ clang libmysqlclient-dev libmysql++-dev php5-fpm php5-memcache memcached nginx mysql-server php5-mysql php5-gd fp-compiler openjdk-7-jdk \
	curl libcurl3 libcurl3-dev php5-curl \
	python python3 
USER=`cat /etc/mysql/debian.cnf |grep user|head -1|awk  '{print $3}'`
PASSWORD=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'`
CPU=`grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}'`
mkdir etc data log

cp src/install/java0.policy  /home/judge/etc
cp src/install/judge.conf  /home/judge/etc
chmod +x src/install/ans2out

if grep "OJ_SHM_RUN=0" etc/judge.conf ; then
	mkdir run0 run1 run2 run3
	chown judge run0 run1 run2 run3
fi

sed -i "s/OJ_USER_NAME=root/OJ_USER_NAME=$USER/g" etc/judge.conf
sed -i "s/OJ_PASSWORD=root/OJ_PASSWORD=$PASSWORD/g" etc/judge.conf
sed -i "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf

sed -i "s/DB_USER=\"root\"/DB_USER=\"$USER\"/g" src/web/include/db_info.inc.php
sed -i "s/DB_PASS=\"root\"/DB_PASS=\"$PASSWORD\"/g" src/web/include/db_info.inc.php

sed -i "s/DB_USER=\"root\"/DB_USER=\"$USER\"/g" src/web/config/system.conf.php
sed -i "s/DB_PASS=\"root\"/DB_PASS=\"$PASSWORD\"/g" src/web/config/system.conf.php

chown www-data src/web/upload data
if grep client_max_body_size /etc/nginx/nginx.conf ; then 
	echo "client_max_body_size already added" ;
else
	sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    80m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf
fi

mysql -h localhost -u$USER -p$PASSWORD < src/install/db.sql
echo "insert into jol.privilege values('admin','administrator','N');"|mysql -h localhost -u$USER -p$PASSWORD 

sed -i "s:root /usr/share/nginx/html;:root /home/judge/src/web;:g" /etc/nginx/sites-enabled/default
sed -i "s:index index.html:index index.php:g" /etc/nginx/sites-enabled/default
sed -i "s:#location ~ \\\.php\\$:location ~ \\\.php\\$:g" /etc/nginx/sites-enabled/default
sed -i "s:#\tfastcgi_split_path_info:\tfastcgi_split_path_info:g" /etc/nginx/sites-enabled/default
sed -i "s:#\tfastcgi_pass unix:\tfastcgi_pass unix:g" /etc/nginx/sites-enabled/default
sed -i "s:#\tfastcgi_index:\tfastcgi_index:g" /etc/nginx/sites-enabled/default
sed -i "s:#\tinclude fastcgi_params;:\tinclude fastcgi_params;\n\t}:g" /etc/nginx/sites-enabled/default
/etc/init.d/nginx restart
sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php5/fpm/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php5/fpm/php.ini
/etc/init.d/php5-fpm restart
service php5-fpm restart
cd src/core
chmod +x ./make.sh
./make.sh
if grep "/usr/bin/judged" /etc/rc.local ; then
	echo "auto start judged added!"
else
	sed -i "s/exit 0//g" /etc/rc.local
	echo "/usr/bin/judged" >> /etc/rc.local
	echo "exit 0" >> /etc/rc.local
	
fi
if grep "bak.sh" /var/spool/cron/crontabs/root ; then
	echo "auto backup added!"
else
	echo "1 0 * * * /home/judge/src/install/bak.sh" >> /var/spool/cron/crontabs/root
fi
/usr/bin/judged

