<div class="card-chart" id="chart_2_5_regularizacao_ambiental">
    <div class="txt-title">Regularização Documental</div>

    <div class="chart"></div>
</div>

@modal(['id'=>'modal_chart_2_5_regularizacao_ambiental', 'title'=>'Unidades Produtivas'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Sócios</th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal


@push('after-scripts')
    <script>
        function chart_2_5_regularizacao_ambiental(ret) {
            var values = [
                ['Tipo', 'Sim', 'Não', 'Sem resposta', 'Não se aplica'],
            ];

            if (!ret) {
                ret = [];
            }

            for(var i =0; i < ret.length; i++) {
                var item = ret[i];
                values.push([item.nome.toUpperCase(), item.sim, item.nao, item.sem_resposta, item.nao_se_aplica]);
            }

            var arrayToDataTable = google.visualization.arrayToDataTable(values);

            var options = {
                height: 60 + 40 + arrayToDataTable.getNumberOfRows() * 18,

                backgroundColor: {
                    fill:'transparent'
                },
                chartArea: {
                    backgroundColor: 'transparent'
                },
                axes : {
                    y: {
                        0: {side:'right'}
                    },
                },
                legend: {
                    position: 'bottomm'
                },
                bars: 'horizontal',
                isStacked:true,
                colors:['#08885b'],
                series: {
                    0:{color:'#5E97F6'},
                    1:{color:'#FF6C6C'},
                    2:{color:'#B4DED3'},
                    3:{color:'#77D662'},
                }
            };

            function selectHandler() {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem && selectedItem.row != null) {
                    var v = arrayToDataTable.getValue(selectedItem.row, 0);
                    openModalChart_2_5_regularizacao_ambiental(v, selectedItem.row, selectedItem.column);
                }
            }

            var chart = new google.charts.Bar($('#chart_2_5_regularizacao_ambiental .chart')[0]);
            google.visualization.events.addListener(chart, 'select', selectHandler);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));
        }

        function openModalChart_2_5_regularizacao_ambiental(name, row, column) {
            var data = getFilterParams();
            data.filter_name = name;
            data.filter_row = row; //cnpj, nota fiscal, dap, car, ccir, itr, matricula (backend controla a ordem)

            console.log(name, row, column);

            //sim, nao, sem_resposta, nao_se_aplica
            var title = name + ' - Resposta: ';

            if (column == 1) {
                data.filter_column = 1;
                title += 'Sim';
            } else if (column == 2) {
                data.filter_column = 0;
                title += 'Não';
            } else if (column == 3) {
                data.filter_column = null;
                title += 'Sem resposta';
            } else if (column == 4) {
                data.filter_column = 'nao_se_aplica';
                title += 'Não se aplica';
            }

            $('#modal_chart_2_5_regularizacao_ambiental .modal-title').html(title);

            $('#modal_chart_2_5_regularizacao_ambiental').modal();

            $('#modal_chart_2_5_regularizacao_ambiental .table').DataTable().clear().destroy();

            $('#modal_chart_2_5_regularizacao_ambiental .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_2_5_regularizacao_ambiental") }}',
                    "data": data,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "produtor", "orderable":false},
                    {"data": "unidadeProdutiva", "orderable":false},
                    {"data": "socios", "orderable":false},
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
