$(document).ready(function(){
    $(".istex-search-bar-wrapper :submit").click(function(){
       var data3 = [];
       data3.push(['ID','Y','X','Auteur','Nombre de document']);
      $('#authors tbody').remove();
      var query=document.getElementsByClassName('istex-search-input')[0].value
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getauthors",
        {
          query: query
        },
        function(data){
            var parsed = JSON.parse(data);
			var r = []
      var x = 0;
	    	for (var k in parsed) {
          if (x<20) {
          x++;
	        var occurence=(parsed[k].length);
	        if (!parsed.hasOwnProperty(k)) 
	            continue
            var res = k.split(",");
	        $( "#authors" ).append('<tr><td>'+res[0]+'</td><td>'+res[1]+'</td><td>'+occurence+'</td></tr>');     
          data3.push([" ",Math.floor((Math.random() * 200) + 1),Math.floor((Math.random() * 100) + 1),res[0],occurence]); 
          }
          else{
            break;
          }
   		 }
         google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawSeriesChart);

        var table = $('#authors').DataTable( {
          lengthChange: false,
          destroy:true,
          "pageLength": 5, "order": [[ 2, "desc" ]],
          "language": {
            "zeroRecords": "Aucun résultats",
            "info": "Page _PAGE_ sur _PAGES_",
            "infoEmpty": "Aucun résultats",        }
        } );
    function drawSeriesChart() {
      //data = []
      //data.push(data3)
      console.log(data3)
      var data = google.visualization.arrayToDataTable(data3);
      console.log(data)
      var options = {
        legend: 'none',
        tooltip:{isHtml:true},
        title: 'Auteurs pour la requete :'+query,
        'width':900,
        'height':600,
        hAxis: {display:false, baselineColor: '#fff',
         gridlineColor: '#fff',
         textPosition: 'none'},
        vAxis: {display:false , baselineColor: '#fff',
         gridlineColor: '#fff',
         textPosition: 'none'},
        bubble: {textStyle: {fontSize: 11}},
        explorer: { maxZoomOut: 5 }
      };

      var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div_authors'));
      

      chart.draw(data, options);
    }
        });
   	});
});