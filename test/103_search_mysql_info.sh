#!/bin/sh

# This script search for common files in all versions of an application
# and different file in all versions of an application

MYSQL="mysql -uoffwave -pOophah2a offwave_recognize"

echo "; ----------------------------------------------------------------------"
echo "; This .INI has been automatically computed by search_mysql_info.sh"
echo "; ----------------------------------------------------------------------"

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
NOTFOUND=""
NFIN=""
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
#	    echo "; IT'S IMPOSSIBLE to separate version $VERSION from others. Please investigate manually"
#	    echo ""
	    NOTFOUND="$NOTFOUND $VERSION"
	    NFIN="$NFIN'$VERSION',"
	fi
    fi
done

rm -f /tmp/onefolder.$$ /tmp/onefile.$$

if [ -n "$NOTFOUND" ]
then
    echo "; ----------------------------------------------------------------------"
    echo "; The following are not easy to distinguish. Using 2 versions for each folders/files"
    NFIN="${NFIN}''"
    # Testing 
    $MYSQL --skip-column-name -B -e "SELECT CONCAT(MIN(version),' or ',MAX(version),'#'), CONCAT( SUBSTRING(filename,3) ,'=dir') FROM auto WHERE type='folder' AND filename!='.' AND version IN ($NFIN) GROUP BY filename HAVING count(*)=2" | sort -g >/tmp/twofolder.$$

    # For each version pair, we remove them from the NFIN list and parse
    LASTVERSION=""
    while read LINE
    do
	VERSION="`echo $LINE | sed -e 's/#.*//'`"
	if [ "$VERSION" != "$LASTVERSION" ]
	then
	    echo ""
	    echo "[$VERSION]"
	    LASTVERSION="$VERSION"
	    VS1="`echo $VERSION | sed -e 's/ .*$//' `"
	    VS2="`echo $VERSION | sed -e 's/^.* \([^ ]*\)$/\1/' `"
	    NFIN="`echo $NFIN | sed -e "s/'$VS1',//"`"
	    NFIN="`echo $NFIN | sed -e "s/'$VS2',//"`"
	fi
	echo $LINE | sed -e 's/^.*# //'
    done </tmp/twofolder.$$ 

    if [ "$NFIN" != "''" ]
    then
	echo ""
	echo "; now search different file list for version pairs"
	# now try to separate by files:
	$MYSQL --skip-column-name -B -e "SELECT CONCAT(MIN(version),' or ',MAX(version),'#'), CONCAT( SUBSTRING(filename,3) ,'=file') FROM auto WHERE type='file' AND filename!='.' AND version IN ($NFIN) GROUP BY filename HAVING count(*)=2" | sort -g >/tmp/twofile.$$
    # For each version pair, we remove them from the NFIN list and parse
	LASTVERSION=""
	while read LINE
	do
	    VERSION="`echo $LINE | sed -e 's/#.*//'`"
	    if [ "$VERSION" != "$LASTVERSION" ]
	    then
		echo ""
		echo "[$VERSION]"
		LASTVERSION="$VERSION"
		NFIN="`echo $NFIN | sed -e "s/'$VERSION',//"`"
	    fi
	    echo $LINE | sed -e 's/^.*# //'
	done </tmp/twofile.$$ 
	if [ "$NFIN" != "''" ]
	    then
	    echo ""
	    echo "; The following version cannot be separated by their files or folders alone: "
	    echo "; $NFIN"
	fi
    fi
#    rm /tmp/twofolder.$$ /tmp/twofile.$$
fi

