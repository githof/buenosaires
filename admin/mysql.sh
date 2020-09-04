#!/bin/sh

# mysql simple :
# mysql.sh

# dump :
# mysql.sh mysqldump > dump.sql

# Depuis la ligne de commande pour mettre dans un fichier :
# mysql -h mysql-projet.enst.fr --user=buenosaires --password=`cat pass-bd` -e 'show tables'

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
    shift
    echo "args: $*"
    $cmd -h mysql-projet.enst.fr --user=buenosaires --password=$PASS $* buenosaires
}
run $cmd "$*"
