#!/bin/bash

# 颜色定义
GREEN="\e[32m"
RED="\e[31m"
YELLOW="\e[33m"
NC="\e[0m"

#detect and refuse to run under WSL
if [ -d /mnt/c ]; then
    echo "WSL is NOT supported."
    exit 1
fi

# 检查是否为 root 用户
if [ "$EUID" -ne 0 ]; then
    echo -e "\e[31m❌ 当前不是 root 用户，请使用 root 用户运行本脚本。\e[0m"
    echo -e "\e[31m❌ This script must be run as root. Please switch to root user.\e[0m"
    exit 1
fi

clear

cat <<'EOF'
  _  _         _      ___     _ 
 | || |_  _ __| |_   / _ \ _ | |
 | __ | || (_-<  _| | (_) | || |
 |_||_|\_,_/__/\__|  \___/ \__/ 

                 __              __ 
  __ _ __ _____/ /_ _____ ___ _/ / 
 /  ' \\ \ / _  / // / -_) _ `/ _ \
/_/_/_/_\_\\_,_/\_, /\__/\_,_/_//_/
               /___/               
                        
HUST OnlineJudge Platform
Baota Panel Install Script
Powered by mxd.

!!!!!!!!!!!!!!!!!!!!!!!!!!
Make sure you read the documentation before deploying: 
请确保在部署之前阅读过文档：
https://blog.mxdyeah.top/mxdyeah_blog_post/hustoj_install_debian12_bt.html

Please make sure that you have followed the tutorial to enable some PHP functions.
请确保已经按照教程放开PHP某些函数。

##########################
After three seconds, the installation will starting.
Press Ctrl+C to stop...
三秒后开始安装，Ctrl+C退出。

##########################
初版脚本，暂时不支持Docker判题，有需要请先单独运行docker.sh
有问题请联系邮箱：i@mxdyeah.top


EOF

sleep 3

echo "请输入你的MySQL信息: "
echo "Input your database username:"
read USER
echo "Input your database password:"
read PASSWORD

# 目录检测项
declare -A paths=(
  [nginx]="/www/server/nginx"
  [mysql]="/www/server/mysql"
  [php]="/www/server/php"
)

missing=()

echo -e "${YELLOW}系统组件检测 / System Component Check:${NC}"

for name in "${!paths[@]}"; do
    path="${paths[$name]}"
    printf "%-20s" "检查 $name / Check $name"
    if [ -d "$path" ]; then
        printf "%*s\n" 40 "${GREEN}[OK]${NC}"
    else
        printf "%*s\n" 40 "${RED}[Failed]${NC}"
        missing+=("$name")
    fi
done

echo ""

# 是否缺组件提示
if [ ${#missing[@]} -ne 0 ]; then
    echo -e "${RED}以下组件未安装 / The following components are missing:${NC}"
    for m in "${missing[@]}"; do
        echo "- $m"
    done
    echo ""
    echo -e "如需强制继续，请输入 ${YELLOW}1${NC}，退出请输入 ${YELLOW}2${NC}： / Enter ${YELLOW}1${NC} to force continue, ${YELLOW}2${NC} to exit:"
    read -p "> " choice
    if [ "$choice" != "1" ]; then
        echo -e "${RED}脚本已退出 / Script exited.${NC}"
        exit 1
    else
        echo -e "${YELLOW}继续执行脚本 / Continuing...${NC}"
    fi
else
    echo -e "${GREEN}所有组件已安装，继续执行脚本 / All components are installed. Continuing...${NC}"
fi

# Debian 12 软件包是 mariadb 而不是 mysql

apt update && apt -y upgrade
for pkg in bzip2 flex net-tools make g++ tzdata mysql-common software-properties-common subversion libmariadbd-dev libmariadb-dev libmariadb-dev 
do
        while ! apt-get install -y "$pkg"
        do
                dpkg --configure -a
                apt-get install -f
                echo "Network fail, retry..."
        done
done

# 解决宝塔收集用户信息问题

chattr +i /www/server/panel/script/site_task.py
chattr +i -R /www/server/panel/logs/request

/usr/sbin/useradd -m -u 1536 -s /sbin/nologin judge
cd /home/judge/ || exit

#using tgz src files
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz
svn up src
#svn co https://github.com/zhblue/hustoj/trunk/trunk/  src

CPU=$(nproc)
#CPU=$(lscpu | grep '^CPU(s):' | awk '{print $2}')

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
sed -i "s/DB_NAME[[:space:]]*=[[:space:]]*\"root\"/DB_NAME=\"$USER\"/g" src/web/include/db_info.inc.php
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


COMPENSATION=$(grep 'mips' /proc/cpuinfo|head -1|awk -F: '{printf("%.2f",$2/5000)}')
sed -i "s/OJ_CPU_COMPENSATION=1.0/OJ_CPU_COMPENSATION=$COMPENSATION/g" etc/judge.conf

cd src/core/judged  || exit
g++ -Wall -c -DOJ_USE_MYSQL  -I/www/server/mysql/include judged.cc
g++ -Wall -o judged judged.o -L/www/server/mysql/lib -lmysqlclient
cd ..
chmod +x ./make.sh
bash make.sh
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

# 改了php.ini，面板还会再改回来，这里让用户手动去面板改。
systemctl enable judged
# PHP_INI=`find /www/ -name php.ini`
# sed -i 's/passthru,exec,system,/passthru,exec,/g'  $PHP_INI
# shutdown warning message for php in BT Panel
# sed -i 's#//ini_set("display_errors", "On");#ini_set("display_errors", "Off");#g' /home/judge/src/web/include/db_info.inc.php


mkdir /var/log/hustoj/
chown www -R /var/log/hustoj/
cd /home/judge/src/install
# Use trixie Debian 13
sed -i "s/ubuntu:22.04/debian:trixie/g" Dockerfile
sed -i "s/libmysqlclient-dev/default-libmysqlclient-dev/" Dockerfile
sed -i "s/openjdk-17-jdk/gcc/" Dockerfile
# if test -f  /.dockerenv ;then
#         echo "Already in docker, skip docker installation, install some compilers ... "
#         apt intall -y flex fp-compiler openjdk-17-jdk mono-devel
# else
#         bash docker.sh
#         sed -i "s/OJ_USE_DOCKER=0/OJ_USE_DOCKER=1/g" /home/judge/etc/judge.conf
#         sed -i "s/OJ_PYTHON_FREE=0/OJ_PYTHON_FREE=1/g" /home/judge/etc/judge.conf
# fi
IP=`curl http://hustoj.com/ip.php`
LIP=`ip a|grep inet|grep brd|head -1|awk '{print $2}'|awk -F/ '{print $1}'`
clear

echo "Remember your database account for HUST Online Judge:"
echo "username:$USER"
echo "password:$PASSWORD"
echo "DO NOT POST THESE INFORMATION ON ANY PUBLIC CHANNEL!"
echo "Register a user as 'admin' on your website."
echo "打开宝塔面板对应网站，注册用户admin，获得管理员权限。"
echo "如果无法打开页面或无法注册用户，请检查上方数据库账号是否能正常连接数据库。"
echo "如果发现数据库账号登录错误，可用sudo bash /home/judge/src/install/fixdb.sh 尝试修复。"
echo "更多宝塔面板安装问题请在博客文档下方留言区留言。"
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
# echo "请注意，Debian12 (Bookworm) 仓库默认只安装"
# echo "OpenJDK17, 若要升级版本, 需要先卸载openjdk-17-*"
# echo "apt purge openjdk-17-*"
# echo "再通过其他方法安装最新版本OpenJDK"
# echo "如果启用Docker判题，默认也是OpenJDK17，可手动更改"
# echo "Dockerfile 来修改默认的 JDK 版本。"

