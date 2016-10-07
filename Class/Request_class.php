<?php

class Request 
{
	//requetes curl 
	function Request_alldoc_query($query){
		$curl = curl_init();
		$query=rawurlencode($query);
		curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.istex.fr/document/?q='.$query.'&size=*&output=id,author.affiliations,author.name',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET"
		));
		
		$response = curl_exec($curl); 
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
		  echo " Erreur #:" . $err;
		} else {
			 $responseencoded=json_decode($response);
			$response = $responseencoded->hits; 
			$response = json_decode(json_encode($response),true);
			$tableau = array();
			$response_array= array();
			
			foreach ($response as $key => $value) {
				$tableau[]=$value;
			}
			foreach ($tableau as $key => $value) {
					$array=array();
					@$affiliations=$value['author'][0]['affiliations'][0];
					$parse = explode(",", $affiliations);
					$id=$value['id'];
					$laboratory=$parse[0];
					$country=$parse[count($parse)-1];
					$country = preg_replace('/\s+/', '', $country, 1); // remplacement du premier espace devant le nom de pays
					if ($country=="U.S.A.") { //Test de tri (provisoire)
						$country="USA";
					}
					@$author=$value['author'][0]['name'];
					
					$array['id']=$id;
					$array['country']=$country;
					$array['laboratory']=$laboratory;
					$array['author']=$author;
					$response_array[]=$array;

				
			}
			//var_dump($tableau);
			//var_dump($response_array);
			return $response_array;
		}
	}


}


?>