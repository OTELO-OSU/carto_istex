$(document).ready(function(){
            var data3 = [];
             data3.push(["ID","r","res","occurence"]);
    $(".istex-search-bar-wrapper :submit").click(function(){
          $('#laboratorys tbody').remove();
        var query=document.getElementsByClassName('istex-search-input')[0].value
        $.post("http://localhost/Backend_Istex_usage/src/index.php/getlaboratorys",
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
          console.log(x);
          var occurence=(parsed[k].length);
          if (!parsed.hasOwnProperty(k)) 
              continue
          var res = k.split(",");
          $( "#laboratorys" ).append('<tbody><tr><td>'+res[0]+'</td><td>'+res[1]+'</td><td>'+occurence+'</td></tr>');     
          data3.push([occurence,res[0],res[1],occurence]); 
          }
          else{
            break;
          }
       }
           google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawSeriesChart);

    function drawSeriesChart() {
      data = []
      data.push(data3)
      var data = google.visualization.arrayToDataTable(data);
      console.log(data)
      var options = {
        title: 'Correlation between life expectancy, fertility rate ' +
               'and population of some world countries (2010)',
        hAxis: {title: 'Life Expectancy'},
        vAxis: {title: 'Fertility Rate'},
        bubble: {textStyle: {fontSize: 11}}
      };

      var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div'));
      chart.draw(data, options);
    }
        });
   	});

});

//LUNDI voir pour google chart array data3 pourquoi string erreur