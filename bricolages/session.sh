
xml=LASTDATA/matrimonios.xml
periode=LASTDATA/periode.xml
corpus=LASTDATA/corpus.xml
periode_test=LASTDATA/periode-test.xml
xml_belgrano=DATA/grep-belgrano.xml
actes=DATAWEB/actes.csv
# id_acte,epoux,epouse,periode
relations=DATAWEB/relations.csv
# id,personne1,personne2,type,periode

REGEXP_EPOUX_BALISES='<epoux.*<prenom.*<epouse.*<prenom.*</epouse>'

bids=belgrano.ids
actesb=actes-belgrano.ids

xml ()
{
  cat "$xml"
}

corpus ()
{
  cat "$corpus"
}

periode ()
{
  cat "$periode"
}

on ()
{
  src="$1"
  shift
  "$src" | $*
}
# on xml grep_prenom pastor lezica

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
    regexp=`echo " $ids" | sed 's# # -e id="#g'`
    echo "$regexp"
    cat "$f" \
	| grep $regexp
}
# grep_ids "404 413" $xml_belgrano

ids_actes ()
{
  sed -n 's#^<ACTE id="\([0-9]*\)".*$#\1#p'
}
# get_by_date 1799 | ids_actes

extract_actes ()
{
    ids=$1
    regexp=`echo " $ids" | sed 's# \([0-9][0-9]*\)# -e ACTE.id="\1"#g'`
	  grep $regexp
}
# xml | extract_actes "806 925 927"
# xml | extract_actes "2434 2435 3756 3757 528 529 530 531 534 538 538 539 540 556 557 558 558 559 566 568 570 571 572 572 573 574 576 577 578 578 579 580 582 594 595 596 597 604 606 642 643 648 649 654 654 655 656 656 657 688 689 766 767 782 783 784 785 788 789 789 790 791 803 804 805 806 807 811 813 911 912 913 913 914 915 915 916 917 919 920 921 922 925 927 929"

acte ()
{
  id=$1
  xml \
  | grep 'ACTE id="'$id'"'
}
#acte 2434

# ids=`cat  actes-belgrano.ids`
# echo $ids
# xml | extract_actes "$ids"

ids_personnes ()
{
  sed 's#<ACTE id="[0-9]*">##' \
  | grep -o 'id="[0-9]*"' \
  | sed 's#id="\([0-9]*\)"#\1#' \
  | sort -n
}
# xml | ids_personnes | tail

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

#___________________________________________________________________
# nov 2019 : corpus

dates ()
{
  sed -n 's#^<ACTE id="\([0-9]*\)">.*<date>\([0-9-]*\)</date>.*$#\1;\2#p'
}
# xml | dates > dates.csv

annees_triees ()
{
  dates \
  | sed 's#[0-9]*-[0-9]*-##' \
  | sort -t';' -k2,2
}
# xml | annees_triees > acte-annee-sorted.csv

grep_date ()
{
  date=$1
  grep "$date</date>"
}
# xml | grep_date 1799

get_by_date ()
{
  date=$1
  xml \
  | grep_date "$date"
}
# get_by_date 1799

grep_decade ()
{
  decade=$1
  grep "$decade[0-9]</date>"
}
# xml | grep_decade 179

get_by_decade ()
{
  decade="$1"
  xml \
  | grep_decade "$decade"
}
# get_by_decade 179

get_decades ()
{
  decades="$*"

  for dec in $decades
  do
    get_by_decade $dec
  done
}
# get_decades 178 179 180

make_decades ()
{
  century=$1
  shift
  ddd="$*"
  decades=""
  for d in $ddd
  do
    decades="$decades $century$d"
  done
  echo $decades
}
#   make_decades 17 4 5 6 7 8 9

get_periode ()
{
  decades=`make_decades 17 4 5 6 7 8 9`
  get_decades $decades
  decades=`make_decades 18 0 1 2`
  get_decades $decades
}
# get_periode | grep_nom belgrano

get_periode_test ()
{
  corpus \
  | grep_date '\(179[5-9]\|1800\)'
}
# get_periode_test

