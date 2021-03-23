<div class="card-chart" id="chart_2_3_tamanho_upa">
    <div class="txt-title">Tamanho da UPA</div>

    <div class="chart"></div>

    <div class="txt-legend">
        Não respondeu: <span>-</span>
        &nbsp;&nbsp;Total: <span>-</span>
    </div>
</div>

@push('after-scripts')
    <script>
        function chart_2_3_tamanho_upa(ret) {
            var values = [
                ['Tamanho', 'Total']
            ];

            if (!ret) {
                ret = [];
            }

            var total = 0;
            for(var i =0; i < ret.itens.length; i++) {
                var item = ret.itens[i];
                values.push([item.nome+" ("+item.count+")", item.count]);

                total += item.count;
            }
            total += ret.nao_respondeu;

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

            var chart = new google.visualization.PieChart($('#chart_2_3_tamanho_upa .chart')[0]);
            chart.draw(arrayToDataTable, options);

            $("#chart_2_3_tamanho_upa .txt-legend span:first").html(ret.nao_respondeu);
            $("#chart_2_3_tamanho_upa .txt-legend span:last").html(total);
        }
    </script>
@endpush
