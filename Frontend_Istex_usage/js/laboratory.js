$(document).ready(function(){
    var data2;
    $(".istex-search-bar-wrapper :submit").click(function(){ //event click sur rechercher
          $('.avert').remove();
          $('#laboratorys tbody').remove();// remise a zero en cas de recherche simultané
          var query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
          $.post("http://localhost/Backend_Istex_usage/src/index.php/getlaboratorys",
          {
            query: query
          },// requete ajax sur le backend
          function(data){
            var parsed = JSON.parse(data); // transformation en JSON
            data2=parsed;
            parse_laboratorys(data2);
          });


        function parse_laboratorys(parsed){
            var data3 = [];
            data3.push(['ID','Y','X','Laboratoire','Nombre de document']);
            var r = []
            var x = 0;
            for (var k in parsed) { // on parcourt le JSON
              if (x<5) {
                 x++;
              var occurence=(parsed[k].length);
              if (!parsed.hasOwnProperty(k)) 
                    continue
                var res = k.split(",");
                $( "#laboratorys" ).append('<tr><td>'+res[0]+'</td><td>'+res[1]+'</td><td>'+occurence+'</td></tr>');  //Affichage dans le tableau   
                data3.push([res[0]+" ("+occurence+")",Math.floor((Math.random() * 200) + 1),Math.floor((Math.random() * 100) + 1),res[0],occurence]); // on push les données dans un array
              }
              else if (x<20) {
                x++;
                var occurence=(parsed[k].length);
                if (!parsed.hasOwnProperty(k)) 
                    continue
                var res = k.split(",");
                $( "#laboratorys" ).append('<tr><td>'+res[0]+'</td><td>'+res[1]+'</td><td>'+occurence+'</td></tr>');  //Affichage dans le tableau   
                data3.push([" ",Math.floor((Math.random() * 200) + 1),Math.floor((Math.random() * 100) + 1),res[0],occurence]); // on push les données dans un array
              }
              else{
                break;
              }
           }

          google.charts.load('current', {'packages':['corechart']}); // on charge les packages de google chart
          google.charts.setOnLoadCallback(drawSeriesChart);
            var table = $('#laboratorys').DataTable( {
              lengthChange: false,
              destroy:true,
              "pageLength": 5, "order": [[ 2, "desc" ]],
              "language": {
                "zeroRecords": "Aucun résultats",
                "info": "Page _PAGE_ sur _PAGES_",
                "infoEmpty": "Aucun résultats",        }
            } );// pagination du tableau precedemment crée

        function drawSeriesChart() { // fonction qui va créé les bubbles
          var data = google.visualization.arrayToDataTable(data3);
          var options = {
            legend: 'none',
            tooltip:{isHtml:true},
            title: 'Laboratoires pour la requete :'+query,
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

          var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div'));
          chart.draw(data, options);
        }

    };
    $(".reloadlaboratory").click(function(){
      parse_laboratorys(data2);
    });
    });
});

