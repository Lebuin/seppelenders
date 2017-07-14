#!/bin/bash

url=$1
f_output=$2

if [ ! -f $f_output ]; then
    echo "date,time_namelookup,time_connect,time_appconnect,time_pretransfer,time_redirect,time_starttransfer,time_total" > $f_output
fi

curl -w $(date -u +%Y-%m-%dT%H:%M:%SZ),%{time_namelookup},%{time_connect},%{time_appconnect},%{time_pretransfer},%{time_redirect},%{time_starttransfer},%{time_total} -o /dev/null -s $url >> $f_output
