#!/bin/bash

cp install_planet/local.php settings/local.php

sudo -u nominatim -H pip install --user osmium


sudo -u nominatim -H ./utils/update.php --init-updates

sudo -u nominatim -H ./utils/update.php --import-osmosis-all
