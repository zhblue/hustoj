FROM ubuntu:22.04

COPY trunk/install/sources.list.sh /opt/sources.list.sh

# Linux: Aliyun Apt Mirrors.
RUN bash /opt/sources.list.sh &&\
    apt-get -y update  && \
    apt-get -y upgrade && \
    DEBIAN_FRONTEND=noninteractive \
    apt-get -y install --no-install-recommends \
        libmysqlclient-dev \
        libmysql++-dev \
        make \
        flex \
        gcc \
        g++ \
        openjdk-11-jdk

COPY trunk /trunk

COPY docker/ /opt/docker/

RUN bash /opt/docker/judge/setup.sh

# sharing data volume with web container
VOLUME [ "/home/judge/data" ]

ENV HOSTNAME=localhost

ENV DATABASE=jol

ENV USERNAME=root

ENV PASSWORD=root

ENTRYPOINT [ "/bin/bash", "/opt/docker/judge/entrypoint.sh" ]
