#!/bin/bash

#detect and refuse to run under WSL
if [ -d /mnt/c ]; then
    echo "WSL is NOT recommended."
#    exit 1
fi
MEM=`free -m|grep Mem|awk '{print $2}'`
NBUFF=512
if [ "$MEM" -lt "2000" ] ; then
        echo "Memory size less than 2GB."
        NBUFF=128
        if grep 'swap' /etc/fstab ; then
                echo "already has swap"
        else
                dd if=/dev/zero of=/swap bs=2M count=1024
                chmod 600 /swap
                mkswap /swap
                swapon /swap
                echo "/swap none swap defaults 0 0 " >> /etc/fstab 
                /etc/init.d/multipath-tools stop
                pkill -9 snapd
                pkill -9 ds-identify
         fi
else
        echo "Memory size : $MEM MB"
        apt install -y memcached
fi

cat << EOM
推荐在安装之前先换源，参考https://blog.mxdyeah.com/post/hustoj-debian14-mirrors
安装脚本将做简单判断。如果下载慢，请参考上面的链接手动调整。

It is recommended to switch to a mirror source before installation; please refer to https://blog.mxdyeah.com/post/hustoj-debian14-mirrors.
The installation script performs basic checks. If the download is slow, please refer to the link above to manually adjust the settings.
EOM

if [ -f /etc/apt/sources.list ]; then
    sed -i 's/deb.debian.org/mirrors.cernet.edu.cn/g' /etc/apt/sources.list
elif [ -f /etc/apt/sources.list.d/debian.sources ]; then
    sed -i 's|deb.debian.org|mirrors.cernet.edu.cn|g' /etc/apt/sources.list.d/debian.sources
fi

apt update && apt -y upgrade
/usr/sbin/useradd -m -u 1536 -s /sbin/nologin judge

cd /home/judge/ || exit

#using tgz src files
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz
PHP_VER=`apt-cache search php-fpm|grep -e '[[:digit:]]\.[[:digit:]]' -o`
if [ "$PHP_VER" = "" ] ; then PHP_VER="8.5"; fi

PACKAGES=(
    bzip2
    net-tools
    flex
    build-essential
    mariadb-client
    libmariadb-dev
    libmariadb-dev-compat
    libmysql++-dev
    memcached
    nginx
    php-mysql
    php-common
    php-gd
    php-zip
    php-yaml
	php-fpm
    php-memcached
    php-memcache
	php-mbstring
    php-xml
	php-curl
	php-intl
	php-xmlrpc
    php-apcu
	php-redis
	redis-server
    fp-compiler
    mono-devel
    default-libmysqlclient-dev
	tzdata
)

for PKG in "${PACKAGES[@]}"; do
    while ! apt install -y "$PKG"; do
        dpkg --configure -a
        apt install -f -y
		echo "Network fail, retry... you might want to change another apt source for install"
		echo "see: https://blog.mxdyeah.com/post/hustoj-debian14-mirrors"
    done
done

apt install -y mariadb-server
systemctl start php$PHP_VER-fpm
systemctl start mariadb
systemctl start nginx

chgrp www-data  /home/judge
chmod +x /home/judge/src/install/*

USER="hustoj"
PASSWORD=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1`
mysql < src/install/db.sql
echo "DROP USER $USER;" | mysql
echo "CREATE USER $USER identified by '$PASSWORD';grant all privileges on jol.* to $USER ;flush privileges;"|mysql
RO_USER="hustoj_ro";
RO_PASSWORD=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1`
echo "CREATE USER $RO_USER identified by '$RO_PASSWORD';grant select on jol.* to $RO_USER ;flush privileges;"|mysql
CPU=$(grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}')
MEM=`free -m|grep Mem|awk '{print $2}'`

if [ "$MEM" -lt "1000" ] ; then
        echo "Memory size less than 1GB."
        if grep 'key_buffer_size        = 1M' /etc/mysql/mariadb.conf.d/50-server.cnf ; then
                echo "already trim config"
        else
                sed -i 's/#key_buffer_size        = 128M/key_buffer_size        = 1M/' /etc/mysql/mariadb.conf.d/50-server.cnf
                sed -i 's/#table_cache            = 64/#table_cache            = 5/' /etc/mysql/mariadb.conf.d/50-server.cnf
                sed -i 's/#skip-name-resolve/skip-name-resolve/' /etc/mysql/mariadb.conf.d/50-server.cnf
                service mariadb restart
                free -h
        fi
else
        echo "Memory size : $MEM MB"
fi

mkdir etc data log backup

cp src/install/java0.policy  /home/judge/etc
cp src/install/judge.conf  /home/judge/etc
chmod +x src/install/ans2out /home/judge/src/install/*.sh

# create enough runX dirs for each CPU core
if grep "OJ_SHM_RUN=0" etc/judge.conf ; then
        for N in `seq 0 $(($CPU-1))`
        do
           mkdir run$N
           chown judge run$N
        done
fi

sed -i "s/OJ_USER_NAME=.*/OJ_USER_NAME=$USER/g" etc/judge.conf
sed -i "s/OJ_PASSWORD=.*/OJ_PASSWORD=$PASSWORD/g" etc/judge.conf
sed -i "s/OJ_COMPILE_CHROOT=1/OJ_COMPILE_CHROOT=0/g" etc/judge.conf
sed -i "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf

