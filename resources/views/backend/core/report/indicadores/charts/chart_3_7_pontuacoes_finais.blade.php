<div class="card-chart" id="chart_3_7_pontuacoes_finais">
    <div class="txt-title">Pontuações Finais dos Formulários Aplicados</div>

    <div class="chart"></div>
</div>

@push('after-scripts')
    <script>
        function chart_3_7_pontuacoes_finais(ret) {
            var values =[
                ['Formulário', 'Máximo', 'Média', 'Mínimo'],
            ];

            for(var i =0; i < ret.length; i++) {
                var item = ret[i];
                values.push([item.formulario+" ("+item.total+")",  item.maximo, item.media, item.minimo]);
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
                theme:'material',
                bars: 'horizontal',
            };

            var arrayToDataTable = google.visualization.arrayToDataTable(values);

            var chart = new google.charts.Bar($('#chart_3_7_pontuacoes_finais .chart')[0]);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));
        }
    </script>
@endpush
