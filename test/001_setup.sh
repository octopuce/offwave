#!/bin/sh

# This script setup a test environment using the .TAR.GZ files available at each CMS location.
# Please ensure you have A LOT of space and wget installed when using is
# Then you may use testall.php to launch a test recursively on this folder.
# This should extract all the version properly.

# The script doesn't remove the testenv, and doesn't download any already existing application.
if ! type realpath | grep -q '/usr/bin' 2>/dev/null; then
    echo "[!] Please install realpath (aptitude install realpath)";
fi

if [ -z $1 ]; then 
    SOURCE="cms.txt"; 
else 
    SOURCE="$1"; 
fi;
SOURCE=`realpath $SOURCE`;
if [ ! -f $SOURCE ]; then
    echo "[!] $SOURCE is not a file.";
    exit;
fi;
mkdir -p testenv
cd testenv
cat $SOURCE | while read CMS VERSION MODE URL
do
echo $CMS $VERSION $MODE $URL
    if [ -d "${CMS}-${VERSION}" ]
	then
	echo "Skipping ${CMS}-${VERSION}: already exists"
	continue
    fi

    if [ "$MODE" == "ZIP" ]
    then
	echo "Download of ${CMS}-${VERSION}"
	wget "$URL" -qO tmp.zip
	mkdir "${CMS}-${VERSION}"
	cd "${CMS}-${VERSION}"
	echo "Extracting of ${CMS}-${VERSION}"
	unzip -q ../tmp.zip
	if [ "$?" != "0" ]
	then
	    echo "[ERROR] Extracting of ${CMS}-${VERSION} FAILED, please check."
	fi
	cd ..
	rm -f tmp.zip
    fi
    if [ "$MODE" == "TGZ" ]
    then
	echo "Download of ${CMS}-${VERSION}"
	wget "$URL" -qO tmp.tar.gz
	mkdir "${CMS}-${VERSION}"
	cd "${CMS}-${VERSION}"
	echo "Extracting of ${CMS}-${VERSION}"
	tar -zxf ../tmp.tar.gz
	if [ "$?" != "0" ]
	then
	    echo "[ERROR] Extracting of ${CMS}-${VERSION} FAILED, please check."
	fi
	cd ..
	rm -f tmp.tar.gz
    fi
    if [ "$MODE" == "TBZ" ]
    then
	echo "Download of ${CMS}-${VERSION}"
	wget "$URL" -qO tmp.tar.bz2
	mkdir "${CMS}-${VERSION}"
	cd "${CMS}-${VERSION}"
	echo "Extracting of ${CMS}-${VERSION}"
	tar -jxf ../tmp.tar.bz2
	if [ "$?" != "0" ]
	then
	    echo "[ERROR] Extracting of ${CMS}-${VERSION} FAILED, please check."
	fi
	cd ..
	rm -f tmp.tar.bz2
    fi
done
