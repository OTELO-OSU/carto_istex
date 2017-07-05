<?php
$PASSWORD=urlencode($argv[1]);
$file = 'var/www/html/Projet_carto_istex/Backend_Istex_usage/src/istex/backend/controller/settings/settings.php';

$current = file_get_contents($file);

$current.="@define('CONST_Database_DSN', 'pgsql://nominatim:$PASSWORD@carto_istex_nominatim:5432/nominatim');"; 

file_put_contents($file, $current);


