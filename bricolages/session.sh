
xml_belgrano=DATA/grep-belgrano.xml
actes=DATAWEB/actes.csv
# id_acte,epoux,epouse,periode
relations=DATAWEB/relations.csv
# id,personne1,personne2,type,periode

bids=belgrano.ids

get_ids ()
{
    f="$1"
    cat $f \
	| sed 's#id="\([0-9]*\)".*#@\1@#gp' \
	| tr '@' '\n' \
	| grep '^[0-9][0-9]*$' \
	| sort -u
}
# ids=`get_ids $xml_belgrano`
# get_ids $xml_belgrano > $bids

grep_ids ()
{
    ids=$1
    f=$2
    regexp=`echo $ids | sed 's# #\\\\\\\\|#g'`
    regexp='\\('$regexp'\\)'
    echo "$regexp"
    # Bon, j'y arrive pas :(
    cat $f \
	| grep "$regexp"
}
# grep_ids "404 413"

filter_csv ()
{
    f_ids=$1
    csv=$2
    field=$3
    if [ "$field" != "" ]
    then
	optfield_join="-2 $field"
	optfield_sort="-k $field,$field"
    fi
    cat $csv \
	| sort $optfield_sort \
	| join -t ',' $optfield_join $f_ids $csv
}
filter_csv $bids $relations 2

