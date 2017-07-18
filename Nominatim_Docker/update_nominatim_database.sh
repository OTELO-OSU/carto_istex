#!/bin/bash


sudo -u nominatim -H pip install --user osmium




exec sudo -u nominatim -H ./utils/update.php --import-osmosis-all
