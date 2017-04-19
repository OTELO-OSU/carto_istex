<?php
namespace istex\backend\controller;
ini_set('memory_limit', '-1');



class RequestController 
{
	/**
     *  Methode d'execution des Requetes CURL
     *
     *  @param $url :
     *          Url a appeler
     *  @param $curlopt :
     *			Option a ajouter
     * 	@return $rawData:
     *			Données Json recu
     */
	function Curlrequest($url,$curlopt){
        $ch = curl_init();
        $curlopt = array(CURLOPT_URL => $url) + $curlopt ;
        curl_setopt_array($ch, $curlopt);
    	$rawData = curl_exec($ch);
	    curl_close($ch);
	    return $rawData;
	}

	/**
     *  Methode de requetes vers l'API ISTEX
     *
     *  @param $query :
     *          Nom rechercher par l'utilisateur
     * 	@return $json:
     *			Json traiter dans les différents controller	
     */
	function Request_alldoc_querypagebypage($query){
		//$curl = curl_init(); // initialisation de curl
		$hash= md5($query); // on hash la query
		$m = new \Memcached(); // initialisation memcached
		$m->addServer('localhost', 11211); // ajout server memecached
		$m->setOption(\Memcached::OPT_COMPRESSION, false);
		$cache=$m->get($hash);//on lit la memoire
		if ($cache) { // si présent dans la memoire on retourne le cache
			return $cache;
		}
		else{ // sinon on effectue la query
		$query=rawurlencode($query); //encodage des caracteres d'espacers pour les passer dans l'url
		$url='https://api.istex.fr/document/?q='.$query.'&size=*&defaultOperator=AND&output=id,author.affiliations,author.name,title&facet=genre,corpusName,publicationDate,copyrightDate,language,wos,score';
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

				
				$url='https://api.istex.fr/document/?q='.$query.'&size=5000&from=5000&defaultOperator=AND&output=id,author.affiliations,author.name,title&facet=genre,corpusName,publicationDate,copyrightDate,language,wos,score';
				$curlopt=array(CURLOPT_RETURNTRANSFER => true,
			 	 CURLOPT_ENCODING => "",
			  	CURLOPT_MAXREDIRS => 10,
			  	CURLOPT_TIMEOUT => 40,
			  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  	CURLOPT_CUSTOMREQUEST => "GET");
				$response2=self::Curlrequest($url,$curlopt);
				$response2 = json_decode($response2);
				$response2 = json_decode(json_encode($response2->hits),true); 

				
				$url='https://api.istex.fr/document/?q='.$query.'&size=5000&from=10000&defaultOperator=AND&output=id,author.affiliations,author.name,title&facet=genre,corpusName,publicationDate,copyrightDate,language,wos,score';
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
			$json=json_encode($result);

			$cache=$m->set($hash, $json, 120);// on set le tableau obtenu dans le cache

			$results= shell_exec('python Requestprocessing.py '.escapeshellarg($hash));
			$response_array=json_decode($results,TRUE);
			
			

			$json=json_encode($response_array);
 
			
	       $cache=$m->set($hash, $json, 120);// on set le tableau obtenu dans le cache
			return $json;

		}
	}

}


?>
