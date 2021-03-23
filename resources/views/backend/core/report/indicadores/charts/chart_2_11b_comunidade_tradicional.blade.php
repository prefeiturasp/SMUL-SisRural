<div class="card-chart" id="chart_2_11b_comunidade_tradicional">
    <div class="txt-title">Comunidade Tradicional</div>

    <div class="chart"></div>

    <div class="txt-legend">Não respondeu: <span>-</span></div>
</div>

@push('after-scripts')
    <script>
        function chart_2_11b_comunidade_tradicional(ret) {
            var values = [
                ['Comunidade Tradicional', 'Total']
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

            var chart = new google.visualization.PieChart($('#chart_2_11b_comunidade_tradicional .chart')[0]);
            chart.draw(arrayToDataTable, options);

            $("#chart_2_11b_comunidade_tradicional .txt-legend span").html(ret.nao_respondeu);
        }
    </script>
@endpush
