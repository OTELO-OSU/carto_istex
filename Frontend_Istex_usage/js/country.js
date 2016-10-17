$(document).ready(function(){
  var mymap = L.map('map').setView([51.505, -0.09], 2);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mymap);

    $(".istex-search-bar-wrapper :submit").click(function(){
    	$('#country tbody').remove();
    	var query=document.getElementsByClassName('istex-search-input')[0].value
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getcountrys",
        {
          query: query
        },
        function(data){
        	console.log(data)
            var parsed = JSON.parse(data);
			var r = []
			var x = 0;
	    	for (var k in parsed) {
	    		if (x<20) {
         			x++;
         	if (k=="") {}
         	else{
	        var occurence=(Object.keys(parsed[k]).length)-1;
	        if (!parsed.hasOwnProperty(k)) 
	            continue
	        $( "#country" ).append('<tr><td>'+k+'</td><td>'+occurence+'</td></tr>');
	        var marker = L.marker([parsed[k]["gps"]["lat"], parsed[k]["gps"]["lon"]]).addTo(mymap);
			marker.bindPopup("<b>Hello world!</b><br>I am a popup.");
      		}
	        	

          
          
   		  	}
          	else{
           	 break;
          	}
   			 }
          	/*	var table = $('#country').DataTable( {
		          lengthChange: false,
		          "pageLength": 5, "order": [[ 2, "desc" ]],
		          "language": {
		            "zeroRecords": "Aucun résultats",
		            "info": "Page _PAGE_ sur _PAGES_",
		            "infoEmpty": "Aucun résultats",        }
		        } );*/
        });
   	});
});