
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
get_ids $xml_belgrano

