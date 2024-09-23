set -xe

# Mysql
mkdir -p /var/run/mysqld
chown -R mysql:mysql /var/run/mysqld
chmod -R 755         /var/run/mysqld
service mysql start
mysql < /trunk/install/db.sql
mysql -e "insert into jol.privilege ( user_id, rightstr ) values('admin','administrator');"
mysql -e "insert into jol.problem(problem_id,title,time_limit,memory_limit,defunct) values(1000,1,1,5,'N');"
mysql -e "insert into jol.source_code values(1,'#include<stdio.h>\nint main(){\nint a,b;\nscanf(\"%d%d\",&a,&b);\nprintf(\"%d\\\\n\",a+b);\n}\n');"
mysql -e "insert into jol.solution (solution_id,user_id,problem_id,ip,in_date) values(1,'1',1000,'127.0.0.1',now());"

# Hustoj basic file system
useradd -m -u 1536 judge
mkdir -p /home/judge/etc
mkdir -p /home/judge/run0
mkdir -p /home/judge/data/1000
echo "1 2" >  /home/judge/data/1000/1.in
echo "3" >  /home/judge/data/1000/1.out
mkdir -p /home/judge/log
mkdir -p /home/judge/backup
mkdir -p /var/log/hustoj
mv /trunk /home/judge/src
chmod -R 700 /home/judge/etc
chmod -R 700 /home/judge/backup
chmod -R 700 /home/judge/src/web/include/
chown judge /home/judge/run0
chmod 750 /home/judge/run0
chown -R www-data:www-data /home/judge/data
chown -R www-data:www-data /home/judge/src/web 

# Judge daemon and client
make      -C /home/judge/src/core/judged
make      -C /home/judge/src/core/judge_client
make exes -C /home/judge/src/core/sim/sim_3_01
cp /home/judge/src/core/judged/judged                /usr/bin/judged
cp /home/judge/src/core/judge_client/judge_client    /usr/bin/judge_client 
cp /home/judge/src/core/sim/sim_3_01/sim_c.exe       /usr/bin/sim_c
cp /home/judge/src/core/sim/sim_3_01/sim_c++.exe     /usr/bin/sim_cc
cp /home/judge/src/core/sim/sim_3_01/sim_java.exe    /usr/bin/sim_java
cp /home/judge/src/core/sim/sim.sh                   /usr/bin/sim.sh
cp /home/judge/src/install/hustoj                    /etc/init.d/hustoj
chmod +x /home/judge/src/install/ans2out
chmod +x /usr/bin/judged
chmod +X /usr/bin/judge_client
chmod +x /usr/bin/sim_c
chmod +X /usr/bin/sim_cc
chmod +x /usr/bin/sim_java
chmod +x /usr/bin/sim.sh


# Adjust system configuration
CPU=`grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}'`
USERNAME=`cat /etc/mysql/debian.cnf |grep user    |head -1|awk  '{print $3}'`
PASSWORD=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'`
PHP_VER=`apt-cache search php-fpm|grep -e '[[:digit:]]\.[[:digit:]]' -o`
NBUFF=512
if [ "$PHP_VER" = "" ] ; then PHP_VER="8.1"; fi

for pkg in sudo w3m net-tools make g++ php$PHP_VER-fpm nginx mysql-server php$PHP_VER-mysql php$PHP_VER-common php$PHP_VER-gd php$PHP_VER-zip php$PHP_VER-mbstring php$PHP_VER-xml php$PHP_VER-curl php$PHP_VER-intl php$PHP_VER-xmlrpc php$PHP_VER-soap php-yaml tzdata
do
	while ! apt-get install -y "$pkg" 
	do
		echo "Network fail, retry... you might want to change another apt source for install"
	done
done

cp /home/judge/src/install/java0.policy  /home/judge/etc/
cp /home/judge/src/install/judge.conf    /home/judge/etc/

