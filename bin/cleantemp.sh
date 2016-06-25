#!/usr/bin/env bash

curr_ts=$(date +%s)

search_dir="../www/tmp"

for entry in "$search_dir"/*
do
	ts=${entry%.*}
	ts=${ts##*flags}

	# Delete files older than one hour
	if [ $((($curr_ts-$ts)/60/60)) -gt 0 ]; then
		rm $entry
	fi
done

