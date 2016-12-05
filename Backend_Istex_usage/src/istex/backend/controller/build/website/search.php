<?php
@define('CONST_ConnectionBucket_PageType', 'Search');

require_once(dirname(dirname(__FILE__)).'/settings/settings.php');
require_once(CONST_BasePath.'/lib/init-website.php');
require_once(CONST_BasePath.'/lib/log.php');
require_once(CONST_BasePath.'/lib/Geocode.php');
require_once(CONST_BasePath.'/lib/output.php');
ini_set('memory_limit', '200M');

$oDB =& getDB();
$oParams = new Nominatim\ParameterParser();

$oGeocode = new Nominatim\Geocode($oDB);

$aLangPrefOrder = $oParams->getPreferredLanguages();
$oGeocode->setLanguagePreference($aLangPrefOrder);


$oGeocode->loadParamArray($oParams);


$oGeocode->setQueryFromParams($oParams);


    $sQuery = substr(rawurldecode($_SERVER['PATH_INFO']), 1);

    $aPhrases = explode('/', $sQuery);
    $aPhrases = array_reverse($aPhrases);
    $sQuery = join(', ', $aPhrases);
    $oGeocode->setQuery($sQuery);



$aSearchResults = $oGeocode->lookup();
if ($aSearchResults === false) $aSearchResults = array();
var_dump($aSearchResults);

if (!count($aSearchResults)==0) {
    $aPlace['lat'] = $aSearchResults[0]['lat'];
    $aPlace['lon'] = $aSearchResults[0]['lon'];

    if (isset($aSearchResults[0]['address']))
    {
        $aPlace['address'] = $aSearchResults[0]['address'];
    }

   


javascript_renderData( $aPlace);
   
}