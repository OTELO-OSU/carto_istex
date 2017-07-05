#!/bin/bash
curl -L $1 --create-dirs -o data.osm.pbf
sudo -u nominatim ./utils/setup.php --osm-file data.osm.pbf --all --threads 2 






sudo -u postgres createuser www-data

tee /etc/apache2/conf-available/nominatim.conf << EOFAPACHECONF
<Directory "/Nominatim-3.0.0/build/website">
  Options FollowSymLinks MultiViews
  AddType text/html   .php
  DirectoryIndex search.php
  Require all granted
</Directory>

Alias /nominatim /Nominatim-3.0.0/build/website
EOFAPACHECONF

a2enconf nominatim
service apache2 start
sudo -u nominatim -H ./utils/update.php --import-osmosis-all
