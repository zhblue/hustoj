#FROM ubuntu:trusty-20171117
#COPY ./sources.list /etc/apt/sources.list 

FROM debian:jessie-slim
#COPY ./sources.debian.list /etc/apt/sources.list
	
COPY ./docker-entrypoint.sh /usr/local/bin/

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
	#	fp-compiler \
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
	
	&& /usr/sbin/useradd -m -u 1536 judge \
	
#FROM daocloud.io/shiningrise/hustoj-dev-base

# code
#RUN set -ex \
	&& cd / && git clone -b docker-dev https://github.com/shiningrise/hustoj.git \	
	&& mv /hustoj/trunk /home/judge/src \
# clear
	&& rm -R /hustoj \
	&& apt-get autoremove -y --purge git \
	
#
	&& USER=`cat /etc/mysql/debian.cnf |grep user|head -1|awk  '{print $3}'` \
	&& PASSWORD=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'` \
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
	&& sed -i "s/DB_USER=\"root\"/DB_USER=\"$USER\"/g" src/web/config/system.conf.php   \
	&& sed -i "s/DB_PASS=\"root\"/DB_PASS=\"$PASSWORD\"/g" src/web/config/system.conf.php   \
	&& chown www-data src/web/upload data   \

# judged
	&& cd /home/judge/src/core/judged \
    && make \
    && chmod +x judged \
    && cp judged /usr/bin \
    && cd ../judge_client \
    && make \
    && chmod +x judge_client \
    && cp judge_client /usr/bin \
	&& cd /home/judge/ \
# db
	&& chown -R mysql:mysql /var/lib/mysql  \
	&& service mysql restart \
	&& service mysql status \
	#&& USER=`cat /etc/mysql/debian.cnf |grep user|head -1|awk  '{print $3}'` \
	#&& PASSWORD=`cat /etc/mysql/debian.cnf |grep password|head -1|awk  '{print $3}'` \
	&& mysql -h localhost -u$USER -p$PASSWORD< /home/judge/src/install/db.sql \
	&& echo "insert into jol.privilege values('admin','administrator','N');"|mysql -h localhost -u$USER -p$PASSWORD \
	#&& service mysql stop \
	&& chmod +x /usr/local/bin/docker-entrypoint.sh \
	&& ln -s /usr/local/bin/docker-entrypoint.sh  /docker-entrypoint.sh

COPY nginx/default.conf /etc/nginx/sites-available/default
	
WORKDIR /home/judge/
EXPOSE 80
VOLUME ["/data"]

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
#CMD judged /home/judge debug && tail -f /dev/null
