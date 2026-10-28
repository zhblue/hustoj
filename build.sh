#!/bin/sh

sudo apt-get -y install debhelper build-essential:native
set -ex \
&& git clone https://github.com/zhblue/hustoj.git \
&& git clone https://github.com/zhblue/hustoj-deb-ubuntu.git \
&& cp hustoj/trunk/* hustoj-deb-ubuntu \
&& chmod 711 -R hustoj-deb-ubuntu \
&& cd hustoj-deb-ubuntu && dpkg-buildpackage 
cd ..
#sudo dpkg -i *.deb || sudo apt-get install -f -y
exit 0
