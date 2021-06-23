#!/bin/bash

xml=$1
lines=$2

header=DATA/header.xml
footer=DATA/footer.xml

check-header-footer ()
{
    if test ! \( -r "$header" -a -r "$footer" \)
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
}
check-header-footer

split-body ()
{
  cat $xml \
  | grep '^ *<ACTE ' \
	| split -a 5 -l $lines - "$xml"
}
split-body

chunks ()
{
    for f in "$xml"?????
    do
	new=`echo $f | sed 's#.xml\(.....\)$#-\1.xml#'`
	cat $header $f $footer > $new
	rm $f
    done
}
chunks
