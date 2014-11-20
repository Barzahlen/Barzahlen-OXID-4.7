#!/bin/bash
DST="barzahlen_oxid_4.7_5.0_plugin_v1.2.0"
if [ -d $DST ]; then
rm -R $DST
fi
mkdir -p $DST/src/copy_this/modules
cp license.txt $DST/license.txt
cp readme.txt $DST/readme.txt
cp changelog.txt $DST/src/changelog.txt
cp ../install.sql $DST/src/install.sql
cp ../update.sql $DST/src/update.sql
cp -r ../src/modules/bz_barzahlen/ $DST/src/copy_this/modules/bz_barzahlen/
zip -r $DST.zip $DST/*
rm -R $DST