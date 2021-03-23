<div class="card-chart" id="chart_1_13b_distribuicao_atendimento_tecnico">
    <div class="txt-title">Distribuição de Atendimento por Técnico</div>

    <div class="chart"></div>
</div>

@modal(['id'=>'modal_chart_1_13b_distribuicao_atendimento_tecnico', 'title'=>'Distribuição de atendimento por técnico'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Tipo de Atendimento</th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal

@push('after-scripts')
<style>
    #chart_1_13b_distribuicao_atendimento_tecnico{
        /* height:1000px; */
    }
</style>
<script>
    function chart_1_13b_distribuicao_atendimento_tecnico(ret) {
        var values = [
            ['Usuário', 'Atendimentos'],
        ];

        if (!ret) {
            ret = [];
        }

        for(var i =0; i < ret.length; i++) {
            var item = ret[i];
            values.push([item.name, item.total]);
        }

        var arrayToDataTable = google.visualization.arrayToDataTable(values);

        var options = {
            height: 40 + 40 + arrayToDataTable.getNumberOfRows() * 18,

            backgroundColor: {
                fill: 'transparent'
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
                position: 'none'
            },
            bars: 'horizontal',
            theme:'material'
        };

        function selectHandler() {
            var selectedItem = chart.getSelection()[0];
            console.log(selectedItem);

            if (selectedItem && selectedItem.row != null) {
                var v = arrayToDataTable.getValue(selectedItem.row, 0);
                openModalChart_1_13b(v);
            }
        }

        var chart = new google.charts.Bar($('#chart_1_13b_distribuicao_atendimento_tecnico .chart')[0]);
        google.visualization.events.addListener(chart, 'select', selectHandler);

        chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));
    }

    function openModalChart_1_13b(fullname) {
            $('#modal_chart_1_13b_distribuicao_atendimento_tecnico').modal();

            $('#modal_chart_1_13b_distribuicao_atendimento_tecnico .table').DataTable().clear().destroy();

            var dataFilterParams = getFilterParams();
            dataFilterParams.fullname = fullname;

            $('#modal_chart_1_13b_distribuicao_atendimento_tecnico .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_13b_DistribuicaoAtendimentoTecnico") }}',
                    "data": dataFilterParams,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "nome"},
                    {"data": "unidadeProdutiva", "orderable":false},
                    {"data": "type"},
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
