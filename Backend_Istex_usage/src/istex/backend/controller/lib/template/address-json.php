<?php

$aFilteredPlaces = array();

if (!sizeof($aPlace))
{
    if (isset($sError))
        $aFilteredPlaces['error'] = $sError;
    else
        $aFilteredPlaces['error'] = 'Unable to geocode';
}
else
{
    if (isset($aPlace['place_id'])) $aFilteredPlaces['place_id'] = $aPlace['place_id'];
    //$aFilteredPlaces['licence'] = "Data © OpenStreetMap contributors, ODbL 1.0. http://www.openstreetmap.org/copyright";
    $sOSMType = formatOSMType($aPlace['osm_type']);
    if ($sOSMType)
    {
        $aFilteredPlaces['osm_type'] = $sOSMType;
        $aFilteredPlaces['osm_id'] = $aPlace['osm_id'];
    }
    if (isset($aPlace['lat'])) $aFilteredPlaces['lat'] = $aPlace['lat'];
    if (isset($aPlace['lon'])) $aFilteredPlaces['lon'] = $aPlace['lon'];
    $aFilteredPlaces['display_name'] = $aPlace['langaddress'];
    if (isset($aPlace['aAddress'])) $aFilteredPlaces['address'] = $aPlace['aAddress'];
    if (isset($aPlace['sExtraTags'])) $aFilteredPlaces['extratags'] = $aPlace['sExtraTags'];
    if (isset($aPlace['sNameDetails'])) $aFilteredPlaces['namedetails'] = $aPlace['sNameDetails'];

    if (isset($aPlace['aBoundingBox']))
    {
        $aFilteredPlaces['boundingbox'] = $aPlace['aBoundingBox'];
    }

    if (isset($aPlace['asgeojson']))
    {
        $aFilteredPlaces['geojson'] = json_decode($aPlace['asgeojson']);
    }

    if (isset($aPlace['assvg']))
    {
        $aFilteredPlaces['svg'] = $aPlace['assvg'];
    }

    if (isset($aPlace['astext']))
    {
        $aFilteredPlaces['geotext'] = $aPlace['astext'];
    }

    if (isset($aPlace['askml']))
    {
        $aFilteredPlaces['geokml'] = $aPlace['askml'];
    }
}

javascript_renderData($aFilteredPlaces);

