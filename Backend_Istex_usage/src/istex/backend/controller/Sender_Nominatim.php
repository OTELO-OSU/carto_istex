<?php


use \Nominatim\ParameterParser as ParameterParser;
use \Nominatim\Geocode as Geocode;


require_once('build/settings/settings.php');
require_once('lib/init-cmd.php');
require_once('lib/Geocode.php');
require_once('lib/ParameterParser.php');

function stripAccents($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}



 $oDB =& getDB();
    $oParams = new ParameterParser();
    $languages=$oParams->getPreferredLanguages("en");
    $oGeocode = new Geocode($oDB);
    $oGeocode->setLanguagePreference($languages);
			$name=$argv[1];
			$hash= md5($name);
			//$name= mb_strtoupper(stripAccents($name),'UTF-8');
			//$name=preg_replace("/[\[{\(].*[\]}\)]|[0-9÷\-z_@~;:?'*-]/", '', $name);
			if ($name=="") {
				$array=array();
				$array['country']="NULL";
				$array['lat']="NULL";
				$array['lon']="NULL";
				$array['id']=$argv[2];
				
			}
			else{
				$m = new \Memcached(); // initialisation memcached
					$m->addServer('localhost', 11211); // ajout server memecached
					$cache=$m->get($hash);//on lit la memoire
					if ($cache) {
						$array=$cache;
						$array["id"]=$argv[2];
					}
					else{
					    $oGeocode->getIncludeAddressDetails();
					    $oGeocode->loadParamArray($oParams);
					    $oGeocode->setQuery($name);
					    $aSearchResults = $oGeocode->lookup();
					    if (!count($aSearchResults)==0)
					    {
					    $array['country'] = $aSearchResults[0]['address']['country'];
					    $array['lat'] = $aSearchResults[0]['lat'];
					    $array['lon'] = $aSearchResults[0]['lon'];
					    $array['id']=$argv[2];

					    $arraytocache=array();
						$arraytocache['country']=$aSearchResults[0]['address']['country'];
						$arraytocache['lat']=$aSearchResults[0]['lat'];
						$arraytocache['lon']=$aSearchResults[0]['lon'];

						// Lundi voir le post it rose(affiliations graph excell et verif que sa marhce nominatim)

					    }
					    else{
					    	$array=array();
									$array['country']="NULL";
									$array['id']=$argv[2];
									$array['lat']="NULL";
									$array['lon']="NULL";

									$arraytocache=array();
									$arraytocache['country']="NULL";
									$arraytocache['lat']="NULL";
									$arraytocache['lon']="NULL";
					    }
					$cache=$m->set($hash, $arraytocache, 30);// on set le tableau obtenu dans le cache

				}
			}

echo json_encode($array);