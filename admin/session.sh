#!/bin/bash

DATA=DATA

diff_versions ()
{
    cd $DATA
    cat matrimonios-before-2016.xml \
	| tr -s ' ' \
	| sed 's#attr="de la"#de="true" la="true"#'
	| diff - export-2018.xml \
	       >| diff.xml
    cat diff.xml | sed -n 's#^.*<ACTE id="\([0-9]*\)">.*$#\1#p' | uniq | wc -l
}
diff_versions
