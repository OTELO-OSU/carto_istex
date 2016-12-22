

   
    	function searchcountry(query){
    	//var query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
        $.post("/Projet_carto_istex/Backend_Istex_usage/src/index.php/getcountrys",
        {
          query: query
        }, // requete ajax sur le backend
        function(data){

            var parsed = JSON.parse(data); // transformation en JSON
			var x = 0;
			undefinedaff=parsed['0']['noaff'];
			documentswithaffiliations=parsed['0']['total'];
			var total=Object.keys(parsed['documents']).length;

			var markers = [] // tableau qui contiendra les differents markers
	    	for (var k in parsed['documents']) { // on parcourt le JSON
			var occurence = parsed['documents'][k]['total'];
	    		if (x<20) {
         			x++;
         	if (k=="") {
         	}
	         	else{
			        if (parsed['documents'].hasOwnProperty(k)) 
					if (occurence==1) {
						var radius=1;
					}
					else{
						var radius=occurence*0.3;
					}
					if (radius>60) {
						var radius=60;
					}
					color = '#'+Math.floor(Math.random()*16777215).toString(16);
					var circle = L.circleMarker([parsed['documents'][k]["gps"]["lat"], parsed['documents'][k]["gps"]["lon"]], {
						color: color,
						fillColor: color ,
						fillOpacity: 0.5,
						radius: radius
					}); // creation d'un marker
					circle.bindPopup("Country: "+k+"<br>Number of publications: "+occurence);
					circle.on('mouseover', function (e) {
		            	this.openPopup();
			        });
			        circle.on('mouseout', function (e) {
			            this.closePopup();
			        });

					circle.bindTooltip("<b>"+occurence+"</b>",{ noHide: true ,permanent:true,direction:'center'}).openTooltip();
					markers.push(circle);// push du marker dans le tableau

	      		}

   		  	}
		   		  	$( "#country" ).append('<tr><td>'+k+'</td><td>'+occurence+'</td></tr>'); //Affichage dans le tableau
   			 
    	}
   			 	
   			 

          		var table = $('#country').DataTable( {
		          lengthChange: false,
		           destroy:true,
		          "pageLength": 5, "order": [[ 1, "desc" ]], // pagination du tableau precedemment crée
		          "pagingType": "numbers",  
		          responsive: true
		        } );

          		$('.country h5').remove();
     			var total = (undefinedaff/documentswithaffiliations)*100;
			    total = total*100;          
			    total= Math.round(total); 
			    total= total/100;  
		        $('.country').append('<h5>'+undefinedaff+' records('+total+'%) do not contain data in the field being analyzed.</h5>');
		        if (typeof(group)!=='undefined') { // si une layer existe deja on la supprime
    		mymap.removeLayer(group);
   			 }
      		group = L.featureGroup(markers); // on met le groupe de markers dans une layer
      		group.addTo(mymap); // on l'ajoute a la map
	        bounds = group.getBounds();	// on obtient le bounds pour placer la vue
			mymap.invalidateSize();  //Resize de la map hidden div
			mymap.fitBounds(bounds); // zoom sur la partie qui des poi qui nous interessent
	       	$('#actions_leaflet #download').remove();
	        $('#actions_leaflet').prepend('<div id="download" class="ui right labeled icon button print" >Download</a><i class="download icon"></i></div>');
			$('.print').on('click', function() {//print de la map
				$.print("#map",{title:"Map of publications per country for query : "+query});
			});

	        $('#legend h5').remove();
          	$('#legend').append('<h5>Map of publications per country for query : "'+query+'" </h5>');
   			
        })
   	
}