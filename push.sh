#/bin/sh
rsync -Cavuz --exclude-from .rsync-exclude ./ buenosaires@www.liafa.univ-paris-diderot.fr:public_html/
