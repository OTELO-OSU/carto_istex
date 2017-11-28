#!/bin/bash

if [ ! -f /var/lib/postgresql/9.5/main-carto_istex/PG_VERSION ]; then
  cp -R /var/lib/postgresql/9.5/main/* /var/lib/postgresql/9.5/main-carto_istex/
  chown -R postgres:postgres /var/lib/postgresql/9.5/main-carto_istex/
  chmod -R 700 /var/lib/postgresql/9.5/main-carto_istex/
fi

sed -i 's/^\(shared_buffers = \).*/\1'$SHARED_BUFFERS'/' /etc/postgresql/9.5/main/postgresql.conf 
sed -i "s/^\(data_directory = \).*/\1'\/var\/lib\/postgresql\/9.5\/main-carto_istex'/" /etc/postgresql/9.5/main/postgresql.conf 

exec su postgres -c "/usr/lib/postgresql/9.5/bin/postgres  --config-file=/etc/postgresql/9.5/main/postgresql.conf" &
chmod 777 /Nominatim-3.0.0/build/
sleep 10
php changepassword.php $POSTGRESQL_PASSWORD
useradd -d /srv/nominatim -s /bin/bash -m nominatim -p $POSTGRESQL_PASSWORD 
sudo -u postgres createuser -s nominatim 
sudo -u postgres psql -c "ALTER USER nominatim WITH PASSWORD '$POSTGRESQL_PASSWORD';"

if su postgres -c "psql nominatim -c '\q'" 2>&1; 
then
	
	sed  -i 's/^\(fsync = \).*/\1On/' /etc/postgresql/9.5/main/postgresql.conf 
	sed  -i 's/^\(full_page_writes = \).*/\1On/' /etc/postgresql/9.5/main/postgresql.conf
     	tail -f /dev/null

else
    tail -f /dev/null
fi















