<div class="card-chart" id="chart_2_1_uso_solo">
    <div class="txt-title">Uso do Solo</div>

    <div class="chart"></div>

    <div class="txt-legend">
        Não respondeu: <span>-</span>
    </div>
</div>

@push('after-scripts')
    <script>
        function chart_2_1_uso_solo(ret) {
            var values =[
                ['Uso do Solo', 'UPAs', 'Área'],
            ];

            for(var i =0; i < ret.itens.length; i++) {
                var item = ret.itens[i];
                values.push([item.nome, item.upas, item.area]);
            }

            var arrayToDataTable = google.visualization.arrayToDataTable(values);

            var options = {
                height: 40 + 40 + arrayToDataTable.getNumberOfRows() * (18*2),
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
                theme:'material',
                bars: 'horizontal',
            };

            var chart = new google.charts.Bar($('#chart_2_1_uso_solo .chart')[0]);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));

            $("#chart_2_1_uso_solo .txt-legend span:first").html(ret.nao_respondeu);
            // $("#chart_2_1_uso_solo .txt-legend span:last").html(total);
        }
    </script>
@endpush
