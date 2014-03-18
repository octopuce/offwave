#
# This script does all the work for you
#

if [ -z $1 ]; then echo "[!] Missing application name"; exit; fi;
WEBAPP=$1;

echo "[.] Parsing the source folders"
./101_create_file_and_folder_list.sh $WEBAPP
echo "[.] Inserting data in mysql"
./102_insert_mysql_application.sh $WEBAPP
echo "[.] Writing config file to /tmp/$WEBAPP.ini";
./103_search_mysql_info.sh > /tmp/$WEBAPP.ini
echo "[x] Done"