grep_nom ()
{
  nom="$1"
  grep -i $nom
}
# xml | grep_nom lezica

grep_prenom ()
{
  prenom="$1"
  nom="$2"
  grep -i "$prenom.*$nom"
}
# xml | grep_prenom pastor lezica

grep_noms ()
{
  file="$1"
  grep -i -f $file
}
# xml | grep_noms noms.txt

get_by_nom_date ()
{
  nom="$1"
  date="$2"
  get_by_date "$date" \
  | grep_nom "$nom"
}
# get_by_nom_date Lezica 1799

epoux_non_balises ()
{
  grep -v "$REGEXP_EPOUX_BALISES"
}
# get_periode | grep_nom lezica | epoux_non_balises | annees_triees > actes-lezica-todo.csv

scan_nom ()
{
  xml \
  | grep_nom "$*" \
  | epoux_non_balises \
  | less
}
# scan_nom caballero

scan_prenom ()
{
  prenom="$1"
  nom="$2"
  xml \
  | grep_prenom "$prenom" "$nom" \
  | epoux_non_balises \
  | less
}
# scan_prenom francisco caballero

update_periode_corpus ()
{
  get_periode \
  >| $periode

  cat $periode \
  | grep "$REGEXP_EPOUX_BALISES" \
  >| $corpus

  get_periode_test \
  >| $periode_test
}

update_git ()
{
  if [ "$1" = "" ]
  then
    echo "message ? [update matrimonios]"
    read msg
    if [ "$msg" = "" ]
    then
      msg="update matrimonios"
    fi
  else
    msg="$1"
  fi

  export=~/Downloads/export.xml
  cat "$export" \
  | grep -v '^ *$' \
  >| $xml
  rm "$export"

  cd LASTDATA
  git ci "$msg"
  if [ $? -ne 0 ]
  then
    echo "pb git (conflit ?)"
    echo "j'arrête ici."
    exit 1
  fi
  cd -
  update_periode_corpus
  cd -
  git ci 'update periode corpus'
  echo 'ATTENTION !!'
  echo "Tu es dans $PWD"
}

noms_complets ()
{
  grep -oE '(<prenom[^>]*>[^<]*<\/prenom>[^<]*)+([^<]*<nom[^>]*>[^<]*<\/nom>)+'
#  sed -nE 's#(<[pre]*nom[^>]*>[^<]*<\/[pre]*nom>[^<]*)#@@@\1#pg'
#  | tr '@' '\n' \
#  | sed 's#<nom[^>]*>\([^<]*\)<\/nom>.*$#\1' \
}
# acte 2794 | noms_complets

untag ()
{
  sed 's#<[^>]*>##g'
}
# acte 10535 | noms_complets | untag

noms_acte ()
{
  acte="$1"
  acte "$acte" \
  | noms_complets \
  | untag
}
# noms_acte 10535

noms ()
{
  grep -o '<nom[^>]*>[^<]*<\/nom>' \
  | sed 's#<[^>]*> *##g' \
  | sort \
  | uniq -c \
  | sort -r
}
# xml | extract_actes 2434 | noms

nom_plus_un ()
{
  nom="$1"
  periode \
  | grep_nom "$nom" \
  | noms
}
# nom_plus_un belgrano

nom_non_balise ()
{
  nom="$1"
  periode \
  | grep_nom "$nom" \
  | epoux_non_balises
}

non_balises_annee ()
{
  annee=$1
  periode \
  | grep_date $annee \
  | epoux_non_balises \
  | ids_actes
}
# non_balises_annee 1795 > NICOLE/actes-1795.txt

#_______________________________________________________________________
#___ big bug import 2017 ___

before=DATA/matrimonios-before-2016-bug.xml
after=DATA/matrimonios-2017-after-bug.xml
before_join=DATA/matrimonios-before-2016-bug-join
after_join=DATA/matrimonios-2017-after-bug-join
ids_bug=DATA/ids-bug

