<div class="card-chart" id="chart_2_4_infraestrutura">
    <div class="txt-title">Infraestrutura de Produção</div>

    <div class="chart"></div>

    <div class="txt-legend">Não respondeu: <span>-</span></div>
</div>

@push('after-scripts')
    <script>
        function chart_2_4_infraestrutura(ret) {
            var values = [
                ['Infraestrutura', 'Total']
            ];

            if (!ret) {
                ret = [];
            }

            for(var i =0; i < ret.itens.length; i++) {
                var item = ret.itens[i];
                values.push([item.nome+" ("+item.total+")", item.total]);
            }

            var options = {
                backgroundColor: {
                    fill:'transparent'
                },
                chartArea: { width:"94%",height:"92%" },
                legend: {'position': 'right'},
                pieSliceText: 'percentage',
                theme: 'material',
                // sliceVisibilityThreshold:0
            };

            var arrayToDataTable = google.visualization.arrayToDataTable(values);

            var chart = new google.visualization.PieChart($('#chart_2_4_infraestrutura .chart')[0]);
            chart.draw(arrayToDataTable, options);

            $("#chart_2_4_infraestrutura .txt-legend span").html(ret.nao_respondeu);
        }
    </script>
@endpush
