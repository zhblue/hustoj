#!/bin/bash

for PHP_INI in `find /etc/php/ -name "php.ini"` 
do 
    # CVE-2026-109618,CVE-2026-109619
    echo "fixing ... PHP_INI"
    sed -i 's|session.use_strict_mode = 0|session.use_strict_mode = 1|'  $PHP_INI
    sed -i 's|;session.upload_progress.enabled = On|session.upload_progress.enabled = Off|'  $PHP_INI
done 

PHP_FPM=$(service --status-all|grep php|awk '{print $4}')
service $PHP_FPM restart
/etc/init.d/$PHP_FPM restart
