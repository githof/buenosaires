
xml_belgrano=DATA/grep-belgrano.xml

get_ids ()
{
    f="$1"
    cat $f \
	| sed 's#id="\([0-9]*\)".*#@\1@#gp' \
	| tr '@' '\n' \
	| grep '^[0-9][0-9]*$' \
	| sort -u
}
ids=`get_ids $xml_belgrano`

grep_ids ()
{
    ids=$1
    f=$2
    regexp=`echo $ids | sed 's# #\\\\\\\\|#g'`
    regexp='\\('$regexp'\\)'
    echo "$regexp"
    cat $f \
	| grep "$regexp"
}
grep_ids "404 413"
