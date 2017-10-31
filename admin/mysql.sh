#!/bin/sh

# mysql simple :
# mysql.sh

# dump :
# mysql.sh mysqldump > dump.sql

PASS=`cat pass-bd`

if [ "$1" = "" ]
then
    cmd=mysql
else
    cmd="$1"
    shift
fi

run ()
{
    cmd="$1"
    $cmd -h mysql-projet.enst.fr --user=buenosaires --password=$PASS buenosaires
}
run $cmd $*
