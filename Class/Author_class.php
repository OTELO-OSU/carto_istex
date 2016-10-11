<?php

class Author
{

	function Sort_by_author($received_array){
		$tableau_author=[]; // Initialisation tableau
		$authorbylabo=[];
		foreach ($received_array as $key => $value) {// on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=$value['author']." , ".$value['laboratory'];// on stocke les valeurs dans un tableau
			$tab[]=$value['id'];
			$tab[]=$value['laboratory'];
			$master_tab[]=$tab;// on stocke le tableau dans un autre
		}
		
		foreach ($master_tab as $key2 => $value2) {// on parcourt le tableau precedemment créé
			$index=$value2[0];;// on definie l'index qui va permettre de determiner le nombre de publications par auteur unique
			if (array_key_exists($index, $tableau_author)) {// array key exist permet de verifier si une clé existe dans un tableau
				$tableau_author[$index]++;// si elle existe on ajoute 1 a chaque valeur
			}
			else{
				$tableau_author[$index] = 1;// sinon on laisse a 1
				}
			}

			$author=array();
			foreach ($tableau_author as $key => $value) {// on parcourt ensuite le tableau 
				$array=array();
				$array[$key] = $value;// on range la valeur dans un autre tableau pour obtenir un tableau de tableau
				$author[] = $array;
			}
			
			$authorwithid=array();
			foreach ($author as $key => $value) {// on parcourt le tableau precedent
				foreach ($value as $key2 => $value2) {
					$arraydocument=array();
					
				foreach ($master_tab as $key3 => $value3) {// on parcourt le tableau principale
					if ($key2==$value3[0]) {// si le nom d'auteur, labo est le meme alors on les regroupes
						$array= array();
						$array[]=$value3[1];
						
						$arraydocument[]=$array;
						$authorwithid[$key2]=$arraydocument;

						
					}
					
				}
			}
		}
	
					
					
			arsort($authorwithid);////tri de l'auteur qui a le plus de documents au plus petit nombre
			var_dump($authorwithid);
			return $authorwithid;
	

	}

	
}


?>