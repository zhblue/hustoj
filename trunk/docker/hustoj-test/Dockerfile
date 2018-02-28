FROM daocloud.io/shiningrise/hustoj-dev-base

COPY nginx/default.conf /etc/nginx/sites-available/default	
COPY ./docker-entrypoint.sh /usr/local/bin/

# code
RUN set -ex \
	&& cd / && git clone -b docker-test https://github.com/shiningrise/hustoj.git \	
	&& mv /hustoj/trunk /home/judge/src \
# clear
	&& rm -R /hustoj \
#	&& apt-get autoremove -y --purge git \
	
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


	
WORKDIR /home/judge/
EXPOSE 80
VOLUME ["/data"]

ENTRYPOINT ["/docker-entrypoint.sh"]
#CMD judged /home/judge debug && tail -f /dev/null
