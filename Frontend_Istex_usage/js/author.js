APP.modules.authors = (function() {
  var data;
    return {
        drawSeriesChartauthor: function() { // fonction qui va créé les bubbles
            data = google.visualization.arrayToDataTable(data);
            var options = {
                legend: 'none',
                tooltip: {
                    isHtml: true
                },
                title: 'BubbleChart of publications per author for query : ' + APP.modules.general.strReplaceAll(name,"_"," "),
                chartArea: {
                    left: 0,
                    top: 20,
                    width: "100%",
                    height: "90%"
                },
                width: 900,
                height: 550,
                hAxis: {
                    display: false,
                    viewWindowMode: 'explicit',
                    viewWindow: {
                        max: 1220
                    },
                    baselineColor: '#fff',
                    gridlineColor: '#fff',
                    textPosition: 'none'
                },
                vAxis: {
                    display: false,
                    viewWindowMode: 'explicit',
                    viewWindow: {
                        max: 1220
                    },
                    baselineColor: '#fff',
                    gridlineColor: '#fff',
                    textPosition: 'none'
                },
                bubble: {
                    textStyle: {
                        fontSize: 10,
                        color: 'black',
                        bold: true,
                    }
                },
                explorer: {
                    maxZoomOut: 3,
                    keepInBounds: false
                }

            };

            var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div_authors'));
            chart.draw(data, options);
            $('#actions_authors #download').remove();
            $('#actions_authors').prepend('<div id="download" class="ui right labeled icon button" > <a href="' + chart.getImageURI() + '" download=authors_' + name + '.png>Download</a><i class="download icon"></i></div>');
            $('#series_chart_div_authors').mouseout(function() {
                $('#actions_authors #download').remove();
                $('#actions_authors').prepend('<div id="download" class="ui right labeled icon button" > <a href="' + chart.getImageURI() + '" download=laboratory_' + name + '.png>Download</a><i class="download icon"></i></div>');


            });

        },

        /**
         * methode de traitement des auteurs
         *
         * @param parsed
         *          array
         */
        parse_authors: function(parsed) {

            data = [];
            data.push(['ID', 'Y', 'X', 'Author', 'Number of publications']);
            var r = []
            var x = 0;

            for (var k in parsed) { // on parcourt le JSON

                if (x < 5) { // les cinq premiers resultat avec affichage du label dans bubble chart
                    x++;
                    var occurence = parsed[k][3];
                    data.push([parsed[k][0] + " (" + occurence + ")", Math.floor((Math.random() * 1000) + 50) - Math.floor((Math.random() * 150) + 50), Math.floor((Math.random() * 800) + 50) - Math.floor((Math.random() * 150) + 50), parsed[k][0], occurence]); // on push les données dans un array
                } else if (x < 20) { // les 20 premiers affichers dans le bubble chart
                    x++;
                    var occurence = parsed[k][3];
                    data.push([" ", Math.floor((Math.random() * 1000) + 50) - Math.floor((Math.random() * 150) + 50), Math.floor((Math.random() * 800) + 50) - Math.floor((Math.random() * 150) + 50), parsed[k][0], occurence]);
                }
            }
            $('.authors h5').remove();
            var total = (undefinedaff / (documentswithaffiliations)) * 100;
            total = total * 100;
            total = Math.round(total);
            total = total / 100;
            $('.authors').append('<h5>' + undefinedaff + ' records(' + total + '%) do not contain data in the field being analyzed.</h5>');
            $('.loading_authors').hide();
            google.charts.load('current', {
                'packages': ['corechart']
            }); // on charge les packages de google chart
            google.charts.setOnLoadCallback(APP.modules.authors.drawSeriesChartauthor);
            var table = $('#authors').DataTable({
                "data": parsed,
                lengthChange: false,
                destroy: true,
                "pageLength": 5,
                "order": [
                    [3, "desc"]
                ],
                "pagingType": "numbers",
                responsive: true,
                "deferRender": true,
                "autoWidth": false
            }); // pagination du tableau precedemment crée
            var buttons = new $.fn.dataTable.Buttons(table, {
                buttons: [{
                    extend: 'csvHtml5',
                    text: 'Export CSV',
                    title: name + "_authors",
                    className: 'ui primary button'
                }]
            }).container().appendTo($('#buttons_authors_master'));
            $('#authors tbody').on('click', 'tr', function() {
                $('.authors_table .header').empty();
                $("#authors_row tbody").remove()
                var data = table.row(this).data();
                author = data[0].replace(/ /g, "_");
                for (row in data[4]) {
                    $("#authors_row").append('<tr><td>' + data[4][row]['title'] + '</td><td>' + data[4][row]['id'] + '</td></tr>'); //Affichage dans le tableau    
                }
                $('.authors_table .header').append("Publications of " + data[0])


                var table_row = $('#authors_row').DataTable({
                    lengthChange: false,
                    destroy: true,
                    "pageLength": 3,
                    "order": [
                        [1, "desc"]
                    ],
                    "pagingType": "numbers",
                    responsive: true,
                    dom: 'frtip',
                }); // pagination du tableau precedemment crée
                var buttons = new $.fn.dataTable.Buttons(table_row, {
                    buttons: [{
                        extend: 'csvHtml5',
                        text: 'Export CSV',
                        title: name + "_" + author,
                        className: 'ui primary button'
                    }]
                }).container().appendTo($('#buttons_authors'));
                $('#actions_infoauthors .dt-buttons').append('<div class="ui negative right labeled icon button">Fermer<i class="remove icon"></i> </div>')
                $('.authors_table').modal('show');
                $('#authors_row tbody').on('click', 'tr', function() {
                    var row = table_row.row(this).data();
                    window.open(APP.config.URL_ISTEX + row[1] + "/fulltext/pdf");
                });

            });
        },


        /**
         * methode de rechargement des données dans le bubble chart
         *
         * @param parsed
         *          array JS
         */
        reload_bubble_author: function(parsed) {
            data = [];
            data.push(['ID', 'Y', 'X', 'Author', 'Number of publications']);
            var r = []
            var x = 0;
            for (var k in parsed) {
                if (x < 5) { // les cinq premiers resultat avec affichage du label dans bubble chart
                    x++;
                    var occurence = parsed[k][3];
                    data.push([parsed[k][0] + " (" + occurence + ")", Math.floor((Math.random() * 1000) + 50) - Math.floor((Math.random() * 150) + 50), Math.floor((Math.random() * 800) + 50) - Math.floor((Math.random() * 150) + 50), parsed[k][0], occurence]); // on push les données dans un array
                } else if (x < 20) { // les 20 premiers affichers dans le bubble chart
                    x++;
                    var occurence = parsed[k][3];
                    data.push([" ", Math.floor((Math.random() * 1000) + 50) - Math.floor((Math.random() * 150) + 50), Math.floor((Math.random() * 800) + 50) - Math.floor((Math.random() * 150) + 50), parsed[k][0], occurence]);
                }
            }
            google.charts.load('current', {
                'packages': ['corechart']
            }); // on charge les packages de google chart
            google.charts.setOnLoadCallback(APP.modules.authors.drawSeriesChartauthor);


        },




        /**
         * methode de requete vers le backend
         *
         * @param query
         *          nom de la recherche utilisateur
         */
        searchauthors: function(query) {
            $.post("/Backend_Istex_usage/src/index.php/getauthors", {
                    query: query
                }, // requete ajax sur le backend
                function(data) {
                    $('.bubbleauthors').attr('style', 'display: inline-block !important;')
                    var parsed = JSON.parse(data); // transformation en array
                    var parseauthor = parsed['documents'];
                    var undefinedaff = parsed['0']['noaff']['noaff'];
                    documentswithaffiliations = parsed['0']['noaff']['total'];
                    APP.modules.authors.parse_authors(parseauthor);
                    APP.modules.countrys.searchcountry(query); // lancement de la recherche des pays une fois terminer
                });

            $(".reloadauthor").click(function() {
                APP.modules.authors.reload_bubble_author(parseauthor); // on recreer le bubble chart
            });
        },



    }
})();