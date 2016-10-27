$(document).ready(function(){
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mymap); // declaration des tiles a utiliser sur leaflet (osm)

    $(".istex-search-bar-wrapper :submit").click(function(){//event click sur rechercher
    	if (typeof(group)!=='undefined') { // si une layer existe deja on la supprime
    		mymap.removeLayer(group);
    	}
    	$('#country tbody').remove(); // remise a zero en cas de recherche simultané
    	$('.country h5').remove();
    	var query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getcountrys",
        {
          query: query
        }, // requete ajax sur le backend
        function(data){
            var parsed = JSON.parse(data); // transformation en JSON
			var x = 0;
			undefinedaff=parsed['0']['noaff']['noaff'];
			var total=Object.keys(parsed['documents']).length;
			var documentswithaffiliations=0;
			var markers = [] // tableau qui contiendra les differents markers
	    	for (var k in parsed['documents']) { // on parcourt le JSON
	    		if (x<20) {
         			x++;
         	if (k=="") {
         	}
	         	else{
			        	var occurence=(Object.keys(parsed['documents'][k]).length)-1;
			        	documentswithaffiliations=documentswithaffiliations+occurence;
			        if (parsed['documents'].hasOwnProperty(k)) 
						radius=occurence*30000;
					if (occurence==1) {
						radius=75000;
					}
					color = '#'+Math.floor(Math.random()*16777215).toString(16);
					var circle = L.circle([parsed['documents'][k]["gps"]["lat"], parsed['documents'][k]["gps"]["lon"]], {
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
		   		  	$( "#country" ).append('<tr><td>'+k+'</td><td>'+occurence+'</td></tr>'); //Affichage dans le tableau
	      		}

   		  	}
   			 if (x==total) {
          		var table = $('#country').DataTable( {
		          lengthChange: false,
		           destroy:true,
		          "pageLength": 5, "order": [[ 1, "desc" ]], // pagination du tableau precedemment crée
		          "pagingType": "numbers",  
		          responsive: true
		        } );

     			var total = (undefinedaff/(undefinedaff+documentswithaffiliations))*100;
			    total = total*100;          
			    total= Math.round(total); 
			    total= total/100;  
		        $('.country').append('<h5>'+undefinedaff+' records('+total+'%) do not contain data in the field being analyzed.</h5>');

   			 	
   			 }

   			 }
      		group = L.featureGroup(markers); // on met le groupe de markers dans une layer
      		group.addTo(mymap); // on l'ajoute a la map
	        bounds = group.getBounds();	// on obtient le bounds pour placer la vue
			
	       	$('#actions_leaflet #download').remove();
	        $('#actions_leaflet').prepend('<div id="download" class="ui right labeled icon button print" >Download</a><i class="download icon"></i></div>');
			$('.print').on('click', function() {//print de la map
				$.print("#map");
			});

	        $('#legend h5').remove();
          	$('#legend').append('<h5>Map of publications per country for query : "'+query+'" </h5>');
   			
        });
   	});
});