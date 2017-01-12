<?php
namespace istex\backend\controller;
class LaboratoryController 
{




	function Sort_by_laboratory($received_array){
		
		$received_array= json_decode($received_array,true);
		if (count($received_array[1])==0) {
			$array[]="empty";
			return $array;
		}
		else{
			$tableau_laboratory=[]; // Initialisation tableau
			$tableau_laboratorys=[];
			$master_tab=[];

			foreach ($received_array[1] as $key => $value) { // on parcourt le tableau que la requetes nous a renvoyé
				$tab=array();
				$tab[]=$value['laboratory']." , ".$value['university'];// on stocke les valeurs dans un tableau
				$tab[]=$value['id'];
				$tab[]=$value['title'];
				
		/*if ($value['laboratory']==NULL && $value['university']!=NULL) {
				$laboratorynull[]=$value['id'];
			}
			else if ($value['university']==NULL && $value['laboratory']!=NULL) {
				$universitynull[]=$value['id'];
			}
			else if($value['university']==NULL && $value['laboratory']==NULL)
			{
				$null[]=$value['id'];
			}*/
			if (($value['laboratory']==NULL && $value['university']==NULL) || ($value['laboratory']==NULL) || ($value['university']==NULL)) {
						
						$test[]=$value['id'];
					}
					else{
						$master_tab[]=$tab;// on stocke le tableau dans un autre
						$verif[]=$value['id'];
						}

			}

			//$laboratorynull = array_map("unserialize", array_unique(array_map("serialize", $laboratorynull)));
			//$universitynull = array_map("unserialize", array_unique(array_map("serialize", $universitynull)));
			//$null = array_map("unserialize", array_unique(array_map("serialize", $null)));
			//var_dump($laboratorynull);
			//var_dump($universitynull);
			//var_dump($null);
			$verif = array_map("unserialize", array_unique(array_map("serialize", $verif)));
			$master_tab = array_map("unserialize", array_unique(array_map("serialize", $master_tab)));
			$test = array_map("unserialize", array_unique(array_map("serialize", $test)));
			$result = array_diff($test, $verif);
			//var_dump($result);
			$received_array[0]['empty']=$received_array[0]['noaff'];
			$received_array[0]['noaff']=$received_array[0]['noaff']+count($result);

			$laboratorywithid = array();


			foreach($master_tab as $arg)
			{
				$array=[];
				$array['id']=$arg[1];
				$array['title']=$arg[2];
			    $laboratorywithid[$arg[0]][] = $array;
			}

			arsort($laboratorywithid); //tri du labo qui a le plus de documents au plus petit nombre
			foreach ($laboratorywithid as $key => $value) {
				$array= array();
				$array["total"]=count($value);
				$array["info"]=$value;
				$laboratorywithid[$key]=$array;
				}
			foreach ($laboratorywithid as $key => $value) {
					$array=array();
					$key=split(",", $key);
					$array[]=$key[0];
					$array[]=$key[1];
					$array[]=$value["total"];
					$array[]=$value['info'];
					$laboratory[]=$array;
				}
			$response=array();
			$array=array();
			$array['noaff']=$received_array[0];
			$response[]=$array;
			$response["documents"]=$laboratory;


			return $response;
		}

	}
	
	
}


?>