$(document).ready(function(){
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mymap); // declaration des tiles a utiliser sur leaflet (osm)

    $(".istex-search-bar-wrapper :submit").click(function(){//event click sur rechercher
    	$('#country tbody').remove(); // remise a zero en cas de recherche simultané
    	var query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getcountrys",
        {
          query: query
        }, // requete ajax sur le backend
        function(data){
        	console.log(data)
            var parsed = JSON.parse(data); // transformation en JSON
			var r = []
			var x = 0;
	    	for (var k in parsed) { // on parcourt le JSON
	    		if (x<20) {
         			x++;
         	if (k=="") {}
         	else{
	        var occurence=(Object.keys(parsed[k]).length)-1;
	        if (!parsed.hasOwnProperty(k)) 
	            continue
	        $( "#country" ).append('<tr><td>'+k+'</td><td>'+occurence+'</td></tr>'); //Affichage dans le tableau
	        var marker = L.marker([parsed[k]["gps"]["lat"], parsed[k]["gps"]["lon"]]).addTo(mymap); // ajout de marker sur la map leaflet
			marker.bindPopup("<b>Pays</b>:"+k+"<br>Nombre de documents: "+occurence);
      		}
	        	

          
          
   		  	}
          	else{
           	 break;
          	}
   			 }
          		var table = $('#country').DataTable( {
		          lengthChange: false,
		          "pageLength": 5, "order": [[ 2, "desc" ]],
		          "language": {
		            "zeroRecords": "Aucun résultats",
		            "info": "Page _PAGE_ sur _PAGES_",
		            "infoEmpty": "Aucun résultats",        } // pagination du tableau precedemment crée
		        } );
        });
   	});
});