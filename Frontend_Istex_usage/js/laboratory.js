  var data3;
  var query;
  function strReplaceAll(string, Find, Replace) { // fonction de remplacement des espace en underscore
      try {
          return string.replace( new RegExp(Find, "gi"), Replace );       
      } catch(ex) {
          return string;
      }
  }

  function drawSeriesChartlabo() { // fonction qui va créé les bubbles
    var data = google.visualization.arrayToDataTable(data3);
    var options = {
      legend: 'none',
      tooltip:{isHtml:true},
      title: 'BubbleChart of publications per laboratory for query : '+query,
      width:900,
      height:550,
      hAxis: {display:false,
        viewWindowMode:'explicit',
        viewWindow
       :{max:520},
       baselineColor: '#fff',
        gridlineColor: '#fff',
        textPosition: 'none'},
      vAxis: {display:false ,viewWindowMode:'explicit',
        viewWindow
       :{max:420},
       baselineColor: '#fff',
   gridlineColor: '#fff',
   textPosition: 'none'},
      bubble: {textStyle: {fontSize: 11}},
       explorer: {
        maxZoomOut:5,
        keepInBounds: false
    }
      
    };

    var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div'));

    chart.draw(data, options);
    $('.command_laboratorys a').remove();
    $('#actions_laboratorys #download').remove();
    $('.command_laboratorys').append('<a href=" '+chart.getImageURI() +'" download=laboratory_'+strReplaceAll(query," ","_")+'.png><i class="download icon"><i></a>');
    $('#actions_laboratorys').prepend('<div id="download" class="ui right labeled icon button" > <a href="'+chart.getImageURI() +'" download=laboratory_'+strReplaceAll(query," ","_")+'.png>Download</a><i class="download icon"></i></div>');

                 
  }

    function parse_laboratorys(parsed){
        data3 = [];
        data3.push(['ID','Y','X','Laboratory','Number of publications']);
        var r = []
        var x = 0;
        for (var k in parsed) { // on parcourt le JSON
          if (x<5) { // les cinq premiers resultat avec affichage du label dans bubble chart
            x++;
            var occurence = parsed[k][2];
            data3.push([parsed[k][0]+" ("+occurence+")"+parsed[k][1],Math.floor((Math.random() * 380) + 30),Math.floor((Math.random() * 290) + 30),parsed[k][0]+", "+parsed[k][1],occurence]); // on push les données dans un array
            }
          else if (x<20) { // les 20 premiers affichers dans le bubble chart
            x++;
            var occurence = parsed[k][2];
            data3.push([" ",Math.floor((Math.random() * 380) + 30),Math.floor((Math.random() * 290) + 30),parsed[k][0]+", "+parsed[k][1],occurence]); // on push les données dans un array
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
          "deferRender": true
        } );// pagination du tableau precedemment crée



};
function reload_bubble_labo(parsed){
        data3 = [];
        data3.push(['ID','Y','X','Laboratory','Number of publications']);
        var r = []
        var x = 0;
        for (var k in parsed) { // on parcourt le JSON
           if (x<5) { // les cinq premiers resultat avec affichage du label dans bubble chart
              x++;
              var occurence = parsed[k][2];
              data3.push([parsed[k][0]+" ("+occurence+")"+parsed[k][1],Math.floor((Math.random() * 380) + 10),Math.floor((Math.random() * 290) + 10),parsed[k][0],occurence]); // on push les données dans un array
              
            }
          else if (x<20) { // les 20 premiers affichers dans le bubble chart
             x++;
            var occurence = parsed[k][2]; 
            data3.push([" ",Math.floor((Math.random() * 380) + 10),Math.floor((Math.random() * 290) + 10),parsed[k][0],occurence]); 
          }
        }
        google.charts.load('current', {'packages':['corechart']}); // on charge les packages de google chart
      google.charts.setOnLoadCallback(drawSeriesChartlabo);

      }


