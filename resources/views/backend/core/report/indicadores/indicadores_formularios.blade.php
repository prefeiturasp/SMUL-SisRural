@cardater
    @slot('body')
        <div class="row">
            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_1_15_unidade_produtiva_formulario_aplicado')
            </div>

            <div class="col-4">
                @include('backend.core.report.indicadores.charts.chart_3_1_7_formularios_aplicados')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_3_3_visitas_aplicacoes_atualizacoes_formulario')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('backend.core.report.indicadores.charts.chart_3_7_pontuacoes_finais')
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <div id="fl_periodo" class="btn-group btn-group-toggle float-right" data-toggle="buttons">
                    <label class="btn btn-dark active">
                        <input type="radio" name="fl_periodo" value="false" checked>Respostas sem período
                    </label>
                    <label class="btn btn-dark">
                        <input type="radio" name="fl_periodo" value="true">Respostas com período
                    </label>
                </div>

                @include('backend.core.report.indicadores.charts.chart_3_x_perguntas_formularios')
                @include('backend.core.report.indicadores.charts.chart_3_x_perguntas_formularios_periodo')
            </div>
        </div>

        <div class="mt-3"></div>
    @endslot
@endcardater

@push('after-scripts')
    <script>
        $("#fl_periodo input").change(function() {
            submitFilter(true);
        });
    </script>
@endpush
