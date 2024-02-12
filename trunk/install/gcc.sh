#!/bin/bash
export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/snap/bin
PWD=$1
SHELL=/bin/bash
TERM=xterm
USER=www-data
#echo $1
cd $1
/usr/bin/gcc -o Main Main.c -lm 2>&1
