<?php
@define('CONST_Pyosmium_Binary', '/srv/nominatim/.local/bin/pyosmium-get-changes');
// base URL of the replication service
@define('CONST_Replication_Url', 'http://planet.openstreetmap.org/replication/day/');
// How often upstream publishes diffs
@define('CONST_Replication_Update_Interval', '86400');
// How long to sleep if no update found yet
@define('CONST_Replication_Recheck_Interval', '900');