chown www-data -R /home/judge
chgrp judge -R  /home/judge
chmod 710 -R /home/judge/data
chmod 700 backup
chmod 700 etc/judge.conf
chown -R root:root etc

sed -i "s/DB_USER[[:space:]]*=[[:space:]]*\".*\"/DB_USER=\"$USER\"/g" src/web/include/db_info.inc.php
sed -i "s/DB_PASS[[:space:]]*=[[:space:]]*\".*\"/DB_PASS=\"$PASSWORD\"/g" src/web/include/db_info.inc.php
sed -i "s/DB_RO_USER[[:space:]]*=[[:space:]]*\".*\"/DB_RO_USER=\"$RO_USER\"/g" src/web/include/db_info.inc.php
sed -i "s/DB_RO_PASS[[:space:]]*=[[:space:]]*\".*\"/DB_RO_USER=\"$RO_PASSWORD\"/g" src/web/include/db_info.inc.php
chmod 700 src/web/include/db_info.inc.php
chown -R www-data:www-data src/web/
chown www-data:www-data src/web/upload
chown www-data:judge data
chmod 750 -R data
if grep "client_max_body_size" /etc/nginx/nginx.conf ; then 
	echo "client_max_body_size already added" ;
else
        sed -i 's/# multi_accept on;/ multi_accept on;/' /etc/nginx/nginx.conf
        sed -i 's/# server_tokens off;/ server_tokens off;/' /etc/nginx/nginx.conf
        sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    500m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf
fi

echo "insert into jol.privilege values('admin','administrator','true','N');"|mysql -h localhost -u"$USER" -p"$PASSWORD"
echo "insert into jol.privilege values('admin','source_browser','true','N');"|mysql -h localhost -u"$USER" -p"$PASSWORD"

if grep "added by hustoj" /etc/nginx/sites-enabled/default ; then
        echo "default site modified!"
else
        echo "modify the default site"
        sed -i "s#listen 80 default_server;#listen 80 default_server backlog=4096;#g" /etc/nginx/sites-enabled/default
        sed -i "s#root /var/www/html;#root /home/judge/src/web;#g" /etc/nginx/sites-enabled/default
        sed -i "s:index index.html:index index.php index.html:g" /etc/nginx/sites-enabled/default
        sed -i "s:#location ~ \\\.php\\$:location ~ \\\.php\\$:g" /etc/nginx/sites-enabled/default
        sed -i "s:#\tinclude snippets:\tinclude snippets:g" /etc/nginx/sites-enabled/default
        sed -i "s|#\tfastcgi_pass unix|\tfastcgi_pass unix|g" /etc/nginx/sites-enabled/default
        sed -i "s:}#added by hustoj::g" /etc/nginx/sites-enabled/default
        sed -i "s:php7.4:php$PHP_VER:g" /etc/nginx/sites-enabled/default
        sed -i "s|# deny access to .htaccess files|}#added by hustoj\n\n\n\t# deny access to .htaccess files|g" /etc/nginx/sites-enabled/default
        sed -i "s|fastcgi_pass 127.0.0.1:9000;|fastcgi_pass 127.0.0.1:9001;\n\t\tfastcgi_buffer_size 256k;\n\t\tfastcgi_buffers $NBUFF 64k;|g" /etc/nginx/sites-enabled/default
fi
/etc/init.d/nginx restart
fi
# 修改 post_max_size 和 upload_max_filesize
sed -i "s/post_max_size = 8M/post_max_size = 500M/g" /etc/php/$PHP_VER/fpm/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 500M/g" /etc/php/$PHP_VER/fpm/php.ini

# 修改 session 存储为 Redis
sed 's|session.save_handler *= *files|session.save_handler = redis\nsession.save_path="tcp://127.0.0.1:6379"|'  /etc/php/$PHP_VER/fpm/php.ini

# 设置时区为Asia/Shanghai
if grep 'date.timezone = Asia/Shanghai' /etc/php/$PHP_VER/fpm/php.ini ; then
    echo "date.timezone = Asia/Shanghai is already set ... "
else
    # 如果存在注释掉的 ;date.timezone = ，则替换；否则追加
    if grep '^;date.timezone =' /etc/php/$PHP_VER/fpm/php.ini ; then
        sed -i 's/;date.timezone =/date.timezone = Asia\/Shanghai/' /etc/php/$PHP_VER/fpm/php.ini
    else
        echo "date.timezone = Asia/Shanghai" >> /etc/php/$PHP_VER/fpm/php.ini
    fi
