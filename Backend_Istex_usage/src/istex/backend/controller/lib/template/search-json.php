<?php
header("content-type: application/json; charset=UTF-8");

$aFilteredPlaces = array();
foreach($aSearchResults as $iResNum => $aPointDetails)
{
    
    $sOSMType = formatOSMType($aPointDetails['osm_type']);
  
    $aPlace['lat'] = $aPointDetails['lat'];
    $aPlace['lon'] = $aPointDetails['lon'];

    if (isset($aPointDetails['address']))
    {
        $aPlace['address'] = $aPointDetails['address'];
    }

   
    $aFilteredPlaces[] = $aPlace;
}

javascript_renderData($aFilteredPlaces);
