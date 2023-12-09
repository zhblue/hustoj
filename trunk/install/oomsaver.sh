#!/bin/bash

pgrep -f "/usr/sbin/sshd" | while read PID;do echo -17 > /proc/$PID/oom_score_adj;done
pgrep -f "php-fpm" | while read PID;do echo -17 > /proc/$PID/oom_score_adj;done
pgrep -f "mysql" | while read PID;do echo -17 > /proc/$PID/oom_score_adj;done
if test -e /swap ;then swapon /swap ;fi
echo 100 > /proc/sys/vm/swappiness
