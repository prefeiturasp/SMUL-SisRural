<div class="card-chart" id="chart_2_2_certificacao_producao">
    <div class="txt-title">Certificação da Produção</div>

    <div class="chart"></div>

    <div class="txt-legend">Não respondeu: <span>-</span></div>
</div>

@push('after-scripts')
    <script>
        function chart_2_2_certificacao_producao(ret) {
            var values = [
                ['Certificação', 'Total']
            ];

            if (!ret) {
                ret = [];
            }

            for(var i =0; i < ret.itens.length; i++) {
                var item = ret.itens[i];
                values.push([item.nome+" ("+item.count+")", item.count]);
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

            var chart = new google.visualization.PieChart($('#chart_2_2_certificacao_producao .chart')[0]);
            chart.draw(arrayToDataTable, options);

            $("#chart_2_2_certificacao_producao .txt-legend span").html(ret.nao_respondeu);
        }
    </script>
@endpush
