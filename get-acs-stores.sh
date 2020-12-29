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
	sed -i 's/CITY CENTRE/NICOSIA CITY CENTRE/g' latest-acs-raw-list
	sed -i 's/HUB NICOSIA/NICOSIA HUB/g' latest-acs-raw-list
	sed -i 's/CORAL PS31 STROVOLOS/STROVOLOS CORAL PS31/g' latest-acs-raw-list
	sed -i 's/KOLONAKIOU/YERMASOYIA KOLONAKIOU/g' latest-acs-raw-list
	sed -i 's/MICHALAKOPOULOU/NICOSIA MICHALAKOPOULOU/g' latest-acs-raw-list
	sed -i 's/KEDRIKA GRAFIA KIPROU/STROVOLOS KEDRIKA GRAFIA KIPROU/g' latest-acs-raw-list
	sed -i 's/ARTEMIDOS/LARNACA ARTEMIDOS/g' latest-acs-raw-list 
	sort latest-acs-raw-list -o latest-acs-raw-list
	sed -e 's/^/<option value="/' latest-acs-raw-list > latest-acs-list-tmp
	sed -i -e 's/$/">/' latest-acs-list-tmp
	paste -d'\0' latest-acs-list-tmp latest-acs-raw-list > latest-acs-list-formatted
	sed -i -e 's:$:</option>:' latest-acs-list-formatted
	cat latest-acs-list-formatted
    else
	echo "Stores have not changed"
    fi
    rm latest-acs*
else
    echo "Current acs-raw-list does not exist"
fi
