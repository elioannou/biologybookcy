#!/bin/bash

curl https://cyp.acscourier.net/en/store-locator > latest-acs.html
grep -oP '\{name:"\K[^"]+' latest-acs.html > latest-acs-raw-list

DIFF=$(diff "acs-raw-list" "latest-acs-raw-list")
if [ -f acs-raw-list ]; then
    if [ "$DIFF" ]; then
	echo "Stores changed"
	echo "$DIFF"
	echo "##### GENERATING NEW LIST #######"
	sed -i 's/ACS STRAKKA/STRAKKA/g' latest-acs-raw-list
	sed -i 's/CITY CENTRE/CITY CENTRE NICOSIA/g' latest-acs-raw-list
	sed -i 's/KOLONAKIOU/KOLONAKIOU YERMASOYIA/g' latest-acs-raw-list
	sed -i 's/MICHALAKOPOULOU/MICHALAKOPOULOU NICOSIA/g' latest-acs-raw-list
	sed -i 's/KEDRIKA GRAFIA KIPROU/KEDRIKA GRAFIA KIPROU STROVOLOS/g' latest-acs-raw-list
	sed -i 's/ARTEMIDOS/ARTEMIDOS LARNAKA/g' latest-acs-raw-list 
	sort latest-acs-raw-list -o latest-acs-raw-list
	sed -i -e 's/^/<option>/' latest-acs-raw-list
	#	sed -i -e 's/$/">/' latest-acs-list-tmp
	#	paste -d'\0' latest-acs-list-tmp latest-acs-raw-list > latest-acs-list-formatted
	sed -i -e 's:$:</option>:' latest-acs-raw-list
	cat latest-acs-raw-list
    else
	echo "Stores have not changed"
    fi
    rm latest-acs*
else
    echo "Current acs-raw-list does not exist"
fi
