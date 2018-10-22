#!/bin/bash

DATA=DATA

no_blank_lines ()
{
    grep -v '^>* *$'
}

adjust_before ()
{
    tr -s ' ' \
       | no_blank_lines \
       | sed 's# de="true" la="true"# attr="de la"#g' \
       | sed 's# de="true"# attr="de"#g' \
       | sed 's# y="true"# attr="y"#g'
}

by_words ()
{
    tr ' ' '\n' \
       | grep -v '^ *$'
}

diff_versions ()
{
    cd $DATA
    before=matrimonios-before-2016.xml
    now=export-2018.xml
    cat $before \
	| adjust_before \
	| diff - $now \
	| no_blank_lines \
	       >| diff.xml
    cat diff.xml \
	| sed -n 's#^.*<ACTE id="\([0-9]*\)">.*$#\1#p' \
	| uniq \
	| wc -l
    cat $now \
	| by_words \
	     > now-words.txt

    cat $before \
	| adjust_before \
	| by_words \
	| diff - now-words.txt \
	       >| diff-words.txt    
}
diff_versions
