<?php
namespace istex\backend\controller;
use \istex\backend\controller\RequestController as RequestApi;
class CountryController 
{
	

function search_array($needle, $haystack) {
     if(in_array($needle, $haystack)) {
          return true;
     }
     foreach($haystack as $element) {
          if(is_array($element) && self::search_array($needle, $element))
               return true;
     }
   return false;
}


	function Sort_by_country($received_array,$noaff){
		$tableau_country=[]; // Initialisation tableau
		$master_tab=[];
		foreach ($received_array as $key => $value) { // on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=$value['country']; // on stocke les valeurs dans un tableau
			$tab[]=$value['id'];
					
				if (((self::search_array($value['id'], $master_tab)==true)&&$value['country']=="NULL")OR$value['country']=="NULL") {
					$test[]=$value['id'];
				}
				else{
					$master_tab[]=$tab;// on stocke le tableau dans un autre
					$verif[]=$value['id'];
					}

		}

		$verif = array_map("unserialize", array_unique(array_map("serialize", $verif)));
		$master_tab = array_map("unserialize", array_unique(array_map("serialize", $master_tab)));
		$test = array_map("unserialize", array_unique(array_map("serialize", $test)));
		//var_dump($test);
		$result = array_diff($test, $verif);

		$noaff[0]['noaff']=$noaff[0]['noaff']+count($result);


					
	  			
	  		
	  	

		foreach ($master_tab as $key2 => $value2) { // on parcourt le tableau precedemment créé
			$index=$value2[0]; // on definie l'index qui va permettre de determiner le nombre de publications par pays
			if (array_key_exists($index, $tableau_country)) { // array key exist permet de verifier si une clé existe dans un tableau
				$tableau_country[$index]++; // si elle existe on ajoute 1 a chaque valeur

			}
			else{
				$tableau_country[$index] = 1; // sinon on laisse a 1
				}
			}

		$country=array();
		foreach ($tableau_country as $key => $value) { // on parcourt ensuite le tableau 
			$array=array();
			$array[$key] = $value; // on range la valeur dans un autre tableau pour obtenir un tableau de tableau
			$country[] = $array;
		}
		
		$countrywithid=array();
		foreach ($country as $key => $value) { // on parcourt le tableau precedent
			foreach ($value as $key2 => $value2) {
				$array= array();
						$Request = new RequestApi;
						$array['gps']=$Request->Request_lat_lon_of_country($key2);
				foreach ($master_tab as $key3 => $value3) { // on parcourt le tableau principale
					if ($key2==$value3[0]) { // si le nom de pays est le meme alors on les regroupes
						$array[]=$value3[1];
						$countrywithid[$key2]=$array;
						
					}
					
				}
										
				}
			}

				
			arsort($countrywithid);	//tri du pays qui a le plus de documents au plus petit nombre
			$response=array();
			$array=array();
			$array["noaff"]=$noaff[0];
			$array["total"]=count($master_tab);
			$response[]=$array;
			$response["documents"]=$countrywithid;
			return $response;
	
	}

	//focntion qui recupere l'affiliations et qui envoie celui ci vers la requete nominatim
	function get_name($received_array){
		$response_array= array();
		foreach ($received_array[1] as $key => $value) {
			$Request = new RequestApi;
			$array=$Request->Request_name_of_country($value['country'],$value['id']);
			
			$response_array[]=$array;	// mise en tableau de la reponse de nominatim
		}
		//$response_array = array_map("unserialize", array_unique(array_map("serialize", $response_array)));
		return $response_array;

	}

	
}


?>