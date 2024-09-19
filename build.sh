#!/bin/sh

sudo apt-get install debhelper
set -ex \
&& git clone https://github.com/zhblue/hustoj.git \
&& git clone https://github.com/zhblue/hustoj-deb-ubuntu.git \
&& mv hustoj/trunk/* hustoj-deb-ubuntu \
&& cd hustoj-deb-ubuntu && dpkg-buildpackage 
cd ..
sudo dpkg -i *.deb || sudo apt-get install -f -y
exit 0
