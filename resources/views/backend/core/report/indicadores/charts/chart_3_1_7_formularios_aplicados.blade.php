<div class="card-chart" id="chart_3_1_7_formularios_aplicados">
    <div class="txt-title">Formulários Aplicados</div>

    <div class="chart"></div>

    <div class="chart-txt"></div>
</div>

@push('after-scripts')
    <script>
        function chart_3_1_7_formularios_aplicados(ret) {
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

            var chart = new google.visualization.PieChart($('#chart_3_1_7_formularios_aplicados .chart')[0]);
            chart.draw(arrayToDataTable, options);

            if (total) {
                $("#chart_3_1_7_formularios_aplicados .chart-txt").html('100% = '+total);
            }
        }
    </script>
@endpush
