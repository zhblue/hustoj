FROM ubuntu:trusty-20171117
COPY ./sources.list /etc/apt/sources.list 

#FROM debian:jessie-slim
#COPY ./sources.debian.list /etc/apt/sources.list

RUN set -ex \
	&& apt-get update \
	&& apt-get install -y \
		git \
		make flex g++ libmysqlclient-dev libmysql++-dev \
		php5-fpm php5-mysql php5-gd \
		php5-memcache memcached \
		curl libcurl3 libcurl3-dev php5-curl \
		nginx \
		python \
		python3 \
		fp-compiler \
	#	openjdk-7-jdk \
	#	clang
# mysql
	&& echo 'mysql-server-5.5 mysql-server/root_password password ""' | debconf-set-selections \
	&& echo 'mysql-server-5.5 mysql-server/root_password_again password ""' |debconf-set-selections \
	#&& apt-get update 
	&& apt-get install -y mysql-server \
	&& rm -rf /var/lib/apt/lists/* \
	
	&& sed -i "s/post_max_size = 8M/post_max_size = 80M/g" /etc/php5/fpm/php.ini     \
	&& sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 80M/g" /etc/php5/fpm/php.ini   \
	&& sed -i "s/max_execution_time = 30/max_execution_time = 180/g" /etc/php5/fpm/php.ini   \
	&& sed -i "s/max_input_time = 60/max_input_time = 360/g" /etc/php5/fpm/php.ini   \
	&& sed -i "s/memory_limit = 128M/memory_limit = 256M/g" /etc/php5/fpm/php.ini   \
	
	&& /usr/sbin/useradd -m -u 1536 judge 
	