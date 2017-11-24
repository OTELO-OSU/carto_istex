#!/bin/bash

cp install_small/local.php settings/local.php

sudo -u nominatim -H pip install --user osmium




exec sudo -u nominatim -H ./utils/update.php --import-osmosis-all
