#!/bin/bash
apt-get update
apt-get install -y subversion
/usr/sbin/useradd -m -u 1536 judge
cd /home/judge/
#using tgz src files
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz
svn up src
#svn co https://github.com/zhblue/hustoj/trunk/trunk/  src
for PKG in make flex g++ clang mariadb-client libmysqlclient-dev libmysql++-dev php-fpm nginx php-mysql php-common php-gd php-zip php-yaml fp-compiler openjdk-11-jdk mono-devel php-mbstring php-xml mariadb-server libmariadb-dev libmariadbclient-dev libmariadb-dev default-libmysqlclient-dev
do
   apt-get install -y $PKG 
done
USER="hustoj"
PASSWORD=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1`
CPU=`grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}'`

mkdir etc data log backup

cp src/install/java0.policy  /home/judge/etc
cp src/install/judge.conf  /home/judge/etc
chmod +x src/install/ans2out

if grep "OJ_SHM_RUN=0" etc/judge.conf ; then
	mkdir run0 run1 run2 run3
	chown judge run0 run1 run2 run3
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
chown www-data src/web/include/db_info.inc.php
chown www-data src/web/upload data
if grep "client_max_body_size" /etc/nginx/nginx.conf ; then 
	echo "client_max_body_size already added" ;
else
	sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    80m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf
fi

mysql < src/install/db.sql
echo "CREATE USER '$USER'@'localhost' identified by '$PASSWORD';grant all privileges on jol.* to '$USER'@'localhost' ; flush privileges;"|mysql
echo "insert into jol.privilege values('admin','administrator','true','N');"|mysql

PHP_VER=`ls /etc/php | head -1`

if grep "added by hustoj" /etc/nginx/sites-enabled/default ; then
	echo "hustoj nginx config added!"
else
	sed -i "s/php7.4/php$PHP_VER/g" /etc/nginx/sites-enabled/default
        sed -i "s#root /var/www/html;#root /home/judge/src/web;#g" /etc/nginx/sites-enabled/default
	sed -i "s:index index.html:index index.php:g" /etc/nginx/sites-enabled/default
	sed -i "s:#location ~ \\\.php\\$:location ~ \\\.php\\$:g" /etc/nginx/sites-enabled/default
	sed -i "s:#\tinclude snippets:\tinclude snippets:g" /etc/nginx/sites-enabled/default
	sed -i "s|#\tfastcgi_pass unix|\tfastcgi_pass unix|g" /etc/nginx/sites-enabled/default
	sed -i "s:}#added_by_hustoj::g" /etc/nginx/sites-enabled/default
	sed -i "s|# deny access to .htaccess files|}#added by hustoj\n\n\n\t# deny access to .htaccess files|g" /etc/nginx/sites-enabled/default
	/etc/init.d/nginx restart
	sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php/$PHP_VER/fpm/php.ini
	sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php/$PHP_VER/fpm/php.ini
fi

COMPENSATION=`grep 'mips' /proc/cpuinfo|head -1|awk -F: '{printf("%.2f",$2/5000)}'`
sed -i "s/OJ_CPU_COMPENSATION=1.0/OJ_CPU_COMPENSATION=$COMPENSATION/g" etc/judge.conf

/etc/init.d/php$PHP_VER-fpm restart
service php$PHP_VER-fpm restart

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

cd /home/judge/src/install/
sed -i "s/ubuntu:22.04/debian12.2/g" Dockerfile
sed -i "s/libmysqlclient-dev/default-libmysqlclient-dev/" Dockerfile
sed -i "s/openjdk-17-jdk/gcc/" Dockerfile

bash docker.sh
/usr/bin/judged
cp /home/judge/src/install/hustoj /etc/init.d/hustoj
update-rc.d hustoj defaults
systemctl enable nginx
systemctl enable mysql
systemctl enable php$PHP_VER-fpm
systemctl enable judged

echo "Note: skip-networking is needed for Andorid based Linux Deploy to start mariadb "
echo "Remember your database account for HUST Online Judge:"
echo "username:$USER"
echo "password:$PASSWORD"
echo "DO NOT POST THESE INFORMATION ON ANY PUBLIC CHANNEL!"