fi

# 启用 Opcache JIT
if grep "opcache.jit_buffer_size" /etc/php/$PHP_VER/fpm/php.ini ; then
    echo "opcache for jit is already enabled ... "
else
    sed -i "s|opcache.lockfile_path=/tmp|opcache.lockfile_path=/tmp\nopcache.jit_buffer_size=16M|g" /etc/php/$PHP_VER/fpm/php.ini
fi
WWW_CONF=$(find /etc/php -name www.conf)
sed -i 's/;request_terminate_timeout = 0/request_terminate_timeout = 128/g' "$WWW_CONF"
sed -i 's/pm.max_children = 5/pm.max_children = 600/g' "$WWW_CONF"
sed -i 's/;listen.backlog = 511/listen.backlog = 4096/g' "$WWW_CONF"

COMPENSATION=$(grep 'mips' /proc/cpuinfo|head -1|awk -F: '{printf("%.2f",$2/7000)}')
sed -i "s/OJ_CPU_COMPENSATION=1.0/OJ_CPU_COMPENSATION=$COMPENSATION/g" etc/judge.conf

PHP_FPM=$(find /etc/init.d/ -name "php*-fpm")
$PHP_FPM restart
PHP_FPM=$(service --status-all|grep php|awk '{print $4}')
if [ "$PHP_FPM" != ""  ]; then service "$PHP_FPM" restart ;else echo "NO PHP FPM";fi;

cd src/core || exit
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
        crontab -l > conf 
        echo "1 0 * * * /home/judge/src/install/bak.sh" >> conf
        echo "0 * * * * /home/judge/src/install/oomsaver.sh" >> conf 
        crontab conf 
        rm -f conf
        /etc/init.d/cron reload
fi
ln -s /usr/bin/mcs /usr/bin/gmcs

/usr/bin/judged
cp /home/judge/src/install/hustoj /etc/init.d/hustoj
update-rc.d hustoj defaults
systemctl enable hustoj
systemctl enable nginx
systemctl enable mariadb
systemctl enable php$PHP_VER-fpm
#systemctl enable judged
#systemctl start fail2ban
#systemctl enable fail2ban

if ps -C memcached; then 
    sed -i 's/static  $OJ_MEMCACHE=false;/static  $OJ_MEMCACHE=true;/g' /home/judge/src/web/include/db_info.inc.php
    sed -i 's/-m 64/-m 8/g' /etc/memcached.conf
    /etc/init.d/memcached restart
fi
/etc/init.d/mariadb start
mkdir /var/log/hustoj/
chown www-data -R /var/log/hustoj/
cd /home/judge/src/install
bash set-nofile.sh
if test -f  /.dockerenv ;then
        echo "Already in docker, skip docker installation, install some compilers ... "
		echo "for debian forky, openjdk17/21 is missing, install openjdk-25"
        apt-get intall -y flex fp-compiler openjdk-25-jdk mono-devel
else
        sed -i 's/ubuntu:22/ubuntu:26/g' Dockerfile
        sed -i 's|/usr/include/c++/9|/usr/include/c++/15|g' Dockerfile
        bash docker.sh
fi
IP=`curl http://hustoj.com/ip.php`
LIP=`ip a|grep inet|grep brd|head -1|awk '{print $2}'|awk -F/ '{print $1}'`

sed -i 's/11.4.0/15.2.0-5/g' /home/judge/src/web/template/syzoj/faqs.php
service nginx restart
clear
reset

cat << EOM
Note: skip-networking is needed for Andorid based Linux Deploy to start mariadb
Remember your database account for HUST Online Judge:
username:$USER
password:$PASSWORD
DO NOT POST THESE INFORMATION ON ANY PUBLIC CHANNEL!
Register a user as 'admin' on http://127.0.0.1/
打开http://127.0.0.1/ 或者 http://$IP  或者 http://$LIP 注册用户admin，获得管理员权限。
如果无法打开页面或无法注册用户，请检查上方数据库账号是否能正常连接数据库。
如果发现数据库账号登录错误，可用sudo bash /home/judge/src/install/fixdb.sh 尝试修复。
遇到服务器内部错误500，查看/var/log/nginx/error.log末尾，寻找详细原因。
更多问题请查阅http://hustoj.com/
不要在QQ群或其他地方公开发送以上信息，否则可能导致系统安全受到威胁。
EOM

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
echo "            QQ扫码加官方群"

cat << EOM
For Java User:
在 Debian 12/13/14，如果你不通过Docker判题，想使用本地环境判题，则需要安装OpenJDK。
需要手动运行 sudo bash /home/judge/src/install/openjdk-install.sh 来安装，
并通过交互指定OpenJDK版本。推荐OpenJDK 8/21，根据实际情况安装。
可以参考 https://blog.mxdyeah.com/post/hustoj-debian14-judge-java
判题 Java 语言前，需要重启 judged，也请参考上面文章。
EOM
