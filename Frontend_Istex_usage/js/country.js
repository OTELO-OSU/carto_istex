$(document).ready(function(){
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mymap); // declaration des tiles a utiliser sur leaflet (osm)

    $(".istex-search-bar-wrapper :submit").click(function(){//event click sur rechercher
    	if (typeof(group)!=='undefined') {
    		mymap.removeLayer(group);
    	}
    	$('#country tbody').remove(); // remise a zero en cas de recherche simultané
    	var query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getcountrys",
        {
          query: query
        }, // requete ajax sur le backend
        function(data){
        	//console.log(data)
            var parsed = JSON.parse(data); // transformation en JSON
			var x = 0;
			var total=Object.keys(parsed).length;
			var markers = []
	    	for (var k in parsed) { // on parcourt le JSON
	    		if (x<20) {
         			x++;
         	if (k=="") {}
         	else{
	        var occurence=(Object.keys(parsed[k]).length)-1;
	        if (!parsed.hasOwnProperty(k)) 
	            continue
	        $( "#country" ).append('<tr><td>'+k+'</td><td>'+occurence+'</td></tr>'); //Affichage dans le tableau
			radius=occurence*25000;
			
			var circle = L.circle([parsed[k]["gps"]["lat"], parsed[k]["gps"]["lon"]], {
			color: 'blue',
			fillColor: '#2590ff',
			fillOpacity: 0.5,
			radius: radius
			}).bindPopup("Country: "+k+"<br>Number of publications: "+occurence);
			markers.push(circle);
      		}

   			 if (x==total) {
          		var table = $('#country').DataTable( {
		          lengthChange: false,
		           destroy:true,
		          "pageLength": 5, "order": [[ 1, "desc" ]], // pagination du tableau precedemment crée
		        } );
   			 	
   			 }
   		  	}
          	else{
           	 break;
          	}
   			 }
      		group = L.featureGroup(markers);
      		group.addTo(mymap);
	        bounds = group.getBounds();	
	        $('#legend h5').remove();
          	$('#legend').append('<h5>Map of publications per country for query : "'+query+'" </h5>');
   			
        });
   	});
});