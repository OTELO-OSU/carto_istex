#!/bin/bash
service memcached start
service apache2 start
tail -f /var/log/apache2/*.log