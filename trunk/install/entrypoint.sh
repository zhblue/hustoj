#!/bin/bash
PHP=`find /etc/init.d/ -name "php*"`
MYSQL=`find /etc/init.d/ -name "mariadb*"`
NGINX=`which nginx`

$PHP start
$MYSQL start
$NGINX -g "daemon off;"
