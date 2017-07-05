#!/bin/bash
curl -L https://planet.openstreetmap.org/pbf/planet-latest.osm.pbf --create-dirs -o data.osm.pbf
sudo -u nominatim ./utils/setup.php --osm-file data.osm.pbf --all --threads 2 
chown nominatim -R /Nominatim-3.0.0
cp install_planet/local.php settings/local.php
sudo -u nominatim -H pip install --user osmium
sudo -u nominatim -H ./utils/update.php --init-updates


sudo -u postgres createuser www-data


sudo -u nominatim -H ./utils/update.php --import-osmosis-all
