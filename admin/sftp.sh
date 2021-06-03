#!/bin/sh

rsync -Cavuz \
      --omit-dir-times \
      --chmod=g+w \
      --filter=":- .gitignore" \
      --filter='- .git' \
      "$BAHOME" BA:www/
rsync -Cavuz \
      --filter='- .git' \
      --filter="- tests" \
      "$BAHOME/res/xmlselect/" \
      BA:www/res/xmlselect/
