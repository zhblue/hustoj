#!/bin/bash
cd /home/judge/src/install || exit 1；
dpkg --configure -a
while ! apt-get install -y podman containerd
do
		echo "Network fail, retry... you might want to make sure podman is available in your apt source"
done

systemctl enable podman
service podman start
#!/bin/bash

OSRS=`lsb_release -rs`

# 最大尝试次数
max_attempts=5
# 当前尝试次数
attempt=0

# 循环尝试构建
while [ $attempt -lt $max_attempts ]; do
    echo "Attempt $((attempt + 1)) of $max_attempts"
    if podman build -t hustoj . ; then
        echo "Docker build succeeded"
	sed -i "s/OJ_USE_DOCKER=0/OJ_USE_DOCKER=1/g" /home/judge/etc/judge.conf
	sed -i "s/OJ_PYTHON_FREE=0/OJ_PYTHON_FREE=1/g" /home/judge/etc/judge.conf
	sed -i "s|OJ_DOCKER_PATH=/usr/bin/docker|OJ_DOCKER_PATH=/usr/bin/podman|g" /home/judge/etc/judge.conf
	pkill -9 judged
	/usr/bin/judged
        exit 0
    else
        echo "Docker build failed, retrying..."
        attempt=$((attempt + 1))
    fi
done

echo "Failed after $max_attempts attempts"

echo "Network fail, retry... you might want to make sure https://hub.docker.com/ is available"
echo "Docker image failed, try download from temporary site ... "
while ! wget -O hustoj.docker.tar.bz2  http://dl3.hustoj.com/docker/hustoj.docker.$OSRS.tar.bz2
do
	echo "Download archive image file fail , try again..."
done
bzip2 -d hustoj.docker.tar.bz2
if podman load -i hustoj.docker.tar 
then
    sed -i "s/OJ_USE_DOCKER=0/OJ_USE_DOCKER=1/g" /home/judge/etc/judge.conf
	sed -i "s/OJ_PYTHON_FREE=0/OJ_PYTHON_FREE=1/g" /home/judge/etc/judge.conf
	sed -i "s|OJ_DOCKER_PATH=/usr/bin/docker|OJ_DOCKER_PATH=/usr/bin/podman|g" /home/judge/etc/judge.conf
	pkill -9 judged
	/usr/bin/judged
    rm hustoj.docker.tar
fi

