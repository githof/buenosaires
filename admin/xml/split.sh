#!/bin/bash

xml=$1
lines=$2

header=DATA/header.xml
footer=DATA/footer.xml

if ! test -r "$header" -o ! test -r "$footer"
then
    (
	echo "erreur"
	echo "j'ai besoin de $header et $footer"
	echo "qui sont sans doute ceci :"
	echo "head -1 $1"
	head -1 $1
	echo "tail -1 $1"
	tail -1 $1
    ) >&2
    exit
fi

cat $xml \
    | tail -n +2 \
    | ghead -n -1 \
    | split -a 5 -l $lines - "$xml"

for f in "$xml"?????
do
    new=`echo $f | sed 's#.xml\(.....\)$#-\1.xml`
    mv $f $new
done

