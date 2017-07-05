<?php
$PASSWORD=urlencode($argv[1]);
$file = '/Nominatim-3.0.0/build/settings/settings.php';

$current = file_get_contents($file);

$current.="@define('CONST_Database_DSN', 'pgsql://nominatim:$PASSWORD@carto_istex_nominatim:5432/nominatim');"; 


file_put_contents($file, $current);


