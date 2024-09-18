FROM ubuntu:22.04

COPY trunk/install/sources.list.sh /opt/sources.list.sh

# Linux: Aliyun Apt Mirrors.
RUN bash /opt/sources.list.sh && \
    apt-get -y update  && \
    apt-get -y upgrade && \
    DEBIAN_FRONTEND=noninteractive \
    apt-get -y install --no-install-recommends \
        nginx \
        php-common \
        php-fpm \
        php-mysql \
        php-gd \
        php-zip \
        php-mbstring \
        php-yaml \
        php-xml

COPY trunk/ /trunk/

COPY docker/ /opt/docker/

RUN bash /opt/docker/web/setup.sh

# sharing data volume with judge container
VOLUME [ "/home/judge/data" ]

ENTRYPOINT [ "/bin/bash", "/opt/docker/web/entrypoint.sh" ]
