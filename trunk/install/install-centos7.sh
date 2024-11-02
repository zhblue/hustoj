#!/bin/bash
DBNAME="jol"
DBUSER="root"
DBPASS=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1`
CPU=`cat /proc/cpuinfo| grep "processor"| wc -l`

yum -y update

# avoid minimal installation no wget
yum -y install wget

# install nginx
wget http://nginx.org/packages/centos/7/x86_64/RPMS/nginx-1.14.0-1.el7_4.ngx.x86_64.rpm
rpm -ivh nginx-1.14.0-1.el7_4.ngx.x86_64.rpm
rm -rf nginx-1.14.0-1.el7_4.ngx.x86_64.rpm

yum -y install epel-release yum-utils
yum -y install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
yum-config-manager --enable remi-php72
yum -y install nginx php-fpm php-mysqlnd php-xml php-gd php-mbstring gcc-c++  mysql-devel glibc-static libstdc++-static flex java-1.8.0-openjdk java-1.8.0-openjdk-devel
yum -y install mariadb mariadb-devel mariadb-server

# install semanage to setup selinux
yum -y install policycoreutils-python
sudo yum install -y yum-utils   device-mapper-persistent-data   lvm2
sudo yum-config-manager     --add-repo     https://mirrors.aliyun.com/docker-ce/linux/centos/docker-ce.repo
sudo yum install docker-ce docker-ce-cli containerd.io docker-compose-plugin
service docker start


systemctl start mariadb.service 
/usr/sbin/useradd -m -u 1536 -s /bin/false judge
cd /home/judge/

#using tgz src files
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz

#svn co https://github.com/zhblue/hustoj/trunk/trunk/ src
cd src/install
while ! wget -O hustoj.docker.tar.bz2  http://dl3.hustoj.com/docker/hustoj.docker.tar.gz
do
  		echo "Download archive image file fail , try again..."
done
gzip -d hustoj.docker.tar.gz
docker import hustoj.docker.tar hustoj
rm hustoj.docker.tar.gz
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

if grep "/usr/bin/judged" /etc/rc.local ; then
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
sed -i "s/OJ_PASSWORD=root/OJ_PASSWORD=$DBPASS/g" etc/judge.conf
sed -i "s/DB_PASS[[:space:]]*=[[:space:]]*\"root\"/DB_PASS=\"$DBPASS\"/g" src/web/include/db_info.inc.php

# change database password at the end of install
mysqladmin -u root password $DBPASS

mkdir /var/log/hustoj/
chown apache -R /var/log/hustoj/

# mono install for c# 
yum -y install yum-utils
rpm --import "http://keyserver.Ubuntu.com/pks/lookup?op=get&search=0x3FA7E0328081BFF6A14DA29AA6A19B38D3D831EF"
yum-config-manager --add-repo http://download.mono-project.com/repo/centos/ 
yum -y update
yum -y install mono
ln -s /usr/bin/mcs /usr/bin/gmcs

#free pascal
wget https://download.sourceforge.net/project/freepascal/Linux/3.0.4/fpc-3.0.4-1.x86_64.rpm
rpm -ivh fpc-3.0.4-1.x86_64.rpm
rm -rf fpc-3.0.4-1.x86_64.rpm

# Go language
yum -y install golang

reset
echo "Remember your database account for HUST Online Judge:"
echo "username:root"
echo "password:$DBPASS"
