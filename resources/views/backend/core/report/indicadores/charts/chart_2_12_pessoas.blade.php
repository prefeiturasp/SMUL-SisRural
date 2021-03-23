<div class="card-chart" id="chart_2_12_pessoas">
    <div class="txt-title">Número de Pessoas Envolvidas com a Atividade Agrícola</div>

    <div class="chart"></div>

    <div class="txt-legend">Não respondeu: <span>-</span></div>
</div>

@push('after-scripts')
    <script>
        function chart_2_12_pessoas(ret) {
            var values =[
                ['Pessoas', 'Total'],
            ];

            for(var i =0; i < ret.itens.length; i++) {
                var item = ret.itens[i];
                values.push([item.nome, item.total]);
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

            var chart = new google.charts.Bar($('#chart_2_12_pessoas .chart')[0]);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));

            $("#chart_2_12_pessoas .txt-legend span").html(ret.nao_respondeu);
        }
    </script>
@endpush
