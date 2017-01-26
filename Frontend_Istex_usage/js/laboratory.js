  var data3;
  var query;
  var URL_ISTEX="https://api.istex.fr/document/";
  function strReplaceAll(string, Find, Replace) { // fonction de remplacement des espace en underscore
      try {
          return string.replace( new RegExp(Find, "gi"), Replace );       
      } catch(ex) {
          return string;
      }
  }
 

  function drawSeriesChartlabo() { // fonction qui va créé les bubbles chart
    var data = google.visualization.arrayToDataTable(data3);
    var options = {
      legend: 'none',
      tooltip:{isHtml:true},
      title: 'BubbleChart of publications per laboratory for query : '+query,
      width:900,
      height:550,
      chartArea:{left:0,top:20,width:"100%",height:"90%"},
      hAxis: {display:false,
        viewWindowMode:'explicit',
        viewWindow
       :{max:1220},
       baselineColor: '#fff',
        gridlineColor: '#fff',
        textPosition: 'none'},
      vAxis: {display:false ,viewWindowMode:'explicit',
        viewWindow
       :{max:1220},
       baselineColor: '#fff',
   gridlineColor: '#fff',
   textPosition: 'none'},
      bubble: {textStyle: {fontSize: 11,
      color: 'black',
      bold: true,
      }},
       explorer: {
        maxZoomOut:5,
        keepInBounds: false
    }
      
    };

    var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div'));

    chart.draw(data, options);
    $('#actions_laboratorys #download').remove();
    $('#actions_laboratorys').prepend('<div id="download" class="ui right labeled icon button" > <a href="'+chart.getImageURI() +'" download=laboratory_'+strReplaceAll(query," ","_")+'.png>Download</a><i class="download icon"></i></div>');
    $('#series_chart_div').mouseout(function() {
    $('#actions_laboratorys #download').remove();
    $('#actions_laboratorys').prepend('<div id="download" class="ui right labeled icon button" > <a href="'+chart.getImageURI() +'" download=laboratory_'+strReplaceAll(query," ","_")+'.png>Download</a><i class="download icon"></i></div>');
});
                 
  }
        /**
         * methode de traitement des laboratoires
         *
         * @param parsed
         *          array
         */
    function parse_laboratorys(parsed){
        data3 = [];
        data3.push(['ID','Y','X','Laboratory','Number of publications']);
        var r = [];
        var x = 0;
        for (var k in parsed) { // on parcourt le JSON
          if (x<5) { // les cinq premiers resultat avec affichage du label dans bubble chart
            x++;
            var occurence = parsed[k][2];
            data3.push([parsed[k][0]+" ("+occurence+")"+parsed[k][1],Math.floor((Math.random() * 1000) + 100)-Math.floor((Math.random() * 150) + 50),Math.floor((Math.random() * 800) + 50)-Math.floor((Math.random() * 150) + 50),parsed[k][0]+", "+parsed[k][1],occurence]); // on push les données dans un array
            }
          else if (x<20) { // les 20 premiers affichers dans le bubble chart
            x++;
            var occurence = parsed[k][2];
            data3.push([" ",Math.floor((Math.random() * 1000) + 50)-Math.floor((Math.random() * 150) + 50),Math.floor((Math.random() * 800) + 50)-Math.floor((Math.random() * 150) +50),parsed[k][0]+", "+parsed[k][1],occurence]); // on push les données dans un array
          }            
       }
      $('.laboratory h5').remove();
      var total = (undefinedaff/(documentswithaffiliations))*100;
      total = total*100;          
      total= Math.round(total); 
      total= total/100;  

      $('.laboratory').append('<h5>'+undefinedaff+' records('+total+'%) do not contain data in the field being analyzed.</h5>');
      $('.laboratory').append('<h5> including '+empty+' records that do not have affiliations.</h5>');
      $('.loading_laboratory').hide();

      google.charts.load('current', {'packages':['corechart']}); // on charge les packages de google chart
      google.charts.setOnLoadCallback(drawSeriesChartlabo);
        var table = $('#laboratorys').DataTable( {
          "data": parsed,
          lengthChange: false,
          destroy:true,
          "pageLength": 5, "order": [[ 2, "desc" ]],
          "pagingType": "numbers",
          responsive: true,
          "deferRender": true,
          "autoWidth": false
        } );// pagination du tableau precedemment crée

        var buttons = new $.fn.dataTable.Buttons(table, {
             buttons: [{extend:'csvHtml5',text:'Export CSV',title: name+"_laboratorys",className:'ui primary button'}]
        }).container().appendTo($('#buttons_laboratorys_master'));
       $('#laboratorys tbody').on('click', 'tr', function () {
        $('.laboratory_table .header').empty();
        $( "#laboratorys_row tbody" ).remove();
          var data = table.row(this).data();
          laboratory=data[0].replace(/ /g,"_");   
          for (row in data[3]) {    
            $( "#laboratorys_row" ).append('<tr><td>'+data[3][row]['title']+'</td><td>'+ data[3][row]['id']+'</td></tr>'); //Affichage dans le tableau    
          }
          $('.laboratory_table .header').append("Publications of "+data[0])

          
          var table_row = $('#laboratorys_row').DataTable( {
                lengthChange: false,
                destroy:true,
                "pageLength": 3, "order": [[ 1, "desc" ]],
                "pagingType": "numbers",
                responsive: true,
                 dom: 'frtip',
              } );// pagination du tableau precedemment crée
          $('.laboratory_table').modal('show');
           var buttons = new $.fn.dataTable.Buttons(table_row, {
             buttons: [{extend:'csvHtml5',text:'Export CSV',title: name+"_"+laboratory,className:'ui primary button'}]
            }).container().appendTo($('#actions_infolabo'));
           $('#actions_infolabo .dt-buttons').append('<div class="ui negative right labeled icon button">Fermer<i class="remove icon"></i> </div>')
          $('#laboratorys_row tbody').on('click', 'tr', function () {
          var row = table_row.row(this).data();
           window.open(URL_ISTEX+row[1]+"/fulltext/pdf");
         });
    });
};


