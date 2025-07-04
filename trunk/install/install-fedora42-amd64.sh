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
                dd if=/dev/zero of=/swap bs=2M count=2048
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
fi

DBNAME="jol"
DBUSER="root"
DBPASS=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1`
CPU=`cat /proc/cpuinfo| grep "processor"| wc -l`

# 此脚本待改进, yum 在 Fedora上已被 dnf 取代。

yum -y update

yum makecache

# avoid minimal installation no wget
yum -y install wget

# install nginx
yum -y install nginx
yum -y install epel-release yum-utils
yum -y install php-fpm php-mysqlnd php-xml php-gd php-mbstring gcc-c++ mysql-devel glibc-static libstdc++-static flex
yum -y install mariadb mariadb-devel mariadb-server

# OpenJDK install script by mxd.
echo "Please choose the OpenJDK version to install:"
echo "1. 8"
echo "2. 11"
echo "3. 17"
echo "4. 21"
read -p "Enter your choice (1-4): " choice

case $choice in
    1) version=8 ;;
    2) version=11 ;;
    3) version=17 ;;
    4) version=21 ;;
    *) echo "Invalid choice. Exiting."; exit 1 ;;
esac

echo '[Adoptium]
name=Adoptium
baseurl=https://mirrors.cernet.edu.cn/Adoptium/rpm/centos$releasever-$basearch/
enabled=1
gpgcheck=1
gpgkey=https://packages.adoptium.net/artifactory/api/gpg/key/public
' | sudo tee /etc/yum.repos.d/adoptium.repo

dnf makecache

# Install the selected Temurin JDK version
sudo dnf install -y temurin-${version}-jdk

# install semanage to setup selinux
yum -y install policycoreutils-python

systemctl start mariadb.service 
/usr/sbin/useradd -m -u 1536 -s /sbin/nologin judge
cd /home/judge/
yum -y install subversion
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz
#svn co https://github.com/zhblue/hustoj/trunk/trunk/ src
cd src/install
mysql -h localhost -uroot < db.sql
echo "insert into jol.privilege values('admin','administrator','true','N');"|mysql -h localhost -uroot
# mysqladmin -u root password $DBPASS
cd ../../

mkdir etc data log backup

cp src/install/java0.policy  /home/judge/etc
cp src/install/judge.conf  /home/judge/etc
chmod +x src/install/ans2out

if grep "OJ_SHM_RUN=0" etc/judge.conf ; then
	mkdir run0 run1 run2 run3
	chown apache run0 run1 run2 run3
fi

# sed -i "s/OJ_USER_NAME=root/OJ_USER_NAME=$USER/g" etc/judge.conf
# sed -i "s/OJ_PASSWORD=root/OJ_PASSWORD=$DBPASS/g" etc/judge.conf
sed -i "s/OJ_COMPILE_CHROOT=1/OJ_COMPILE_CHROOT=0/g" etc/judge.conf
sed -i "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf

chmod 700 backup
chmod 700 etc/judge.conf

# sed -i "s/DB_USER=\"root\"/DB_USER=\"$USER\"/g" src/web/include/db_info.inc.php
# sed -i "s/DB_PASS=\"root\"/DB_PASS=\"$DBPASS\"/g" src/web/include/db_info.inc.php

sed -i "s+//date_default_timezone_set(\"PRC\");+date_default_timezone_set(\"PRC\");+g" src/web/include/db_info.inc.php
sed -i "s+//pdo_query(\"SET time_zone ='\+8:00'\");+pdo_query(\"SET time_zone ='\+8:00'\");+g" src/web/include/db_info.inc.php

chmod 775 -R /home/judge/data && chgrp -R apache /home/judge/data
chmod 700 src/web/include/db_info.inc.php

chown apache src/web/include/db_info.inc.php
chown apache src/web/upload data run0 run1 run2 run3

# cp /etc/nginx/nginx.conf /home/judge/src/install/nginx.origin
mv /etc/nginx/conf.d/default.conf /home/judge/src/install/default.conf.bak
cp /home/judge/src/install/default.conf /etc/nginx/conf.d/default.conf

# startup nginx.service when booting.
systemctl enable nginx.service 

# open http/https services.
firewall-cmd --permanent --add-service=http --add-service=https --zone=public

# reload firewall config
firewall-cmd --reload

sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php.ini

# startup php-fpm.service when booting.
systemctl enable php-fpm.service

# startup mariadb.service when booting.
systemctl enable mariadb.service

# check module selinux policy modules
checkmodule /home/judge/src/install/my-phpfpm.te -M -m -o my-phpfpm.mod
checkmodule /home/judge/src/install/my-ifconfig.te -M -m -o my-ifconfig.mod

# package policy modules
semodule_package -m my-phpfpm.mod -o my-phpfpm.pp
semodule_package -m my-ifconfig.mod -o my-ifconfig.pp

# install policy modules
semodule -i my-phpfpm.pp
semodule -i my-ifconfig.pp

# clean up
echo "clean up selinux module output files"
rm -rf my-phpfpm.mod my-phpfpm.pp
rm -rf my-ifconfig.mod my-ifconfig.pp

# restart nginx.service
systemctl restart nginx.service

# restart php-fpm.service.
systemctl restart php-fpm.service

chmod 755 /home/judge
chown apache -R /home/judge/src/web/

mkdir /var/lib/php/session
chown apache /var/lib/php/session

cd /home/judge/src/core
chmod +x make.sh
./make.sh

if grep "/usr/bin/judged" /etc/rc.d/rc.local ; then
	echo "auto start judged added!"
else
	chmod +x /etc/rc.d/rc.local
	sed -i "s/exit 0//g" /etc/rc.d/rc.local
	echo "/usr/bin/judged" >> /etc/rc.d/rc.local
	echo "exit 0" >> /etc/rc.d/rc.local
	
fi
/usr/bin/judged

# change pwd
cd /home/judge/

# write password at the end of install
sed -i "s/OJ_PASSWORD[[:space:]]*=[[:space:]]*root/OJ_PASSWORD=$DBPASS/g" etc/judge.conf
sed -i "s/DB_PASS[[:space:]]*=[[:space:]]*\"root\"/DB_PASS=\"$DBPASS\"/g" src/web/include/db_info.inc.php

# change database password at the end of install
mysqladmin -u root password $DBPASS

# mono install for c# 
yum -y install yum-utils
rpm --import "http://keyserver.Ubuntu.com/pks/lookup?op=get&search=0x3FA7E0328081BFF6A14DA29AA6A19B38D3D831EF"
yum-config-manager --add-repo http://download.mono-project.com/repo/centos/ 
yum -y update
yum -y install mono
ln -s /usr/bin/mcs /usr/bin/gmcs

# Install FreePascal
dnf install -y fpc-units-x86_64-linux fpc

# Go language
yum -y install golang

reset
echo "Remember your database account for HUST Online Judge:"
echo "username:root"
echo "password:$DBPASS"
