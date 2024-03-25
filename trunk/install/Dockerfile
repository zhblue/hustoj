FROM ubuntu:22.04
ENV DEBIAN_FRONTEND noninteractive
#RUN     echo "nameserver 8.8.8.8" >> /etc/resolv.conf
#RUN     echo "100.100.2.148 mirrors.cloud.aliyuncs.com" >> /etc/hosts
RUN     apt-get update && apt-get -y upgrade

RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends        apt-utils 	
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends        libmariadb-dev 
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends        libmysqlclient-dev 
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends        libmysql++-dev 
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends        build-essential 
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends        flex 
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends       g++ 
#RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends       python 
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends        python3 
#RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends	fp-compiler 
#RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends	mono-devel 
#RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends	busybox 
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends	dos2unix
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends        openjdk-17-jdk
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends	subversion 
RUN    	DEBIAN_FRONTEND=noninteractive  apt-get -y install --no-install-recommends	wget
RUN     apt-get -y install sqlite3
RUN     useradd -m -u 1536 judge
RUN     cd /home/judge/ && \ 
	wget dl.hustoj.com/hustoj.tar.gz && \
        tar xzf hustoj.tar.gz 
#Github update
#RUN	svn up /home/judge/src 
RUN     cd /home/judge/src/core/ && bash make.sh 

# for more compilers, VMs and runtimes , remove # and run "docker build -t hustoj ."

#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends ruby
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends gobjc
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends clang
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends guile-3.0
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends lua5.3
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends nodejs
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends golang
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends sqlite3
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends gfortran
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends octave
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends gnucobol
#RUN     DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends r-base

# install debian package of similarity-tester
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install --no-install-recommends similarity-tester
RUN cp /usr/bin/sim_c++ /usr/bin/sim_cc
# make Chinese Character works in Docker
RUN apt-get install -y locales locales-all
RUN locale-gen zh_CN.UTF-8 && dpkg-reconfigure locales && /usr/sbin/update-locale LANG=zh_CN.UTF-8
ENV LANG zh_CN.UTF-8  
ENV LANGUAGE zh_CN:zh  
ENV LC_ALL zh_CN.UTF-8 

# override endl not to flush the io buffer
RUN echo "#ifndef HUSTOJ">>`find /usr/include/c++/ -name iostream`
RUN echo "#define HUSTOJ">>`find /usr/include/c++/ -name iostream`
RUN echo "std::ostream& endl(std::ostream& s) {">>`find /usr/include/c++/ -name iostream`
RUN echo "s<<'\\\\n'; ">>`find /usr/include/c++/ -name iostream`
RUN echo "return s; ">>`find /usr/include/c++/ -name iostream`
RUN echo "}">>`find /usr/include/c++/ -name iostream`
RUN echo "#endif">>`find /usr/include/c++/ -name iostream`