function reload_bubble_labo(parsed){
        data3 = [];
        data3.push(['ID','Y','X','Laboratory','Number of publications']);
        var r = [];
        var x = 0;
        for (var k in parsed) { // on parcourt le JSON
           if (x<5) { // les cinq premiers resultat avec affichage du label dans bubble chart
            x++;
            var occurence = parsed[k][2];
            data3.push([parsed[k][0]+" ("+occurence+")"+parsed[k][1],Math.floor((Math.random() * 1000) + 100)-Math.floor((Math.random() * 150) + 50),Math.floor((Math.random() * 800) + 50)-Math.floor((Math.random() * 150) + 50),parsed[k][0]+", "+parsed[k][1],occurence]); // on push les données dans un array
            }
          else if (x<20) { // les 20 premiers affichers dans le bubble chart
            x++;
            var occurence = parsed[k][2];
            data3.push([" ",Math.floor((Math.random() * 1000) + 50)-Math.floor((Math.random() * 150) + 50),Math.floor((Math.random() * 800) + 50)-Math.floor((Math.random() * 150) +50),parsed[k][0]+", "+parsed[k][1],occurence]); // on push les données dans un array
          } 
        }
        google.charts.load('current', {'packages':['corechart']}); // on charge les packages de google chart
      google.charts.setOnLoadCallback(drawSeriesChartlabo);

      }

        /**
         * methode d'initialisation et requete vers le backend
         *
         * @param query
         *          nom de la recherche utilisateur
         */
