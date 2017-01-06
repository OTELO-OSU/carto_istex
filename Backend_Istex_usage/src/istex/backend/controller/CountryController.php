<?php
namespace istex\backend\controller;
use \istex\backend\controller\RequestController as RequestApi;

class CountryController 
{
	



	function Sort_by_country($received_array,$noaff){
		$noaff=json_decode($noaff,true);
		$tableau_country=[]; // Initialisation tableau
		$master_tab=[];
		foreach ($received_array as $key => $value) { // on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=$value['country']; // on stocke les valeurs dans un tableau
			$tab[]=$value['country_code'];
			$tab[]=$value['id'];
			$tab[]=$value['lat'];
			$tab[]=$value['lon'];
				if ($value['country']=="NULL") {
					$test[]=$value['id'];
				}
				else{
					$master_tab[]=$tab;// on stocke le tableau dans un autre
					$verif[]=$value['id'];
					}

		}
		$master_tab = array_map("unserialize", array_unique(array_map("serialize", $master_tab)));
		//var_dump($master_tab);
		if (isset($test)) {
		$verif = array_map("unserialize", array_unique(array_map("serialize", $verif)));
		$test = array_map("unserialize", array_unique(array_map("serialize", $test)));
		
		$result = array_diff($test, $verif);
		//var_dump($result);
		$noaff[0]['empty']=$noaff[0]['noaff'];
		$noaff[0]['noaff']=$noaff[0]['noaff']+count($result);
		}

$countrywithid = array();
foreach($master_tab as $arg)
{
	$array= array();
	$Request = new RequestApi;
	$countrywithid[$arg[0]]["gps"]["lat"]=$arg[3];
	$countrywithid[$arg[0]]["gps"]["lon"]=$arg[4];
	$countrywithid[$arg[0]]["gps"]["country_code"]=$arg[1];
	$array[]=$arg[2];
    $countrywithid[$arg[0]][] = $array;
}
				
			arsort($countrywithid);	//tri du pays qui a le plus de documents au plus petit nombre
			foreach ($countrywithid as $key => $value) {
			$array= array();
			$array["gps"]=$value["gps"];
			$array["total"]=count($value)-1;
			$countrywithid[$key]=$array;
			}
			$response=array();
			$array=array();
			$array["noaff"]=$noaff[0]['noaff'];
			$array["empty"]=$noaff[0]['empty'];
			$array["total"]=$noaff[0]['total'];
			$response[]=$array;
			$response["documents"]=$countrywithid;
			return $response;
	
	}


	//focntion qui recupere l'affiliations et qui envoie celui ci vers la requete nominatim
	function get_name($received_array,$query){
		$hash = md5($query);
		$json=json_encode($received_array);
		$results= shell_exec('python Multiquery.py '.escapeshellarg($hash));
		$results= json_decode($results);
		foreach ($results as $key => $value) {
			$value=json_decode($value,true);
			$response_array[]=$value;
		}

		
			
		return $response_array;
		
		

	}
	

	
}


?>