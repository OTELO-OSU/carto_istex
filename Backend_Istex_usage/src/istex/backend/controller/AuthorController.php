<?php
namespace istex\backend\controller;
class AuthorController 
{

	


	function Sort_by_author($received_array){

		 // Initialisation tableau
		$master_tab=[];
		foreach ($received_array[1] as $key => $value) {// on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=str_replace(".","",$value['author'])." , ".$value['laboratory'];// on stocke les valeurs dans un tableau et on remplace les . par rien du tout
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
				
			
		
		/*foreach ($master_tab as $key2 => $value2) {// on parcourt le tableau precedemment créé
			$index=$value2[0];;// on definie l'index qui va permettre de determiner le nombre de publications par auteur unique
			if (isset($tableau_author[$index])) {// array key exist permet de verifier si une clé existe dans un tableau
				$tableau_author[$index]++;// si elle existe on ajoute 1 a chaque valeur
			}
			else{
				$tableau_author[$index] = 1;// sinon on laisse a 1
				}
			}*/

			
			
			/*foreach ($tableau_author as $key => $value) {// on parcourt ensuite le tableau 
				$array=array();
				$array[$key] = $value;// on range la valeur dans un autre tableau pour obtenir un tableau de tableau
				$author[] = $array;
			}*/
			
			//$authorwithid=array();
			/*foreach ($master_tab as $key => $value) {// on parcourt le tableau precedent
				//foreach ($value as $key2 => $value2) {
						$arraydocument=array();
					
				foreach ($master_tab as $key3 => $value3) {// on parcourt le tableau principale
					if ($value[0]==$value3[0]) {// si le nom d'auteur, labo est le meme alors on les regroupes
						$array= array();
						$array[]=$value3[2];
						$arraydocument[]=$array;
						$authorwithid[$value[0]]=$arraydocument;

						
					}
					
				}
			//}
		}*/
		$authorwithid = array();

foreach($master_tab as $arg)
{
    $authorwithid[$arg[0]][] = $arg[1];
}

	
					
					
			arsort($authorwithid);////tri de l'auteur qui a le plus de documents au plus petit nombre
			$response=array();
			$array=array();
			$array["noaff"]=$received_array[0];
			$response[]=$array;
			$response["documents"]=$authorwithid;
			return $response;
	

	}

	
}


?>
