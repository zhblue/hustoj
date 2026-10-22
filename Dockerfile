FROM ubuntu:24.04

#COPY trunk/install/sources.list.sh /opt/sources.list.sh

ARG APT_MIRROR="Y"
ARG APT_CA="N"

RUN [ "$APT_CA" = "Y" ] && apt-get -y update && apt install -y ca-certificates  || true

# Linux: Aliyun Apt Mirrors.
RUN [ "$APT_MIRROR" != "N" ] && bash /opt/sources.list.sh || true

RUN apt-get -y update  && \
    apt-get -y upgrade && \
    DEBIAN_FRONTEND=noninteractive \
    apt-get -y install --no-install-recommends \
        nginx \
        mysql-server \
        libmysqlclient-dev \
        libmysql++-dev \
        php-common \
        php-fpm \
        php-mysql \
        php-gd \
        php-zip \
        php-mbstring \
        php-xml \
        php-yaml \
        make \
        flex \
        gcc \
        g++ 
      
COPY trunk /trunk

COPY docker/ /opt/docker/

RUN bash /opt/docker/setup.sh

# VOLUME [ "/volume", "/home/judge/backup", "/home/judge/data", "/home/judge/etc", "/home/judge/web", "/var/lib/mysql" ]
VOLUME [ "/volume" ]
EXPOSE 80

ENTRYPOINT [ "/bin/bash", "/opt/docker/entrypoint.sh" ]
