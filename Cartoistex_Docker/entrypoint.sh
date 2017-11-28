#!/bin/bash
php Projet_carto_istex/Cartoistex_Docker/changepassword.php $POSTGRESQL_PASSWORD
service apache2 start
tail -f /dev/null
