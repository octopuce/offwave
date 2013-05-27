#!/bin/sh

# This script insert all files and folders of all versions of an application into a MySQL table.

# Thanks to that, you can find the files/folder that appear in ALL versions of the application
# and files / folders that appears only in ONE version of the application.
# therefore, building an easy way to recognize a specific version

MYSQL="mysql -uoffwave -pOophah2a offwave_recognize"

# Prepare the environment: 
$MYSQL -e "DROP TABLE IF EXISTS auto;"
$MYSQL -e "CREATE TABLE IF NOT EXISTS auto (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  version varchar(128) NOT NULL,
  filename varchar(255) NOT NULL,
  type varchar(8) CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (id),
  KEY version (version),
  KEY filename (filename)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='List of files and folders for all versions of an application' AUTO_INCREMENT=1 ;"

cd testenv
for files in JOOMLA-*.file
do
    echo -n "$files files..." 
    VERSION="`echo $files | sed -e 's/^JOOMLA-//' -e 's/.file$//' `"
    while read FILENAME
    do
	echo "INSERT INTO auto SET version='$VERSION', filename='$FILENAME', type='file';"
    done <$files | $MYSQL
    echo " done"
done

for folders in JOOMLA-*.folder
do
    echo -n "$files folders..." 
    VERSION="`echo $folders | sed -e 's/^JOOMLA-//' -e 's/.folder$//' `"
    while read FOLDERNAME
    do
	echo "INSERT INTO auto SET version='$VERSION', filename='$FOLDERNAME', type='folder';"
    done <$folders | $MYSQL
    echo " done"
done

