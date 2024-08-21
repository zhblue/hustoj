#!/bin/bash
IN_SCREEN=no
if echo "$TERM"|grep "screen" ; then
        IN_SCREEN=yes;
fi

if [ "$IN_SCREEN" == "no" ] ;then
        echo "not in screen";
        apt update
        apt install screen -y
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
        
        ALIPING=`LANG=c ping -c 5 mirrors.aliyun.com|grep ttl|awk '{print $8}'|awk -F= '{print $2*1000}'|sort -n|head -1`
        NEPING=`LANG=c ping -c 5 mirrors.163.com|grep ttl|awk '{print $8}'|awk -F= '{print $2*1000}'|sort -n|head -1`
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

