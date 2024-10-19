#!/bin/bash
# usage : sudo ./install-judge.sh <ojurl> <judger_name> <judger_password> <localpath>
# example : 
# step 0: registe a new user name judger1 on OJ web (http://10.1.2.100/) ,password :judg3r_p@ss
# step 1: add http_judge privilege to judger1
# step 2: install a new ubuntu on another node in same LAN , and open a new console to execute step 3
# step 3: sudo ./install-judge.sh http://10.1.2.100/ judger1 judg3r_p@ss /home/judge/
if [ $# != 4 ] ; then 
	head -7 $0
	exit 1; 
fi 

URL=$1
USER=$2
PASSWORD=$3
TARGET=$4
OSRS=`lsb_release -rs`
apt-get update
apt-get install -y subversion
/usr/sbin/useradd -m -u 1536 -s /sbin/nologin judge
mkdir -p $TARGET
cd $TARGET/
mkdir src
svn co https://github.com/zhblue/hustoj/trunk/trunk/core src/core
svn co https://github.com/zhblue/hustoj/trunk/trunk/install src/install
apt-get install -y make flex g++ libmysqlclient-dev libmysql++-dev
cd src/install
sed -i "s/ubuntu:22.04/ubuntu:$OSRS/g" Dockerfile
sudo bash docker.sh
cd ../..
CPU=`grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}'`
mkdir etc data log
cp src/install/java0.policy  $TARGET/etc
cp src/install/judge.conf  $TARGET/etc
chmod +x src/install/ans2out
if grep "OJ_SHM_RUN=0" etc/judge.conf ; then
	mkdir run0 run1 run2 run3
	chown judge run0 run1 run2 run3
fi
sed -i "s/OJ_HTTP_JUDGE=0/OJ_HTTP_JUDGE=1/g" etc/judge.conf
sed -i "s|OJ_HTTP_BASEURL=http://127.0.0.1/JudgeOnline|OJ_HTTP_BASEURL=$URL|g" etc/judge.conf
sed -i "s/OJ_HTTP_USERNAME=admin/OJ_HTTP_USERNAME=$USER/g" etc/judge.conf
sed -i "s/OJ_HTTP_PASSWORD=admin/OJ_HTTP_PASSWORD=$PASSWORD/g" etc/judge.conf
sed -i "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf
COMPENSATION=`grep 'mips' /proc/cpuinfo|awk -F: '{printf("%.2f",$2/5000)}'`
sed -i "s/OJ_CPU_COMPENSATION=1.0/OJ_CPU_COMPENSATION=$COMPENSATION/g" etc/judge.conf
sed -i "s/OJ_USE_DOCKER=0/OJ_USE_DOCKER=1/g" etc/judge.conf

cd src/core
chmod +x ./make.sh
if [ ! -e /usr/bin/judged ] ; then
	bash ./make.sh
fi
/usr/bin/judged $TARGET

