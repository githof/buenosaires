
xml_belgrano=DATA/grep-belgrano.xml

cat $xml_belgrano \
    | sed -n 's#id="\([0-9]*\)"#\1@#gp' \
    | tr '@' '\n'

