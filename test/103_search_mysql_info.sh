#!/bin/sh

# This script search for common files in all versions of an application
# and different file in all versions of an application

MYSQL="mysql -uoffwave -pOophah2a offwave_recognize"

echo "; This .INI has been automatically computed by search_mysql_info.sh"

VERSIONS="`$MYSQL --skip-column-name -B -e 'SELECT COUNT(DISTINCT version) FROM auto'`"
echo "; Your application have $VERSIONS different versions"
echo ""
echo "; The following FOLDERS appears in ALL of them:"
echo "[allversion]"
$MYSQL --skip-column-name -B -e "SELECT CONCAT( SUBSTRING(filename,3) ,'=dir') FROM auto WHERE type='folder' AND filename!='.' GROUP BY filename HAVING count(*)=$VERSIONS"

echo ""

echo "; The following FOLDERS appears in ONE version:"
$MYSQL --skip-column-name -B -e "SELECT CONCAT(version,'#'), CONCAT( SUBSTRING(filename,3) ,'=dir') FROM auto WHERE type='folder' AND filename!='.' GROUP BY filename HAVING count(*)=1" >/tmp/onefolder.$$
$MYSQL --skip-column-name -B -e "SELECT CONCAT(version,'#'), CONCAT( SUBSTRING(filename,3) ,'=file') FROM auto WHERE type='file' AND filename!='.' GROUP BY filename HAVING count(*)=1" >/tmp/onefile.$$
for VERSION in ` $MYSQL --skip-column-name -e "SELECT DISTINCT version FROM auto ORDER BY 1 "` 
do
    cat /tmp/onefolder.$$ | grep "^${VERSION}#" | awk '{print $2}' >/tmp/search_mysql_info.$$
    if [ "`wc -l /tmp/search_mysql_info.$$ | awk '{print $1}'`" -gt "0" ]
    then
	echo "[$VERSION]"
	cat /tmp/search_mysql_info.$$
	rm -f /tmp/search_mysql_info.$$
	echo ""
    else
	# ok, no folder appears only ONCE for this version, search for files :) 
	cat /tmp/onefile.$$ | grep "^${VERSION}#" | awk '{print $2}' >/tmp/search_mysql_info.$$
	if [ "`wc -l /tmp/search_mysql_info.$$ | awk '{print $1}'`" -gt "0" ]
	then
	    echo "[$VERSION]"
	    cat /tmp/search_mysql_info.$$
	    rm -f /tmp/search_mysql_info.$$
	    echo ""
	else
	    echo "; IT'S IMPOSSIBLE to separate version $VERSION from others. Please investigate manually"
	    echo ""
	fi
    fi
done

rm -f /tmp/onefolder.$$ /tmp/onefile.$$
