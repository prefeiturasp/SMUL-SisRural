<div class="card-chart" id="chart_2_11_genero">
    <div class="txt-title">Gênero</div>

    <div class="chart"></div>

    <div class="txt-legend">
        Não respondeu: <span>-</span>
        &nbsp;&nbsp;Total: <span>-</span>
    </div>
</div>

@push('after-scripts')
    <script>
        function chart_2_11_genero(ret) {
            var values = [
                ['Gênero', 'Total']
            ];

            if (!ret) {
                ret = [];
            }

            var total = 0;
            for(var i =0; i < ret.itens.length; i++) {
                var item = ret.itens[i];
                values.push([item.nome+" ("+item.total+")", item.total]);
                total += item.total;
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

            var chart = new google.visualization.PieChart($('#chart_2_11_genero .chart')[0]);
            chart.draw(arrayToDataTable, options);

            $("#chart_2_11_genero .txt-legend span:first").html(ret.nao_respondeu);
            $("#chart_2_11_genero .txt-legend span:last").html(total);
        }
    </script>
@endpush
