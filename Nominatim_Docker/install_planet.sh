#!/bin/bash
curl -L https://planet.openstreetmap.org/pbf/planet-latest.osm.pbf --create-dirs -o data.osm.pbf
sudo -u nominatim ./utils/setup.php --osm-file data.osm.pbf --all --threads 2 