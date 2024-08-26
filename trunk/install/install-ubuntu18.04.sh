#!/bin/bash
sed -i 's/tencentyun/aliyun/g' /etc/apt/sources.list

apt-get update
apt-get install -y subversion
/usr/sbin/useradd -m -u 1536 -s /sbin/nologin judge
cd /home/judge/

#using tgz src files
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz
svn up src
#svn co https://github.com/zhblue/hustoj/trunk/trunk/  src

for pkg in "net-tools make flex g++ clang libmysqlclient-dev libmysql++-dev php-fpm nginx mysql-server php-mysql  php-common php-gd php-zip fp-compiler openjdk-11-jdk mono-devel php-mbstring php-xml php-curl php-intl php-xmlrpc php-soap  php-yaml"
do
	while ! apt-get install -y $pkg 
	do
		echo "Network fail, retry... you might want to change another apt source for install"
	done
done

USER=`cat /etc/mysql/debian.cnf |grep user|head -1|awk  '{print $3}'`
PASSWORD=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'`
CPU=`grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}'`

mkdir etc data log backup

cp src/install/java0.policy  /home/judge/etc
cp src/install/judge.conf  /home/judge/etc
chmod +x src/install/ans2out

# create enough runX dirs for each CPU core
if grep "OJ_SHM_RUN=0" etc/judge.conf ; then
	for N in `seq 0 $(($CPU-1))`
	do
	   mkdir run$N
	   chown judge run$N
	done
fi

sed -i "s/OJ_USER_NAME=root/OJ_USER_NAME=$USER/g" etc/judge.conf
sed -i "s/OJ_PASSWORD=root/OJ_PASSWORD=$PASSWORD/g" etc/judge.conf
sed -i "s/OJ_COMPILE_CHROOT=1/OJ_COMPILE_CHROOT=0/g" etc/judge.conf
sed -i "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf

chmod 700 backup
chmod 700 etc/judge.conf

sed -i "s/DB_USER[[:space:]]*=[[:space:]]*\"root\"/DB_USER=\"$USER\"/g" src/web/include/db_info.inc.php
sed -i "s/DB_PASS[[:space:]]*=[[:space:]]*\"root\"/DB_PASS=\"$PASSWORD\"/g" src/web/include/db_info.inc.php

chmod 700 src/web/include/db_info.inc.php
chown -R www-data src/web/

chown -R root:root src/web/.svn
chmod 750 -R src/web/.svn

chown www-data src/web/upload data
if grep "client_max_body_size" /etc/nginx/nginx.conf ; then 
	echo "client_max_body_size already added" ;
else
	sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    80m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf
fi

mysql -h localhost -u$USER -p$PASSWORD < src/install/db.sql
echo "insert into jol.privilege values('admin','administrator','true','N');"|mysql -h localhost -u$USER -p$PASSWORD 

if grep "added by hustoj" /etc/nginx/sites-enabled/default ; then
	echo "default site modified!"
else
	echo "modify the default site"
	sed -i "s#root /var/www/html;#root /home/judge/src/web;#g" /etc/nginx/sites-enabled/default
	sed -i "s:index index.html:index index.php:g" /etc/nginx/sites-enabled/default
	sed -i "s:#location ~ \\\.php\\$:location ~ \\\.php\\$:g" /etc/nginx/sites-enabled/default
	sed -i "s:#\tinclude snippets:\tinclude snippets:g" /etc/nginx/sites-enabled/default
	sed -i "s|#\tfastcgi_pass unix|\tfastcgi_pass unix|g" /etc/nginx/sites-enabled/default
	sed -i "s:}#added by hustoj::g" /etc/nginx/sites-enabled/default
	sed -i "s:php7.0:php7.2:g" /etc/nginx/sites-enabled/default
	sed -i "s|# deny access to .htaccess files|}#added by hustoj\n\n\n\t# deny access to .htaccess files|g" /etc/nginx/sites-enabled/default
