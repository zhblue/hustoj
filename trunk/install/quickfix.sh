#!/bin/bash
if grep security.ubuntu.com/ubuntu  /etc/apt/sources.list 
then
    echo "already have repo"
else
    echo deb http://security.ubuntu.com/ubuntu `lsb_release -c|awk '{print $2}'`-security main restricted universe multiverse >> /etc/apt/sources.list
fi

apt-get update 
apt-get upgrade

PHP_FPM=$(service --status-all|grep php|awk '{print $4}')
apt-get install redis-server php-redis
for PHP_INI in `find /etc/php/ -name "php.ini"` 
do 
    # CVE-2026-109618,CVE-2026-109619
    echo "fixing ... $PHP_INI"
    sed -i 's|session.use_strict_mode = 0|session.use_strict_mode = 1|'  $PHP_INI
    sed -i 's|;session.upload_progress.enabled = On|session.upload_progress.enabled = Off|'  $PHP_INI
    sed -i 's|session.save_handler *= *files|session.save_handler = redis\nsession.save_path="tcp://127.0.0.1:6379"|'  $PHP_INI
done 

service $PHP_FPM restart
/etc/init.d/$PHP_FPM restart
