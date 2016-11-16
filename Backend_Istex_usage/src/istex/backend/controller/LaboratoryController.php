<?php
namespace istex\backend\controller;
class LaboratoryController 
{

function search_array($needle, $haystack) {
     if(isset($needle, $haystack)) {
          return true;
     }
     foreach($haystack as $element) {
          if(is_array($element) && self::search_array($needle, $element))
               return true;
     }
   return false;
}

	function Sort_by_laboratory($received_array){
	
		$tableau_laboratory=[]; // Initialisation tableau
		$tableau_laboratorys=[];
		$master_tab=[];
		foreach ($received_array[1] as $key => $value) { // on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=$value['laboratory']." , ".$value['university'];// on stocke les valeurs dans un tableau
			$tab[]=$value['id'];
			

		
		if (((self::search_array($value['id'], $master_tab)==true)&&$value['laboratory']==NULL && $value['university']==NULL) || ($value['laboratory']==NULL) || ($value['university']==NULL)OR($value['laboratory']==NULL && $value['university']==NULL) || ($value['laboratory']==NULL) || ($value['university']==NULL)) {
					
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

//var_dump($master_tab);
	//$master_tab = array_map("unserialize", array_unique(array_map("serialize", $master_tab)));
	
		
 
		
		

		/*foreach ($master_tab as $key2 => $value2) { // on parcourt le tableau precedemment créé
			$index=$value2[0];// on definie l'index qui va permettre de determiner le nombre de publications par labo
			if (isset($tableau_laboratory[$index])) {// array key exist permet de verifier si une clé existe dans un tableau
				$tableau_laboratory[$index]++;// si elle existe on ajoute 1 a chaque valeur

			}
			else{
				$tableau_laboratory[$index] = 1;// sinon on laisse a 1
				

				}
			}*/
	/*foreach ($tableau_laboratory as $value2 => $value3) {
			$valuetocompare=explode(",", $value2);
				foreach ($tableau_laboratory as $value => $value4) {
					//echo "<br>Valeur:".$value[0];
					 //$leven=levenshtein($value2[0], $value[0]);
					// echo $leven;
					//echo "<br><br>";
					$expr = '/[A-Z]/';
					$mastervalue=explode(",", $value);
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
					$result3 = strtoupper($result3);
					}
					if(preg_match_all($expr, $valuetocompare[1], $matches)){
					$result4 = implode('', $matches[0]);
					$result4 = strtoupper($result4);
					}
				

				if (preg_match("/".metaphone($result1)."/",  metaphone($result2))){
				$percent=levenshtein(metaphone($result1), metaphone($result2));
				$percent3=levenshtein(metaphone($result3), metaphone($result4));
				$percent5=levenshtein(metaphone($mastervalue[1]), metaphone($valuetocompare[1]));
				similar_text(metaphone($result3), metaphone($result4),$percent4);
				similar_text(metaphone($result1), metaphone($result2),$percent2);
				
						if (($percent<=5 AND $percent2>=90 ) AND ($percent3<=5 AND $percent4>=90 )AND($percent5!==0 AND $percent5<=2)) {
			
			$arr=array();
			$arr[$value]=$value3+$value4;
			
			$array[]=$arr;
			
					}
				}
				else if($percent5==0){
			

			$arr=array();
			$arr[$value]=$value4;
			
			$array[]=$arr;

			
			/*if (array_key_exists($index, $tableau_laboratorys)) {
					
				$tableau_laboratorys[$index]++;// si elle existe on ajoute 1 a chaque valeur

			}
			else{
				$tableau_laboratorys[$index] = 1;// sinon on laisse a 1
				

				
				}
				}
				
			}*/
			// array key exist permet de verifier si une clé existe dans un tableau
					
			
	//$array = array_map("unserialize", array_unique(array_map("serialize", $array)));
				//var_dump($array);
		

			
			

		
			$laboratory=array();
			/*foreach ($master_tab as $key => $value) {// on parcourt ensuite le tableau 
				$array=array();
				$array[$value[0]] = $value[1];// on range la valeur dans un autre tableau pour obtenir un tableau de tableau
				$laboratory[] = $array;
			}*/
			$laboratorywithid=array();
			foreach ($master_tab as $key => $value1) {// on parcourt le tableau precedent
				//foreach ($value1 as $value2 => $value2) {
					$arraydocument=array();
					foreach ($master_tab as $key3 => $value3) {
						if ($value1[0]==$value3[0]) {// si le nom de labo est le meme alors on les regroupes
						// echo $leven;
						//echo "<br><br>";
					//if ($leven<=20) {
					
							$array= array();
							$array[]=$value3[1];
							$arraydocument[]=$array;
							$laboratorywithid[$value1[0]]=$arraydocument;
						}
					}
					/*foreach ($master_tab as $key3 => $value) {// on parcourt le tableau principale
				//	$leven=levenshtein($key2, $value3[0]);
						//if ($key2==$value3[0]) {// si le nom de labo est le meme alors on les regroupes
					// echo $leven;
					$valuetocompare=explode(",", $value2);

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
					$result3 = strtoupper($result3);
					}
					if(preg_match_all($expr, $valuetocompare[1], $matches)){
					$result4 = implode('', $matches[0]);
					$result4 = strtoupper($result4);
					}
				

				if (preg_match("/".metaphone($result1)."/",  metaphone($result2))){
				$percent=levenshtein(metaphone($result1), metaphone($result2));
				$percent3=levenshtein(metaphone($result3), metaphone($result4));
				$percent5=levenshtein(metaphone($mastervalue[1]), metaphone($valuetocompare[1]));
				similar_text(metaphone($result3), metaphone($result4),$percent4);
				similar_text(metaphone($result1), metaphone($result2),$percent2);
				
						if (($percent<=5 AND $percent2>=90 ) AND ($percent3<=5 AND $percent4>=90 )AND($percent5!==0 AND $percent5<=2)) {
					//echo "<br><br>";
				//if ($leven<=20) {
					
						$array= array();
						$array[]=$value[1];
						$arraydocument[]=$array;
						foreach ($array as $key => $value){
						    $out[] = array_merge((array)[$key], (array)$value);
						
						
				}
					}
					elseif($percent5==0){
						$array= array();
						$array[]=$value[1];
						$arraydocument[]=$array;
						$laboratorywithid[$value[0]]=$arraydocument;
					}
					
				}	

					
				}*/
			//}
		}

				
	//	var_dump($out);
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