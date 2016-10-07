<?php

class Laboratory
{

	function Sort_by_laboratory($received_array){
		$tableau_laboratory=[];
		foreach ($received_array as $key => $value) {
			$tab=array();
			$tab[]=$value['laboratory'];
			$tab[]=$value['id'];
			$tab[]=$value['author'];
			$master_tab[]=$tab;
			$response_array=array();
		}
		

		foreach ($master_tab as $key2 => $value2) {
			$index=$value2[0];
			if (array_key_exists($index, $tableau_laboratory)) {
				$tableau_laboratory[$index]++;

			}
			else{
				$tableau_laboratory[$index] = 1;
				

				}
			}

			$laboratory=array();
			foreach ($tableau_laboratory as $key => $value) {
				$array=array();
				$array[$key] = $value;
				$laboratory[] = $array;
			}

			$laboratorywithid=array();
			foreach ($laboratory as $key => $value) {
				foreach ($value as $key2 => $value2) {
					$arraydocument=array();
					
					foreach ($master_tab as $key3 => $value3) {
						if ($key2==$value3[0]) {
						$array= array();
						$array[]=$value3[2];
						$array[]=$value3[1];
						$arraydocument[]=$array;
						$laboratorywithid[$key2]=$arraydocument;
					}
					
				}	
					
				}
			}

				
					
		ksort($laboratorywithid); // tri par ordre alaphabetique en fonction du labo
		//var_dump($laboratorywithid);
		return $laboratorywithid;


	}
	
	
}


?>