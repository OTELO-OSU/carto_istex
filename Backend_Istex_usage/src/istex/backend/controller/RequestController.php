<?php
namespace istex\backend\controller;
class RequestController 
{
	//fonction requetes curl 
	function Curlrequest($url,$curlopt){
        $ch = curl_init();
        $curlopt = array(CURLOPT_URL => $url) + $curlopt ;
        curl_setopt_array($ch, $curlopt);
    	$rawData = curl_exec($ch);
	    curl_close($ch);
	    return $rawData;
	}


	

	function Request_alldoc_querypagebypage($query){
		//$curl = curl_init(); // initialisation de curl
		$hash= md5($query); // on hash la query
		$m = new \Memcached(); // initialisation memcached
		$m->addServer('localhost', 11211); // ajout server memecached
		$cache=$m->get($hash);//on lit la memoire
		if ($cache) { // si présent dans la memoire on retourne le cache
			return $cache;
		}
		else{ // sinon on effectue la query
		$query=rawurlencode($query); //encodage des caracteres d'espacers pour les passer dans l'url
		$url='https://api.istex.fr/document/?q='.$query.'&size=*&defaultOperator=AND&output=id,author.affiliations,author.name';
		$curlopt=array(CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 40,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET");
		$response=self::Curlrequest($url,$curlopt);
	

		
			 $responseencoded=json_decode($response); // decodage de la chaine JSON
			 
			 if ($responseencoded->total>=5000) {  // Si les resultats de la requete son superieur a 5000
			 	$response1 = json_decode(json_encode($responseencoded->hits),true); // Passage du format JSON en tableau php

				
				$url='https://api.istex.fr/document/?q='.$query.'&size=5000&from=5000&defaultOperator=AND&output=id,author.affiliations,author.name';
				$curlopt=array(CURLOPT_RETURNTRANSFER => true,
			 	 CURLOPT_ENCODING => "",
			  	CURLOPT_MAXREDIRS => 10,
			  	CURLOPT_TIMEOUT => 40,
			  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  	CURLOPT_CUSTOMREQUEST => "GET");
				$response2=self::Curlrequest($url,$curlopt);
				$response2 = json_decode($response2);
				$response2 = json_decode(json_encode($response2->hits),true); 

				
				$url='https://api.istex.fr/document/?q='.$query.'&size=5000&from=10000&defaultOperator=AND&output=id,author.affiliations,author.name';
				$curlopt=array(CURLOPT_RETURNTRANSFER => true,
			 	 CURLOPT_ENCODING => "",
			  	CURLOPT_MAXREDIRS => 10,
			  	CURLOPT_TIMEOUT => 40,
			  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  	CURLOPT_CUSTOMREQUEST => "GET");
				$response3=self::Curlrequest($url,$curlopt);
				$response3 = json_decode($response3);
				$response3 = json_decode(json_encode($response3->hits),true); 

				
				 	

				$result = array_merge($response1, $response2,$response3); // on merge les differents tableau de reponse en un seul
			 	}	

			 else{
			 	$result=json_decode(json_encode($responseencoded->hits),true); // Si moins de 5000 resultats on garde le resultats de la premiere requetes
			 }
			
			
			$response_array= array();// initialisation d'un tableau
			$noaffiliations= array();// initialisation d'un tableau
			
			

			foreach ($result as $key => $value) {  //On parcourt le tableau resultat
					$array=array();
					//Revoir demain mardi, probeme de calcul author affiliations voir reque envrionmental
					if (!array_key_exists('author', $value)) {
						$noaffiliations[]=1	;				
					}
					else{
					foreach ($value['author'] as $key => $value2) {
						//var_dump($value2);
						
						@$author=$value2['name']; // recuperation du nom de l'auteur
							
						@$affiliations=$value2['affiliations']; // recuperation de l'affiliation
					if (@$affiliations!==NULL) {	
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
						
						$country = preg_replace('/\s+/', '', $country, 1); // remplacement du premier espace devant le nom de pays
						
						$array['id']=$id; // on stocke les differents champs dans un tableau
						$array['country']=$country;
						$array['laboratory']=$laboratory[0];
						$array['university']=$university[0];
						$array['author']=$author;
						
						
						$response_array[]=$array; // on stocke le tableau dans un autre tableau
												

						
						

					}
					else{
						$noaffiliations[]=1;
					}
						
						
					}
				}



				
			}
			$response=array();
			$array=array();
			$array["noaff"]=count($noaffiliations);
			$array["total"]=count($result);
			$response[]=$array;
			$response[]=$response_array;
	        $cache=$m->set($hash, $response, 120);// on set le tableau obtenu dans le cache
			return $response;

		}
	}




function stripAccents($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}


function Match_result_for_laboratory($received_array){ // Fonction permettant de rechercher si les affiliations on un bon laboratoires
		$array = array();
		$tableau_reference_laboratory=array("DEPARTMENT", "LABORATORY", "DIVISION", "SCHOOL", "ACADEMY", "CRPG", "LIEC", "LSE", "GEORESSOURCES","LABORATOIRE","DEPARTEMENT"," CNRS "," C.N.R.S ","MUSEUM","SECTION"," DEPT "," LABO "," DIV ","IRAP","I.R.A.P","DIPARTIMENTO","CENTRE NATIONAL DE LA RECHERCHE SCIENTIFIQUE"); // tableau pour effectuer la comparaison
		foreach ($tableau_reference_laboratory as $key => $valueref) {
			foreach ($received_array as $key => $value2) {
				if (preg_match("/".$valueref."/i",  mb_strtoupper(self::stripAccents($value2),'UTF-8'))){
					$array[]= $value2;
					return $array;
					
				}
				else{

				}
			
			}
				
		}
	}
	function Match_result_for_university($received_array){// Fonction permettant de rechercher si les affiliations on une bonne institution
		$array = array();
		$tableau_reference_university = array(" UNIV ", " INST ", "UNIVERSITY", "INSTITUTE", "INSTITUTION", "CENTER", "HOSPITAL", "COLLEGE", "FACULTY", "COUNCIL", "CEA", "MAX PLANK","IFREMER","UNIVERSITE","ECOLE","UNIVERSITIES","UNIVERSITES","OBSERVATORY","OBSERVATOIRE","AGENCY","AGENCE","BRGM","NATIONAL LABORATORY", "NATIONAL DEPARTMENT", "NATIONAL DIVISION", "NATIONAL SCHOOL", "NATIONAL ACADEMY","CENTRE","FOUNDATION","UNIVERSITA","NATIONAL LABO", "NATIONAL DEPT", "NATIONAL DIV",);// tableau pour effectuer la comparaison
		foreach ($tableau_reference_university as $key => $valueref) {
			foreach ($received_array as $key => $value2) {
				if (preg_match("/".$valueref."/i",  mb_strtoupper(self::stripAccents($value2),'UTF-8'))){
					$array[]= $value2;
					return $array;

				}
				else{

				}
			
			}				
		}
	}







	//Fonction de demande de nom de pays a nominatim
	function Request_name_of_country($name,$id){
				$name= mb_strtoupper(self::stripAccents($name),'UTF-8');
				$name=preg_replace('/[0-9-z_@~]/', '', $name);
				$hash= md5($name);
				$m = new \Memcached(); // initialisation memcached
				$m->addServer('localhost', 11211); // ajout server memecached
				$curl = curl_init();
				$name=rawurlencode($name);
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://nominatim.otelo.univ-lorraine.fr/search.php/'.$name.'?format=json&addressdetails=1&limit=1&polygon_svg=0&accept-language=en',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 40,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET"
			));
				$cache=$m->get($hash);//on lit la memoire
				if ($cache) {
					$responsedecoded=$cache;
				}
				else{

				$response = curl_exec($curl); 
				$responsedecoded = json_decode($response);
				$cache=$m->set($hash, $responsedecoded, 10);// on set le tableau obtenu dans le cache
				}

