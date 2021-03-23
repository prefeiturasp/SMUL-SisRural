<div id="chart_3_x_perguntas_formularios_periodo" class="chart_3_checklists_periodo d-none">
    <form class="form-inline">
        <div class="form-group">
            <label class="font-weight-bold">Perguntas dos Formulários</label>
            <select class="form-control ml-3" id="3_x_checklist_periodo"></select>
        </div>
    </form>

    <div class="panel-chart mt-4"></div>
</div>

@modal(['id'=>'modal_chart_3_x_perguntas_formularios_periodo', 'title'=>'Respostas do Formulário'])
@slot('body')
    <table class="table table-sm table-ater">
        <thead>
            <tr>
                <th width="60">#</th>
                <th>Formulário</th>
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
    <div class="components_3_x_card_periodo">
        @cardater(['title'=> ' ','titleTag'=>'h2'])
        @slot('body')
            <span></span>
        @endslot
        @endcardater
    </div>
</div>

@push('after-scripts')
    <script>
        var dataChart_3_x_chart_periodo = null;

        //Initialize
        function chart_3_x_perguntas_formularios_periodo(ret) {
            dataChart_3_x_chart_periodo = ret;

            $("#chart_3_x_perguntas_formularios_periodo #3_x_checklist_periodo").empty();
            $("#chart_3_x_perguntas_formularios_periodo .panel-chart").empty();

            if (ret) {
                $("#chart_3_x_perguntas_formularios").addClass("d-none");
                $("#chart_3_x_perguntas_formularios_periodo").removeClass("d-none");

                for (var i = 0; i < ret.length; i++) {
                    var option = $("<option/>")
                        .val(ret[i].id)
                        .text(ret[i].checklist);

                    option.appendTo($("#3_x_checklist_periodo"));
                }
            }

            //Render charts
            $("#3_x_checklist_periodo").change();
        }

        //Select Checklist (Chart Data)
        function onChangeFormularioPeriodo(event) {
            var checklist_id = $(this).val();

            if (checklist_id) {
                var checklist = dataChart_3_x_chart_periodo.filter(function(v) {
                    return v.id == checklist_id;
                })[0];

                chart_3_x_checklist_periodo(checklist);
            }
        }
        $("#3_x_checklist_periodo").change(onChangeFormularioPeriodo);


        function chart_3_x_chart_periodo(pergunta, checklist_id) {
            var columns = ['Mês/Ano'];

            var respostasIni = pergunta.respostas[0];
            for (var i = 0; i < respostasIni.length; i++) {
                columns.push(respostasIni[i].resposta_descricao);
            }

            var values = [
                columns
            ];

            if (!pergunta) {
                pergunta = [];
            }

            // //Acumulador
            // for (var i = 0; i < pergunta.respostas.length; i++) {
            //     var respostaDateSort = pergunta.respostas[i];
            //     var prevRespostasDateSort = null;

            //     if (i > 0) {
            //         prevRespostasDateSort = pergunta.respostas[i - 1];
            //     }

            //     if (prevRespostasDateSort) {
            //         for (var j = 0; j < respostaDateSort.length; j++) {
            //             respostaDateSort[j].count += prevRespostasDateSort[j].count;
            //         }
            //     }
            // }

            for (var i = 0; i < pergunta.respostas.length; i++) {
                var respostaDateSort = pergunta.respostas[i];

                var respostaValues = [respostaDateSort[0].date];
                for (var j = 0; j < respostaDateSort.length; j++) {
                    respostaValues.push(respostaDateSort[j].count);
                }

                values.push(respostaValues);
            }

            var colors = [];
            for (var i = 0; i < pergunta.respostas[0].length; i++) {
                var respostaDateSort = pergunta.respostas[0][i];
                if (respostaDateSort.cor) {
                    colors.push(defaultColors[respostaDateSort.cor]);
                } else {
                    colors.push(otherColors[i]);
                }
            }

            var options = {
                backgroundColor: {
                    fill: 'transparent'
                },
                chartArea: {
                    backgroundColor: 'transparent'
                },
                axes: {
                    x: {
                        0: {
                            side: 'top'
                        }
                    },
                },
                colors: colors.length > 1 ? colors : null
            };

            var arrayToDataTable = google.visualization.arrayToDataTable(values);

            function selectHandler() {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem && selectedItem.row != null) {
                    var period = arrayToDataTable.getValue(selectedItem.row, 0);

                    var valueColumn = columns[selectedItem.column];

                    var selectedItem = pergunta.respostas[0].filter(item => item.resposta_descricao == valueColumn)[0];
                    selectedItem.date = period;
                    selectedItem.date_sort = period;

                    openModalChart_3_X_periodo(period, selectedItem);
                }
            }

            // var chart = new google.charts.Line($('#chart_3_x_perguntas_formularios_periodo #pergunta_' + pergunta.id + "_" +
            //     checklist_id + '_periodo .chart')[0]);
            // google.visualization.events.addListener(chart, 'select', selectHandler);
            // chart.draw(arrayToDataTable, google.charts.Line.convertOptions(options));

            var chart = new google.charts.Bar($('#chart_3_x_perguntas_formularios_periodo #pergunta_' + pergunta.id + "_" +
                checklist_id + '_periodo .chart')[0]);
            google.visualization.events.addListener(chart, 'select', selectHandler);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));
        }

        function chart_3_x_checklist_periodo(checklist) {
            $("#chart_3_x_perguntas_formularios_periodo .panel-chart").empty();

            var cardChecklist = $($(".components_3_x_card_periodo").html()); //Force clone
            cardChecklist.addClass("mb-4");
            cardChecklist.find('.card-header').addClass('bg-light')
            cardChecklist.find('.card-title').html(checklist.checklist + ' - Respostas por Período');

            var contentPerguntas = $("<div/>")
                .addClass("row");

            for (var i = 0; i < checklist.perguntas.length; i++) {
                var pergunta = checklist.perguntas[i];

                var perguntaDiv = $("<div/>")
                    .addClass("col-12 my-4")
                    .attr("id", 'pergunta_' + pergunta.id + "_" + checklist.id + "_periodo")
                    .append("<div class='txt-title font-weight-bold'>" + pergunta.pergunta + "</div>")
                    .append("<div class='chart'></div>");

                contentPerguntas.append(perguntaDiv);
            }

            cardChecklist.find('.card-body').prepend(contentPerguntas);

            cardChecklist.appendTo('.chart_3_checklists_periodo .panel-chart');

            for (var i = 0; i < checklist.perguntas.length; i++) {
                var pergunta = checklist.perguntas[i];
                chart_3_x_chart_periodo(pergunta, checklist.id);
            }
        }

        function openModalChart_3_X_periodo(period, selectedItem) {
            $('#modal_chart_3_x_perguntas_formularios_periodo').modal();

            $('#modal_chart_3_x_perguntas_formularios_periodo .table').DataTable().clear().destroy();

            var dataFilterParams = getFilterParams();
            dataFilterParams.filter_checklist_id = selectedItem.checklist_id;
            dataFilterParams.filter_pergunta_id = selectedItem.pergunta_id;
            dataFilterParams.filter_resposta_id = selectedItem.resposta_id;
            dataFilterParams.period = period;

            $('#modal_chart_3_x_perguntas_formularios_periodo .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength": 30,
                "ajax": {
                    "url": '{{ route('admin.core.indicadores.dataChart_3_X_PerguntasFormulariosPeriod') }}',
                    "data": dataFilterParams,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [{
                        "data": "uid"
                    },
                    {
                        "data": "checklist"
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
