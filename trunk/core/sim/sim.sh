#!/bin/bash
umask 0077
EXTENSION=`echo "$1" | cut -d'.' -f2`
FIRST=""
for i in `ls ../data/$2/ac/*.$EXTENSION`
do
        echo "i:$i"
        if [ ! -e "/usr/bin/sim_$EXTENSION" ]
        then
                EXTENSION="text";
        fi
        sim=`/usr/bin/sim_$EXTENSION -p $1 $i |grep ^$1|awk '{print $4}'`
        if [ ! -z "$sim" ] && [ "$sim" -gt 80 ]
        then
                sim_s_id=`basename $i`
                echo "$sim $sim_s_id" >sim
                exit $sim
        fi
        FIRST="false"
done
if [ -z "$FIRST" ] ;then
    echo "first answer"
else
    echo $FIRST
fi
echo "0 0" > sim
exit 0;
