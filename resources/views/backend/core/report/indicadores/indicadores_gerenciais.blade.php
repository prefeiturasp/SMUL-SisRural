@cardater
    @slot('body')
        <div class="row">
            <div class="col-8 d-flex flex-column">
                <div class="row h-100">
                    <div class="col-md-3">
                        @include('backend.core.report.indicadores.charts.chart_1_1_unidade_produtiva')
                    </div>
                    <div class="col-md-3">
                        @include('backend.core.report.indicadores.charts.chart_1_2_produtor')
                    </div>
                    <div class="col-md-3">
                        @include('backend.core.report.indicadores.charts.chart_1_4_upas_atendidas')
                    </div>
                    <div class="col-md-3">
                        @include('backend.core.report.indicadores.charts.chart_1_5_atendimentos_realizados')
                    </div>
                </div>

                <div class="row h-100 mt-4">
                    <div class="col-md-4">
                        @include('backend.core.report.indicadores.charts.chart_1_6_tecnicos_ativos')
                    </div>
                    <div class="col-md-4">
                        @include('backend.core.report.indicadores.charts.chart_5_1_caderno_campo')
                    </div>
                    <div class="col-md-4">
                        @include('backend.core.report.indicadores.charts.chart_1_15_unidade_produtiva_formulario_aplicado')
                    </div>
                </div>
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_1_7_formularios_aplicados')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_1_8_visitas_aplicacoes_atualizacoes')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_1_13b_distribuicao_atendimento_tecnico')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_1_9_plano_acoes_acoes')
            </div>
        </div>

        <div class="mt-3"></div>
    @endslot
@endcardater
