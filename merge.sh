#!/bin/bash

while read line2; do
    while read line1; do
	printf "$line1$line2\n";
    done < acs-stores.o;
done < acs-stores.list;