function init_request(query){
        // remise a zero en cas de recherche simultané
        documentswithaffiliations=null;
        $('#warning').hide();     
        $('.loading_country').show();
        $('.loading_laboratory').show();
        $('.loading_authors').show();
        $('.avert').remove();
        $('.laboratory h5').remove();
        $('#laboratorys tbody').remove();
        $('.authors h5').remove();
        $('.country h5').remove();
        $('#authors tbody').remove();
        $('#country tbody').remove(); 
        $('#country .row').remove();
        $('#authors .row').remove();
        $('#laboratorys_info').remove();
        $('#laboratorys_paginate').remove();  
        $('#laboratorys_filter ').remove(); 
        $('#country_info').remove();
        $('#country_paginate').remove();  
        $('#country_filter ').remove();   
        $('#authors_info').remove();
        $('#authors_paginate').remove();  
        $('#authors_filter ').remove();  
        $('#improve').attr('style', 'display: none !important;'); 
        $('.leafletmap').attr('style', 'display: none !important;'); 
        $('.bubblelaboratorys').attr('style', 'display: none !important;');  
        $('.bubbleauthors').attr('style', 'display: none !important;'); 
        $('.dt-buttons').remove();  
        $('#improve').html('<i class="icon settings"></i> Improve');
        $('#improve').removeClass('green');




          $.post("/Backend_Istex_usage/src/index.php/getlaboratorys",
          {
            query: query
          },// requete ajax sur le backend
          function(data){ 

            $('.bubblelaboratorys').attr('style', 'display: inline-block !important;')
            if (data=='["empty"]') {
                $('#noresult').show(); 
                $('.loading_country').hide();
                $('.loading_laboratory').hide();
                $('.loading_authors').hide();    
            }
            else{
              searchauthors(query);
              var parsed = JSON.parse(data); // transformation du JSON en array JS
              parselabo=parsed['documents'];
              undefinedaff=parsed['0']['noaff']['noaff'];
              empty=parsed['0']['noaff']['empty'];
              documentswithaffiliations=parsed['0']['noaff']['total'];
              console.log(documentswithaffiliations);
              if (documentswithaffiliations==15000) {
                $('#warning').show();     
              }
              parse_laboratorys(parselabo);
              }
            });
            $(".reloadlaboratory").click(function(){
              reload_bubble_labo(parselabo);// on recreer le bubble chart
            });
          
}


        /**
         * methode qui recupere les modifications sur le widget facets d'istex
         *
         * 
         */

