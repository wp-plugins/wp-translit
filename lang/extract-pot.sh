#!/bin/sh

find ../ -iname "*.php" >files.tmp && \
xgettext --language=PHP --indent --keyword=__ --keyword=_e --keyword=__ngettext:1,2 -s -n --from-code=UTF8 -o messages.pot -f files.tmp && \
rm files.tmp
