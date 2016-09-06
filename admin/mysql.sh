#!/bin/sh

PASS=`cat pass-bd`
mysql -h mysql-projet.enst.fr --user=buenosaires.TPT --password=$PASS buenosaires_TPT