fi
/etc/init.d/nginx restart
sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php/7.2/fpm/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php/7.2/fpm/php.ini
sed -i 's/;request_terminate_timeout = 0/request_terminate_timeout = 128/g' `find /etc/php -name www.conf`
sed -i 's/pm.max_children = 5/pm.max_children = 200/g' `find /etc/php -name www.conf`
 
COMPENSATION=`grep 'mips' /proc/cpuinfo|head -1|awk -F: '{printf("%.2f",$2/3000)}'`
sed -i "s/OJ_CPU_COMPENSATION=1.0/OJ_CPU_COMPENSATION=$COMPENSATION/g" etc/judge.conf

/etc/init.d/php7.2-fpm restart
service php7.2-fpm restart

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
	crontab -l > conf && echo "1 0 * * * /home/judge/src/install/bak.sh" >> conf && crontab conf && rm -f conf
fi
ln -s /usr/bin/mcs /usr/bin/gmcs

/usr/bin/judged
cp /home/judge/src/install/hustoj /etc/init.d/hustoj
update-rc.d hustoj defaults
systemctl enable hustoj
systemctl enable nginx
systemctl enable mysql
systemctl enable php7.2-fpm

mkdir /var/log/hustoj/
chown www-data -R /var/log/hustoj/

cls
reset

echo "Remember your database account for HUST Online Judge:"
echo "username:$USER"
echo "password:$PASSWORD"
echo "DO NOT POST THESE INFOMANTION ON ANY PUBLIC CHANNEL!"
echo "█████████████████████████████████████████"
echo "████ ▄▄▄▄▄ ██▄▄ ▀  █▀█▄▄██ ███ ▄▄▄▄▄ ████"
echo "████ █   █ █▀▄  █▀██ ██▄▄  █▄█ █   █ ████"
echo "████ █▄▄▄█ █▄▀ █▄█▀█  ▄▄█▀▀▄██ █▄▄▄█ ████"
echo "████▄▄▄▄▄▄▄█▄▀▄█ █ █▄█▄▀ █ ▀▄█▄▄▄▄▄▄▄████"
echo "████ ▄▀▀█▄▄ █▄ █▄▄▄█▄█▀███▄  ██▀ ▄▀▀█████"
echo "████▀█▀▀▀▀▄▀▀▄▀ ▄▄█▄ █▀▀ ▄▀▀▄  █▄▄▀▄█████"
echo "████▄█ ▀▄▀▄▄ ▄ █▀█▀█ ▄▀▄ █▀▀▄█  ███  ████"
echo "████▄ █▄ █▄▀▀▄██▀▄ ▄ ▄▄█▄█▀█▀   ▄█▀▄▀████"
echo "████▄▄█   ▄▄██ █▄▄▀  ▄▀█▀▀▀ ▄█▀▄▄▀█ ▀████"
echo "█████▄   ▀▄▄█ ▄▀▄▄▀▄▄▄▀▄▀█▀  ▀▀█▄█▀█▄████"
echo "████ ▀ █▄▀▄▄█▀▀▄▀▀▄▄▄ ▀▀█▀ ▀▄▄█▀ ▀█ █████"
echo "████ █▀   ▄ ▄ ▀█▀▄█ █▄▄███▀██▀▀██ ▀▄█████"
echo "████▄▄▄██▄▄█ ▀█▄▄▄▀█ █▀▀█▀ █ ▄▄▄ █▀▄▀████"
echo "████ ▄▄▄▄▄ █ ▄  ▄▄▀  ▄ ▀▄▄▄▄ █▄█   ▄█████"
echo "████ █   █ ██ ▄▄▀▀█ ▀▀▀▀▀ ▄▀  ▄  ▀███████"
echo "████ █▄▄▄█ █▀▄▄▄▀▀█ ▀▄ ▄▀██▄█ ██ █ █▄████"
echo "████▄▄▄▄▄▄▄█▄███▄█▄▄▄████▄▄▄▄▄▄█▄██▄█████"
echo "█████████████████████████████████████████"
echo "             QQ 扫码加群                 "

