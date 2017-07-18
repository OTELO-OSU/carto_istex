#!/bin/bash

 exec su postgres -c "/usr/lib/postgresql/9.5/bin/postgres  --config-file=/etc/postgresql/9.5/main/postgresql.conf" &

sleep 10


if su postgres -c "psql nominatim -c '\q'" 2>&1; 
then
	./update_nominatim_database.sh
else

    tail -f /dev/null
fi















