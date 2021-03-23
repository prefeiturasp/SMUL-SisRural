<div class="card-chart" id="chart_2_10_relacao_propriedade">
    <div class="txt-title">Relação com a Propriedade</div>

    <div class="chart"></div>

    <div class="txt-legend">
        Total: <span>-</span>
    </div>
</div>

@push('after-scripts')
    <script>
        function chart_2_10_relacao_propriedade(ret) {
            var values = [
                ['Relação Propriedade', 'Total']
            ];

            if (!ret) {
                ret = [];
            }

            var total = 0;
            for(var i =0; i < ret.length; i++) {
                var item = ret[i];
                values.push([item.nome+" ("+item.total+")", item.total]);
                total += item.total;
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

            var chart = new google.visualization.PieChart($('#chart_2_10_relacao_propriedade .chart')[0]);
            chart.draw(arrayToDataTable, options);

            $("#chart_2_10_relacao_propriedade .txt-legend span").html(total);
        }
    </script>
@endpush
