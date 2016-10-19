$(document).ready(function(){
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mymap); // declaration des tiles a utiliser sur leaflet (osm)
 function strReplaceAll(string, Find, Replace) {
    try {
        return string.replace( new RegExp(Find, "gi"), Replace );       
    } catch(ex) {
        return string;
    }
}
    $(".istex-search-bar-wrapper :submit").click(function(){//event click sur rechercher
    	if (typeof(group)!=='undefined') { // si une layer existe deja on la supprime
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
			var markers = [] // tableau qui contiendra les differents markers
	    	for (var k in parsed) { // on parcourt le JSON
	    		if (x<20) {
         			x++;
         	if (k=="") {}
         	else{
	        var occurence=(Object.keys(parsed[k]).length)-1;
	        if (!parsed.hasOwnProperty(k)) 
	            continue
			radius=occurence*25000;
			
			var circle = L.circle([parsed[k]["gps"]["lat"], parsed[k]["gps"]["lon"]], {
			color: 'blue',
			fillColor: '#2590ff',
			fillOpacity: 0.5,
			radius: radius
			}).bindPopup("Country: "+k+"<br>Number of publications: "+occurence); // creation d'un marker
			markers.push(circle);// push du marker dans le tableau
      		}

   		  	}
   		  	$( "#country" ).append('<tr><td>'+k+'</td><td>'+occurence+'</td></tr>'); //Affichage dans le tableau
   			 if (x==total) {
          		var table = $('#country').DataTable( {
		          lengthChange: false,
		           destroy:true,
		          "pageLength": 5, "order": [[ 1, "desc" ]], // pagination du tableau precedemment crée
		          "pagingType": "numbers"
		        } );
   			 	
   			 }

   			 }
      		group = L.featureGroup(markers); // on met le groupe de markers dans une layer
      		group.addTo(mymap); // on l'ajoute a la map
	        bounds = group.getBounds();	// on obtient le bounds pour placer la vue
	        var url;
			leafletImage(mymap, function(err, canvas) {
		    var img = document.createElement('img');
		    var dimensions = mymap.getSize();
		    img.width = dimensions.x;
		    img.height = dimensions.y;
		    url = canvas.toDataURL()
		    console.log(url)
			})
	        $('.command_map a').remove();
	        console.log(url)
	        //Telechargement a verifier car banni nominatim et verif affichage tout pays dans le tableau
          	$('.command_map').append('<a href=" '+ url +'" download=country_map_'+strReplaceAll(query," ","_")+'.png><i class="download icon"><i></a>');
	        $('#legend h5').remove();
          	$('#legend').append('<h5>Map of publications per country for query : "'+query+'" </h5>');
   			
        });
   	});
});