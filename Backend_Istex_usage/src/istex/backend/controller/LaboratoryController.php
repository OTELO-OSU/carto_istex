<?php
namespace istex\backend\controller;
class LaboratoryController 
{




	function Sort_by_laboratory($received_array){
		$received_array= json_decode($received_array,true);
		$tableau_laboratory=[]; // Initialisation tableau
		$tableau_laboratorys=[];
		$master_tab=[];
		
		foreach ($received_array[1] as $key => $value) { // on parcourt le tableau que la requetes nous a renvoyé
			$tab=array();
			$tab[]=$value['laboratory']." , ".$value['university'];// on stocke les valeurs dans un tableau
			$tab[]=$value['id'];
			
	if ($value['laboratory']==NULL && $value['university']!=NULL) {
			$laboratorynull[]=$value['id'];
		}
		else if ($value['university']==NULL && $value['laboratory']!=NULL) {
			$universitynull[]=$value['id'];
		}
		else if($value['university']==NULL && $value['laboratory']==NULL)
		{
			$null[]=$value['id'];
		}
		if (($value['laboratory']==NULL && $value['university']==NULL) || ($value['laboratory']==NULL) || ($value['university']==NULL)) {
					
					$test[]=$value['id'];
				}
				else{
					$master_tab[]=$tab;// on stocke le tableau dans un autre
					$verif[]=$value['id'];
					}

		}

		//var_dump($laboratorynull);
		//var_dump($universitynull);
		//var_dump($null);
		//var_dump($received_array[0]['noaff']);
		$laboratorynull = array_map("unserialize", array_unique(array_map("serialize", $laboratorynull)));
		$universitynull = array_map("unserialize", array_unique(array_map("serialize", $universitynull)));
		$null = array_map("unserialize", array_unique(array_map("serialize", $null)));
		//var_dump($laboratorynull);
		//var_dump($universitynull);
		//var_dump($null);
		$verif = array_map("unserialize", array_unique(array_map("serialize", $verif)));
		$master_tab = array_map("unserialize", array_unique(array_map("serialize", $master_tab)));
		$test = array_map("unserialize", array_unique(array_map("serialize", $test)));
		$result = array_diff($test, $verif);
		//var_dump($result);
		$received_array[0]['noaff']=$received_array[0]['noaff']+count($result);

		$laboratorywithid = array();


		foreach($master_tab as $arg)
		{
		    $laboratorywithid[$arg[0]][] = $arg[1];
		}


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
		

			
			

		
			//$laboratory=array();
			/*foreach ($master_tab as $key => $value) {// on parcourt ensuite le tableau 
				$array=array();
				$array[$value[0]] = $value[1];// on range la valeur dans un autre tableau pour obtenir un tableau de tableau
				$laboratory[] = $array;
			}*/
			//$laboratorywithid=array();
			/*foreach ($master_tab as $key => $value1) {// on parcourt le tableau precedent
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
					}*/
					/*foreach ($laboratorywithid as $key3 => $value) {// on parcourt le tableau principale
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
		//}

				/*foreach ($laboratorywithid as $key => $value) {
							$mastervalue=explode(",", $key);
					foreach ($laboratorywithid as $key2 => $value2) {
						
							$valuetocompare=explode(",", $key2);
							$expr = '/[A-Z]/';
							

						$masterinstitution=ltrim($mastervalue[1]);
						$compareinstitution=ltrim($valuetocompare[1]);
						$masterlaboratory=ltrim($mastervalue[0]);
						$comparelaboratory=ltrim($valuetocompare[0]);

						similar_text(@$masterinstitution,@$compareinstitution,$percent);
					
						

						if ($percent >=90) {
							if(preg_match_all($expr, $masterlaboratory, $matches)){
							$result1 = implode('', $matches[0]);
							$result1 = strtoupper($result1);
							}
							if(preg_match_all($expr, $comparelaboratory, $matches)){
							$result2 = implode('', $matches[0]);
							$result2 = strtoupper($result2);
							}

							if(preg_match_all($expr, $masterinstitution, $matches)){
							$result3 = implode('', $matches[0]);
							$result3 = strtoupper($result3);
							}
							if(preg_match_all($expr, $compareinstitution, $matches)){
							$result4 = implode('', $matches[0]);
							$result4 = strtoupper($result4);
							}
							if (preg_match("/".metaphone($result1)."/",  metaphone($result2))){
							$percent=levenshtein(metaphone($result1), metaphone($result2));
							similar_text($result3, $result4,$percent3);
							$percent5=levenshtein(metaphone($masterinstitution), metaphone($compareinstitution));
							similar_text(metaphone($result3), metaphone($result4),$percent4);
							similar_text(metaphone($result1), metaphone($result2),$percent2);
							}
							
					else{

						}

						
						if (($percent<=3 AND $percent2>=90 ) AND ($percent3<=100 AND $percent4>=90 )AND($percent5!==0 AND $percent5<=2)) {
							
							
							//var_dump("probably same");
							//var_dump($percent3);
							//var_dump($masterinstitution);
							//var_dump($compareinstitution);
							//var_dump($masterlaboratory);
							//var_dump($comparelaboratory);
							
								$array= array();
								$array[$key]=$value;
								$arraydocument[]=$array;
								//unset($laboratorywithid[$key2]);

									//var_dump($key2);
							//var_dump($arraydocument);
								foreach ($array as $key2 => $value){
									foreach ($value as $key3 => $value2) {
									
									//foreach ($value2 as $key => $value3) {
										
										
								    $out[] = array_merge((array)[$key3], (array)$value2);
								//$out = array_map("unserialize", array_unique(array_map("serialize", $out)));
									//}
									}
								    
								//$laboratorywithid[$key2]=$out;
								
								}
								
							//echo "<br><br>";
						//if ($leven<=20) {
							
								
								
						}
							}
							elseif($percent==100){
								$array= array();
								$array[]=$value;
								$arraydocument[]=$array;
								$laboratorywithid[$key]=$arraydocument;
							}
							else{
								//var_dump($masterinstitution);
							}
							
						}
							//}


					}*/






				
	
		arsort($laboratorywithid); //tri du labo qui a le plus de documents au plus petit nombre
		foreach ($laboratorywithid as $key => $value) {
			$array= array();
			$array["total"]=count($value);
			$laboratorywithid[$key]=$array;
			}
		$response=array();
		$array=array();
		$array["noaff"]=$received_array[0];
		$response[]=$array;
		$response["documents"]=$laboratorywithid;


		return $response;


	}
	
	
}


?>