<div class="card-chart" id="chart_1_8_visitas_aplicacoes_atualizacoes">
    <div class="txt-title">Visitas, Atualizações e Aplicações de Formulário</div>

    <div class="chart"></div>

    <div class="txt-legend">
        <small>Somente aparecem cadernos de campo e formulários finalizados.</small>
    </div>
</div>

@modal(['id'=>'modal_chart_1_8_visitas_aplicacoes_atualizacoes_produtores', 'title'=>'Produtores/as'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal


@modal(['id'=>'modal_chart_1_8_visitas_aplicacoes_atualizacoes_cadernos', 'title'=>'Cadernos de Campo'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Criado em</th>
                    <th></th>
                    <th>Finalizado em</th>
                    <th></th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal


@modal(['id'=>'modal_chart_1_8_visitas_aplicacoes_atualizacoes_formularios', 'title'=>'Formulários'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Formulário</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Criado em</th>
                    <th></th>
                    <th>Finalizado em</th>
                    <th></th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal

@modal(['id'=>'modal_chart_1_8_visitas_aplicacoes_atualizacoes_plano_acoes', 'title'=>'Plano de Ações'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Nome</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Coletivo?</th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal

@push('after-scripts')
    <script>
        function chart_1_8_visitas_aplicacoes_atualizacoes(ret) {
            var values =[
                ['Mês/Ano', 'Novos/as Produtores/as', 'Cadernos', 'Formulário', 'Plano de Ação'],

                //P/ gráfico com anotations (google.visualization.ColumnChart)
                // ['Mês/Ano', 'Novos Produtores', { role: 'annotation' }, 'Cadernos',  { role: 'annotation' }, 'Formulário', { role: 'annotation' }, 'Plano de Ação', { role: 'annotation' }],
            ];

            for(var i =0; i < ret.length; i++) {
                var item = ret[i];
                values.push([item.date, item.produtores_count, item.cadernos_count, item.formularios_count, item.pdas_count]);

                //P/ gráfico com anotations (google.visualization.ColumnChart)
                // values.push([item.date, item.produtores_count, item.produtores_count, item.cadernos_count, item.cadernos_count, item.formularios_count, item.formularios_count, item.pdas_count, item.pdas_count]);
            }

            var options = {
                height:400,
                backgroundColor: {
                    fill:'transparent'
                },
                chartArea: {
                    backgroundColor: 'transparent'
                },
                axes: {
                    x: {
                        0: {side: 'top'}
                    },
                },
                // vAxis:{
                //     logScale: true, //p/ escala logaritma com gráfico "google.visualization.ColumnChart"
                // },
                theme:'material'
            };

            var arrayToDataTable = google.visualization.arrayToDataTable(values);
            function selectHandler() {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem && selectedItem.row != null) {
                    var v = arrayToDataTable.getValue(selectedItem.row, 0);

                    //Ordem das legendas
                    switch(selectedItem.column){
                        case(1):
                            openModalChart_1_8_Produtores(v);
                            break;
                        case(2):
                            openModalChart_1_8_Cadernos(v);
                            break;
                        case(3):
                            openModalChart_1_8_Formularios(v);
                            break;
                        case(4):
                            openModalChart_1_8_PlanoAcoes(v);
                            break;
                    }
                }
            }

            //Grafico com Anotations e Escala Logaritma
            // var chart = new google.visualization.ColumnChart($('#chart_1_8_visitas_aplicacoes_atualizacoes .chart')[0]);
            // google.visualization.events.addListener(chart, 'select', selectHandler);
            // chart.draw(arrayToDataTable, options);

            var chart = new google.charts.Bar($('#chart_1_8_visitas_aplicacoes_atualizacoes .chart')[0]);
            google.visualization.events.addListener(chart, 'select', selectHandler);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));
        }

        function openModalChart_1_8_Produtores(period) {
            var data = getFilterParams();
            data.period = period;

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_produtores').modal();

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_produtores .table').DataTable().clear().destroy();

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_produtores .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_8_Produtores") }}',
                    "data": data,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "nome"},
                    {"data": "unidadeProdutiva", "orderable":false},
                    {
                        "data":"actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    },
                ]
            });
        }

        function openModalChart_1_8_Cadernos(period) {
            var data = getFilterParams();
            data.period = period;

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_cadernos').modal();

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_cadernos .table').DataTable().clear().destroy();

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_cadernos .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_8_Cadernos") }}',
                    "data": data,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "produtor.nome"},
                    {"data": "datatable_unidade_produtiva.nome"},
                    {"data": "created_at_formatted", "name": "created_at"},
                    {"data": 'created_at_formatted', "name":"created_at_formatted", visible:false},
                    {"data": "finished_at_formatted", "name": "finished_at"},
                    {"data": 'finished_at_formatted', "name":"finished_at_formatted", visible:false},
                    {
                        "data":"actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    },
                ]
            });
        }

        function openModalChart_1_8_Formularios(period) {
            var data = getFilterParams();
            data.period = period;

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_formularios').modal();

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_formularios .table').DataTable().clear().destroy();

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_formularios .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_8_Formularios") }}',
                    "data": data,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "checklist.nome"},
                    {"data": "produtor.nome"},
                    {"data": "unidade_produtiva.nome"},
                    {"data": "created_at_formatted", "name": "created_at"},
                    {"data": 'created_at_formatted', "name":"created_at_formatted", visible:false},
                    {"data": "finished_at_formatted", "name": "finished_at"},
                    {"data": 'finished_at_formatted', "name":"finished_at_formatted", visible:false},
                    {
                        "data":"actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    },
                ]
            });
        }

        function openModalChart_1_8_PlanoAcoes(period) {
            var data = getFilterParams();
            data.period = period;

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_plano_acoes').modal();

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_plano_acoes .table').DataTable().clear().destroy();

            $('#modal_chart_1_8_visitas_aplicacoes_atualizacoes_plano_acoes .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_8_PlanoAcoes") }}',
                    "data": data,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "nome"},
                    {"data": "produtor.nome"},
                    {"data": "unidadeProdutiva", "orderable":false},
                    {"data": "fl_coletivo"},
                    {
                        "data":"actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    },
                ]
            });
        }
    </script>
@endpush
