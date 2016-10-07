<?php

class Author
{

	function Sort_by_author($received_array){
		$tableau_author=[];
		foreach ($received_array as $key => $value) {
			$tab=array();
			$tab[]=$value['author'];
			$tab[]=$value['id'];
			$tab[]=$value['laboratory'];
			$master_tab[]=$tab;
			$response_array=array();
		}
		

		foreach ($master_tab as $key2 => $value2) {
			$index=$value2[0];
			if (array_key_exists($index, $tableau_author)) {
				$tableau_author[$index]++;
			}
			else{
				$tableau_author[$index] = 1;
				}
			}

			$author=array();
			foreach ($tableau_author as $key => $value) {
				$array=array();
				$array[$key] = $value;
				$author[] = $array;
			}

			$authorwithid=array();
			foreach ($author as $key => $value) {
				foreach ($value as $key2 => $value2) {
					$arraydocument=array();
					
				foreach ($master_tab as $key3 => $value3) {
					if ($key2==$value3[0]) {
						$array= array();
						$array[]=$value3[2];
						$array[]=$value3[1];
						$arraydocument[]=$array;
						$authorwithid[$key2]=$arraydocument;

						
					}
					
				}
						
					
					
					
				}
			}

				
			ksort($authorwithid);// tri par ordre alaphabetique en fonction de l'auteur
			//var_dump($authorwithid);
			return $authorwithid;
	}
	
}


?>