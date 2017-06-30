#!/bin/bash
curl -L http://download.geofabrik.de/europe/monaco-latest.osm.pbf --create-dirs -o data.osm.pbf
sudo -u nominatim ./utils/setup.php --osm-file data.osm.pbf --all --threads 2 