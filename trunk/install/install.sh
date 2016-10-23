#!/bin/bash
#before install check DB setting in 
#	judge.conf 
#	hustoj-read-only/web/include/db_info.inc.php
#	and down here
#and run this with root

#mkdir /var/www/judge/JudgeOnline
#cd trunk/install
#CENTOS/REDHAT/FEDORA WEBBASE=/var/www/html APACHEUSER=apache 
WEBBASE=/var/www/html
APACHEUSER=www-data
DBUSER='DBUSERNAME'
DBPASS='DBPASSWORD'

#try install tools
sudo apt-get install make flex g++ clang libmysql++-dev php5 apache2 mysql-server php5-mysql php5-gd php5-cli mono-gmcs subversion
sudo /etc/init.d/mysql start

sudo yum -y update
sudo yum -y install php httpd php-mysql mysql-server php-xml php-gd gcc-c++  mysql-devel php-mbstring glibc-static flex
sudo /etc/init.d/mysqld start

#create user and homedir
sudo  /usr/sbin/useradd -m -u 1536 judge



#compile and install the core
cd ../core/
sudo ./make.sh
cd ../..
#install web and db
sudo cp -R trunk/web $WEBBASE/judge
sudo chmod -R 771 $WEBBASE/judge
sudo chown -R $APACHEUSER $WEBBASE/judge
sudo mysql -h localhost -u$DBUSER -p$DBPASS < db.sql

#create work dir set default conf
sudo    mkdir /home/judge
sudo    mkdir /home/judge/etc
sudo    mkdir /home/judge/data
sudo    mkdir /home/judge/log
sudo    mkdir /home/judge/run0
sudo    mkdir /home/judge/run1
sudo    mkdir /home/judge/run2
sudo    mkdir /home/judge/run3
sudo cp java0.policy  judge.conf /home/judge/etc
sudo chown -R judge /home/judge
sudo chgrp -R $APACHEUSER /home/judge/data
sudo chgrp -R root /home/judge/etc /home/judge/run?
sudo chmod 775 /home/judge /home/judge/data /home/judge/etc /home/judge/run?

#boot up judged
sudo cp judged /etc/init.d/judged
sudo chmod +x  /etc/init.d/judged
sudo ln -s /etc/init.d/judged /etc/rc3.d/S93judged
sudo ln -s /etc/init.d/judged /etc/rc2.d/S93judged
sudo /etc/init.d/judged start
sudo /etc/init.d/apache2 restart
sudo /etc/init.d/httpd restart

