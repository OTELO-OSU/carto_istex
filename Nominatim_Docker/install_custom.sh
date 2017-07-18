#!/bin/bash
curl -L $1 --create-dirs -o data.osm.pbf
sudo -u nominatim ./utils/setup.php --osm-file data.osm.pbf --all --threads 2 


sudo -u postgres createuser www-data

exec sudo -u nominatim -H ./utils/update.php --import-osmosis-all
