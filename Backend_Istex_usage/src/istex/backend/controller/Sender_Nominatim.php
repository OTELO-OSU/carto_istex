<?php
use \Nominatim\ParameterParser as ParameterParser;
use \Nominatim\Geocode as Geocode;
require('build/settings/settings.php');
require('lib/init-cmd.php');
require('lib/Geocode.php');
require('lib/ParameterParser.php');
ini_set('max_execution_time', 5);
	$name=$argv[1];
	$oDB =& getDB();
	$oParams = new ParameterParser();
	$languages=$oParams->getPreferredLanguages("en");
	$oGeocode = new Geocode($oDB);
	$oGeocode->setLanguagePreference($languages);
	$oGeocode->getIncludeAddressDetails();
	$oGeocode->loadParamArray($oParams);
	$oGeocode->setQuery($name);
	$aSearchResults = $oGeocode->lookup();
	if (!count($aSearchResults)==0)
	{
	    $array['country'] = $aSearchResults[0]['address']['country'];
	    $array['lat'] = $aSearchResults[0]['lat'];
	    $array['lon'] = $aSearchResults[0]['lon'];
	    //$array['id']=$argv[2];
	}
	else{
		$array=array();
		$array['country']="NULL";
		//$array['id']=$argv[2];
		$array['lat']="NULL";
		$array['lon']="NULL";			
	}

echo json_encode($array);