#!/bin/sh

if [ "$1" -a -f "$1" ] ; then

	# first make messages.pot
	find ../ -iname "*.php" >files.tmp && \
	xgettext --language=PHP --indent --keyword=__ --keyword=_e --keyword=__ngettext:1,2 -s -n --from-code=UTF8 -o messages.pot -f files.tmp && \
	rm files.tmp

	# now update po file(s)
	for po in $@ ; do
		cp $po $po.old
		echo "Updating $po..."
		msgmerge $po.old messages.pot > $po
	done

else

echo <<EOF
Usage:

        ./`basename $0` lang.po lang2.po ...

to update one or more <yourlang>.po files from the php files in
parent directory. The old .po file is save in a .po.old file.

When you e.g. want to update wpt-sr_RS.po, run ./`basename $0` wpt-sr_RS.po,
then edit wpt-sr_RS.po to update your translation.

After updating your translation, to compile wpt-sr_RS.po file use command:
msgfmt -o wpt-sr_RS.mo wpt-sr_RS.po
or replace sr_RS with locale for your language.

EOF

fi

