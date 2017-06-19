var APP = (function() {
    return {
        modules: {},
        config: {},
    }
})();


APP.config.URL_ISTEX = "https://api.istex.fr/document/";

APP.modules.general = (function() {
    return {
        strReplaceAll: function(string, Find, Replace) { // fonction de remplacement des espace en underscore
            try {
                return string.replace(new RegExp(Find, "gi"), Replace);
            } catch (ex) {
                return string;
            }
        },

        /**
         * methode d'initialisation 
         *
         * 
         */
        init_request: function(arg) {
          var query;
          if (arg != "search") {
            query=arg;
           APP.modules.general.process_query(query);

          }
          else if (arg=="search"){
            $(".istex_result").hide();
            $(".istex-search-bar-wrapper").addClass("ui fluid icon input");
            $('.istex-search-bar-wrapper input').last().attr('id', 'searchbutton');
                $('#noresult').hide();
                document.getElementById("istex-widget-search").style.marginTop = "2%";
                document.getElementById("istex-widget-search").style.marginBottom = "1px";
                $(".istex_result").show();
                
                query = document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
                if (query == "") {
                    query = "*";
                }
                  
                
               APP.modules.general.process_query(query);

                }
                name = query.replace(/ /g, "_");

        },



        process_query:function(query){
          console.log(query)
           $('#nodatacountry').remove();
                $('#warning').hide();
                $('#warningcountry').hide();
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
                $('#improve').hide();
                if (query != null) {

                APP.modules.laboratorys.get_laboratorys(query);
                }
                setTimeout(function() {
                    $('.rzslider .rz-pointer').off("click")
                    $('.rzslider .rz-pointer').on("click", function() {
                        APP.modules.general.build_request_facets("slider");

                    });

                }, 1000)
                $('#improve').off("click");
                $('#improve').on('click', function() {
                    $('.loading_country').show();
                    $('.country h5').remove();
                    $('#country tbody').remove(); // remise a zero en cas de recherche simultané
                    $('#country .row').remove();
                    $('#country_info').remove();
                    $('#improve').addClass('green')
                    $('#improve').html('<i class="checkmark box icon"></i>Improved');

                    APP.modules.countrys.searchcountry(query, 'improved');

            })
              },

        /**
         * methode qui recupere les modifications sur le widget facets d'istex
         *
         * 
         */

        build_request_facets: function(slider) {
            setTimeout(function() {
                var query = document.getElementsByClassName('istex-search-input')[0].value // recuperation de la valeur de l'input
                labelscorpus = $(".corpus input:checked");
                var corpus;
                var genre;
                var language;
                var wos;
                $.each(labelscorpus, function(index, value) {
                    val = value.nextSibling.nodeValue;
                    var value = val.replace(/[0-9]/g, '');
                    if (corpus === undefined) {
                        corpus = value;

                    } else {
                        corpus = corpus + " OR " + value;
                    }
                })
                if (corpus === undefined) {
                    corpus = "";
                } else {
                    corpus = " AND corpusName:(" + corpus + ")";
                }


                labelsgenre = $(".genre input:checked")
                $.each(labelsgenre, function(index, value) {
                    value = value.closest("li").title;
                    if (genre === undefined) {
                        genre = value;
                    } else {
                        genre = genre + " OR " + value;
                    }

                })
                if (genre === undefined) {
                    genre = "";
                } else {
                    genre = " AND genre:(" + genre + ")";
                }

                labelslanguage = $(".language input:checked")
                $.each(labelslanguage, function(index, value) {
                    val = value.nextSibling.nodeValue;
                    var value = val.replace(/[0-9]/g, '');
                    var value = value.replace(/\s+/g, '');
                    switch (value) {
                        case 'Français':
                            value = 'fre';
                            break;
                        case 'Anglais':
                            value = 'eng';
                            break
                        case 'Latin':
                            value = 'lat';
                            break;
                        case 'Allemand':
                            value = 'ger';
                            break;
                        case 'Espagnol':
                            value = 'spa';
                            break;
                        case 'Néerlandais':
                            value = 'dut';
                            break;
                        case 'Italien':
                            value = 'ita';
                            break;
                        case 'Portugais':
                            value = 'por';
                            break;
                        case 'Russe':
                            value = 'rus';
                            break;
                        case 'Gallois':
                            value = 'wel';
                            break;
                        case 'Galicien':
                            value = 'glg';
                            break;
                        case 'Grec':
                            value = 'gre';
                            break;
                        case 'Arabe':
                            value = 'ara';
                            break;
                        case 'Hébreu':
                            value = 'heb';
                            break;
                        case 'Polonais':
                            value = 'pol';
                            break;
                        case 'Danois':
                            value = 'dan';
                            break;
                        case 'Suédois':
                            value = 'swe';
                            break;
                        case 'Mohawk':
                            value = 'moh';
                            break;
                        case 'Syriaque':
                            value = 'syr';
                            break;
                        case 'Persan':
                            value = 'per';
                            break;
                        case 'Français moyen':
                            value = 'frm';
                            break;
                        case 'Multilingue':
                            value = 'mul';
                            break;
                        case 'Non spécifié':
                            value = 'unknown';
                            break;
                        default:
                            value = language;
                            break;
                    }
                    if (language === undefined) {
                        language = value;
                    } else {
                        language = language + " OR " + value;
                    }
                })

                if (language === undefined) {
                    language = "";
                } else {
                    language = " AND language:(" + language + ")";
                }

                labelswos = $(".wos input:checked")
                $.each(labelswos, function(index, value) {
                    value = value.closest("li").title;
                    value = '"' + value + '"';
                    if (wos === undefined) {
                        wos = value;
                    } else {
                        wos = wos + " OR " + value;
                    }

                })
                if (wos === undefined) {
                    wos = "";
                } else {
                    wos = " AND categories.wos:(" + wos + ")";
                }
                if (slider == "slider") {

                    var publicationdate = " AND publicationDate:[" + $(".istex-facet-pubdate  rzslider .rz-bubble")[2].innerText + " TO " + $(".istex-facet-pubdate  rzslider .rz-bubble")[3].innerText + "]";
                    var copyrightdate = " AND copyrightDate:[" + $(".istex-facet-copyrightdate  rzslider .rz-bubble")[2].innerText + " TO " + $(".istex-facet-copyrightdate  rzslider .rz-bubble")[3].innerText + "]";
                    var quality = " AND qualityIndicators.score:[" + $(".istex-facet-quality  rzslider .rz-bubble")[2].innerText + " TO " + $(".istex-facet-quality  rzslider .rz-bubble")[3].innerText + "]";
                    allquery = query + corpus + publicationdate + copyrightdate + language + wos + genre + quality;

                } else {
                    var publicationdate = ""
                    var copyrightdate = ""
                    var quality = ""
                    allquery = query + corpus + publicationdate + copyrightdate + language + wos + genre + quality;
                }
                APP.modules.general.init_request(allquery);
            }, 1000);

        }
    }
})()


$(document).ready(function() {

    $(".istex-search-bar-wrapper :submit").click(function() { 
          APP.modules.general.init_request("search");

    });
   $(".istex_result").hide();
            $(".istex-search-bar-wrapper").addClass("ui fluid icon input");
            $('.istex-search-bar-wrapper input').last().attr('id', 'searchbutton');
    $("form.istex-facets").off("change");
    $("form.istex-facets").on("change", function() {
        APP.modules.general.build_request_facets("checkbox");

    });

});