id_colon_xml ()
{
    xml_or_none=$1
    awkbs='BEGIN { OFS=FS=":" }'
    awkbs=$awkbs'{ if(NF > 1) { printf("%5d", $1); $1="";}'
    awkbs=$awkbs'  print $0 }'

    cat $xml_or_none \
	| sed 's#\(<ACTE[^>]* id="\)\([0-9]*\)"#\2:\1\2"#' \
	| awk "$awkbs"
}
# head -20 $before | id_colon_xml > test-colon
# cat $before | id_colon_xml | head -1700 | tee test-colon2 > /dev/null

rm_id_colon ()
{
    f_or_none=$1
    cat $f_or_none \
	| sed 's#^ *[0-9]*:##' \

}
# cat test-colon | rm_id_colon | head -3

ids_id_colon ()
{
    f_or_none=$1
    cat $f_or_none \
	| sed -n 's#^\( *[0-9]*\):.*$#\1#p' \

}
# ids_id_colon test-colon  | head -3

before_bug ()
{
    cat "$xml" \
	| sed 's#\(ACTE[^>]*\) num="#\1 id="#' \
	      | tee test-id.xml \
	| id_colon_xml \
	      | tee test-num.xml \
	| sort -t ':' -n \
	      | tee test-sort.xml \
	| sed -e '1{H;d;}' -e '${p;x;s/^\n//;}' \
	      | tee test-end.xml \
	| sed 's#^[^:]*:##' \
	      > $before
    # pour le sed qui met la première ligne à la fin :
    # https://stackoverflow.com/questions/26433652/sed-move-multiple-lines-to-the-end-of-a-text-file#answer-26433778
}
# before_bug

ids_diff_before_after ()
{
    diff -y --suppress-common-lines $before $after \
	| sed -n 's#^<ACTE[^>]* id="\([0-9]*\)".*$#\1#p' \
	| awk '{ printf("%5d\n", $1) }'
}
# ids_diff_before_after > $ids_bug

join_xml ()
{
    xml=$1
    out=`echo $xml | sed 's#\.xml#-join#'`
    cat $xml \
	| id_colon_xml \
	| grep '^ *[0-9]*:' \
	| join -t ':' $ids_bug - \
	      > $out
}
# join_xml $before

trunc ()
{
    sed 's#^\(...........................................\).*$#\1#'
}

diff_before_after ()
{
    join_xml $before
    join_xml $after
    ids_id_colon $before_join > ids-before
    ids_id_colon $after_join > ids-after
    diff -y --suppress-common-lines ids-before ids-after > ids-diff
    # -> 107 ids dans ids-before qui ne sont pas dans ids-after,
    # tous ceux que j'ai testés sont effectivement absents de after xml
}
# diff_before_after

get_one_from_join ()
{
    acte=$1
    f_or_none=$2
    cat $f_or_none \
	| sed -n 's#^ *'$acte':\(.*\)$#\1#p'
}
# get_one_from_join 1 $before_join

newline_rm_id ()
{
    f_or_none=$1
    cat $f_or_none \
	| rm_id_colon \
	| sed 's#>#>@#g' \
	| tr '@' '\n'
}

diff_with_newlines ()
{
    cat $before_join \
	| newline_rm_id \
	      > before-newlines
    cat $after_join \
	| newline_rm_id \
	      > after-newlines
    diff -y --suppress-common-lines before-newlines after-newlines \
	 > diff-newlines

    patternattr='<nom [^>]*attr='
    cat diff-newlines \
	| grep "$patternattr" \
	       > diff-attr
    cat diff-newlines \
	| grep -v "$patternattr" \
	       > diff-others
}
# diff_with_newlines
# cat diff-others | grep '<epoux.*<epoux'

#___________________________________________________________________
# oct 2018 : tests diff 2016 / 2018

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
# diff_versions

nettoie_diff ()
{
    cd $DATA
    cat diff.xml \
	| grep -v '^[0-9]*a[0-9]*,[0-9]*$' \
	       > diff-2006-2008.xml
}
# nettoie_diff


$*
