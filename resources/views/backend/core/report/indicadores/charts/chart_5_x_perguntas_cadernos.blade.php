<div id="chart_5_x_perguntas_cadernos" class="chart_5_cadernos">
</div>

@modal(['id'=>'modal_chart_5_x_perguntas_cadernos', 'title'=>'Respostas do Caderno'])
@slot('body')
    <table class="table table-sm table-ater">
        <thead>
            <tr>
                <th width="60">#</th>
                <th>ID Produtor</th>
                <th>Produtor</th>
                <th>Unidade Produtiva</th>
                <th>Sócios</th>
                <th>Pergunta</th>
                <th>Resposta</th>
                <th>Ação</th>
            </tr>
        </thead>
    </table>
@endslot
@endmodal

<div class="d-none">
    <div class="components_5_x_card">
        @cardater(['title'=> ' ','titleTag'=>'h2'])
        @slot('body')
            <hr />

            <div class="font-weight-bold  mb-3">Respostas de perguntas do tipo texto.</div>

            <table class="table table-sm table-ater">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>ID Produtor</th>
                        <th>Produtor</th>
                        <th>Unidade Produtiva</th>
                        <th>Sócios</th>
                        <th>Pergunta</th>
                        <th style="min-width:400px;">Resposta</th>
                        <th>Ação</th>
                    </tr>
                    <tr class="filters">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        @endslot
        @endcardater
    </div>
</div>

@push('after-scripts')
    <script>
        function chart_5_x_chart(pergunta, caderno_id) {
            var values = [
                ['Resposta', 'Total']
            ];

            if (!pergunta) {
                pergunta = [];
            }

            var total = 0;
            for (var i = 0; i < pergunta.respostas.length; i++) {
                var item = pergunta.respostas[i];
                values.push([item.resposta_descricao, item.count * 1]);
                total += item.count * 1;
            }

            var arrayToDataTable = google.visualization.arrayToDataTable(values);

            var options = {
                backgroundColor: {
                    fill: 'transparent'
                },
                chartArea: {
                    width: "94%",
                    height: "88%"
                },
                legend: {
                    'position': 'right'
                },
                pieSliceText: 'percentage',
                theme: 'material',
                sliceVisibilityThreshold: 0,
            };

            if (arrayToDataTable.getNumberOfRows() > 5) {
                options.height = 40 + 40 + arrayToDataTable.getNumberOfRows() * 18;
                options.bars = 'horizontal';
            }

            function selectHandler() {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem && selectedItem.row != null) {
                    var v = arrayToDataTable.getValue(selectedItem.row, 0);
                    var selectedItem = pergunta.respostas.filter(item => item.resposta_descricao == v)[0];
                    openModalChart_5_X(selectedItem);
                }
            }

            var chart = null;
            if (pergunta.tipo_pergunta == 'multiple_check') {
                chart = new google.charts.Bar($('#chart_5_x_perguntas_cadernos #pergunta_' + pergunta.id + "_" +
                    caderno_id + ' .chart')[0]);
            } else {
                chart = new google.visualization.PieChart($('#chart_5_x_perguntas_cadernos #pergunta_' + pergunta.id + "_" +
                    caderno_id + ' .chart')[0]);
            }

            google.visualization.events.addListener(chart, 'select', selectHandler);
            chart.draw(arrayToDataTable, options);
        }

        function chart_5_x_datatable(container, caderno) {
            var dataFilterParams = getFilterParams();
            dataFilterParams.filter_template_id = caderno.id;

            container.find('.table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "orderCellsTop": true,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength": 30,
                "ajax": {
                    "url": '{{ route('admin.core.indicadores.dataChart_5_X_PerguntasCadernos') }}',
                    "data": dataFilterParams,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [{
                        "data": "uid"
                    },
                    {
                        "data": "produtor_uid"
                    },
                    {
                        "data": "produtor"
                    },
                    {
                        "data": "unidade_produtiva"
                    },
                    {
                        "data": "socios"
                    },
                    {
                        "data": "pergunta",
                    },
                    {
                        "data": "resposta"
                    },
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function(data) {
                            return htmlDecode(data);
                        }
                    },
                ],
                "initComplete": function() {
                    var self = this;

                    this.api().columns(5).every(function() {
                        var column = this;

                        var select = $('<select><option value="">Todos</option></select>')
                            .appendTo(container.find('.filters th').eq(column.index()).empty())
                            .on('change', function(evt) {
                                column
                                    .search($(this).val(), true, false)
                                    .draw();
                            });

                        var perguntasTextoKeys = Object.keys(caderno.perguntasTexto);
                        for (var i = 0; i < perguntasTextoKeys.length; i++) {
                            var key = perguntasTextoKeys[i];
                            var item = caderno.perguntasTexto[key];

                            select.append('<option value="' + item + '">' + item + '</option>')
                        }
                    });
                }
            });
        }

        function chart_5_x_caderno(caderno) {
            var cardCaderno = $($(".components_5_x_card").html()); //Force clone
            cardCaderno.addClass("mb-4");
            cardCaderno.find('.card-header').addClass('bg-light')
            cardCaderno.find('.card-title').html(caderno.caderno + ' - Respostas das Perguntas');

            var contentPerguntas = $("<div/>")
                .addClass("row");

            for (var i = 0; i < caderno.perguntas.length; i++) {
                var pergunta = caderno.perguntas[i];

                var perguntaDiv = $("<div/>")
                    .addClass("col-6 card-chart")
                    .attr("id", 'pergunta_' + pergunta.id + "_" + caderno.id)
                    .attr("style", "border:0px;")
                    .append("<div class='txt-title'>" + pergunta.pergunta + "</div>")
                    .append("<div class='chart'></div>");

                contentPerguntas.append(perguntaDiv);
            }

            cardCaderno.find('.card-body').prepend(contentPerguntas);

            cardCaderno.appendTo('.chart_5_cadernos');

            for (var i = 0; i < caderno.perguntas.length; i++) {
                var pergunta = caderno.perguntas[i];
                chart_5_x_chart(pergunta, caderno.id);
            }

            chart_5_x_datatable(cardCaderno, caderno);
        }

        function chart_5_x_perguntas_cadernos(ret) {
            $("#chart_5_x_perguntas_cadernos").empty();

            for (var i = 0; i < ret.length; i++) {
                chart_5_x_caderno(ret[i]);
            }
        }

        function openModalChart_5_X(selectedItem) {
            $('#modal_chart_5_x_perguntas_cadernos').modal();

            $('#modal_chart_5_x_perguntas_cadernos .table').DataTable().clear().destroy();

            var dataFilterParams = getFilterParams();
            dataFilterParams.filter_template_id = selectedItem.caderno_id;
            dataFilterParams.filter_pergunta_id = selectedItem.pergunta_id;
            dataFilterParams.filter_resposta_id = selectedItem.resposta_id;

            $('#modal_chart_5_x_perguntas_cadernos .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength": 30,
                "ajax": {
                    "url": '{{ route('admin.core.indicadores.dataChart_5_X_PerguntasCadernos') }}',
                    "data": dataFilterParams,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [{
                        "data": "uid"
                    },
                    {
                        "data": "produtor_id"
                    },
                    {
                        "data": "produtor"
                    },
                    {
                        "data": "unidade_produtiva"
                    },
                    {
                        "data": "socios"
                    },
                    {
                        "data": "pergunta"
                    },
                    {
                        "data": "resposta"
                    },
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function(data) {
                            return htmlDecode(data);
                        }
                    },
                ]
            });
        }

    </script>
@endpush
