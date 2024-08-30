#!/bin/bash
echo "Welcome to install HUSTOJ on your BT panel,please prepare your database account!"
echo "Press Ctrl+C to Stop..."
echo "Input your database username:"
read DBUSER
echo "Input your database password:"
read DBPASS

DBNAME="jol"
CPU=`cat /proc/cpuinfo| grep "processor"| wc -l`

yum -y update

# avoid minimal installation no wget
yum -y install wget

yum -y install epel-release yum-utils
yum -y install http://rpms.remirepo.net/enterprise/remi-release-7.rpm

yum -y install gcc-c++  mysql-devel glibc-static libstdc++-static flex java-1.8.0-openjdk java-1.8.0-openjdk-devel

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
docker build -t hustoj .
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
	chown www run0 run1 run2 run3
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

chmod 750 -R /home/judge/data && chown -R www /home/judge/data
chmod 700 src/web/include/db_info.inc.php

chown www src/web/include/db_info.inc.php
chown www src/web/upload data run0 run1 run2 run3

# open http/https services.
firewall-cmd --permanent --add-service=http --add-service=https --zone=public

# reload firewall config
firewall-cmd --reload

sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php.ini

# clean up
echo "clean up selinux module output files"
rm -rf my-phpfpm.mod my-phpfpm.pp
rm -rf my-ifconfig.mod my-ifconfig.pp

# restart nginx.service
systemctl restart nginx.service

# restart php-fpm.service.
systemctl restart php-fpm.service

chmod 755 /home/judge
chown www -R /home/judge/src/web/


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

cd /home/judge/src/install
bash docker.sh

mkdir /var/log/hustoj/
chown www -R /var/log/hustoj/

reset
echo "Remember your database account for HUST Online Judge:"
echo "username:root"
echo "password:$DBPASS"