function init_request(query){
        $('.loading_country').show();
        $('.loading_laboratory').show();
        $('.loading_authors').show();
        $('.avert').remove();
        $('.laboratory h5').remove();
        $('#laboratorys tbody').remove();// remise a zero en cas de recherche simultané
        $('.authors h5').remove();
        $('.country h5').remove();
        $('#authors tbody').remove();// remise a zero en cas de recherche simultané
        $('#country tbody').remove(); // remise a zero en cas de recherche simultané
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
          $.post("/Projet_carto_istex/Backend_Istex_usage/src/index.php/getlaboratorys",
          {
            query: query
          },// requete ajax sur le backend
          function(data){ 
            if (data=='["empty"]') {
                $('#noresult').show(); 
                $('.loading_country').hide();
                $('.loading_laboratory').hide();
                $('.loading_authors').hide();    
            }
            else{
              searchauthors(query);
              var parsed = JSON.parse(data); // transformation en JSON
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



function build_request_facets(){
  setTimeout(function(){ 
  
     query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
                    labelscorpus=$(".corpus input:checked")
                   // console.log(labels)
                   // console.log(labels.closest("li"))
                   var corpus
                   var genre
                   var language
                   var wos

                    $.each(labelscorpus,function(index,value){
                      val=value.labels[0].textContent;
                      var value = val.replace(/[0-9]/g, '');
                      if (corpus===undefined) {
                      corpus=value

                      }
                      else{
                        corpus=corpus+" OR "+value
                      }
                    })
                    if (corpus===undefined) {
                        corpus=""
                    }
                    else{
                      corpus=" AND corpusName:("+corpus+")"
                    }
                      
                     
                labelsgenre=$(".genre input:checked")
                 $.each(labelsgenre,function(index,value){
                    value=value.closest("li").title;
                    if (genre===undefined) {
                      genre=value
                      }
                      else{
                        genre=genre+" OR "+value
                      }
                    
                  })
                    if (genre===undefined) {
                        genre=""
                    }
                    else{
                      genre=" AND genre:("+genre+")"
                    }
   
                labelslanguage=$(".language input:checked")
                  $.each(labelslanguage,function(index,value){
                      val=value.labels[0].textContent;
                      var value = val.replace(/[0-9]/g, '');
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
                              value = 'deu' ;
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
                              value = 'grc' ;
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
                      language=value
                      }
                      else{
                        language=language+" OR "+value
                      }
                    })
                    
                    if (language===undefined) {
                        language=""
                    }
                    else{
                      language=" AND language:("+language+")"
                    }

                labelswos=$(".wos input:checked")
                 $.each(labelswos,function(index,value){
                      value=value.closest("li").title;
                      value='"'+value+'"'
                       if (wos===undefined) {
                      wos=value

                      }
                      else{
                        wos=wos+" OR "+value
                      }
                   
                  })
                    if (wos===undefined) {
                        wos=""
                    }
                    else{
                      wos=" AND wos:("+wos+")"
                    }

                publicationdate=" AND publicationDate:["+$(".istex-facet-pubdate  rzslider .rz-bubble")[2].innerText+" TO "+$(".istex-facet-pubdate  rzslider .rz-bubble")[3].innerText+"]"
                copyrightdate=" AND copyrightDate:["+$(".istex-facet-copyrightdate  rzslider .rz-bubble")[2].innerText+" TO "+$(".istex-facet-copyrightdate  rzslider .rz-bubble")[3].innerText+"]"
                quality=" AND score:["+$(".istex-facet-quality  rzslider .rz-bubble")[2].innerText+" TO "+$(".istex-facet-quality  rzslider .rz-bubble")[3].innerText+"]"
                query=query+corpus+publicationdate+copyrightdate+language+wos+genre+quality
                
                init_request(query);



              },800);
   
}


$(document).ready(function(){
     $(".istex_result").hide();
     $( ".istex-search-bar-wrapper" ).addClass( "ui fluid icon input" )
     $(".istex-search-bar-wrapper :submit").click(function(){//event click sur rechercher
        $('#noresult').hide(); 
        document.getElementById("istex-widget-search").style.marginTop="2%";
        document.getElementById("istex-widget-search").style.marginBottom="1px";
        $(".istex_result").show();
        $('#warning').hide();     
        query=document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
        if (query=="") {
          query="*";
        }
        init_request(query);
        setTimeout(function(){
           $('.rzslider .rz-pointer').off("click")
          $('.rzslider .rz-pointer').on("click",function(){
               build_request_facets()    

            }); 

        },1000)
         
  

      })

     $("form.istex-facets").off("change")
     $("form.istex-facets").on("change",function(){
      build_request_facets()
     
    });



});

