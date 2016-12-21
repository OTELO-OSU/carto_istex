<?php
namespace istex\backend\controller;
class LaboratoryController 
{




	function Sort_by_laboratory($received_array){
		$received_array= json_decode($received_array,true);
		$tableau_laboratory=[]; // Initialisation tableau
		$tableau_laboratorys=[];
		$master_tab=[];
		
		foreach ($received_array[1] as $key => $value) { // on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=$value['laboratory']." , ".$value['university'];// on stocke les valeurs dans un tableau
			$tab[]=$value['id'];
			
	if ($value['laboratory']==NULL && $value['university']!=NULL) {
			$laboratorynull[]=$value['id'];
		}
		else if ($value['university']==NULL && $value['laboratory']!=NULL) {
			$universitynull[]=$value['id'];
		}
		else if($value['university']==NULL && $value['laboratory']==NULL)
		{
			$null[]=$value['id'];
		}
		if (($value['laboratory']==NULL && $value['university']==NULL) || ($value['laboratory']==NULL) || ($value['university']==NULL)) {
					
					$test[]=$value['id'];
				}
				else{
					$master_tab[]=$tab;// on stocke le tableau dans un autre
					$verif[]=$value['id'];
					}

		}


		$laboratorynull = array_map("unserialize", array_unique(array_map("serialize", $laboratorynull)));
		$universitynull = array_map("unserialize", array_unique(array_map("serialize", $universitynull)));
		$null = array_map("unserialize", array_unique(array_map("serialize", $null)));
		//var_dump($laboratorynull);
		//var_dump($universitynull);
		//var_dump($null);
		$verif = array_map("unserialize", array_unique(array_map("serialize", $verif)));
		$master_tab = array_map("unserialize", array_unique(array_map("serialize", $master_tab)));
		$test = array_map("unserialize", array_unique(array_map("serialize", $test)));
		$result = array_diff($test, $verif);
		//var_dump($result);
		$received_array[0]['noaff']=$received_array[0]['noaff']+count($result);

		$laboratorywithid = array();


		foreach($master_tab as $arg)
		{
		    $laboratorywithid[$arg[0]][] = $arg[1];
		}

		arsort($laboratorywithid); //tri du labo qui a le plus de documents au plus petit nombre
		foreach ($laboratorywithid as $key => $value) {
			$array= array();
			$array["total"]=count($value);
			$laboratorywithid[$key]=$array;
			}
		$response=array();
		$array=array();
		$array["noaff"]=$received_array[0];
		$response[]=$array;
		$response["documents"]=$laboratorywithid;


		return $response;


	}
	
	
}


?>