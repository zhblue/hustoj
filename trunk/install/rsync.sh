#!/bin/bash
#judge_client gives PID as 1st parameter
PID=$1
#source data should be on webserver
WEBSERVER=172.10.2.101
#ssh may using different port
PORT=22
#call rsync to get all data for this problem, id_rsa.pub should be installed to WEBSERVER's /home/judge/.ssh/autherized_keys
rsync -vzrtopg --progress --delete -e "ssh -p $PORT"  judge@$WEBSERVER:/home/judge/data/$PID /home/judge/data/
