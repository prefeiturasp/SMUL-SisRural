@cardater
    @slot('body')
        <div class="row">
            <div class="col-md-3">
                @include('backend.core.report.indicadores.charts.chart_5_1_caderno_campo')
            </div>

            <div class="col-md-3">
                @include('backend.core.report.indicadores.charts.chart_5_2_caderno_campo_upas')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_5_5_caderno_tecnico')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_5_x_perguntas_cadernos')
            </div>
        </div>

        <div class="mt-3"></div>
    @endslot
@endcardater
