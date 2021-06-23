#!/bin/sh

rsync -Cavuz \
      --filter=":- .gitignore" \
      --filter='- .git' \
      "$BAHOME/" BA:www/
rsync -Cavuz \
      --filter='- .git' \
      --filter="- tests" \
      "$BAHOME/res/xmlselect/" \
      BA:www/res/xmlselect/
