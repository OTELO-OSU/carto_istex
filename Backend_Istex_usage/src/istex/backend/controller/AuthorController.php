<?php
namespace istex\backend\controller;
class AuthorController 
{

	function Sort_by_author($received_array){
		$received_array= json_decode($received_array,true);
		if (count($received_array[1])==0) {
			$array[]="empty";
			return $array;
		}
		else{
			 // Initialisation tableau
			$master_tab=[];
			foreach ($received_array[1] as $key => $value) {// on parcourt le tableau que la requetes nous a renvoyé
				$tab=array();
				$tab[]=str_replace(".","",$value['author'])." , ".$value['laboratory']." , ".$value['university'];// on stocke les valeurs dans un tableau et on remplace les . par rien du tout
				$tab[]=$value['laboratory'];
				$tab[]=$value['id'];
			
			if ($value['laboratory']==NULL) {
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
			$result = array_diff($test, $verif);
			
			$received_array[0]['noaff']=$received_array[0]['noaff']+count($result);
					
			$authorwithid = array();

	foreach($master_tab as $arg)
	{
	    $authorwithid[$arg[0]][] = $arg[1];
	}
		

				arsort($authorwithid);////tri de l'auteur qui a le plus de documents au plus petit nombre
				foreach ($authorwithid as $key => $value) {
				$array= array();
				$array["total"]=count($value);
				$authorwithid[$key]=$array;
				}
				foreach ($authorwithid as $key => $value) {
					$array=array();
					$key=split(",", $key);
					$array[]=$key[0];
					$array[]=$key[1];
					$array[]=$key[2];
					$array[]=$value["total"];
					$author[]=$array;
				}
				$response=array();
				$array=array();
				$array["noaff"]=$received_array[0];
				$response[]=$array;

				$response["documents"]=$author;
				return $response;
	
		}
	}

	
}


?>
