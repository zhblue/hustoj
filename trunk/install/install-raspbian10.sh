#!/bin/bash
apt-get update
apt-get install -y subversion
/usr/sbin/useradd -m -u 1536 -s /bin/false judge
cd /home/judge/

#using tgz src files
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz
svn up src

PHP_VER=`apt-cache search php-fpm|grep -e '[[:digit:]]\.[[:digit:]]' -o`
if [ "$PHP_VER" = "" ] ; then PHP_VER="7.4"; fi

for PKG in make flex g++ clang libmariadb++-dev  mariadb-server php${PHP_VER}-fpm php${PHP_VER}-memcache php-zip php-xml php-mbstring memcached nginx php${PHP_VER}-mysql php${PHP_VER}-gd fp-compiler openjdk-9-jdk
do
	apt-get install -y $PKG
done

USER="hustoj"
PASSWORD=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1`

mkdir etc data log

cp src/install/java0.policy  /home/judge/etc
cp src/install/judge.conf  /home/judge/etc

if grep "OJ_SHM_RUN=0" etc/judge.conf ; then
	mkdir run0 run1 run2 run3
	chown judge run0 run1 run2 run3
fi

sed -i "s/OJ_USER_NAME=root/OJ_USER_NAME=$USER/g" etc/judge.conf
sed -i "s/OJ_PASSWORD=root/OJ_PASSWORD=$PASSWORD/g" etc/judge.conf

sed -i "s/DB_USER[[:space:]]*=[[:space:]]*\"root\"/DB_USER=\"$USER\"/g" src/web/include/db_info.inc.php
sed -i "s/DB_PASS[[:space:]]*=[[:space:]]*\"root\"/DB_PASS=\"$PASSWORD\"/g" src/web/include/db_info.inc.php

chown www-data src/web/upload data
if grep "client_max_body_size" /etc/nginx/nginx.conf ; then 
	echo "client_max_body_size already added" ;
else
	sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    80m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf
fi

mysql < src/install/db.sql
echo "CREATE USER '$USER'@'localhost' identified by '$PASSWORD';grant all privileges on jol.* to '$USER'@'localhost' ;\n flush privileges;\n"|mysql
echo "insert into jol.privilege values('admin','administrator','true','N');"|mysql -h localhost -u$USER -p$PASSWORD 

#cp src/install/nginx.default /etc/nginx/sites-available/default

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
	sed -i "s:php7.0:php${PHP_VER}:g" /etc/nginx/sites-enabled/default
	sed -i "s|# deny access to .htaccess files|}#added by hustoj\n\n\n\t# deny access to .htaccess files|g" /etc/nginx/sites-enabled/default
fi



/etc/init.d/nginx restart
sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php${PHP_VER}/fpm/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php${PHP_VER}/fpm/php.ini
sed -i 's/;request_terminate_timeout = 0/request_terminate_timeout = 128/g' `find /etc/php -name www.conf`
 
/etc/init.d/php${PHP_VER}-fpm restart
service php${PHP_VER}-fpm restart
cd src/core
bash ./make.sh
if grep "/usr/bin/judged" /etc/rc.local ; then
	echo "auto start judged added!"
else
	sed -i "s/exit 0//g" /etc/rc.local
	echo "/usr/bin/judged" >> /etc/rc.local
	echo "exit 0" >> /etc/rc.local
	
fi
/usr/bin/judged

