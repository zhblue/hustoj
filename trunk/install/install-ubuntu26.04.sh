#!/bin/bash

# 针对 Ubuntu 26.04 LTS (Resolute Raccoon) 的 HUSTOJ 安装脚本

# 检查是否在 WSL 下运行
if [ -d /mnt/c ]; then
    echo "警告：不建议在 WSL 下运行，建议使用原生 Ubuntu 26.04 环境。"
fi

MEM=`free -m|grep Mem|awk '{print $2}'`
NBUFF=512
if [ "$MEM" -lt "2000" ] ; then
        echo "内存小于 2GB，正在配置 Swap..."
        NBUFF=128
        if grep 'swap' /etc/fstab ; then
                echo "交换分区已存在。"
        else
                dd if=/dev/zero of=/swap bs=2M count=1024
                chmod 600 /swap
                mkswap /swap
                swapon /swap
                echo "/swap none swap defaults 0 0 " >> /etc/fstab 
                # 26.04 中 snapd 依然存在，但我们可以优化其内存占用
                pkill -9 snapd
         fi
else
        echo "内存容量 : $MEM MB"
        apt-get install -y memcached
fi

# 更新软件源至 2026 年主流镜像 (阿里云)
# 26.04 使用新版 deb822 格式的情况较多，但传统的 sources.list 依然有效
sed -i 's/tencentyun/aliyun/g' /etc/apt/sources.list
sed -i 's/cn.archive.ubuntu/mirrors.aliyun/g' /etc/apt/sources.list
# 解决自动重启提示问题
if [ -f /etc/needrestart/needrestart.conf ]; then
    sed -i "s|#\$nrconf{restart} = 'i'|\$nrconf{restart} = 'a'|g" /etc/needrestart/needrestart.conf
fi

apt-get update && apt-get -y upgrade

# 安装基础依赖
apt-get install -y software-properties-common
add-apt-repository -y universe
add-apt-repository -y multiverse
add-apt-repository -y restricted

apt-get update

# 创建判题用户
/usr/sbin/useradd -m -u 1536 -s /sbin/nologin judge

cd /home/judge/ || exit

# 获取源码
wget -O hustoj.tar.gz http://dl.hustoj.com/hustoj.tar.gz
tar xzf hustoj.tar.gz

# 数据库依赖
apt-get install -y libmysqlclient-dev libmysql++-dev libmariadb-dev-compat libmariadb-dev mariadb-server

# 动态探测 PHP 版本 (26.04 默认为 8.3 或 8.4)
PHP_VER=`apt-cache search php-fpm|grep -e '[[:digit:]]\.[[:digit:]]' -o | head -n 1`
if [ "$PHP_VER" = "" ] ; then PHP_VER="8.3"; fi
echo "检测到 PHP 版本为: $PHP_VER"

# 安装全套组件
for pkg in bzip2 flex fail2ban net-tools make g++ php$PHP_VER-fpm nginx php$PHP_VER-mysql php$PHP_VER-common php$PHP_VER-gd php$PHP_VER-zip php$PHP_VER-mbstring php$PHP_VER-xml php$PHP_VER-curl php$PHP_VER-intl php$PHP_VER-soap php-memcached php-yaml php-apcu tzdata
do
        apt-get install -y "$pkg" || {
            dpkg --configure -a
            apt-get install -f -y
            apt-get install -y "$pkg"
        }
done

# 启动服务
service php$PHP_VER-fpm start
service mariadb start
service nginx start

# 配置权限
chgrp www-data /home/judge
chmod +x /home/judge/src/install/*

# 数据库初始化
USER="hustoj"
PASSWORD=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1`
mysql < src/install/db.sql
echo "CREATE USER IF NOT EXISTS $USER identified by '$PASSWORD'; grant all privileges on jol.* to $USER; flush privileges;" | mysql

# 判题核心配置
CPU=$(grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}')
mkdir -p etc data log backup
cp src/install/java0.policy /home/judge/etc
cp src/install/judge.conf /home/judge/etc
chmod +x src/install/ans2out /home/judge/src/install/*.sh

# 根据内核调度优化
if [ "$CPU" -lt "1" ]; then CPU=1; fi
for N in `seq 0 $(($CPU-1))`
do
   mkdir -p run$N
   chown judge run$N
done

# 写入配置文件
sed -i "s/OJ_USER_NAME=.*/OJ_USER_NAME=$USER/g" etc/judge.conf
sed -i "s/OJ_PASSWORD=.*/OJ_PASSWORD=$PASSWORD/g" etc/judge.conf
sed -i "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf

# Nginx 性能微调
sed -i "s/php7.4/php$PHP_VER/g" /etc/nginx/sites-enabled/default
sed -i "s|fastcgi_pass 127.0.0.1:9000;|fastcgi_pass 127.0.0.1:9001;\n\t\tfastcgi_buffer_size 256k;\n\t\tfastcgi_buffers $NBUFF 64k;|g" /etc/nginx/sites-enabled/default

# PHP 性能微调
sed -i "s/post_max_size = 8M/post_max_size = 500M/g" /etc/php/$PHP_VER/fpm/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 500M/g" /etc/php/$PHP_VER/fpm/php.ini
# 26.04 默认已启用大部分 JIT 优化，这里做确认
if ! grep -q "opcache.jit_buffer_size" /etc/php/$PHP_VER/fpm/php.ini; then
    echo "opcache.jit_buffer_size=32M" >> /etc/php/$PHP_VER/fpm/php.ini
fi

# 编译 Core
cd src/core || exit
chmod +x ./make.sh
./make.sh
cp judged /usr/bin/

# 设置自启动
systemctl enable nginx mariadb php$PHP_VER-fpm fail2ban
/usr/bin/judged

# Docker 适配：26.04 环境下使用 ubuntu:26.04 基础镜像
if test -f /.dockerenv ;then
        apt-get install -y flex fp-compiler openjdk-21-jdk mono-devel
else
        # 针对 26.04 更新 Dockerfile 模板
        if [ -f Dockerfile ]; then
            sed -i 's/ubuntu:24/ubuntu:26/g' Dockerfile
            sed -i 's|/usr/include/c++/11|/usr/include/c++/13|g' Dockerfile
            bash docker.sh
        fi
fi

echo "------------------------------------------------------"
echo "HUSTOJ 安装完成！"
echo "数据库账号: $USER"
echo "数据库密码: $PASSWORD"
echo "请访问系统并注册 admin 用户获取权限。"
echo "------------------------------------------------------"
