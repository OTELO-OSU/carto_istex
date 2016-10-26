<?php
namespace istex\backend\controller;
class LaboratoryController 
{



	function Sort_by_laboratory($received_array){
		$tableau_laboratory=[]; // Initialisation tableau
		foreach ($received_array[1] as $key => $value) { // on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=$value['laboratory']." , ".$value['university'];// on stocke les valeurs dans un tableau
			$tab[]=$value['id'];
			$tab[]=$value['author'];
			if (($value['laboratory']==NULL && $value['university']==NULL) OR ($value['laboratory']==NULL) OR ($value['university']==NULL) ) {
					$received_array[0]['noaff']++;
			}
			else{
				$master_tab[]=$tab;// on stocke le tableau dans un autre
				}

		}
		
		foreach ($master_tab as $key2 => $value2) { // on parcourt le tableau precedemment créé
			$index=$value2[0];// on definie l'index qui va permettre de determiner le nombre de publications par labo
			if (array_key_exists($index, $tableau_laboratory)) {// array key exist permet de verifier si une clé existe dans un tableau
				$tableau_laboratory[$index]++;// si elle existe on ajoute 1 a chaque valeur

			}
			else{
				$tableau_laboratory[$index] = 1;// sinon on laisse a 1
				

				}
			}

			$laboratory=array();
			foreach ($tableau_laboratory as $key => $value) {// on parcourt ensuite le tableau 
				$array=array();
				$array[$key] = $value;// on range la valeur dans un autre tableau pour obtenir un tableau de tableau
				$laboratory[] = $array;
			}

			$laboratorywithid=array();
			foreach ($laboratory as $key => $value) {// on parcourt le tableau precedent
				foreach ($value as $key2 => $value2) {
					$arraydocument=array();
					
					foreach ($master_tab as $key3 => $value3) {// on parcourt le tableau principale
						if ($key2==$value3[0]) {// si le nom de labo est le meme alors on les regroupes
						$array= array();
						$array[]=$value3[2];
						$array[]=$value3[1];
						$arraydocument[]=$array;
						$laboratorywithid[$key2]=$arraydocument;
					}
					
				}	
					
				}
			}

				
					
		arsort($laboratorywithid); //tri du labo qui a le plus de documents au plus petit nombre
		$response=array();
		$array=array();
		$array["noaff"]=$received_array[0];
		$response[]=$array;
		$response["documents"]=$laboratorywithid;

		return $response;


	}
	
	
}


?>