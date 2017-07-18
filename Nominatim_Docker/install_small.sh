#!/bin/bash
curl -L http://download.geofabrik.de/europe/monaco-latest.osm.pbf --create-dirs -o data.osm.pbf
sudo -u postgres createuser www-data
sudo -u nominatim ./utils/setup.php --osm-file data.osm.pbf --all --threads 2 
chown nominatim -R /Nominatim-3.0.0
cp install_small/local.php settings/local.php
sudo -u nominatim -H pip install --user osmium
sudo -u nominatim -H ./utils/update.php --init-updates




exec sudo -u nominatim -H ./utils/update.php --import-osmosis-all
