#!/bin/bash

#detect and refuse to run under WSL
if [ -d /mnt/c ]; then
    echo "WSL is NOT supported."
    exit 1
fi
echo "Welcome to install HUSTOJ on your BT panel,please prepare your database account!"
echo "Press Ctrl+C to Stop..."
echo "Input your database username:"
read USER
echo "Input your database password:"
read PASSWORD

OSRS=`lsb_release -rs`

sed -i 's/tencentyun/aliyun/g' /etc/apt/sources.list
sed -i 's/cn.archive.ubuntu/mirrors.aliyun/g' /etc/apt/sources.list
sed -i "s|#\$nrconf{restart} = 'i'|\$nrconf{restart} = 'a'|g" /etc/needrestart/needrestart.conf


apt-get update && apt-get -y upgrade

apt-get install -y software-properties-common
add-apt-repository universe
add-apt-repository multiverse
add-apt-repository restricted
# 解决宝塔收集用户信息问题
chattr +i /www/server/panel/script/site_task.py
chattr +i -R /www/server/panel/logs/request

apt-get update && apt-get -y upgrade

apt-get install -y subversion
/usr/sbin/useradd -m -u 1536 -s /sbin/nologin judge
cd /home/judge/ || exit

#using tgz src files
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz
svn up src
#svn co https://github.com/zhblue/hustoj/trunk/trunk/  src

#手工解决阿里云软件源的包依赖问题
apt install libssl1.1=1.1.1f-1ubuntu2.8 -y --allow-downgrades
apt-get install -y libmysqlclient-dev
apt-get install -y libmysql++-dev

for pkg in net-tools make g++ mysql-client
do
        while ! apt-get install -y "$pkg"
        do
                echo "Network fail, retry... you might want to change another apt source for install"
                echo "Or you might need to add [universe] [multiverse] to your /etc/apt/sources.list"
        done
done


CPU=$(grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}')

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
chgrp www /home/judge
chown -R www src/web/

chown -R root:root src/web/.svn
chmod 750 -R src/web/.svn

chown www:judge src/web/upload
chown www:judge data
chmod 711 -R data
mysql -h localhost -u"$USER" -p"$PASSWORD" < src/install/db.sql
echo "insert into jol.privilege values('admin','administrator','true','N');"|mysql -h localhost -u"$USER" -p"$PASSWORD"
echo "insert into jol.privilege values('admin','source_browser','true','N');"|mysql -h localhost -u"$USER" -p"$PASSWORD" 


COMPENSATION=$(grep 'mips' /proc/cpuinfo|head -1|awk -F: '{printf("%.2f",$2/5000)}')
sed -i "s/OJ_CPU_COMPENSATION=1.0/OJ_CPU_COMPENSATION=$COMPENSATION/g" etc/judge.conf


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
        crontab -l > conf && echo "1 0 * * * /home/judge/src/install/bak.sh" >> conf && crontab conf && rm -f conf
fi
ln -s /usr/bin/mcs /usr/bin/gmcs

/usr/bin/judged
cp /home/judge/src/install/hustoj /etc/init.d/hustoj
update-rc.d hustoj defaults
#systemctl enable judged
PHP_INI=`find /www/ -name php.ini`
sed -i 's/passthru,exec,system,/passthru,exec,/g'  $PHP_INI
#shutdown warning message for php in BT Panel
sed -i 's#//ini_set("display_errors", "Off");#ini_set("display_errors", "Off");#g' /home/judge/src/web/include/db_info.inc.php


mkdir /var/log/hustoj/
chown www -R /var/log/hustoj/
cd /home/judge/src/install
if test -f  /.dockerenv ;then
        echo "Already in docker, skip docker installation, install some compilers ... "
        apt-get intall -y flex fp-compiler openjdk-14-jdk mono-devel
else
        sed -i "s/ubuntu:22.04/ubuntu:$OSRS/g" Dockerfile
	sed -i 's|/usr/include/c++/9|/usr/include/c++/11|g' Dockerfile 
	bash docker.sh
         sed -i "s/OJ_USE_DOCKER=0/OJ_USE_DOCKER=1/g" /home/judge/etc/judge.conf
         sed -i "s/OJ_PYTHON_FREE=0/OJ_PYTHON_FREE=1/g" /home/judge/etc/judge.conf
         sed -i "s/OJ_INTERNAL_CLIENT=1/OJ_INTERNAL_CLIENT=0/g" /home/judge/etc/judge.conf
	sed -i "s|OJ_DOCKER_PATH=/usr/bin/docker|OJ_DOCKER_PATH=/usr/bin/docker|g" /home/judge/etc/judge.conf
fi
clear
reset

echo "Remember your database account for HUST Online Judge:"
echo "username:$USER"
echo "password:$PASSWORD"
echo "DO NOT POST THESE INFORMATION ON ANY PUBLIC CHANNEL!"
echo "Register a user as 'admin' on http://127.0.0.1/ "
echo "打开http://127.0.0.1/ 注册用户admin，获得管理员权限。"
echo "不要在QQ群或其他地方公开发送以上信息，否则可能导致系统安全受到威胁。"
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
echo "    使用Java前请重启服务器！sudo reboot"
