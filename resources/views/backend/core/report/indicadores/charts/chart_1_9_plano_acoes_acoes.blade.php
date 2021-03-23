<div class="card-chart" id="chart_1_9_plano_acoes_acoes">
    <div class="txt-title">Planos de Ação</div>

    <div class="chart"></div>
</div>

@push('after-scripts')
    <script>
        function chart_1_9_plano_acoes_acoes(ret) {
            var values = [
                ['Tipo', 'Não Ininciada', 'Em Andamento', 'Concluídas', 'Canceladas'],
            ];

            if (!ret) {
                ret = [];
            }

            for(var i =0; i < ret.length; i++) {
                var item = ret[i];
                values.push([item.nome.toUpperCase()+" ("+item.upas+" UPAS)", item.nao_iniciado, item.em_andamento, item.concluido, item.cancelado]);
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
                    0:{color:'#B4DED3'},
                    1:{color:'#5E97F6'},
                    2:{color:'#77D662'},
                    3:{color:'#FF6C6C'},
                }
                // theme: 'material'
            };

            var chart = new google.charts.Bar($('#chart_1_9_plano_acoes_acoes .chart')[0]);
            chart.draw(arrayToDataTable, google.charts.Bar.convertOptions(options));
        }
    </script>
@endpush
