#!/bin/bash

 exec su postgres -c "/usr/lib/postgresql/9.5/bin/postgres  --config-file=/etc/postgresql/9.5/main/postgresql.conf" &
chmod 777 /Nominatim-3.0.0/build/
sleep 10


if su postgres -c "psql nominatim -c '\q'" 2>&1; 
then
	sed  -i 's/^\(fsync = \).*/\1On/' /etc/postgresql/9.5/main/postgresql.conf 
	sed  -i 's/^\(full_page_writes = \).*/\1On/' /etc/postgresql/9.5/main/postgresql.conf 
	./update_nominatim_database.sh
else

    tail -f /dev/null
fi















