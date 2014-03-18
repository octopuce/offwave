#
# This script finds folders with a single child, moves the content within the parent
# and destroys the now empty folder
#

WD=$(pwd);
echo $WD
for f in `find $WD -maxdepth 1 -not -iname "\.*" -type d `;do
    
    let count=0;
    for ff in `find $f -maxdepth 1 -not -iname "\.*" -type d `; do
# echo $ff $count;
	let count=count+1;
    done;
#echo "$f : $count";
    if [ $count == 2 ]; then
	mv $ff/* "$f";
rmdir $ff;
	echo removed $ff;
    fi;
done;

