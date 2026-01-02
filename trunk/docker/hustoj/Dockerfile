FROM ubuntu:24.04
# COPY ./sources.list /etc/apt/sources.list  
# /etc/apt/sources.list
#FROM debian:jessie-slim

RUN set -ex \
	&& echo 'mariadb-server mysql-server/root_password password ""' | debconf-set-selections \
	&& echo 'mariadb-server mysql-server/root_password_again password ""' |debconf-set-selections \
	&& apt-get update \
	&& apt-get install -y \
		git \
		make flex g++  \
		libmariadb-dev-compat libmariadb-dev \
		php8.3-fpm php8.3-mysql php8.3-gd php8.3-mbstring php8.3-xml php8.3-zip php8.3-curl\
		php8.3-memcache memcached \
		nginx \
		#fp-compiler \
		mariadb-server \
		#openjdk-7-jdk \
		#clang \
	#&& apt-get remove --purge git
	&& rm -rf /var/lib/apt/lists/* 
	
RUN set -ex \
	&& /usr/sbin/useradd -m -u 1536 judge \
	&& cd / && git clone https://github.com/zhblue/hustoj.git \	
	&& mv /hustoj/trunk /home/judge/src \
	&& rm -R /hustoj 
	
RUN set -ex \
	&& USER=hustoj \
	&& PASSWORD=`tr -cd '[:alnum:]' < /dev/urandom | fold -w30 | head -n1` \
	&& CPU=`grep "cpu cores" /proc/cpuinfo |head -1|awk '{print $4}'`   \	
	&& cd /home/judge/                  \
	&& mkdir etc data log   \
	&& cp src/install/java0.policy  /home/judge/etc   \
	&& cp src/install/judge.conf  /home/judge/etc   \
	&& mkdir run0 run1 run2 run3   \
	&& chown judge run0 run1 run2 run3   \
	&& sed -i "s/OJ_USER_NAME=root/OJ_USER_NAME=$USER/g" etc/judge.conf   \
	&& sed -i "s/OJ_PASSWORD=root/OJ_PASSWORD=$PASSWORD/g" etc/judge.conf   \
	&& sed -i "s/OJ_RUNNING=1/OJ_RUNNING=$CPU/g" etc/judge.conf   \
	&& sed -i "s/DB_USER=\"root\"/DB_USER=\"$USER\"/g" src/web/include/db_info.inc.php   \
	&& sed -i "s/DB_PASS=\"root\"/DB_PASS=\"$PASSWORD\"/g" src/web/include/db_info.inc.php   \
	&& chown www-data src/web/upload data   \
	&& sed -i "s:include /etc/nginx/mime.types;:client_max_body_size    80m;\n\tinclude /etc/nginx/mime.types;:g" /etc/nginx/nginx.conf   \
	&& chown -R mysql:mysql /var/lib/mysql \
	&& sed -i "s:root /var/www/html;:root /home/judge/src/web;:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:index index.html:index index.php:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#location ~ \\\.php\\$:location ~ \\\.php\\$:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tfastcgi_split_path_info:\tfastcgi_split_path_info:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tfastcgi_pass unix:\tfastcgi_pass unix:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:php7.4-fpm:php8.3-fpm:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tfastcgi_index:\tfastcgi_index:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tinclude fastcgi_params;:\tinclude fastcgi_params;\n\t}:g" /etc/nginx/sites-enabled/default   \
	&& sed -i "s:#\tinclude snippets/fastcgi-php.conf;:\tinclude snippets/fastcgi-php.conf;:g" /etc/nginx/sites-enabled/default   \
	&& sed -i 's/\t#}/\t}/g' /etc/nginx/sites-enabled/default \
	&& sed -i 's/#location/location/g' /etc/nginx/sites-enabled/default \
	&& sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php/8.3/fpm/php.ini     \
	&& sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php/8.3/fpm/php.ini \
	# mariadb配置
	&& chown -R mysql:mysql /var/lib/mysql  \
	&& sed -i 's/^bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/' /etc/mysql/mariadb.conf.d/50-server.cnf \
	&& service mariadb restart \
	&& sleep 5 \
	&& service mariadb status \
	&& mysql -e "CREATE DATABASE IF NOT EXISTS jol;" \
	&& mysql -e "CREATE USER IF NOT EXISTS '$USER'@'%' IDENTIFIED BY \"$PASSWORD\";" \
	&& mysql -e "CREATE USER IF NOT EXISTS '$USER'@'localhost' IDENTIFIED BY \"$PASSWORD\";" \
	&& mysql -e "GRANT ALL PRIVILEGES ON jol.* TO '$USER'@'%';" \
	&& mysql -e "FLUSH PRIVILEGES;" \
	&& service mariadb restart \
	&& mysql -h localhost -u$USER -p"$PASSWORD" < /home/judge/src/install/db.sql \
	&& mysql -h localhost -u$USER -p"$PASSWORD" -e "INSERT INTO jol.privilege VALUES('admin','administrator','true','N');" \
	&& service mariadb stop 

RUN set -ex \
	&& cd /home/judge/src/core/judged \
    && make \
    && chmod +x judged \
    && cp judged /usr/bin \
    && cd ../judge_client \
    && make \
    && chmod +x judge_client \
    && cp judge_client /usr/bin \
	&& cd /home/judge/
	
RUN chmod +x /home/judge/src/core/make.sh
RUN cd /home/judge/src/core/ && ./make.sh
RUN cd /usr/bin && rm awk && cp -s mawk awk

WORKDIR /home/judge/
EXPOSE 80
VOLUME ["/data"]

COPY ./docker-entrypoint.sh /usr/local/bin/
RUN set -ex \
	&& chmod +x /usr/local/bin/docker-entrypoint.sh \
	&& ln -s /usr/local/bin/docker-entrypoint.sh  /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]
#CMD judged /home/judge debug && 
#CMD tail -f /dev/null

