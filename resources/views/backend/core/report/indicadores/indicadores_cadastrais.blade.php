@cardater
    @slot('body')
        <div class="row">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_2_1_uso_solo')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_2_certificacao_producao')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_3_tamanho_upa')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_4_infraestrutura')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_2_5_regularizacao_ambiental')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_2_6_canal_comercializacao')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_7_associativismo')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_8_renda_agricula_familiar')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_9_rendimento_comercializacao')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_10_relacao_propriedade')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_11_genero')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_12_pessoas')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_13_fontes_agua')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_14_esgoto')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_2_15_destinacao_residuos_solidos')
            </div>
        </div>

        <div class="mt-3"></div>
    @endslot
@endcardater
