  var query;
  var data4;
   function strReplaceAll(string, Find, Replace) { // fonction de remplacement des espace en underscore
      try {
          return string.replace( new RegExp(Find, "gi"), Replace );       
      } catch(ex) {
          return string;
      }
    }

 function drawSeriesChartauthor() {// fonction qui va créé les bubbles
      var data = google.visualization.arrayToDataTable(data4);
      var options = {
            legend: 'none',
            tooltip:{isHtml:true},
            title: 'BubbleChart of publications per author for query : '+query,
            width:'100%',
            height:550,
            hAxis: {display:false,
              viewWindowMode:'explicit',
              viewWindow
             :{max:1220},
              baselineColor: '#fff',
              gridlineColor: '#fff',
              textPosition: 'none'
            },
            vAxis: {display:false ,viewWindowMode:'explicit',
              viewWindow
             :{max:1220},
              baselineColor: '#fff',
              gridlineColor: '#fff',
              textPosition: 'none'
           },
            bubble: {textStyle: {fontSize: 10, 
            color: 'black',
            bold: true,
          }},
             explorer: {
        maxZoomOut:3,
        keepInBounds: false
    }
            
          };

      var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div_authors'));
      chart.draw(data, options);
      $('.command_authors a').remove();
      $('#actions_authors #download').remove();
      $('.command_authors').append('<a href=" '+chart.getImageURI() +'" download=authors_'+strReplaceAll(query," ","_")+'.png><i class="download icon"><i></a>');
      $('#actions_authors').prepend('<div id="download" class="ui right labeled icon button" > <a href="'+chart.getImageURI() +'" download=authors_'+strReplaceAll(query," ","_")+'.png>Download</a><i class="download icon"></i></div>');

    }

    function parse_authors(parsed){
     
        data4 = [];
        data4.push(['ID','Y','X','Author','Number of publications']);
        var r = []
        var x = 0;

        for (var k in parsed) { // on parcourt le JSON
          
           if (x<5) { // les cinq premiers resultat avec affichage du label dans bubble chart
              x++;
              var occurence = parsed[k][3];
              data4.push([parsed[k][0]+" ("+occurence+")",Math.floor((Math.random() * 1000) + 50)-Math.floor((Math.random() * 150) + 50),Math.floor((Math.random() * 800) + 50)-Math.floor((Math.random() * 150) +50),parsed[k][0],occurence]); // on push les données dans un array
            }
          else if (x<20) { // les 20 premiers affichers dans le bubble chart
             x++;
            var occurence = parsed[k][3];
            data4.push([" ",Math.floor((Math.random() * 1000) + 50)-Math.floor((Math.random() * 150) + 50),Math.floor((Math.random() * 800) + 50)-Math.floor((Math.random() * 150) +50),parsed[k][0],occurence]); 
          }
          }
      $('.authors h5').remove();
      var total = (undefinedaff/(documentswithaffiliations))*100;
      total = total*100;          
      total= Math.round(total); 
      total= total/100;  
      $('.authors').append('<h5>'+undefinedaff+' records('+total+'%) do not contain data in the field being analyzed.</h5>');
      $('.loading_authors').hide();
      google.charts.load('current', {'packages':['corechart']}); // on charge les packages de google chart
      google.charts.setOnLoadCallback(drawSeriesChartauthor);
      var table = $('#authors').DataTable( {
          "data": parsed,
          lengthChange: false,
          destroy:true,
          "pageLength": 5, "order": [[ 3, "desc" ]],
          "pagingType": "numbers",
          responsive: true,
          "deferRender": true
        } );// pagination du tableau precedemment crée
      $('#authors tbody').on('click', 'tr', function () {
         $('.authors_table .header').empty();
        $( "#authors_row tbody" ).remove()
          var data = table.row(this).data();
          author=data[0].replace(/ /g,"_");   
          for (row in data[4]) {    
            $( "#authors_row" ).append('<tr><td>'+data[4][row]['title']+'</td><td>'+ data[4][row]['id']+'</td></tr>'); //Affichage dans le tableau    
          }
          $('.authors_table .header').append("Publications of "+data[0])

          
          var table_row = $('#authors_row').DataTable( {
                lengthChange: false,
                destroy:true,
                "pageLength": 5, "order": [[ 1, "desc" ]],
                "pagingType": "numbers",
                responsive: true,
                 dom: 'frtip',
              } );// pagination du tableau precedemment crée
          var buttons = new $.fn.dataTable.Buttons(table_row, {
             buttons: [{extend:'csvHtml5',text:'Export CSV',title: author,className:'ui primary button'}]
        }).container().appendTo($('#buttons_authors'));
          $('.authors_table').modal('show');
           $('#authors_row tbody').on('click', 'tr', function () {
          var row = table_row.row(this).data();
           window.open(URL_ISTEX+row[1]+"/fulltext/pdf");
         });
   
    });
  } 


  function reload_bubble_author(parsed){
        data4 = [];
        data4.push(['ID','Y','X','Author','Number of publications']);
        var r = []
        var x = 0;
        for (var k in parsed) { // on parcourt le JSON
           if (x<5) { // les cinq premiers resultat avec affichage du label dans bubble chart
                  x++;
                  var occurence = parsed[k][3];
                  data4.push([parsed[k][0]+" ("+occurence+")",Math.floor((Math.random() * 1000) + 50)-Math.floor((Math.random() * 150) + 50),Math.floor((Math.random() * 800) + 50)-Math.floor((Math.random() * 150) +50),parsed[k][0],occurence]); // on push les données dans un array
            }
          else if (x<20) { // les 20 premiers affichers dans le bubble chart
                x++;
                var occurence = parsed[k][3];
                data4.push([" ",Math.floor((Math.random() * 1000) + 50)-Math.floor((Math.random() * 150) + 50),Math.floor((Math.random() * 800) + 50)-Math.floor((Math.random() * 150) +50),parsed[k][0],occurence]); 
          }
        }
        google.charts.load('current', {'packages':['corechart']}); // on charge les packages de google chart
        google.charts.setOnLoadCallback(drawSeriesChartauthor);
                   

      }

  function searchauthors(query){
       
       // query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
        $.post("/Projet_carto_istex/Backend_Istex_usage/src/index.php/getauthors",
        {
          query: query
        }, // requete ajax sur le backend
        function(data){
            var parsed = JSON.parse(data); // transformation en array
             parseauthor=parsed['documents'];
            undefinedaff=parsed['0']['noaff']['noaff'];
            documentswithaffiliations=parsed['0']['noaff']['total'];
            parse_authors(parseauthor);
            searchcountry(query);
        });

          
    $(".reloadauthor").click(function(){
      reload_bubble_author(parseauthor); // on recreer le bubble chart
       });
    };