				@$country = json_decode(json_encode($responsedecoded[0]->address->country),true); 

				if (!$responsedecoded==NULL) { // si la reponse n'est pas vide (correspondance nominatim)
				$array=array();
				$array['country']=$country;
				$array['id']=$id;
				return $array;
					
				}
				else{
					$array=array();
					$array['country']="NULL";
					$array['id']=$id;
					return $array;

				}
				


				$err = curl_error($curl);
				curl_close($curl);

	}

	//Fonction de demande de latitude,longitude de pays a nominatim
	function Request_lat_lon_of_country($name){
				$name= mb_strtoupper(self::stripAccents($name),'UTF-8');
				$name=preg_replace('/[0-9-z_@~]/', '', $name);
				$hash= md5($name);
				$curl = curl_init();
				$name=rawurlencode($name);
				$m = new \Memcached(); // initialisation memcached
				$m->addServer('localhost', 11211); // ajout server memecached
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://nominatim.otelo.univ-lorraine.fr/search.php/'.$name.'?format=json&addressdetails=1&limit=1&polygon_svg=0&accept-language=en',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 40,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET"
			));
				$cache=$m->get($hash);//on lit la memoire
				if ($cache) {
					$responsedecoded=$cache;
				}
				else{

				$response = curl_exec($curl); 
				$responsedecoded = json_decode($response);
				$cache=$m->set($hash, $responsedecoded, 30);// on set le tableau obtenu dans le cache
				}
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