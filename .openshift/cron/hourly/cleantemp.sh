#!/usr/bin/env bash

curr_ts=$(date +%s)

for entry in "${OPENSHIFT_REPO_DIR}/www/tmp"/*
do
	ts=${entry%.*}
	ts=${ts##*flags}

	# Delete files older than one hour
	if [ $((($curr_ts-$ts)/60/60)) -gt 0 ]; then
		rm $entry
	fi
done

