<?php
namespace istex\backend\controller;
class RequestController 
{
	//requetes curl 
	
	function simpleCachedCurl($url,$expires,$curlopt){
    $hash = md5($url);
    $filename = dirname(__FILE__).'/cache/' . $hash . '.cache';
    $changed = file_exists($filename) ? filemtime($filename) : 0;
    $now = time();
    $diff = $now - $changed;   
    if ( !$changed || ($diff > $expires) ) {
        $ch = curl_init();
        $curlopt = array(CURLOPT_URL => $url) + $curlopt ;
        curl_setopt_array($ch, $curlopt);
        $rawData = curl_exec($ch);
        curl_close($ch);
        if(!$rawData){
            if($changed){
                $cache = unserialize(file_get_contents($filename));
                return $cache;
            }else{
                return false;
            }
        }
        $cache = fopen($filename, 'wb');
        $write = fwrite($cache, serialize($rawData));
        fclose($cache);
        return $rawData;
    }
    $cache = unserialize(file_get_contents($filename));
    return $cache;
}


	function Request_alldoc_querypagebypage($query){
		//$curl = curl_init(); // initialisation de curl
		$query=rawurlencode($query); //encodage des caracteres d'espacers pour les passer dans l'url
		$url='https://api.istex.fr/document/?q='.$query.'&size=*&defaultOperator=AND&output=id,author.affiliations,author.name';
		$curlopt=array(CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 40,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET");
		$response=self::simpleCachedCurl($url,"180",$curlopt);
		/*curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.istex.fr/document/?q='.$query.'&size=*&defaultOperator=AND&output=id,author.affiliations,author.name',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 40,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  //CURLOPT_FORBID_REUSE => true
		)); // Reglage des differentes variable de CURL
		
		$response = curl_exec($curl); //Execution de la requete
		$err = curl_error($curl); //En cas d'erreur
		curl_close($curl);// fermeture du socket curl
		*/

		
			 $responseencoded=json_decode($response); // decodage de la chaine JSON
			 
			 if ($responseencoded->total>=5000) {  // Si les resultats de la requete son superieur a 5000
			 	$response1 = json_decode(json_encode($responseencoded->hits),true); // Passage du format JSON en tableau php

				/*$curl = curl_init();// initialisation de curl
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://api.istex.fr/document/?q='.$query.'&size=5000&from=5000&defaultOperator=AND&output=id,author.affiliations,author.name',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 40,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET",
			 // CURLOPT_FORBID_REUSE => true
				));*/
				//$response2 = curl_exec($curl); 
				//$err = curl_error($curl);
				//curl_close($curl);
				$url='https://api.istex.fr/document/?q='.$query.'&size=5000&from=5000&defaultOperator=AND&output=id,author.affiliations,author.name';
				$curlopt=array(CURLOPT_RETURNTRANSFER => true,
			 	 CURLOPT_ENCODING => "",
			  	CURLOPT_MAXREDIRS => 10,
			  	CURLOPT_TIMEOUT => 40,
			  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  	CURLOPT_CUSTOMREQUEST => "GET");
				$response2=self::simpleCachedCurl($url,"180",$curlopt);
				$response2 = json_decode($response2);
				$response2 = json_decode(json_encode($response2->hits),true); 

				/*$curl2 = curl_init();
				curl_setopt_array($curl2, array(
				  CURLOPT_URL => 'https://api.istex.fr/document/?q='.$query.'&size=5000&from=10000&defaultOperator=AND&output=id,author.affiliations,author.name',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 40,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET",
			 // CURLOPT_FORBID_REUSE => true
			));
				$response3 = curl_exec($curl2); 
				$response3 = json_decode($response3);
				$response3 = json_decode(json_encode($response3->hits),true); 
				$err = curl_error($curl2);
				curl_close($curl2);*/
				$url='https://api.istex.fr/document/?q='.$query.'&size=5000&from=10000&defaultOperator=AND&output=id,author.affiliations,author.name';
				$curlopt=array(CURLOPT_RETURNTRANSFER => true,
			 	 CURLOPT_ENCODING => "",
			  	CURLOPT_MAXREDIRS => 10,
			  	CURLOPT_TIMEOUT => 40,
			  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  	CURLOPT_CUSTOMREQUEST => "GET");
				$response3=self::simpleCachedCurl($url,"180",$curlopt);
				$response3 = json_decode($response3);
				$response3 = json_decode(json_encode($response3->hits),true); 

				
				 	

				$result = array_merge($response1, $response2,$response3); // on merge les differents tableau de reponse en un seul
				
			 	}	

			 else{
			 	$result=json_decode(json_encode($responseencoded->hits),true); // Si moins de 5000 resultats on garde le resultats de la premiere requetes
			 }
			 //echo count($result)." resultats<br>";
			
			
			$response_array= array();// initialisation d'un tableau
			$noaffiliations= array();// initialisation d'un tableau
			
			

			foreach ($result as $key => $value) {  //On parcourt le tableau resultat
					$array=array();
					@$affiliations=$value['author'][0]['affiliations']; // recuperation de l'affiliation
					if ($affiliations!==NULL) {	
						if (count($affiliations)==2) {
							if (preg_match("/\@/", $affiliations[0])) {
								$parse = explode(",", $affiliations[1]);
								$country=$parse[count($parse)-1]; // recupeartion du nom de pays
							}
							else{
								$parse = explode(",", $affiliations[0]);
								$country=$parse[count($parse)-1]; // recupeartion du nom de pays
							}
						}
						else{
						$parse = explode(",", $affiliations[0]); // on parse l'affiliation
						$country=$parse[count($parse)-1]; // recupeartion du nom de pays
						}
						$country = str_replace(".", "", $country);
						$id=$value['id']; // recuperation de l'id de la publication
						//$laboratory=$parse[0]; // recuperation du nom de labo 
						$laboratory= self::Match_result_for_laboratory($parse);
						$university= self::Match_result_for_university($parse);
						
						//@$university=$parse[1];
						$country = preg_replace('/\s+/', '', $country, 1); // remplacement du premier espace devant le nom de pays
						@$author=$value['author'][0]['name']; // recuperation du nom de l'auteur
						
						$array['id']=$id; // on stocke les differents champs dans un tableau
						$array['country']=$country;
						$array['laboratory']=$laboratory[0];
						$array['university']=$university[0];
						$array['author']=$author;
						//var_dump($parse);
						
						
						$response_array[]=$array; // on stocke le tableau dans un autre tableau
												

						
						

					}
					else{
						$noaffiliations[]=1;
					}



				
			}
			$response=array();
			$array=array();
			$array["noaff"]=count($noaffiliations);
			$response[]=$array;
			$response[]=$response_array;

			//echo count($response_array)." resultats avec affiliations<br>";
			
			return $response;

		
	}




function stripAccents($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}


function Match_result_for_laboratory($received_array){
		$array = array();
		$tableau_reference_laboratory=array("DEPARTMENT", "LABORATORY", "DIVISION", "SCHOOL", "ACADEMY", "CRPG", "LIEC", "LSE", "GEORESSOURCES","LABORATOIRE","DEPARTEMENT"," CNRS "," C.N.R.S ","MUSEUM","SECTION"," DEPT "," LABO "," DIV ","IRAP","I.R.A.P","DIPARTIMENTO","CENTRE NATIONAL DE LA RECHERCHE SCIENTIFIQUE");
		foreach ($tableau_reference_laboratory as $key => $valueref) {
			foreach ($received_array as $key => $value2) {
				if (preg_match("/".$valueref."/i",  mb_strtoupper(self::stripAccents($value2),'UTF-8'))){
					$array[]= $value2;
					return $array;
					
				}
				else{
					//echo $value[('laboratory')]."<br>";

				}
			
			}
				
		}
	}
	function Match_result_for_university($received_array){
		$array = array();
		$tableau_reference_university = array(" UNIV ", " INST ", "UNIVERSITY", "INSTITUTE", "INSTITUTION", "CENTER", "HOSPITAL", "COLLEGE", "FACULTY", "COUNCIL", "CEA", "MAX PLANK","IFREMER","UNIVERSITE","ECOLE","UNIVERSITIES","UNIVERSITES","OBSERVATORY","OBSERVATOIRE","AGENCY","AGENCE","BRGM","NATIONAL LABORATORY", "NATIONAL DEPARTMENT", "NATIONAL DIVISION", "NATIONAL SCHOOL", "NATIONAL ACADEMY","CENTRE","FOUNDATION","UNIVERSITA","NATIONAL LABO", "NATIONAL DEPT", "NATIONAL DIV",);
		foreach ($tableau_reference_university as $key => $valueref) {
			foreach ($received_array as $key => $value2) {
				if (preg_match("/".$valueref."/i",  mb_strtoupper(self::stripAccents($value2),'UTF-8'))){
					$array[]= $value2;
					return $array;

				}
				else{
					//echo $value[('laboratory')]."<br>";

				}
			
			}				
		}
	}







	//Fonction de demande de nom de pays a nominatim
	function Request_name_of_country($name,$id){
				//sleep(2);
				$curl = curl_init();
				$name=rawurlencode($name);
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'http://nominatim.openstreetmap.org/search/'.$name.'?format=json&addressdetails=1&limit=1&polygon_svg=0&accept-language=en',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 40,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET"
			));
				$response = curl_exec($curl); 
				$responsedecoded = json_decode($response);
				@$country = json_decode(json_encode($responsedecoded[0]->address->country),true); 
				//@$latitude = json_decode(json_encode($responsedecoded[0]->lat),true); 
				//@$longitude = json_decode(json_encode($responsedecoded[0]->lon),true); 

				if (!$responsedecoded==NULL) { // si la reponse n'est pas vide (correspondance nominatim)
				$array=array();
				$array['country']=$country;
				$array['id']=$id;
				return $array;
					
				}
				

				//var_dump($country);

				$err = curl_error($curl);
				curl_close($curl);

	}

	//Fonction de demande de latitude,longitude de pays a nominatim
	function Request_lat_lon_of_country($name){
				$curl = curl_init();
				$name=rawurlencode($name);
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'http://nominatim.openstreetmap.org/search/'.$name.'?format=json&addressdetails=1&limit=1&polygon_svg=0&accept-language=en',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 40,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET"
			));
				$response = curl_exec($curl); 
				$responsedecoded = json_decode($response);
				@$latitude = json_decode(json_encode($responsedecoded[0]->lat),true); //acquisition de la latitude
				@$longitude = json_decode(json_encode($responsedecoded[0]->lon),true); //acquisition de la longitude

				//if (!$responsedecoded==NULL) { // si la reponse n'est pas vide (correspondance nominatim)
				$array=array(); // mise en tableau
				$array['lat']=$latitude;
				$array['lon']=$longitude;
				return $array;
					
				//}
				


				$err = curl_error($curl);
				curl_close($curl);

	}



}


?>