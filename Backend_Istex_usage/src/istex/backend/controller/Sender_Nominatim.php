<?php
//Import librairie nominatim
//use \Nominatim\ParameterParser as ParameterParser;

require('settings/settings.php');
require('lib/init-cmd.php');
require('lib/Geocode.php');
require('lib/ParameterParser.php');
// Choix de la feature a utiliser si improve ou non
if ($argv[2]) {
        $aCMDResult=array("addressdetails"=>true);
}
else{
        $aCMDResult=array("addressdetails"=>true,"featureType"=>"country");
}
ini_set('max_execution_time', 3);//timeout 3 secondes

$oParams = new Nominatim\ParameterParser($aCMDResult);
	if (empty($argv[1])) {
		$oDB =& getDB();//import settings bdd
		$array=array();
		$array['country']="NULL";
		$array['country_code']="NULL";
		$array['lat']="NULL";
		$array['lon']="NULL";
		if ($argv[2]) {
			$array['improved']=1;
		}

	}
	else{
		$aCMDResult["search"]=$argv[1];
		$aCMDResult["limit"]=2;
		$name=$argv[1];//recuperation de l'argument(pays)
		$oDB =& getDB();//import settings bdd
    		$oGeocode = new Nominatim\Geocode($oDB);//Objet Geocode
		$oGeocode->setLanguagePreference($oParams->getPreferredLanguages("en"));
    		$oGeocode->loadParamArray($oParams);
		$oGeocode->setQuery($name);//on set la query
		$aSearchResults = $oGeocode->lookup();//on cherche

		if (!count($aSearchResults)==0)//Si il y a un resultat
		{	
			if (empty($aSearchResults[0]['address']['country'])) {
				$array['country'] = $aSearchResults[1]['address']['country'];
			    $array['country_code'] = $aSearchResults[1]['address']['country_code'];
			    $array['lat'] = $aSearchResults[1]['lat'];
			    $array['lon'] = $aSearchResults[1]['lon'];
			}
			else{
			    $array['country'] = $aSearchResults[0]['address']['country'];
			    $array['country_code'] = $aSearchResults[0]['address']['country_code'];
			    $array['lat'] = $aSearchResults[0]['lat'];
			    $array['lon'] = $aSearchResults[0]['lon'];
		}

		}
		else{//Sinon NULL
			$array=array();
			$array['country']="NULL";
			$array['country_code']="NULL"; 
			$array['lat']="NULL";
			$array['lon']="NULL";	
			if ($argv[2]) {
			$array['improved']=1;
}
		
		}
	}

echo json_encode($array);//Retourne du json 







