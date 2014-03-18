#!/bin/sh

# This script create <applicationname>-<version>.file and <applicationname>-<version>.folder files in the testenv/ folder for a specific application.

CMS="$1"
cd testenv
WD=`pwd`
for i in ${CMS}-*
do
    if [ ! -d $i ]; then continue; fi;
    COUNT=`ls $WD/$i|wc -l`;
    if [ 0 == $COUNT ]; then echo "skipping empty folder $i"; continue; fi;
    echo -n "$i ..."
    cd $i 
    find -type d >../${i}.folder
    find -type f >../${i}.file
    cd .. 
    echo " done"
done

# Then launch insert_mysql_application.sh to create tables with files for MySQL index  / search
