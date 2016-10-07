<?php

class Country
{
	function Sort_by_country($received_array){
		$tableau_country=[];
		foreach ($received_array as $key => $value) {
			$tab=array();
			$tab[]=$value['country'];
			$tab[]=$value['id'];
			$master_tab[]=$tab;
			$response_array=array();
		}
		

		foreach ($master_tab as $key2 => $value2) {
			$index=$value2[0];
			if (array_key_exists($index, $tableau_country)) {
				$tableau_country[$index]++;

			}
			else{
				$tableau_country[$index] = 1;
				}
			}

		$country=array();
		foreach ($tableau_country as $key => $value) {
			$array=array();
			$array[$key] = $value;
			$country[] = $array;
		}

		$countrywithid=array();
		foreach ($country as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$array= array();
				foreach ($master_tab as $key3 => $value3) {
					if ($key2==$value3[0]) {
						$array[]=$value3[1];
						$countrywithid[$key2]=$array;
						
					}
					
				}
										
				}
			}

				
			arsort($countrywithid);	//tri du pays qui a le plus de document au plus petit nombre
			var_dump($countrywithid);
			return $countrywithid;
	
	}

	
}


?>