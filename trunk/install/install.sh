#!/bin/bash
IN_SCREEN=no
if echo "$TERM"|grep "screen" ; then
        IN_SCREEN=yes;
fi

if [ "$IN_SCREEN" == "no" ] ;then
        echo "not in screen";
        apt update
        if ! apt install screen -y  ; then
                echo " 自动更新进程或其他工具锁定了apt目录，安装无法继续，请终止相关进程或重启后操作。"
                echo " apt locked , stop auto update proccess and try again"
                exit
        fi
        chmod +x $0
        screen bash $0 $*
else
        echo "in screen";
        OSID=`lsb_release -is|tr 'UDC' 'udc'`
        OSRS=`lsb_release -rs`
        INSTALL="install-$OSID$OSRS.sh"
        URL="http://dl.hustoj.com/$INSTALL"
        wget -O "$INSTALL" "$URL"
        chmod +x "$INSTALL"
        
        ALIPING=`LANG=c ping -c 5 mirrors.aliyun.com|grep ttl| awk -F= '{print $4}'|awk '{print $1*1000}'|sort -n|head -1`
        NEPING=`LANG=c ping -c 5 mirrors.163.com    |grep ttl| awk -F= '{print $4}'|awk '{print $1*1000}'|sort -n|head -1`
        echo "aliyun:$ALIPING"
        echo "netease:$NEPING"
        if [ "$ALIPING" -gt "$NEPING" ] ; then
                echo "163 is faster"
                sed -i 's/aliyun/163/g'  "./$INSTALL"
        else
                echo "aliyun is faster"
        fi

        "./$INSTALL"
        echo "不要重复运行这个脚本，如果不能访问，检查80端口是否打开，ip地址是否正确。"
        echo "公网地址可能是：http://"`curl http://hustoj.com/ip.php`
        sleep 60;
fi

config="/home/judge/etc/judge.conf"
VIRTUAL="/var/www/virtual/"
SERVER=`cat $config|grep 'OJ_HOST_NAME' |awk -F= '{print $2}'`
USER=`cat $config|grep 'OJ_USER_NAME' |awk -F= '{print $2}'`
PASSWORD=`cat $config|grep 'OJ_PASSWORD' |awk -F= '{print $2}'`
DATABASE=`cat $config|grep 'OJ_DB_NAME' |awk -F= '{print $2}'`
PORT=`cat $config|grep 'OJ_PORT_NUMBER' |awk -F= '{print $2}'`
IP=`curl http://hustoj.com/ip.php`
LIP=`ip a|grep inet|grep brd|head -1|awk '{print $2}'|awk -F/ '{print $1}'`
cd /home/judge/src/web/
wget dl.hustoj.com/hello.tar.gz
tar xzf hello.tar.gz
chown www-data -R hello
clear
reset

echo "Remember your database account for HUST Online Judge:"
echo "username:$USER"
echo "password:$PASSWORD"
echo "DO NOT POST THESE INFORMATION ON ANY PUBLIC CHANNEL!"
echo "Register a user as 'admin' on http://127.0.0.1/ "
echo "打开http://127.0.0.1/ 或者 http://$IP  或者 http://$LIP 注册用户admin，获得管理员权限。"
echo "如果无法打开页面或无法注册用户，请检查上方数据库账号是否能正常连接数据库。"
echo "如果发现数据库账号登录错误，可用sudo bash /home/judge/src/install/fixdb.sh 尝试修复。"
echo "遇到服务器内部错误500，查看/var/log/nginx/error.log末尾，寻找详细原因。"
echo "更多问题请查阅http://hustoj.com/"
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