sed -i "s#OJ_USER_NAME[[:space:]]*=[[:space:]]*root#OJ_USER_NAME=$USERNAME#g"    /home/judge/etc/judge.conf
sed -i "s#OJ_PASSWORD[[:space:]]*=[[:space:]]*root#OJ_PASSWORD=$PASSWORD#g"      /home/judge/etc/judge.conf
sed -i "s#OJ_COMPILE_CHROOT[[:space:]]*=[[:space:]]*1#OJ_COMPILE_CHROOT=0#g"     /home/judge/etc/judge.conf
sed -i "s#OJ_RUNNING[[:space:]]*=[[:space:]]*1#OJ_RUNNING=$CPU#g"                /home/judge/etc/judge.conf
sed -i "s#OJ_SHM_RUN[[:space:]]*=[[:space:]]*1#OJ_SHM_RUN=0#g"                   /home/judge/etc/judge.conf
sed -i "s#DB_USER[[:space:]]*=[[:space:]]*\"root\"#DB_USER=\"$USERNAME\"#g"                  /home/judge/src/web/include/db_info.inc.php
sed -i "s#DB_PASS[[:space:]]*=[[:space:]]*\"root\"#DB_PASS=\"$PASSWORD\"#g"                  /home/judge/src/web/include/db_info.inc.php

	echo "modify the default site"
	sed -i "s#root /var/www/html;#root /home/judge/src/web;#g" /etc/nginx/sites-enabled/default
	sed -i "s:index index.html:index index.php:g" /etc/nginx/sites-enabled/default
	sed -i "s:#location ~ \\\.php\\$:location ~ \\\.php\\$:g" /etc/nginx/sites-enabled/default
	sed -i "s:#\tinclude snippets:\tinclude snippets:g" /etc/nginx/sites-enabled/default
	sed -i "s|#\tfastcgi_pass unix|\tfastcgi_pass unix|g" /etc/nginx/sites-enabled/default
	sed -i "s:}#added by hustoj::g" /etc/nginx/sites-enabled/default
	sed -i "s:php7.4:php$PHP_VER:g" /etc/nginx/sites-enabled/default
	sed -i "s|# deny access to .htaccess files|}#added by hustoj\n\n\n\t# deny access to .htaccess files|g" /etc/nginx/sites-enabled/default
        sed -i "s|fastcgi_pass 127.0.0.1:9000;|fastcgi_pass 127.0.0.1:9000;\n\t\tfastcgi_buffer_size 256k;\n\t\tfastcgi_buffers $NBUFF 64k;|g" /etc/nginx/sites-enabled/default

sed -i "s/post_max_size = 8M/post_max_size = 500M/g" /etc/php/$PHP_VER/fpm/php.ini     
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 500M/g" /etc/php/$PHP_VER/fpm/php.ini 

if grep "client_max_body_size" /etc/nginx/nginx.conf ; then 
    echo "client_max_body_size already added" ;
else
    sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    280m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf
fi

# Nginx & PHP starting test
PHP_INIT=`find /etc/init.d -name "php*-fpm"`
PHP_SERVICE=`basename $PHP_INIT`
service nginx restart
service $PHP_SERVICE start
judge_client 1 0 /home/judge/ | grep "final result:4"
cd /home/judge/src/web
chmod 755 /home/judge

for page in `cat test.lst` 
do 
	if w3m -dump http://127.0.0.1/$page |grep HUSTOJ > /dev/null ; then 
 		echo "OK        $page "  
   	else 
    		echo "Fail         $page" 
      	fi
done

echo "Testing template bs3 ..."
sed -i 's/static  $OJ_TEMPLATE="syzoj";/static  $OJ_TEMPLATE="bs3";/' include/db_info.inc.php
for page in `cat test.lst` 
do 
	if w3m -dump http://127.0.0.1/$page |grep HUSTOJ > /dev/null ; then 
 		echo "OK        $page "  
   	else 
    		echo "Fail         $page" 
      	fi
done
sed -i 's/static  $OJ_TEMPLATE="bs3";/static  $OJ_TEMPLATE="syzoj";/' include/db_info.inc.php
exit 0
#w3m -dump http://127.0.0.1/status.php | grep 'AWT'
#w3m -dump http://hustoj.com/ip.php
#ls -lh /home/judge/run0/log/
#cat /home/judge/run0/log/ce.txt
#cat /home/judge/run0/log/Main.c

