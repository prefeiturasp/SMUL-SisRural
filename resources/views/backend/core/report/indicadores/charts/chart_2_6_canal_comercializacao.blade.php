<div class="card-chart" id="chart_2_6_canal_comercializacao">
    <div class="txt-title">Canais de Comercialização</div>

    <div class="chart"></div>

    <div class="txt-legend">Não respondeu: <span>-</span></div>
</div>

@push('after-scripts')
    <script>
        function chart_2_6_canal_comercializacao(ret) {
            var values =[
                ['Comercialização', 'UPAs'],
            ];

            for(var i =0; i < ret.itens.length; i++) {
                var item = ret.itens[i];
                values.push([item.nome+" ("+item.total+")", item.total]);
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

            var chart = new google.charts.Bar($('#chart_2_6_canal_comercializacao .chart')[0]);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));

            $("#chart_2_6_canal_comercializacao .txt-legend span").html(ret.nao_respondeu);
        }
    </script>
@endpush
