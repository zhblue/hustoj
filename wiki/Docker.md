用Docker进行部署可以参考下面的内容
==


Dockerfile
--
下面这个Dockerfile用于构建包含hustoj的镜像，其内容可以按需进行扩充和修改，协议采用GPLv2。

```
FROM ubuntu:22.04
ENV DEBIAN_FRONTEND noninteractive
RUN     sed -i 's/archive.ubuntu/mirrors.aliyun/g' /etc/apt/sources.list &&\
        apt-get update && apt-get -y upgrade
RUN     DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends wget w3m apt-utils

# make Chinese Character works in Docker
RUN apt-get install -y locales locales-all
RUN locale-gen zh_CN.UTF-8 && dpkg-reconfigure locales && /usr/sbin/update-locale LANG=zh_CN.UTF-8
ENV LANG zh_CN.UTF-8
ENV LANGUAGE zh_CN:zh
ENV LC_ALL zh_CN.UTF-8

VOLUME ["/home/judge","/var/backups","/var/lib/mysql"]

RUN wget dl.hustoj.com/install-ubuntu22.04.sh
RUN bash install-ubuntu22.04.sh
RUN sed -i 's|DB_HOST="localhost"|DB_HOST="127.0.0.1"|g' /home/judge/src/web/include/db_info.inc.php
# override endl not to flush the io buffer
RUN  echo "std::ostream& endl(std::ostream& s) { \
s<<'\\\\n'; \
return s; \
}" >> `find /usr/include/c++/ -name iostream`
RUN usermod -d /var/lib/mysql mysql
RUN set -ex \
        && chown -R mysql:mysql /var/lib/mysql
# RUN  sed -i 's/80 default_server;/8080 default_server;/g' /etc/nginx/sites-enabled/default
RUN echo "#!/bin/bash\n service mysql start\nservice php8.1-fpm start \n judged \n nginx -g \"daemon off;\"">/start.sh
RUN chmod +x /start.sh
#EXPOSE 8080
ENTRYPOINT ["/start.sh"]
```

linux install cmd lines
--
下面的Linux命令可以下载上面的Dockerfile，并构建一个名为worker的镜像，运行一个名为hustojcontainer的容器，工作在宿主机的8080端口。
```
mkdir hustoj
cd hustoj
wget -O Dockerfile http://dl.hustoj.com/Dockerfile.worker
docker build . -t worker
docker run -d -p8080:80 --name hustojcontainer worker
docker ps 
```

linux restart cmd line
--
当宿主机重启或dockerd服务重启，需要手工启动容器hustojcontainer。
```
docker start hustojcontainer
```

# 最后，强烈建议用物理机或者云服务器直接部署，不要用docker部署。无论哪种情况，请做好数据备份。



另一种方法
----

### 基于 Docker 安装

基于 Docker 安装，可用于快速体验 HUSTOJ 的全部功能，**可能存在未知的魔法问题，请慎重考虑用于生产环境！！！**

使用构建好的 Docker 镜像（GitLab CI/CD系统自动构建）

```shell
docker run -d           \
    --name hustoj       \
    -p 8080:80          \
    -v ~/volume:/volume \
    registry.gitlab.com/mgdream/hustoj
```

由于 Web端/数据库/判题机 全部被打包在同一个镜像，无法扩展，不推荐使用此镜像做分布式判题，另外请不要在 Docker 中使用 SHM 文件系统，会由于内存空间不足无法挂载沙箱环境而导致莫名其妙的运行错误

部署后使用浏览器访问 <http://localhost:8080>

### 基于Docker安装（分布式）

Docker分布式改造基本完成，目前支持web/mysql/judge基础镜像，支持使用环境变量进行配置。
目前judge镜像仍处于不稳定状态，有能力的用户对`docker/judge`进行完善。

在本地执行前需要先创建Docker网络`docker network create hustoj`，使用下面的命令来运行对应的服务。

- MySQL服务

```shell script
docker run -d \
    --network hustoj \
    --name hustoj.mysql \
    -e MYSQL_USER=<mysql_username> \
    -e MYSQL_PASSWORD=<mysql_password> \
    -v mysql:/var/lib/mysql \
    registry.gitlab.com/mgdream/hustoj:mysql
```

基础镜像为mysql:5.7，所有的环境变量都继承自[mysql:5.7](https://hub.docker.com/_/mysql)官方镜像，默认提供数据库为`jol`。

- Web服务

```shell script
docker run -d \
    --network hustoj
    --name hustoj.web \
    -e DB_HOST=<mysql_server> \
    -e DB_NAME=<mysql_database> \
    -e DB_USER=<mysql_username> \
    -e DB_PASS=<mysql_password> \
    -v data:/home/judge/data \
    -p 80:80 \
    registry.gitlab.com/mgdream/hustoj:web
```

基础镜像为ubuntu:18.04，使用php版本为php7.2，所有的环境变量都继承自db_info.inc.php文件，后续会完善php与nginx的环境变量配置。
