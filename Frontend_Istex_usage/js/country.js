
 		/**
         * methode de requete vers le backend
         *
         * @param query
         *          nom de la recherche utilisateur
         * @param improve
         * 			amelioration de la recherche
         */
   
    	function searchcountry(query,improve){
        $.post("/Backend_Istex_usage/src/index.php/getcountrys",
        {
          query: query,
          improve:improve
        }, // requete ajax sur le backend
        function(data){
        	$('.leafletmap').attr('style', 'display: inline-block !important;')
        	$('#improve').attr('style', 'display: inline-block !important;')
            var parsed = JSON.parse(data); // transformation en JSON
			var x = 0;
			undefinedaff=parsed['0']['noaff'];
			empty=parsed['0']['empty'];
			documentswithaffiliations=parsed['0']['total'];
			var total=Object.keys(parsed['documents']).length;

			var markers = [] // tableau qui contiendra les differents markers
			var array=[]
			for (var k in parsed['documents']) { // on parcourt le JSON
				var occurence = parsed['documents'][k]['total'];
				if (x<20) {
	    			array.push(occurence)
					x++;
				}
			}
			//Definiton des randoms radius
    		max=Math.max.apply(null,array)
    		base=7
    		coef=0.3
    		div=1
    		if (max>100&&max<400) {
    			coef=0.8
    			div=4
    		}
    		if (max>400&&max<1000) {
    			coef=0.6
    			div=4
    		}
    		if (max>1000) {
    			coef=0.4
    			div=8
    		}
    		if (max<20) {
    			base=8
    			coef=10
    			div=3
    		}
    		
    		x=0
	    	for (var k in parsed['documents']) { // on parcourt le JSON
			var occurence = parsed['documents'][k]['total'];
	    		if (x<20) {
         			x++;
         	if (k=="") {
         	}
	         	else{
			        if (parsed['documents'].hasOwnProperty(k)) 
					if (occurence==1) {
						var radius=base;
					}
					else{
						var radius=occurence*coef;
						radius=radius/div;
					}
					if (radius>60) {
						var radius=occurence*coef;
						radius=radius/div;
					}
					if (radius<10) {
						radius=10;
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
   		  			country_code=parsed['documents'][k]["gps"]["country_code"];
		   		  	$( "#country" ).append('<tr><td><i class="'+country_code+' flag"></i>'+k+'</td><td>'+occurence+'</td></tr>'); //Affichage dans le tableau
   			 
    	}
   			 	
   			 

          		var table = $('#country').DataTable( {
		          lengthChange: false,
		           destroy:true,
		          "pageLength": 5, "order": [[ 1, "desc" ]], // pagination du tableau precedemment crée
		          "pagingType": "numbers",  
		          responsive: true,
		          dom: 'frtip',
		          "autoWidth": false
		        } );

		        var buttons = new $.fn.dataTable.Buttons(table, {
    		     buttons: [{extend:'csvHtml5',text:'Export CSV',title: name+"_country",className:'ui primary button'}]
				}).container().appendTo($('#buttons_country'));


          		$('.country h5').remove();
     			var total = (undefinedaff/documentswithaffiliations)*100;
			    total = total*100;          
			    total= Math.round(total); 
			    total= total/100;  
		        $('.country').append('<h5>'+undefinedaff+' records('+total+'%) do not contain data in the field being analyzed.</h5>');
		        $('.country').append('<h5> including '+empty+' records that do not have affiliations.</h5>');
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
				$('#improve').show();
		        $('#legend h5').remove();
	          	$('#legend').append('<h5>Map of publications per country for query : "'+query+'" </h5>');
	   			$('.loading_country').hide();

        })
   	
}