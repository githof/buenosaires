#/bin/sh
rsync -Cavuz --exclude-from .git-ignore ./ buenosaires@www.liafa.univ-paris-diderot.fr:public_html/
