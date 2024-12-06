#!/bin/bash
cd /home/judge/src/install || exit 1；
while ! apt-get install -y podman containerd
do
		echo "Network fail, retry... you might want to make sure podman is available in your apt source"
done

systemctl enable podman
service podman start

IP=`curl http://hustoj.com/ip.php`
while ! podman build -t hustoj .
do
                echo "Visit http://$IP to regist your admin account of HUSTOJ instance."
		echo "Left this console working on retry ... podman activation."
		echo "Network fail, retry... you might want to make sure podman image source is available"
done

sed -i "s/OJ_USE_DOCKER=0/OJ_USE_DOCKER=1/g" /home/judge/etc/judge.conf
sed -i "s/OJ_PYTHON_FREE=0/OJ_PYTHON_FREE=1/g" /home/judge/etc/judge.conf
sed -i "s/OJ_INTERNAL_CLIENT=1/OJ_INTERNAL_CLIENT=0/g" /home/judge/etc/judge.conf
sed -i "s|OJ_DOCKER_PATH=/usr/bin/docker|OJ_DOCKER_PATH=/usr/bin/podman|g" /home/judge/etc/judge.conf
pkill -9 judged
/usr/bin/judged
