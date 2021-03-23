<div class="card-chart" id="chart_2_15_destinacao_residuos_solidos">
    <div class="txt-title">Destinação de Resíduos Sólidos</div>

    <div class="chart"></div>

    <div class="txt-legend">Não respondeu: <span>-</span></div>
</div>

@push('after-scripts')
    <script>
        function chart_2_15_destinacao_residuos_solidos(ret) {
            var values = [
                ['Nome', 'Total']
            ];

            if (!ret) {
                ret = [];
            }

            for(var i =0; i < ret.itens.length; i++) {
                var item = ret.itens[i];
                values.push([item.nome+" ("+item.count+")", item.count]);
            }

            var arrayToDataTable = google.visualization.arrayToDataTable(values);
            var columnRange = arrayToDataTable.getColumnRange(1);

            var options = {
                bars: 'horizontal',
                legend: {
                   position: 'none'
                },
                // sliceVisibilityThreshold:0,
            };

            var chart = new google.charts.Bar($('#chart_2_15_destinacao_residuos_solidos .chart')[0]);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));

            $("#chart_2_15_destinacao_residuos_solidos .txt-legend span").html(ret.nao_respondeu);
        }
    </script>
@endpush
