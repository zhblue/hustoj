#/bin/bash

OLD=$1
NEW=$2

if test ! -f "$OLD" ; then
    echo "$OLD does not exist."
    echo "Usage: $0  OLD_PATH/db_info.inc.php  NEW_PATH/db_info.inc.php "
    exit
fi

if test -z $2 ; then
   echo "Usage: $0  OLD_PATH/db_info.inc.php  NEW_PATH/db_info.inc.php "
   exit
fi

if test ! -f "$NEW" ; then
    echo "$NEW does not exist."
    echo "Usage: $0  OLD_PATH/db_info.inc.php  NEW_PATH/db_info.inc.php "
    exit
fi


VARLIST=`grep static $OLD | awk -F\; '{print $1}' |grep =`
rm  merge.sed
for VAR in $VARLIST
do
        if [[ $VAR =~ "\$" ]] ; then
                IFS='=' read -r KEY VALUE<<< $VAR
                if [[ $KEY =~ "AI" ]] ; then continue; fi
                if [[ $KEY =~ "FANCY" ]] ; then continue; fi
                if [[ $KEY =~ "BLOCKLY" ]] ; then continue; fi
                if [[ $KEY =~ "LOG" ]] ; then continue; fi
                VALUE=$( echo "$VALUE" | sed 's|"|\\"|g'  )
#               echo $VALUE
                echo -e 's|\'$KEY'=.*;|\'$KEY'='$VALUE';|' >> merge.sed
        fi
done
sed -i -f merge.sed $NEW 
rm merge.sed
