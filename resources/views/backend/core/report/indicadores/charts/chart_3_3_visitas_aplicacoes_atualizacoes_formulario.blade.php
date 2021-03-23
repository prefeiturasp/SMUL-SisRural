<div class="card-chart" id="chart_3_3_visitas_aplicacoes_atualizacoes_formulario">
    <div class="txt-title">Aplicações de Formulários no Tempo</div>

    <div class="chart"></div>

    <div class="txt-legend">
        <small>Somente aparecem formulários finalizados.</small>
    </div>
</div>

@modal(['id'=>'modal_chart_3_3_visitas_aplicacoes_atualizacoes_formulario', 'title'=>'Aplicações de formulários'])
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
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal

@push('after-scripts')
    <script>
        function chart_3_3_visitas_aplicacoes_atualizacoes_formulario(ret) {
            var values =[
                ['Mês/Ano', 'Formulário'],
            ];

            for(var i =0; i < ret.length; i++) {
                var item = ret[i];
                values.push([item.date, item.formularios_count]);
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
                theme:'material'
            };

            var arrayToDataTable = google.visualization.arrayToDataTable(values);
            function selectHandler() {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem && selectedItem.row != null) {
                    var v = arrayToDataTable.getValue(selectedItem.row, 0);
                    openModalChart_3_3_Formularios(v);
                }
            }

            var chart = new google.charts.Bar($('#chart_3_3_visitas_aplicacoes_atualizacoes_formulario .chart')[0]);
            google.visualization.events.addListener(chart, 'select', selectHandler);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));
        }

        function openModalChart_3_3_Formularios(period) {
            var data = getFilterParams();
            data.period = period;

            $('#modal_chart_3_3_visitas_aplicacoes_atualizacoes_formulario').modal();

            $('#modal_chart_3_3_visitas_aplicacoes_atualizacoes_formulario .table').DataTable().clear().destroy();

            $('#modal_chart_3_3_visitas_aplicacoes_atualizacoes_formulario .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_8_Formularios") }}', //Aponta para o datatable já existente (Gráfica 1_8)
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
