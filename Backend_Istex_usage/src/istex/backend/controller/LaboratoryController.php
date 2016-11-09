<?php
namespace istex\backend\controller;
class LaboratoryController 
{



	function Sort_by_laboratory($received_array){
		$tableau_laboratory=[]; // Initialisation tableau
		$tableau_laboratorys=[];
		foreach ($received_array[1] as $key => $value) { // on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=$value['laboratory']." , ".$value['university'];// on stocke les valeurs dans un tableau
			$tab[]=$value['id'];
			if (($value['laboratory']==NULL && $value['university']==NULL) OR ($value['laboratory']==NULL) OR ($value['university']==NULL) ) {
					$received_array[0]['noaff']++;
			}
			else{
				$master_tab[]=$tab;// on stocke le tableau dans un autre
				}


		}
		
 
	$master_tab = array_map("unserialize", array_unique(array_map("serialize", $master_tab)));
		
	
		foreach ($master_tab as $key2 => $value2) { // on parcourt le tableau precedemment créé
			$index=$value2[0];// on definie l'index qui va permettre de determiner le nombre de publications par labo
			if (array_key_exists($index, $tableau_laboratory)) {// array key exist permet de verifier si une clé existe dans un tableau
				$tableau_laboratory[$index]++;// si elle existe on ajoute 1 a chaque valeur

			}
			else{
				$tableau_laboratory[$index] = 1;// sinon on laisse a 1
				

				}
			$valuetocompare=explode(",", $value2[0]);
				/*foreach ($master_tab as $key => $value) {
					//echo "<br>Valeur:".$value[0];
					 //$leven=levenshtein($value2[0], $value[0]);
					// echo $leven;
					//echo "<br><br>";
							$expr = '/[A-Z]/';
					$mastervalue=explode(",", $value[0]);
					if(preg_match_all($expr, $mastervalue[0], $matches)){
					$result1 = implode('', $matches[0]);
					$result1 = strtoupper($result1);
					}
					if(preg_match_all($expr, $valuetocompare[0], $matches)){
					$result2 = implode('', $matches[0]);
					$result2 = strtoupper($result2);
					}

					if(preg_match_all($expr, $mastervalue[1], $matches)){
					$result3 = implode('', $matches[0]);
					$result3 = strtoupper($result1);
					}
					if(preg_match_all($expr, $valuetocompare[1], $matches)){
					$result4 = implode('', $matches[0]);
					$result4 = strtoupper($result2);
					}


				if (preg_match("/".metaphone($result1)."/",  metaphone($result2))){
					
			$index=$value[0];
			$arr=array();
		
			$arr[]=$value[0];
			$arr[]=$value2[0];
			$array[]=$arr;
				}
				else{
			
				$percent=levenshtein(metaphone($result1), metaphone($result2));
				$percent3=levenshtein(metaphone($result3), metaphone($result4));
				similar_text(metaphone($result3), metaphone($result4),$percent4);
				similar_text(metaphone($result1), metaphone($result2),$percent2);

				if ($percent3<=5 AND $percent4>=90 ) {

			
			/*if (array_key_exists($index, $tableau_laboratorys)) {
					
				$tableau_laboratorys[$index]++;// si elle existe on ajoute 1 a chaque valeur

			}
			else{
				$tableau_laboratorys[$index] = 1;// sinon on laisse a 1
				

				}
				}
				}
				}
*/			
			// array key exist permet de verifier si une clé existe dans un tableau
					
			}

				//var_dump($array);
		

			
			

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
				//	$leven=levenshtein($key2, $value3[0]);
						if ($key2==$value3[0]) {// si le nom de labo est le meme alors on les regroupes
					// echo $leven;
					//echo "<br><br>";
				//if ($leven<=20) {
				
						$array= array();
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