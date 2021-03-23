<div class="card-chart" id="chart_1_7_formularios_aplicados">
    <div class="txt-title">Formulários Aplicados</div>

    <div class="chart"></div>

    <div class="chart-txt"></div>
</div>

@modal(['id'=>'modal_chart_1_7_formularios_aplicados', 'title'=>'Formulários aplicados'])
    @slot('body')
        <table class="table table-sm table-ater">
            <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Formulário</th>
                    <th>Produtor/a</th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    @endslot
@endmodal


@push('after-scripts')
    <script>
        function chart_1_7_formularios_aplicados(ret) {
            var values = [
                ['Nome do Formulário', 'Total']
            ];

            if (!ret) {
                ret = [];
            }

            var total = 0;
            for(var i =0; i < ret.length; i++) {
                var item = ret[i];
                values.push([item.nome, item.count]);

                total += item.count*1;
            }

            var options = {
                backgroundColor: {
                    fill:'transparent'
                },
                chartArea: { width:"94%",height:"88%" },
                legend: {'position': 'right'},
                pieSliceText: 'percentage',
                theme: 'material'
            };

            var arrayToDataTable = google.visualization.arrayToDataTable(values);

            function selectHandler() {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem && selectedItem.row != null) {
                    var v = arrayToDataTable.getValue(selectedItem.row, 0);
                    var selectedItem = ret.filter(item=>item.nome == v)[0];
                    openModalChart_1_7(selectedItem.id);
                }
            }

            var chart = new google.visualization.PieChart($('#chart_1_7_formularios_aplicados .chart')[0]);
            // google.visualization.events.addListener(chart, 'select', selectHandler);

            chart.draw(arrayToDataTable, options);

            if (total) {
                $("#chart_1_7_formularios_aplicados .chart-txt").html('100% = '+total);
            }
        }

        function openModalChart_1_7(id) {
            $('#modal_chart_1_7_formularios_aplicados').modal();

            $('#modal_chart_1_7_formularios_aplicados .table').DataTable().clear().destroy();

            var dataFilterParams = getFilterParams();
            dataFilterParams.filter_checklist_id = id;

            $('#modal_chart_1_7_formularios_aplicados .table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "pageLength":30,
                "ajax": {
                    "url": '{{ route("admin.core.indicadores.dataChart_1_7_FormulariosAplicados") }}',
                    "data": dataFilterParams,
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "checklist.nome"},
                    {"data": "produtor.nome"},
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
