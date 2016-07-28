
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
    attr=$2
    f=$3
    regexp=`echo " $ids" | sed 's# # -e id="#g'`
    echo "$regexp"
    cat $f \
	| grep $regexp
}
grep_ids "404 413" num $xml_belgrano

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
	| sort -t ',' $optfield_sort \
	| join -t ',' $optfield_join $f_ids -
}
# filter_csv $bids $relations 2

filter_and_cut ()
{
    f_ids=$1
    csv=$2
    field_in=$3
    field_out=$4
    filter_csv $* \
	| cut -d ',' -f $field_out
}

actes_from_relations ()
{
    (
	filter_and_cut $bids $relations 2 2
	filter_and_cut $bids $relations 3 2
    ) \
	| sort
}
# actes_from_relations > actes-belgrano.ids
