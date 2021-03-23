<div id="chart_3_x_perguntas_formularios" class="chart_3_checklists">
    <form class="form-inline">
        <div class="form-group">
            <label class="font-weight-bold">Perguntas dos Formulários</label>
            <select class="form-control ml-3" id="3_x_checklist"></select>
        </div>
    </form>

    <div class="panel-chart mt-4"></div>
</div>

@modal(['id'=>'modal_chart_3_x_perguntas_formularios', 'title'=>'Respostas do Formulário'])
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
    <div class="components_3_x_card">
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
                        <th>Resposta</th>
                        <th>Ação</th>
                    </tr>
                </thead>
            </table>
        @endslot
        @endcardater
    </div>
</div>

@push('after-scripts')
    <script>
        var dataChart_3_x_chart = null;

        var defaultColors = [];
        defaultColors['verde'] = '#77d662';
        defaultColors['amarelo'] = '#fee07e';
        defaultColors['vermelho'] = '#ff9ea0';
        defaultColors['cinza'] = '#C3C3C3';

        var otherColors = ['#3366cc', '#dc3912', '#ff9900', '#109618', '#990099', '#0099c6', '#dd4477', '#66aa00',
            '#b82e2e', '#316395', '#994499', '#22aa99', '#aaaa11', '#6633cc', '#e67300', '#8b0707', '#651067',
            '#329262', '#5574a6', '#3b3eac', '#b77322', '#16d620', '#b91383', '#f4359e', '#9c5935', '#a9c413',
            '#2a778d', '#668d1c', '#bea413', '#0c5922', '#743411'
        ];

        //Initialize
        function chart_3_x_perguntas_formularios(ret) {
            dataChart_3_x_chart = ret;

            $("#chart_3_x_perguntas_formularios #3_x_checklist").empty();
            $("#chart_3_x_perguntas_formularios .panel-chart").empty();

            if (ret) {
                $("#chart_3_x_perguntas_formularios").removeClass("d-none");
                $("#chart_3_x_perguntas_formularios_periodo").addClass("d-none");

                for (var i = 0; i < ret.length; i++) {
                    var option = $("<option/>")
                        .val(ret[i].id)
                        .text(ret[i].checklist);

                    option.appendTo($("#3_x_checklist"));
                }
            }

            //Render charts
            $("#3_x_checklist").change();
        }

        //Select Checklist (Chart Data)
        function onChangeFormulario(event) {
            var checklist_id = $(this).val();
            if (checklist_id) {
                var checklist = dataChart_3_x_chart.filter(function(v) {
                    return v.id == checklist_id;
                })[0];

                chart_3_x_checklist(checklist);
            }
        }
        $("#3_x_checklist").change(onChangeFormulario);

        function chart_3_x_checklist(checklist) {
            $("#chart_3_x_perguntas_formularios .panel-chart").empty();

            var cardChecklist = $($(".components_3_x_card").html()); //Force clone
            cardChecklist.addClass("mb-4");
            cardChecklist.find('.card-header').addClass('bg-light')
            cardChecklist.find('.card-title').html(checklist.checklist + " - Respostas");

            var contentCategorias = $("<div/>");

            for (var i = 0; i < checklist.categorias.length; i++) {
                var categoria = checklist.categorias[i];

                var contentPerguntas = $("<div/>")
                    .addClass("row");

                for (var j = 0; j < categoria.perguntas.length; j++) {
                    var pergunta = categoria.perguntas[j];

                    var perguntaDiv = $("<div/>")
                        .addClass("col-4")
                        .attr("id", 'pergunta_' + pergunta.id + "_" + checklist.id)
                        .append("<div class='txt-title'>" + pergunta.pergunta + "</div>")
                        .append("<div class='chart'></div>");

                    contentPerguntas.append(perguntaDiv);
                }

                //Titulo categoria
                contentCategorias.append($("<div/>").addClass("row mt-4").html("<div class='col font-weight-bold h5'>" +
                    categoria.nome + "</div>"));

                contentCategorias.append(contentPerguntas);
            }

            cardChecklist.find('.card-body').prepend(contentCategorias);

            $('#chart_3_x_perguntas_formularios .panel-chart').append(cardChecklist);

            for (var i = 0; i < checklist.categorias.length; i++) {
                var categoria = checklist.categorias[i];

                for (var j = 0; j < categoria.perguntas.length; j++) {
                    chart_3_x_chart(categoria.perguntas[j], checklist.id);
                }
            }

            chart_3_x_datatable(cardChecklist, checklist);
        }

        //Charts
        function chart_3_x_chart(pergunta, checklist_id) {
            var values = [
                ['Resposta', 'Total']
            ];

            if (!pergunta) {
                pergunta = [];
            }

            var colors = [];
            var total = 0;
            for (var i = 0; i < pergunta.respostas.length; i++) {
                var item = pergunta.respostas[i];
                values.push([item.resposta_descricao, item.count * 1]);
                total += item.count * 1;
                if (item.cor) {
                    colors.push(defaultColors[item.cor]);
                } else {
                    colors.push(otherColors[i]);
                }
            }

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
                colors: colors.length > 0 ? colors : null
            };

            var arrayToDataTable = google.visualization.arrayToDataTable(values);

            function selectHandler() {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem && selectedItem.row != null) {
                    var v = arrayToDataTable.getValue(selectedItem.row, 0);
                    var selectedItem = pergunta.respostas.filter(item => item.resposta_descricao == v)[0];
                    openModalChart_3_X(selectedItem);
                }
            }

            var chart = null;
            if (pergunta.tipo_pergunta == 'multipla-escolha') {
                chart = new google.charts.Bar($('#chart_3_x_perguntas_formularios #pergunta_' + pergunta.id + "_" +
                    checklist_id + ' .chart')[0]);
            } else {
                chart = new google.visualization.PieChart($('#chart_3_x_perguntas_formularios #pergunta_' + pergunta.id +
                    "_" + checklist_id + ' .chart')[0]);
            }

            google.visualization.events.addListener(chart, 'select', selectHandler);
            chart.draw(arrayToDataTable, options);
        }

        function chart_3_x_datatable(container, checklist) {
            var dataFilterParams = getFilterParams();
            dataFilterParams.filter_checklist_id = checklist.id;

            container.find('.table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength": 30,
                "ajax": {
                    "url": '{{ route('admin.core.indicadores.dataChart_3_X_PerguntasFormularios') }}',
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

        function openModalChart_3_X(selectedItem) {
            $('#modal_chart_3_x_perguntas_formularios').modal();

            $('#modal_chart_3_x_perguntas_formularios .table').DataTable().clear().destroy();

            var dataFilterParams = getFilterParams();
            dataFilterParams.filter_checklist_id = selectedItem.checklist_id;
            dataFilterParams.filter_pergunta_id = selectedItem.pergunta_id;
            dataFilterParams.filter_resposta_id = selectedItem.resposta_id;

            $('#modal_chart_3_x_perguntas_formularios .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength": 30,
                "ajax": {
                    "url": '{{ route('admin.core.indicadores.dataChart_3_X_PerguntasFormularios') }}',
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
