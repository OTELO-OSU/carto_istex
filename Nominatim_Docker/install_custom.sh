#!/bin/bash
sudo -u postgres createuser -s nominatim 
sudo -u postgres psql -c "ALTER USER nominatim WITH PASSWORD '$POSTGRESQL_PASSWORD';"
curl -L $1 --create-dirs -o data.osm.pbf
sudo -u nominatim ./utils/setup.php --osm-file data.osm.pbf --all --threads 2 


sudo -u postgres createuser www-data
sed  -i 's/^\(fsync = \).*/\1On/' /etc/postgresql/9.5/main/postgresql.conf 
sed  -i 's/^\(full_page_writes = \).*/\1On/' /etc/postgresql/9.5/main/postgresql.conf 

exec sudo -u nominatim -H ./utils/update.php --import-osmosis-all
