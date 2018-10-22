
diff_versions ()
{
    cd $DATA
    cat matrimonios-before-2016.xml \
	| tr -s ' ' \
	| diff - export-2018.xml \
	       >| diff.xml
    cat diff.xml | sed -n 's#^.*<ACTE id="\([0-9]*\)">.*$#\1#p' | uniq | wc -l
}
