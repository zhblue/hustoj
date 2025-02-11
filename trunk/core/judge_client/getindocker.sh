#!/bin/bash
echo "This script is only for test, all modification will be lost until you add them to the Dockerfile and run docker.sh / podman.sh "
/usr/bin/docker container run --pids-limit 100 --rm --cap-add SYS_PTRACE --cap-add CAP_SYS_ADMIN --net=host -v /home/judge:/home/judge -it hustoj /bin/bash
