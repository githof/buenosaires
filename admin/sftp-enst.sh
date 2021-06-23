#!/bin/sh

rsync -Cavuz \
      --filter=":- .gitignore" \
      --filter='- .git' \
      "$BAHOME" enst:buenosaires/
rsync -Cavuz \
      --filter='- .git' \
      --filter="- tests" \
      "$BAHOME/res/xmlselect/" \
      enst:buenosaires/res/xmlselect/
