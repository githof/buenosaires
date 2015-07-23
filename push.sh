#/bin/sh
rsync -Cavuz --exclude-from .gitignore ./ buenosaires@www.liafa.univ-paris-diderot.fr:public_html/