function build_request_facets(slider){
  setTimeout(function(){
                  var query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
                  labelscorpus=$(".corpus input:checked");
                   var corpus;
                   var genre;
                   var language;
                   var wos;
                    $.each(labelscorpus,function(index,value){
                        val=value.nextSibling.nodeValue;
                        var value = val.replace(/[0-9]/g, '');
                        if (corpus===undefined) {
                          corpus=value;

                        }
                        else{
                          corpus=corpus+" OR "+value;
                        }
                    })
                    if (corpus===undefined) {
                        corpus="";
                    }
                    else{
                        corpus=" AND corpusName:("+corpus+")";
                    }
                      
                     
                labelsgenre=$(".genre input:checked")
                 $.each(labelsgenre,function(index,value){
                    value=value.closest("li").title;
                    if (genre===undefined) {
                      genre=value;
                      }
                      else{
                        genre=genre+" OR "+value;
                      }
                    
                  })
                    if (genre===undefined) {
                        genre="";
                    }
                    else{
                        genre=" AND genre:("+genre+")";
                    }
   
                labelslanguage=$(".language input:checked")
                  $.each(labelslanguage,function(index,value){
                        val=value.nextSibling.nodeValue;
                      var value = val.replace(/[0-9]/g, '');
                      var value=value.replace(/\s+/g, '');
                       switch(value){
                            case 'Français':
                              value = 'fre';
                              break;
                            case 'Anglais' : 
                              value = 'eng';
                              break
                            case 'Latin' : 
                              value = 'lat';
                              break;
                            case 'Allemand' : 
                              value = 'ger' ;
                              break;
                            case  'Espagnol':
                               value = 'spa';
                               break;
                            case 'Néerlandais' : 
                              value = 'dut' ;
                              break;  
                            case 'Italien' : 
                              value = 'ita' ;
                              break;  
                            case 'Portugais' : 
                              value = 'por' ;
                              break;  
                            case 'Russe' : 
                              value = 'rus' ;
                              break;  
                            case  'Gallois' : 
                              value = 'wel' ;
                              break;  
                            case 'Galicien' : 
                              value = 'glg' ;
                              break;   
                            case 'Grec' : 
                              value = 'gre';
                              break;  
                            case 'Arabe' : 
                              value = 'ara';
                              break;  
                            case 'Hébreu' : 
                              value = 'heb' ;
                              break;  
                            case 'Polonais' : 
                              value = 'pol' ;
                              break;  
                            case 'Danois' : 
                              value = 'dan' ;
                              break;  
                            case 'Suédois' : 
                              value = 'swe' ;
                              break;  
                            case  'Mohawk': 
                              value = 'moh';
                              break;  
                            case 'Syriaque' : 
                              value = 'syr' ;
                              break;  
                            case  'Persan': 
                              value =  'per';
                              break;  
                            case  'Français moyen': 
                              value = 'frm' ;
                              break;  
                            case 'Multilingue' : 
                              value = 'mul' ;
                              break;  
                            case  'Non spécifié' : 
                              value = 'unknown' ;
                            break;
                            default : 
                               value =  language;
                            break;
                          }
                    if (language===undefined) {
                        language=value;
                      }
                      else{
                        language=language+" OR "+value;
                      }
                    })
                    
                    if (language===undefined) {
                        language="";
                    }
                    else{
                      language=" AND language:("+language+")";
                    }

                labelswos=$(".wos input:checked")
                 $.each(labelswos,function(index,value){
                        value=value.closest("li").title;
                        value='"'+value+'"';
                    if (wos===undefined) {
                        wos=value;
                    }
                    else{
                        wos=wos+" OR "+value;
                    }
                   
                  })
                    if (wos===undefined) {
                        wos="";
                    }
                    else{
                        wos=" AND wos:("+wos+")";
                    }
                  if (slider=="slider"){
                
                var publicationdate=" AND publicationDate:["+$(".istex-facet-pubdate  rzslider .rz-bubble")[2].innerText+" TO "+$(".istex-facet-pubdate  rzslider .rz-bubble")[3].innerText+"]";
                var copyrightdate=" AND copyrightDate:["+$(".istex-facet-copyrightdate  rzslider .rz-bubble")[2].innerText+" TO "+$(".istex-facet-copyrightdate  rzslider .rz-bubble")[3].innerText+"]";
                var quality=" AND score:["+$(".istex-facet-quality  rzslider .rz-bubble")[2].innerText+" TO "+$(".istex-facet-quality  rzslider .rz-bubble")[3].innerText+"]";
                allquery=query+corpus+publicationdate+copyrightdate+language+wos+genre+quality;
              
                  }
                  else{
                    var publicationdate=""
                    var copyrightdate=""
                    var quality=""
                    allquery=query+corpus+publicationdate+copyrightdate+language+wos+genre+quality;
                  }
                init_request(allquery);
             },1000);  
   
}


$(document).ready(function(){
     $('#improve').hide();
     $(".istex_result").hide();
     $( ".istex-search-bar-wrapper" ).addClass( "ui fluid icon input" );
     $('.istex-search-bar-wrapper input').last().attr('id', 'searchbutton');
     $(".istex-search-bar-wrapper :submit").click(function(){//event click sur rechercher
        $('#noresult').hide(); 
        document.getElementById("istex-widget-search").style.marginTop="2%";
        document.getElementById("istex-widget-search").style.marginBottom="1px";
        $(".istex_result").show();     
        query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
        if (query=="") {
          query="*";
        }
        name=query.replace(/ /g,"_");
        init_request(query);
        setTimeout(function(){
           $('.rzslider .rz-pointer').off("click")
          $('.rzslider .rz-pointer').on("click",function(){
               build_request_facets("slider");    

            }); 

        },1000)
        $('#improve').off("click");
        $('#improve').on('click', function() {
        $('.loading_country').show();
        $('.country h5').remove();
        $('#country tbody').remove(); // remise a zero en cas de recherche simultané
        $('#country .row').remove();
        $('#country_info').remove();
        $('#improve').addClass('green')
        $('#improve').html('<i class="checkmark box icon"></i>Improved');
       
        searchcountry(query,'improved');


      });

      })

     $("form.istex-facets").off("change");
     $("form.istex-facets").on("change",function(){
      build_request_facets("checkbox");
     
    });



});

