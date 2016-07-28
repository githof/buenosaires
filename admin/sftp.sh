#!/bin/sh

rsync -Cavuz --filter=":- .gitignore" "$BAHOME" BA:www/
