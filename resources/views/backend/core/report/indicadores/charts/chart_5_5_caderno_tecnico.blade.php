<div class="card-chart" id="chart_5_5_caderno_tecnico">
    <div class="txt-title">Distribuição da Aplicação dos Cadernos de Campo por Técnicos</div>

    <div class="chart"></div>
</div>

@push('after-scripts')
    <script>
        function chart_5_5_caderno_tecnico(ret) {
            var values = [
                ['Técnico', 'Total de Cadernos']
            ];

            if (!ret) {
                ret = [];
            }

            for(var i =0; i < ret.length; i++) {
                var item = ret[i];
                values.push([item.nome+" ("+item.count+")", item.count]);
            }

            var arrayToDataTable = google.visualization.arrayToDataTable(values);
            var columnRange = arrayToDataTable.getColumnRange(1);

            var options = {
                height: 60 + 40 + arrayToDataTable.getNumberOfRows() * 18,

                bars: 'horizontal',
                legend: {
                   position: 'none'
                },
                sliceVisibilityThreshold:0,
            };

            var chart = new google.charts.Bar($('#chart_5_5_caderno_tecnico .chart')[0]);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));
        }
    </script>
@endpush
