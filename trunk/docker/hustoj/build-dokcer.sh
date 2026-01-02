
docker build -f Dockerfile -t hustoj-dev  ./

docker rm -f /hustoj-dev

#rm -R /home/test/
#mkdir -p /home/test
#chmod -R 777 /home/test/

#docker run -d -it --privileged --name hustoj-dev -p 80:80 hustoj-dev
docker run -d -it --privileged --name hustoj -p 8080:80 -v /data/hustoj/:/data shiningrise/hustoj:20260102
#docker run -d -it --name hustoj-dev -p 80:80 hustoj-dev
docker exec -i -t hustoj-dev /bin/bash

SELECT user, host FROM mysql.user;
CREATE USER 'hustoj'@'localhost' IDENTIFIED BY 'hustoj';
CREATE USER 'hustoj'@'%' IDENTIFIED BY 'hustoj';
FLUSH PRIVILEGES;

docker build -f Dockerfile -t hustoj  ./
docker build -f Dockerfile --no-cache -t hustoj  ./
