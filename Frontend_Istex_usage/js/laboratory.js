APP.modules.laboratorys = (function() {
    return {
        /**
         * methode de requete vers le backend
         *
         * @param query
         *          nom de la recherche utilisateur
         */
        get_laboratorys: function(query) {
            var documentswithaffiliations = null;
            $.post("/Backend_Istex_usage/src/index.php/getlaboratorys", {
                    query: query
                }, // requete ajax sur le backend
                function(data) {

                    $('.bubblelaboratorys').attr('style', 'display: inline-block !important;')
                    if (data == '["empty"]') {
                        $('#noresult').show();
                        $('.loading_country').hide();
                        $('.loading_laboratory').hide();
                        $('.loading_authors').hide();
                    } else {
                        APP.modules.authors.searchauthors(query);
                        var parsed = JSON.parse(data); // transformation du JSON en array JS
                        parselabo = parsed['documents'];
                        undefinedaff = parsed['0']['noaff']['noaff'];
                        empty = parsed['0']['noaff']['empty'];
                        var documentswithaffiliations = parsed['0']['noaff']['total'];
                        // console.log(documentswithaffiliations);
                        if (documentswithaffiliations == 15000) {
                            $('#warning').show();
                        }
                        APP.modules.laboratorys.parse_laboratorys(parselabo, documentswithaffiliations);
                    }
                });
            $(".reloadlaboratory").click(function() {
                APP.modules.laboratorys.reload_bubble_labo(parselabo); // on recreer le bubble chart
            });

        },

        drawSeriesChartlabo: function() { // fonction qui va créé les bubbles chart
            data = google.visualization.arrayToDataTable(data);
            var options = {
                legend: 'none',
                tooltip: {
                    isHtml: true
                },
                title: 'BubbleChart of publications per laboratory for query : ' + APP.modules.general.strReplaceAll(name,"_"," "),
                width: 900,
                height: 550,
                chartArea: {
                    left: 0,
                    top: 20,
                    width: "100%",
                    height: "90%"
                },
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
                        fontSize: 11,
                        color: 'black',
                        bold: true,
                    }
                },
                explorer: {
                    maxZoomOut: 5,
                    keepInBounds: false
                }

            };

            var chart = new google.visualization.BubbleChart(document.getElementById('series_chart_div'));

            chart.draw(data, options);
            $('#actions_laboratorys #download').remove();
            $('#actions_laboratorys').prepend('<div id="download" class="ui right labeled icon button" > <a href="' + chart.getImageURI() + '" download=laboratory_' + name + '.png>Download</a><i class="download icon"></i></div>');
            $('#series_chart_div').mouseout(function() {
                $('#actions_laboratorys #download').remove();
                $('#actions_laboratorys').prepend('<div id="download" class="ui right labeled icon button" > <a href="' + chart.getImageURI() + '" download=laboratory_' + name+ '.png>Download</a><i class="download icon"></i></div>');
            });

        },


        /**
         * methode de traitement des laboratoires
         *
         * @param parsed
         *          array
         */
        parse_laboratorys: function(parsed, documentswithaffiliations) {
            data = [];
            data.push(['ID', 'Y', 'X', 'Laboratory', 'Number of publications']);
            var r = [];
            var x = 0;
            for (var k in parsed) { // on parcourt le JSON
                if (x < 5) { // les cinq premiers resultat avec affichage du label dans bubble chart
                    x++;
                    var occurence = parsed[k][2];
                    data.push([parsed[k][0] + " (" + occurence + ")" + parsed[k][1], Math.floor((Math.random() * 1000) + 100) - Math.floor((Math.random() * 150) + 50), Math.floor((Math.random() * 800) + 50) - Math.floor((Math.random() * 150) + 50), parsed[k][0] + ", " + parsed[k][1], occurence]); // on push les données dans un array
                } else if (x < 20) { // les 20 premiers affichers dans le bubble chart
                    x++;
                    var occurence = parsed[k][2];
                    data.push([" ", Math.floor((Math.random() * 1000) + 50) - Math.floor((Math.random() * 150) + 50), Math.floor((Math.random() * 800) + 50) - Math.floor((Math.random() * 150) + 50), parsed[k][0] + ", " + parsed[k][1], occurence]); // on push les données dans un array
                }
            }
            $('.laboratory h5').remove();
            var total = (undefinedaff / (documentswithaffiliations)) * 100;
            total = total * 100;
            total = Math.round(total);
            total = total / 100;

            $('.laboratory').append('<h5>' + undefinedaff + ' records(' + total + '%) do not contain data in the field being analyzed.</h5>');
            $('.laboratory').append('<h5> including ' + empty + ' records that do not have affiliations.</h5>');
            $('.loading_laboratory').hide();

            google.charts.load('current', {
                'packages': ['corechart']
            }); // on charge les packages de google chart
            google.charts.setOnLoadCallback(APP.modules.laboratorys.drawSeriesChartlabo);
            var table = $('#laboratorys').DataTable({
                "data": parsed,
                lengthChange: false,
                destroy: true,
                "pageLength": 5,
                "order": [
                    [2, "desc"]
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
                    title: name + "_laboratorys",
                    className: 'ui primary button'
                }]
            }).container().appendTo($('#buttons_laboratorys_master'));
            $('#laboratorys tbody').on('click', 'tr', function() {
                $('.laboratory_table .header').empty();
                $("#laboratorys_row tbody").remove();
                var data = table.row(this).data();
                laboratory = data[0].replace(/ /g, "_");
                for (row in data[3]) {
                    $("#laboratorys_row").append('<tr><td>' + data[3][row]['title'] + '</td><td>' + data[3][row]['id'] + '</td></tr>'); //Affichage dans le tableau    
                }
                $('.laboratory_table .header').append("Publications of " + data[0])


                var table_row = $('#laboratorys_row').DataTable({
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
                $('.laboratory_table').modal('show');
                var buttons = new $.fn.dataTable.Buttons(table_row, {
                    buttons: [{
                        extend: 'csvHtml5',
                        text: 'Export CSV',
                        title: name + "_" + laboratory,
                        className: 'ui primary button'
                    }]
                }).container().appendTo($('#actions_infolabo'));
                $('#actions_infolabo .dt-buttons').append('<div class="ui negative right labeled icon button">Fermer<i class="remove icon"></i> </div>')
                $('#laboratorys_row tbody').on('click', 'tr', function() {
                    var row = table_row.row(this).data();
                    window.open(APP.config.URL_ISTEX + row[1] + "/fulltext/pdf");
                });
            });
        },


        reload_bubble_labo: function(parsed) {
            data = [];
            data.push(['ID', 'Y', 'X', 'Laboratory', 'Number of publications']);
            var r = [];
            var x = 0;
            for (var k in parsed) { // on parcourt le JSON
                if (x < 5) { // les cinq premiers resultat avec affichage du label dans bubble chart
                    x++;
                    var occurence = parsed[k][2];
                    data.push([parsed[k][0] + " (" + occurence + ")" + parsed[k][1], Math.floor((Math.random() * 1000) + 100) - Math.floor((Math.random() * 150) + 50), Math.floor((Math.random() * 800) + 50) - Math.floor((Math.random() * 150) + 50), parsed[k][0] + ", " + parsed[k][1], occurence]); // on push les données dans un array
                } else if (x < 20) { // les 20 premiers affichers dans le bubble chart
                    x++;
                    var occurence = parsed[k][2];
                    data.push([" ", Math.floor((Math.random() * 1000) + 50) - Math.floor((Math.random() * 150) + 50), Math.floor((Math.random() * 800) + 50) - Math.floor((Math.random() * 150) + 50), parsed[k][0] + ", " + parsed[k][1], occurence]); // on push les données dans un array
                }
            }
            google.charts.load('current', {
                'packages': ['corechart']
            }); // on charge les packages de google chart
            google.charts.setOnLoadCallback(APP.modules.laboratorys.drawSeriesChartlabo);

        }
    }
})()